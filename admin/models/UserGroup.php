<?php

class UserGroup {
    private $db;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    /**
     * Get all user groups
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT ug.*, 
                       COUNT(uga.user_id) as user_count
                FROM user_groups ug
                LEFT JOIN user_group_assignments uga ON ug.id = uga.group_id
                GROUP BY ug.id
                ORDER BY ug.name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user groups: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user group by ID with its permissions
     */
        public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT ug.*, 
                       COUNT(uga.user_id) as user_count
                FROM user_groups ug
                LEFT JOIN user_group_assignments uga ON ug.id = uga.group_id
                WHERE ug.id = ?
                GROUP BY ug.id
            ");
            $stmt->execute([$id]);
            $group = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($group) {
                // Get permissions for this group
                $stmt = $this->db->prepare("
                    SELECT p.id, p.module, p.action 
                    FROM permissions AS p
                    JOIN user_group_permissions AS ugp ON p.id = ugp.permission_id
                    WHERE ugp.user_group_id = ?
                ");
                $stmt->execute([$id]);
                $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $group['permissions'] = $permissions;
            }
            
            return $group;
        } catch (PDOException $e) {
            error_log("Error fetching user group: " . $e->getMessage());
            return null;
        }
    }

    
    /**
     * Create new user group
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_groups (name, description) 
                VALUES (?, ?)
            ");
            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? null
            ]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error creating user group: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user group and its permissions
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Update group info
            $stmt = $this->db->prepare("
                UPDATE user_groups 
                SET name = ?, description = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $id
            ]);
            
            // Update permissions if provided
            if (isset($data['permissions'])) {
                // Delete existing permissions
                $stmt = $this->db->prepare("DELETE FROM permissions WHERE group_id = ?");
                $stmt->execute([$id]);
                
                // Insert new permissions
                if (!empty($data['permissions'])) {
                    $stmt = $this->db->prepare("
                        INSERT INTO permissions (module, action, group_id) 
                        VALUES (?, ?, ?)
                    ");
                    
                    foreach ($data['permissions'] as $permission) {
                        $stmt->execute([$permission['module'], $permission['action'], $id]);
                    }
                }
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error updating user group: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete user group
     */
    public function delete($id) {
        try {
            // Check if group is in use
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_group_assignments 
                WHERE group_id = ?
            ");
            $stmt->execute([$id]);
            $inUse = $stmt->fetchColumn();
            
            if ($inUse > 0) {
                return ['error' => 'Cannot delete user group: it is currently assigned to users'];
            }
            
            // Delete the group (permissions will be deleted automatically due to foreign key constraint)
            $stmt = $this->db->prepare("DELETE FROM user_groups WHERE id = ?");
            return $stmt->execute([$id]);
            
        } catch (PDOException $e) {
            error_log("Error deleting user group: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get active user groups for dropdown
     */
    public function getActive() {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name 
                FROM user_groups 
                ORDER BY name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching active user groups: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get permissions assigned to a user group
     */
    public function getPermissions($groupId) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.* 
                FROM permissions p
                JOIN user_group_permissions ugp ON p.id = ugp.permission_id
                WHERE ugp.user_group_id = ?
            ");
            $stmt->execute([$groupId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching group permissions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Assign permissions to a user group
     */
    public function assignPermissions($groupId, $permissionIds) {
        try {
            // First, remove all existing permissions for this group
            $this->removeAllPermissions($groupId);
            
            // Then add the new permissions
            if (!empty($permissionIds)) {
                $stmt = $this->db->prepare("
                    INSERT INTO user_group_permissions (user_group_id, permission_id) 
                    VALUES (?, ?)
                ");
                
                foreach ($permissionIds as $permissionId) {
                    $stmt->execute([$groupId, $permissionId]);
                }
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error assigning permissions: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove all permissions from a user group
     */
    public function removeAllPermissions($groupId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM user_group_permissions WHERE user_group_id = ?");
            return $stmt->execute([$groupId]);
        } catch (PDOException $e) {
            error_log("Error removing permissions: " . $e->getMessage());
            return false;
        }
    }
}