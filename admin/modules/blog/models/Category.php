<?php
class Category {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllCategories($status = null) {
        try {
            $sql = "SELECT c.*, COUNT(a.id) as article_count 
                    FROM categories c 
                    LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'";
            
            if ($status) {
                $sql .= " WHERE c.status = ?";
            }
            
            $sql .= " GROUP BY c.id ORDER BY c.sort_order, c.name";
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($status) {
                $stmt->execute([$status]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }
    
    public function getCategoryById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching category: " . $e->getMessage());
            return false;
        }
    }
    
    public function getCategoryBySlug($slug) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE slug = ?");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching category by slug: " . $e->getMessage());
            return false;
        }
    }
    
    public function createCategory($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO categories (name, slug, description, parent_id, sort_order, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            return $stmt->execute([
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['parent_id'] ?? null,
                $data['sort_order'] ?? 0,
                $data['status'] ?? 'active'
            ]);
        } catch (PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateCategory($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE categories 
                SET name = ?, slug = ?, description = ?, parent_id = ?, sort_order = ?, status = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['parent_id'] ?? null,
                $data['sort_order'] ?? 0,
                $data['status'] ?? 'active',
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteCategory($id) {
        try {
            // Update articles to remove category assignment
            $stmt = $this->pdo->prepare("UPDATE articles SET category_id = NULL WHERE category_id = ?");
            $stmt->execute([$id]);
            
            // Delete category
            $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }
    
    public function generateSlug($name, $id = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $id)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM categories WHERE slug = ?";
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