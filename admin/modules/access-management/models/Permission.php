<?php

class Permission
{
    private $db;
    private $table = 'permissions';

    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Get all permissions with related user groups
     */
    public function getAll()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, 
                       GROUP_CONCAT(ug.name SEPARATOR ', ') as user_groups,
                       p.created_at,
                       p.updated_at
                FROM permissions p
                LEFT JOIN user_group_permissions ugp ON p.id = ugp.permission_id
                LEFT JOIN user_groups ug ON ugp.user_group_id = ug.id
                GROUP BY p.id
                ORDER BY p.module ASC, p.action ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching permissions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get permission by ID
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, 
                       GROUP_CONCAT(ug.name SEPARATOR ', ') as user_groups
                FROM permissions p
                LEFT JOIN user_group_permissions ugp ON p.id = ugp.permission_id
                LEFT JOIN user_groups ug ON ugp.user_group_id = ug.id
                WHERE p.id = ?
                GROUP BY p.id
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching permission: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new permission
     */
    public function create($data)
    {
        try {
            $name = $data['module'] . ' - ' . $data['action'];
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (name, module, action, description, created_at, updated_at) 
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");
            $result = $stmt->execute([
                $name,
                $data['module'],
                $data['action'],
                $data['description'] ?? null
            ]);

            if ($result) {
                $id = $this->db->lastInsertId();
                log_action('access-management', $this->table, 'create', $id, null, $data);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error creating permission: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update permission
     */
    public function update($id, $data)
    {
        try {
            $old_data = $this->getById($id);
            $name = $data['module'] . ' - ' . $data['action'];
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET name = ?, module = ?, action = ?, description = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $name,
                $data['module'],
                $data['action'],
                $data['description'] ?? null,
                $id
            ]);

            if ($result) {
                log_action('access-management', $this->table, 'update', $id, $old_data, $data);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error updating permission: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete permission
     */
    public function delete($id)
    {
        try {
            $old_data = $this->getById($id);
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_group_permissions 
                WHERE permission_id = ?
            ");
            $stmt->execute([$id]);
            $inUse = $stmt->fetchColumn();

            if ($inUse > 0) {
                return ['error' => 'Cannot delete permission: it is currently assigned to user groups'];
            }

            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                log_action('access-management', $this->table, 'delete', $id, $old_data, null);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error deleting permission: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get permissions by resource
     */
    public function getByResource($resource)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE module = ? 
                ORDER BY action ASC
            ");
            $stmt->execute([$resource]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching permissions by resource: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get available resources (modules)
     */
    public function getResources()
    {
        $modulesPath = get_setting('base_path') . 'admin/modules/';
        $resources = [];

        if (is_dir($modulesPath)) {
            foreach (scandir($modulesPath) as $module) {
                if ($module === '.' || $module === '..') {
                    continue;
                }

                $fullPath = $modulesPath . $module;
                if (is_dir($fullPath)) {
                    $resources[] = strtolower($module);
                }
            }
        }

        return $resources;
    }

    /**
     * Get available actions
     */
    public function getActions()
    {
        return [
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete'
        ];
    }

    /**
     * Get user permissions (module/action list for a user)
     */
    public function getUserPermissions($user_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.module, p.action 
                FROM permissions p
                JOIN user_group_permissions ugp ON p.id = ugp.permission_id
                JOIN user_group_assignments uga ON uga.group_id = ugp.user_group_id
                JOIN users u ON u.id = uga.user_id
                WHERE u.id = ? AND u.is_active = 1
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user permissions: " . $e->getMessage());
            return [];
        }
    }
}