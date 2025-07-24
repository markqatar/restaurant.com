<?php

class Product {
    private $db;
    private $table = 'final_products';
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Create product
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, name_ar, description, description_ar, category_id, recipe_id, 
                   price, cost, image, barcode, is_active, show_on_website, preparation_time, sort_order) 
                  VALUES (:name, :name_ar, :description, :description_ar, :category_id, :recipe_id,
                          :price, :cost, :image, :barcode, :is_active, :show_on_website, :preparation_time, :sort_order)";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':name' => $data['name'],
            ':name_ar' => $data['name_ar'] ?? null,
            ':description' => $data['description'] ?? null,
            ':description_ar' => $data['description_ar'] ?? null,
            ':category_id' => $data['category_id'],
            ':recipe_id' => $data['recipe_id'] ?? null,
            ':price' => $data['price'],
            ':cost' => $data['cost'] ?? 0,
            ':image' => $data['image'] ?? null,
            ':barcode' => $data['barcode'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
            ':show_on_website' => $data['show_on_website'] ?? 1,
            ':preparation_time' => $data['preparation_time'] ?? 0,
            ':sort_order' => $data['sort_order'] ?? 0
        ]);
    }
    
    // Read all products
    public function read($category_id = null, $active_only = false) {
        $query = "SELECT p.*, c.name as category_name, c.name_ar as category_name_ar
                  FROM " . $this->table . " p
                  LEFT JOIN product_categories c ON p.category_id = c.id";
        
        $conditions = [];
        $params = [];
        
        if ($category_id) {
            $conditions[] = "p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        if ($active_only) {
            $conditions[] = "p.is_active = 1";
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY p.sort_order, p.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Read single product
    public function readOne($id) {
        $query = "SELECT p.*, c.name as category_name, r.name as recipe_name
                  FROM " . $this->table . " p
                  LEFT JOIN product_categories c ON p.category_id = c.id
                  LEFT JOIN recipes r ON p.recipe_id = r.id
                  WHERE p.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Update product
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, name_ar = :name_ar, description = :description, 
                      description_ar = :description_ar, category_id = :category_id, 
                      recipe_id = :recipe_id, price = :price, cost = :cost, 
                      image = :image, barcode = :barcode, is_active = :is_active,
                      show_on_website = :show_on_website, preparation_time = :preparation_time,
                      sort_order = :sort_order
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':name_ar' => $data['name_ar'] ?? null,
            ':description' => $data['description'] ?? null,
            ':description_ar' => $data['description_ar'] ?? null,
            ':category_id' => $data['category_id'],
            ':recipe_id' => $data['recipe_id'] ?? null,
            ':price' => $data['price'],
            ':cost' => $data['cost'] ?? 0,
            ':image' => $data['image'] ?? null,
            ':barcode' => $data['barcode'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
            ':show_on_website' => $data['show_on_website'] ?? 1,
            ':preparation_time' => $data['preparation_time'] ?? 0,
            ':sort_order' => $data['sort_order'] ?? 0
        ]);
    }
    
    // Delete product
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([':id' => $id]);
    }
    
    // Get featured products
    public function getFeatured($limit = 8) {
        $query = "SELECT p.*, c.name as category_name
                  FROM " . $this->table . " p
                  LEFT JOIN product_categories c ON p.category_id = c.id
                  WHERE p.is_active = 1 AND p.show_on_website = 1
                  ORDER BY p.sort_order, p.name
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Search products
    public function search($keyword, $category_id = null) {
        $query = "SELECT p.*, c.name as category_name
                  FROM " . $this->table . " p
                  LEFT JOIN product_categories c ON p.category_id = c.id
                  WHERE p.is_active = 1 AND p.show_on_website = 1
                  AND (p.name LIKE :keyword OR p.description LIKE :keyword)";
        
        $params = [':keyword' => '%' . $keyword . '%'];
        
        if ($category_id) {
            $query .= " AND p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        $query .= " ORDER BY p.sort_order, p.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get products by category
    public function getByCategory($category_id) {
        return $this->read($category_id, true);
    }
    
    // Update product status
    public function updateStatus($id, $is_active) {
        $query = "UPDATE " . $this->table . " SET is_active = :is_active WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':is_active' => $is_active
        ]);
    }
    
    // Count products
    public function count($category_id = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $params = [];
        
        if ($category_id) {
            $query .= " WHERE category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>