<?php

class Branch {
    private $db;
    private $table = 'branches';
    
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    // Create branch
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email1, email2, tel1, tel2, address, city_id, referente, is_active) 
                  VALUES (:name, :email1, :email2, :tel1, :tel2, :address, :city_id, :referente, :is_active)";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':name' => $data['name'],
            ':email1' => $data['email1'] ?? null,
            ':email2' => $data['email2'] ?? null,
            ':tel1' => $data['tel1'] ?? null,
            ':tel2' => $data['tel2'] ?? null,
            ':address' => $data['address'] ?? null,
            ':city_id' => $data['city_id'] ?? null,
            ':referente' => $data['referente'] ?? null,
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }
    
    // Read all branches
    public function read($active_only = false) {
        $query = "SELECT b.*, c.name as city_name,
                         (SELECT COUNT(*) FROM user_branch_assignments uba WHERE uba.branch_id = b.id) as users_count
                  FROM " . $this->table . " b
                  LEFT JOIN cities c ON b.city_id = c.id";
        
        if ($active_only) {
            $query .= " WHERE b.is_active = 1";
        }
        
        $query .= " ORDER BY b.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Alias for getAll() method - used by SettingController
    public function getAll($active_only = false) {
        return $this->read($active_only);
    }
    
    // Read single branch
    public function readOne($id) {
        $query = "SELECT b.*, c.name as city_name
                  FROM " . $this->table . " b
                  LEFT JOIN cities c ON b.city_id = c.id
                  WHERE b.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Update branch
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, email1 = :email1, email2 = :email2,
                      tel1 = :tel1, tel2 = :tel2, address = :address,
                      city_id = :city_id, referente = :referente,
                      is_active = :is_active, updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':email1' => $data['email1'] ?? null,
            ':email2' => $data['email2'] ?? null,
            ':tel1' => $data['tel1'] ?? null,
            ':tel2' => $data['tel2'] ?? null,
            ':address' => $data['address'] ?? null,
            ':city_id' => $data['city_id'] ?? null,
            ':referente' => $data['referente'] ?? null,
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }
    
    // Delete branch
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Remove user assignments
            $assignments_query = "DELETE FROM user_branch_assignments WHERE branch_id = :branch_id";
            $assignments_stmt = $this->db->prepare($assignments_query);
            $assignments_stmt->execute([':branch_id' => $id]);
            
            // Delete branch
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([':id' => $id]);
            
            $this->db->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Get countries for dropdown
    public function getCountries() {
        $query = "SELECT id, name FROM countries ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        
    // Assign user to branch
    public function assignUser($user_id, $branch_id, $is_primary = false) {
        // If setting as primary, remove primary from other assignments for this user
        if ($is_primary) {
            $update_query = "UPDATE user_branch_assignments SET is_primary = FALSE WHERE user_id = :user_id";
            $update_stmt = $this->db->prepare($update_query);
            $update_stmt->execute([':user_id' => $user_id]);
        }
        
        $query = "INSERT INTO user_branch_assignments (user_id, branch_id, is_primary) 
                  VALUES (:user_id, :branch_id, :is_primary)
                  ON DUPLICATE KEY UPDATE is_primary = :is_primary";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':user_id' => $user_id,
            ':branch_id' => $branch_id,
            ':is_primary' => $is_primary ? 1 : 0
        ]);
    }
    
    // Remove user from branch
    public function removeUser($user_id, $branch_id) {
        $query = "DELETE FROM user_branch_assignments WHERE user_id = :user_id AND branch_id = :branch_id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':user_id' => $user_id,
            ':branch_id' => $branch_id
        ]);
    }
    
    // Get user's branches
    public function getUserBranches($user_id) {
        $query = "SELECT b.*, uba.is_primary
                  FROM " . $this->table . " b
                  JOIN user_branch_assignments uba ON b.id = uba.branch_id
                  WHERE uba.user_id = :user_id AND b.is_active = 1
                  ORDER BY uba.is_primary DESC, b.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get branch users
    public function getBranchUsers($branch_id) {
        $query = "SELECT u.*, uba.is_primary,
                         CONCAT(u.first_name, ' ', u.last_name) as full_name
                  FROM users u
                  JOIN user_branch_assignments uba ON u.id = uba.user_id
                  WHERE uba.branch_id = :branch_id AND u.is_active = 1
                  ORDER BY uba.is_primary DESC, u.first_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':branch_id' => $branch_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Check if user has access to branch
    public function userHasAccess($user_id, $branch_id) {
        $query = "SELECT COUNT(*) as count FROM user_branch_assignments 
                  WHERE user_id = :user_id AND branch_id = :branch_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':branch_id' => $branch_id
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    // Get user's primary branch
    public function getUserPrimaryBranch($user_id) {
        $query = "SELECT b.* FROM " . $this->table . " b
                  JOIN user_branch_assignments uba ON b.id = uba.branch_id
                  WHERE uba.user_id = :user_id AND uba.is_primary = 1 AND b.is_active = 1
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Count branches
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Search branches
    public function search($keyword) {
        $query = "SELECT b.*, c.name as city_name
                  FROM " . $this->table . " b
                  LEFT JOIN cities c ON b.city_id = c.id
                  WHERE b.is_active = 1 
                  AND (b.name LIKE :keyword OR b.address LIKE :keyword OR b.referente LIKE :keyword)
                  ORDER BY b.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>