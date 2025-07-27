<?php
class Page {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllPages($status = null) {
        try {
            $sql = "SELECT p.*, u.username as author_name 
                    FROM pages p 
                    LEFT JOIN users u ON p.author_id = u.id";
            
            if ($status) {
                $sql .= " WHERE p.status = ?";
            }
            
            $sql .= " ORDER BY p.sort_order, p.title";
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($status) {
                $stmt->execute([$status]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching pages: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPageById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, u.username as author_name 
                FROM pages p 
                LEFT JOIN users u ON p.author_id = u.id 
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching page: " . $e->getMessage());
            return false;
        }
    }
    
    public function getPageBySlug($slug) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, u.username as author_name 
                FROM pages p 
                LEFT JOIN users u ON p.author_id = u.id 
                WHERE p.slug = ? AND p.status = 'published'
            ");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching page by slug: " . $e->getMessage());
            return false;
        }
    }
    
    public function createPage($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO pages (title, slug, content, excerpt, featured_image, meta_title, meta_description, meta_keywords, 
                                 status, template, parent_id, sort_order, author_id, published_at, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $publishedAt = ($data['status'] === 'published') ? date('Y-m-d H:i:s') : null;
            
            return $stmt->execute([
                $data['title'],
                $data['slug'],
                $data['content'] ?? '',
                $data['excerpt'] ?? null,
                $data['featured_image'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['status'] ?? 'draft',
                $data['template'] ?? 'default',
                $data['parent_id'] ?? null,
                $data['sort_order'] ?? 0,
                $data['author_id'],
                $publishedAt
            ]);
        } catch (PDOException $e) {
            error_log("Error creating page: " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePage($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE pages 
                SET title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?, meta_title = ?, 
                    meta_description = ?, meta_keywords = ?, status = ?, template = ?, parent_id = ?, 
                    sort_order = ?, published_at = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $publishedAt = ($data['status'] === 'published') ? date('Y-m-d H:i:s') : null;
            
            return $stmt->execute([
                $data['title'],
                $data['slug'],
                $data['content'] ?? '',
                $data['excerpt'] ?? null,
                $data['featured_image'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['status'] ?? 'draft',
                $data['template'] ?? 'default',
                $data['parent_id'] ?? null,
                $data['sort_order'] ?? 0,
                $publishedAt,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating page: " . $e->getMessage());
            return false;
        }
    }
    
    public function deletePage($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pages WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting page: " . $e->getMessage());
            return false;
        }
    }
    
    public function generateSlug($title, $id = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $id)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM pages WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
?>