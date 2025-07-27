<?php
class Slideshow {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllSlides($status = null) {
        try {
            $sql = "SELECT s.*, p.title as page_title, a.title as article_title 
                    FROM slideshows s 
                    LEFT JOIN pages p ON s.page_id = p.id
                    LEFT JOIN articles a ON s.article_id = a.id";
            
            if ($status) {
                $sql .= " WHERE s.status = ?";
            }
            
            $sql .= " ORDER BY s.sort_order, s.id";
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($status) {
                $stmt->execute([$status]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching slides: " . $e->getMessage());
            return [];
        }
    }
    
    public function getSlideById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT s.*, p.title as page_title, a.title as article_title 
                FROM slideshows s 
                LEFT JOIN pages p ON s.page_id = p.id
                LEFT JOIN articles a ON s.article_id = a.id
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching slide: " . $e->getMessage());
            return false;
        }
    }
    
    public function createSlide($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO slideshows (title, image, caption, link_url, link_text, page_id, article_id, sort_order, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            return $stmt->execute([
                $data['title'],
                $data['image'],
                $data['caption'] ?? null,
                $data['link_url'] ?? null,
                $data['link_text'] ?? null,
                $data['page_id'] ?? null,
                $data['article_id'] ?? null,
                $data['sort_order'] ?? 0,
                $data['status'] ?? 'active'
            ]);
        } catch (PDOException $e) {
            error_log("Error creating slide: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateSlide($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE slideshows 
                SET title = ?, image = ?, caption = ?, link_url = ?, link_text = ?, page_id = ?, article_id = ?, sort_order = ?, status = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['title'],
                $data['image'],
                $data['caption'] ?? null,
                $data['link_url'] ?? null,
                $data['link_text'] ?? null,
                $data['page_id'] ?? null,
                $data['article_id'] ?? null,
                $data['sort_order'] ?? 0,
                $data['status'] ?? 'active',
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating slide: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteSlide($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM slideshows WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting slide: " . $e->getMessage());
            return false;
        }
    }
    
    public function getActiveSlides($limit = null) {
        try {
            $sql = "SELECT s.*, p.title as page_title, a.title as article_title 
                    FROM slideshows s 
                    LEFT JOIN pages p ON s.page_id = p.id
                    LEFT JOIN articles a ON s.article_id = a.id
                    WHERE s.status = 'active'
                    ORDER BY s.sort_order, s.id";
            
            if ($limit) {
                $sql .= " LIMIT ?";
            }
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($limit) {
                $stmt->execute([$limit]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching active slides: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPages() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, title FROM pages WHERE status = 'published' ORDER BY title");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching pages: " . $e->getMessage());
            return [];
        }
    }
    
    public function getArticles() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, title FROM articles WHERE status = 'published' ORDER BY title");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching articles: " . $e->getMessage());
            return [];
        }
    }
}
?>