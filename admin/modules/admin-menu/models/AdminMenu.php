<?php

class AdminMenu {
    private $db;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    /**
     * Get menu items for sidebar navigation
     */
    public function getMenuItems($language = 'en', $user_permissions = []) {
        try {
            $titleColumn = $this->getTitleColumn($language);
            
            $stmt = $this->db->prepare("
                SELECT id, parent_id, {$titleColumn} as title, url, icon, sort_order, 
                       is_separator, css_class, target, permission_module, permission_action
                FROM admin_menu_items 
                WHERE is_active = 1 
                ORDER BY parent_id IS NULL DESC, sort_order ASC, title ASC
            ");
            $stmt->execute();
            $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Filter by permissions if provided
            if (!empty($user_permissions)) {
                $menuItems = $this->filterByPermissions($menuItems, $user_permissions);
            }
            
            return $this->buildMenuTree($menuItems);
        } catch (PDOException $e) {
            error_log("Error fetching menu items: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all menu items for management
     */
    public function getAllMenuItems($language = 'en') {
        try {
            $titleColumn = $this->getTitleColumn($language);
            
            $stmt = $this->db->prepare("
                SELECT id, parent_id, title, title_ar, title_it, url, icon, 
                       sort_order, is_active, permission_module, permission_action,
                       is_separator, css_class, target, created_at
                FROM admin_menu_items 
                ORDER BY parent_id IS NULL DESC, sort_order ASC, title ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all menu items: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single menu item by ID
     */
    public function getMenuItem($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM admin_menu_items WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching menu item: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new menu item
     */
    public function createMenuItem($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO admin_menu_items 
                (parent_id, title, title_ar, title_it, url, icon, sort_order, 
                 is_active, permission_module, permission_action, is_separator, 
                 css_class, target) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $data['parent_id'] ?: null,
                $data['title'],
                $data['title_ar'] ?? null,
                $data['title_it'] ?? null,
                $data['url'] ?? null,
                $data['icon'] ?? 'fas fa-circle',
                $data['sort_order'] ?? 0,
                $data['is_active'] ?? 1,
                $data['permission_module'] ?? null,
                $data['permission_action'] ?? 'view',
                $data['is_separator'] ?? 0,
                $data['css_class'] ?? null,
                $data['target'] ?? '_self'
            ]);
        } catch (PDOException $e) {
            error_log("Error creating menu item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update menu item
     */
    public function updateMenuItem($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE admin_menu_items 
                SET parent_id = ?, title = ?, title_ar = ?, title_it = ?, 
                    url = ?, icon = ?, sort_order = ?, is_active = ?, 
                    permission_module = ?, permission_action = ?, is_separator = ?, 
                    css_class = ?, target = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $data['parent_id'] ?: null,
                $data['title'],
                $data['title_ar'] ?? null,
                $data['title_it'] ?? null,
                $data['url'] ?? null,
                $data['icon'] ?? 'fas fa-circle',
                $data['sort_order'] ?? 0,
                $data['is_active'] ?? 1,
                $data['permission_module'] ?? null,
                $data['permission_action'] ?? 'view',
                $data['is_separator'] ?? 0,
                $data['css_class'] ?? null,
                $data['target'] ?? '_self',
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating menu item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete menu item
     */
    public function deleteMenuItem($id) {
        try {
            // Check if item has children
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM admin_menu_items WHERE parent_id = ?");
            $stmt->execute([$id]);
            $hasChildren = $stmt->fetchColumn() > 0;
            
            if ($hasChildren) {
                return ['error' => 'Cannot delete menu item: it has child items'];
            }
            
            $stmt = $this->db->prepare("DELETE FROM admin_menu_items WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting menu item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get parent menu items for dropdown
     */
    public function getParentItems($language = 'en') {
        try {
            $titleColumn = $this->getTitleColumn($language);
            
            $stmt = $this->db->prepare("
                SELECT id, {$titleColumn} as title 
                FROM admin_menu_items 
                WHERE parent_id IS NULL AND is_active = 1
                ORDER BY sort_order ASC, title ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching parent items: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get title column based on language
     */
    private function getTitleColumn($language) {
        switch ($language) {
            case 'ar':
                return 'COALESCE(title_ar, title)';
            case 'it':
                return 'COALESCE(title_it, title)';
            default:
                return 'title';
        }
    }
    
    /**
     * Filter menu items by user permissions
     */
    private function filterByPermissions($menuItems, $permissions) {
        return array_filter($menuItems, function($item) use ($permissions) {
            if (empty($item['permission_module'])) {
                return true; // No permission required
            }
            
            // Check if user has permission for this module
            foreach ($permissions as $permission) {
                if ($permission['module'] === $item['permission_module'] && 
                    $permission['action'] === $item['permission_action']) {
                    return true;
                }
            }
            
            return false;
        });
    }
    
    /**
     * Build hierarchical menu tree
     */
    private function buildMenuTree($menuItems) {
        $tree = [];
        $lookup = [];
        
        // Create lookup array
        foreach ($menuItems as $item) {
            $lookup[$item['id']] = $item;
            $lookup[$item['id']]['children'] = [];
        }
        
        // Build tree
        foreach ($menuItems as $item) {
            if ($item['parent_id'] === null) {
                $tree[] = &$lookup[$item['id']];
            } else {
                if (isset($lookup[$item['parent_id']])) {
                    $lookup[$item['parent_id']]['children'][] = &$lookup[$item['id']];
                }
            }
        }
        
        return $tree;
    }
}