<?php

class User {
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    // Create user
    public function create($data) {
        try {
            
            $query = "INSERT INTO " . $this->table . " 
                      (username, email, password, first_name, last_name, phone, is_active) 
                      VALUES (:username, :email, :password, :first_name, :last_name, :phone, :is_active)";
            
            $stmt = $this->db->prepare($query);
            
            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $result = $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => $data['password'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':phone' => $data['phone'] ?? null,
                ':is_active' => $data['is_active'] ?? 1
            ]);
            
            if ($result) {
                // Get the new user ID
                $userId = $this->db->lastInsertId();
                
                // Create default user preferences
                $prefsQuery = "INSERT INTO user_preferences (user_id, theme, language, avatar) 
                              VALUES (:user_id, :theme, :language, :avatar)";
                $prefsStmt = $this->db->prepare($prefsQuery);
                
                $prefsResult = $prefsStmt->execute([
                    ':user_id' => $userId,
                    ':theme' => $data['default_theme'] ?? 'light',
                    ':language' => $data['default_language'] ?? 'en',
                    ':avatar' => $data['avatar'] ?? 'default.png'
                ]);
                
                if ($prefsResult) {
                    return $userId; // Return the user ID instead of just true
                } else {
                    return false;
                }
            } else {
                return false;
            }
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }
    
    // Read all users
    public function read($limit = null, $offset = null) {
        $query = "SELECT u.*, GROUP_CONCAT(ug.name SEPARATOR ', ') as user_groups
                  FROM " . $this->table . " u
                  LEFT JOIN user_group_assignments uga ON u.id = uga.user_id
                  LEFT JOIN user_groups ug ON uga.group_id = ug.id
                  GROUP BY u.id
                  ORDER BY u.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT " . $limit;
            if ($offset) {
                $query .= " OFFSET " . $offset;
            }
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Read single user
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Update user
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET username = :username, email = :email, first_name = :first_name, 
                      last_name = :last_name, phone = :phone, is_active = :is_active,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':phone' => $data['phone'] ?? null,
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }
    
    // Change password
    public function changePassword($id, $password) {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password, updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        return $stmt->execute([
            ':id' => $id,
            ':password' => $hashedPassword
        ]);
    }
    
    // Verify password
    public function verifyPassword($userId, $password) {
        $user = $this->readOne($userId);
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }
    
    // Check if username exists
    public function usernameExists($username, $excludeUserId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE username = :username";
        $params = [':username' => $username];
        
        if ($excludeUserId) {
            $query .= " AND id != :user_id";
            $params[':user_id'] = $excludeUserId;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    // Update username
    public function updateUsername($userId, $username) {
        $query = "UPDATE " . $this->table . " SET username = :username, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':username' => $username,
            ':id' => $userId
        ]);
    }
    
    // Delete user
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([':id' => $id]);
    }
    
    // Authenticate user
    public function authenticate($username, $password) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (username = :username OR email = :username) AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':username' => $username]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    // Get user permissions
    public function getPermissions($user_id) {
        $query = "SELECT p.module, p.action 
                  FROM permissions p
                  JOIN user_group_assignments uga ON p.group_id = uga.group_id
                  WHERE uga.user_id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Assign user to group
    public function assignToGroup($user_id, $group_id) {
        $query = "INSERT INTO user_group_assignments (user_id, group_id) VALUES (:user_id, :group_id)";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':user_id' => $user_id,
            ':group_id' => $group_id
        ]);
    }
    
    // Remove user from group
    public function removeFromGroup($user_id, $group_id) {
        $query = "DELETE FROM user_group_assignments WHERE user_id = :user_id AND group_id = :group_id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':user_id' => $user_id,
            ':group_id' => $group_id
        ]);
    }
    
    // Count total users
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>