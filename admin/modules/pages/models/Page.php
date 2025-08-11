<?php

class Page {
    private $db;
    private $table = 'pages';
    private $translations_table = 'page_translations';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->runMigrations();
    }

    // Run one-time migrations: move *_ar fields into translations table then drop them; add featured_image column
    private function runMigrations() {
        try {
            // 1. If legacy Arabic columns exist, migrate their data into page_translations
            $col = $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE 'title_ar'")->fetch(PDO::FETCH_ASSOC);
            if ($col) {
                // Ensure translations table exists (should by separate migration)
                $this->db->exec("CREATE TABLE IF NOT EXISTS `page_translations` (
                  `id` INT NOT NULL AUTO_INCREMENT,
                  `page_id` INT NOT NULL,
                  `language_code` VARCHAR(10) NOT NULL,
                  `title` VARCHAR(200) NOT NULL,
                  `slug` VARCHAR(200) DEFAULT NULL,
                  `content` TEXT DEFAULT NULL,
                  `meta_title` VARCHAR(200) DEFAULT NULL,
                  `meta_description` TEXT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `uniq_page_language` (`page_id`,`language_code`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                $rows = $this->db->query("SELECT id, title_ar, content_ar, meta_title_ar, meta_description_ar FROM `{$this->table}` WHERE (title_ar IS NOT NULL AND title_ar <> '')")->fetchAll(PDO::FETCH_ASSOC);
                if ($rows) {
                    foreach ($rows as $r) {
                        $slug = $this->generateSlug($r['title_ar'], 'ar', $r['id']);
                        $stmt = $this->db->prepare("INSERT INTO {$this->translations_table} (page_id, language_code, title, slug, content, meta_title, meta_description, created_at, updated_at)
                            VALUES (:pid,'ar',:title,:slug,:content,:meta_title,:meta_description,NOW(),NOW())
                            ON DUPLICATE KEY UPDATE title=VALUES(title), slug=VALUES(slug), content=VALUES(content), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description), updated_at=NOW()");
                        $stmt->execute([
                            ':pid' => $r['id'],
                            ':title' => $r['title_ar'],
                            ':slug' => $slug,
                            ':content' => $r['content_ar'],
                            ':meta_title' => $r['meta_title_ar'],
                            ':meta_description' => $r['meta_description_ar'],
                        ]);
                    }
                }
                // Drop legacy columns
                $this->db->exec("ALTER TABLE `{$this->table}` DROP COLUMN title_ar, DROP COLUMN content_ar, DROP COLUMN meta_title_ar, DROP COLUMN meta_description_ar");
            }

            // 2. Ensure featured_image column
            $fcol = $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE 'featured_image'")->fetch(PDO::FETCH_ASSOC);
            if (!$fcol) {
                $this->db->exec("ALTER TABLE `{$this->table}` ADD COLUMN `featured_image` VARCHAR(255) NULL AFTER `sort_order`");
            }
        } catch (Exception $e) {
            // Silent fail to avoid breaking front-end; could log
        }
    }

    // Get all pages (with default public language title joined)
    public function getAll($only_published = false, $defaultLang = null) {
        $defaultLang = $defaultLang ?: get_default_public_language_from_db();
        $sql = "SELECT p.*, t.title, t.slug FROM {$this->table} p
                LEFT JOIN {$this->translations_table} t ON t.page_id = p.id AND t.language_code = :lang" .
                ($only_published ? " WHERE p.is_published = 1" : "") .
                " ORDER BY p.sort_order, t.title";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':lang' => $defaultLang]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find single with all translations
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$page) return null;
        $page['translations'] = $this->getTranslations($id);
        return $page;
    }

    public function findBySlug($slug, $lang = null) {
        $lang = $lang ?: get_default_public_language_from_db();
        $stmt = $this->db->prepare("SELECT p.*, t.title, t.content, t.meta_title, t.meta_description FROM {$this->table} p
            INNER JOIN {$this->translations_table} t ON t.page_id = p.id
            WHERE t.slug = :slug AND t.language_code = :lang AND p.is_published = 1 LIMIT 1");
        $stmt->execute([':slug' => $slug, ':lang' => $lang]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($baseData, $translations) {
        $sql = "INSERT INTO {$this->table} (is_published, sort_order, featured_image, created_at) VALUES (:is_published,:sort_order,:featured_image,NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':is_published' => $baseData['is_published'] ?? 0,
            ':sort_order' => $baseData['sort_order'] ?? 0,
            ':featured_image' => $baseData['featured_image'] ?? null,
        ]);
        $pageId = $this->db->lastInsertId();
        $this->saveTranslations($pageId, $translations);
        return $pageId;
    }

    public function update($id, $baseData, $translations) {
        $sql = "UPDATE {$this->table} SET is_published=:is_published, sort_order=:sort_order, featured_image=:featured_image WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':is_published' => $baseData['is_published'] ?? 0,
            ':sort_order' => $baseData['sort_order'] ?? 0,
            ':featured_image' => $baseData['featured_image'] ?? null,
        ]);
        $this->saveTranslations($id, $translations, true);
        return true;
    }

    public function delete($id) {
        $this->db->prepare("DELETE FROM {$this->translations_table} WHERE page_id = :id")->execute([':id' => $id]);
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function generateSlug($title, $language_code, $excludePageId = null) {
        $base = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = $base ?: uniqid();
        $i = 1;
        while ($this->slugExists($slug, $language_code, $excludePageId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function slugExists($slug, $language_code, $excludePageId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->translations_table} WHERE slug = :slug AND language_code = :lang" . ($excludePageId ? " AND page_id != :pid" : "");
        $stmt = $this->db->prepare($sql);
        $params = [':slug' => $slug, ':lang' => $language_code];
        if ($excludePageId) $params[':pid'] = $excludePageId;
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    private function getTranslations($pageId) {
        $stmt = $this->db->prepare("SELECT language_code, title, slug, content, meta_title, meta_description FROM {$this->translations_table} WHERE page_id = :id");
        $stmt->execute([':id' => $pageId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[$row['language_code']] = $row;
        }
        return $result;
    }

    private function saveTranslations($pageId, $translations, $isUpdate = false) {
        foreach ($translations as $lang => $t) {
            $title = trim($t['title'] ?? '');
            if ($title === '') {
                // skip empty translation on create/update (could enforce deletion)
                continue;
            }
            $slug = $t['slug'] ?? $this->generateSlug($title, $lang, $pageId);
            $content = $t['content'] ?? null;
            $meta_title = $t['meta_title'] ?? null;
            $meta_description = $t['meta_description'] ?? null;

            // Upsert
            $sql = "INSERT INTO {$this->translations_table} (page_id, language_code, title, slug, content, meta_title, meta_description, created_at, updated_at)
                    VALUES (:pid,:lang,:title,:slug,:content,:meta_title,:meta_description,NOW(),NOW())
                    ON DUPLICATE KEY UPDATE title=VALUES(title), slug=VALUES(slug), content=VALUES(content), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description), updated_at=NOW()";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':pid' => $pageId,
                ':lang' => $lang,
                ':title' => $title,
                ':slug' => $slug,
                ':content' => $content,
                ':meta_title' => $meta_title,
                ':meta_description' => $meta_description,
            ]);
        }
    }
}
?>