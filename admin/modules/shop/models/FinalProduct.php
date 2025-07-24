<?php

class FinalProduct {
    private $db;
    private $table = 'final_products';
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Create final product with branch assignment
    public function create($data) {
        // Set default branch if not provided
        if (empty($data['branch_id']) && !empty($data['user_id'])) {
            $data['branch_id'] = getDefaultBranchId($data['user_id']);
        }
        
        $query = "INSERT INTO " . $this->table . " 
                  (name, description, price, cost, category_id, branch_id, barcode, 
                   image_url, is_active, created_by) 
                  VALUES (:name, :description, :price, :cost, :category_id, :branch_id, 
                          :barcode, :image_url, :is_active, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'],
            ':cost' => $data['cost'] ?? null,
            ':category_id' => $data['category_id'] ?? null,
            ':branch_id' => $data['branch_id'] ?? null,
            ':barcode' => $data['barcode'] ?? null,
            ':image_url' => $data['image_url'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
            ':created_by' => $data['created_by']
        ]);
    }
    
    // Read all final products with branch filtering
    public function read($active_only = false, $user_id = null) {
        $base_query = "SELECT fp.*, c.name as category_name, b.name as branch_name,
                              u.first_name, u.last_name
                       FROM " . $this->table . " fp
                       LEFT JOIN categories c ON fp.category_id = c.id
                       LEFT JOIN branches b ON fp.branch_id = b.id
                       LEFT JOIN users u ON fp.created_by = u.id";
        
        $params = [];
        
        if ($active_only) {
            $base_query .= " WHERE fp.is_active = 1";
        }
        
        // Apply branch filtering if user_id provided
        if ($user_id) {
            $filtered = addBranchFilter($base_query, $user_id, $params, 'fp');
            $base_query = $filtered['query'];
            $params = $filtered['params'];
        }
        
        $base_query .= " ORDER BY fp.name";
        
        $stmt = $this->db->prepare($base_query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Read single final product with branch access check
    public function readOne($id, $user_id = null) {
        $base_query = "SELECT fp.*, c.name as category_name, b.name as branch_name,
                              u.first_name, u.last_name
                       FROM " . $this->table . " fp
                       LEFT JOIN categories c ON fp.category_id = c.id
                       LEFT JOIN branches b ON fp.branch_id = b.id
                       LEFT JOIN users u ON fp.created_by = u.id
                       WHERE fp.id = ?";
        
        $params = [$id];
        
        // Apply branch filtering if user_id provided
        if ($user_id) {
            $filtered = addBranchFilter($base_query, $user_id, $params, 'fp');
            $base_query = $filtered['query'];
            $params = $filtered['params'];
        }
        
        $stmt = $this->db->prepare($base_query);
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Update final product with branch validation
    public function update($id, $data, $user_id = null) {
        // Validate branch access
        if ($user_id && !empty($data['branch_id']) && !validateBranchAccess($user_id, $data['branch_id'])) {
            throw new Exception('Accesso alla filiale non autorizzato');
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, description = :description, price = :price, 
                      cost = :cost, category_id = :category_id, branch_id = :branch_id,
                      barcode = :barcode, image_url = :image_url, is_active = :is_active,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'],
            ':cost' => $data['cost'] ?? null,
            ':category_id' => $data['category_id'] ?? null,
            ':branch_id' => $data['branch_id'] ?? null,
            ':barcode' => $data['barcode'] ?? null,
            ':image_url' => $data['image_url'] ?? null,
            ':is_active' => $data['is_active'] ?? 1
        ];
        
        // Apply branch filtering to WHERE clause if user_id provided
        if ($user_id) {
            $filtered = addBranchFilter($query, $user_id, $params, '', 'branch_id');
            $query = $filtered['query'];
            $params = $filtered['params'];
        }
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }
    
    // Delete final product with branch access check
    public function delete($id, $user_id = null) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $params = [$id];
        
        // Apply branch filtering if user_id provided
        if ($user_id) {
            $filtered = addBranchFilter($query, $user_id, $params, '', 'branch_id');
            $query = $filtered['query'];
            $params = $filtered['params'];
        }
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }
    
    // Search products by branch
    public function searchByBranch($keyword, $branch_id = null, $user_id = null) {
        $base_query = "SELECT fp.*, c.name as category_name, b.name as branch_name
                       FROM " . $this->table . " fp
                       LEFT JOIN categories c ON fp.category_id = c.id
                       LEFT JOIN branches b ON fp.branch_id = b.id
                       WHERE fp.is_active = 1 
                       AND (fp.name LIKE ? OR fp.description LIKE ? OR fp.barcode LIKE ?)";
        
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($branch_id) {
            $base_query .= " AND fp.branch_id = ?";
            $params[] = $branch_id;
        }
        
        // Apply branch filtering if user_id provided
        if ($user_id) {
            $filtered = addBranchFilter($base_query, $user_id, $params, 'fp');
            $base_query = $filtered['query'];
            $params = $filtered['params'];
        }
        
        $base_query .= " ORDER BY fp.name LIMIT 50";
        
        $stmt = $this->db->prepare($base_query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get products by category and branch
    public function getByCategory($category_id, $user_id = null) {
        $base_query = "SELECT fp.*, b.name as branch_name
                       FROM " . $this->table . " fp
                       LEFT JOIN branches b ON fp.branch_id = b.id
                       WHERE fp.category_id = ? AND fp.is_active = 1";
        
        $params = [$category_id];
        
        // Apply branch filtering if user_id provided
        if ($user_id) {
            $filtered = addBranchFilter($base_query, $user_id, $params, 'fp');
            $base_query = $filtered['query'];
            $params = $filtered['params'];
        }
        
        $base_query .= " ORDER BY fp.name";
        
        $stmt = $this->db->prepare($base_query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Count products by branch
    public function countByBranch($user_id = null) {
        $base_query = "SELECT b.name as branch_name, COUNT(fp.id) as product_count
                       FROM branches b
                       LEFT JOIN " . $this->table . " fp ON b.id = fp.branch_id AND fp.is_active = 1";
        
        $params = [];
        
        // Apply branch filtering if user_id provided
        if ($user_id) {
            $filtered = addBranchFilter($base_query, $user_id, $params, 'b', 'id');
            $base_query = $filtered['query'];
            $params = $filtered['params'];
        }
        
        $base_query .= " GROUP BY b.id, b.name ORDER BY b.name";
        
        $stmt = $this->db->prepare($base_query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>