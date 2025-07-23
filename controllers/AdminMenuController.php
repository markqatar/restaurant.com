<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/AdminMenu.php';

class AdminMenuController {
    private $menuModel;
    
    public function __construct() {
        $this->menuModel = new AdminMenu();
    }
    
    public function index() {
        $menu_items = $this->menuModel->getAllMenuItems($_SESSION['language'] ?? 'en');
        $total_items = count($menu_items);
        $active_items = count(array_filter($menu_items, function($item) { 
            return $item['is_active']; 
        }));
        
        include __DIR__ . '/../views/admin-menu/index.php';
    }
    
    public function create() {
        $parent_items = $this->menuModel->getParentItems($_SESSION['language'] ?? 'en');
        include __DIR__ . '/../views/admin-menu/create.php';
    }
    
    public function store() {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('menu-management.php?action=create');
                return;
            }
            
            // Validate required fields
            if (empty($_POST['title'])) {
                set_notification(t('msg.required_field') . ': ' . t('title'), 'error');
                redirect('menu-management.php?action=create');
                return;
            }
            
            $data = [
                'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
                'title' => trim($_POST['title']),
                'title_ar' => trim($_POST['title_ar'] ?? ''),
                'title_it' => trim($_POST['title_it'] ?? ''),
                'url' => trim($_POST['url'] ?? ''),
                'icon' => trim($_POST['icon'] ?? 'fas fa-circle'),
                'sort_order' => intval($_POST['sort_order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'permission_module' => trim($_POST['permission_module'] ?? ''),
                'permission_action' => trim($_POST['permission_action'] ?? 'view'),
                'is_separator' => isset($_POST['is_separator']) ? 1 : 0,
                'css_class' => trim($_POST['css_class'] ?? ''),
                'target' => trim($_POST['target'] ?? '_self')
            ];
            
            if ($this->menuModel->createMenuItem($data)) {
                set_notification(t('msg.created_successfully'), 'success');
                redirect('menu-management.php');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('menu-management.php?action=create');
            }
        }
    }
    
    public function edit($id) {
        $menu_item = $this->menuModel->getMenuItem($id);
        if (!$menu_item) {
            set_notification(t('msg.not_found'), 'error');
            redirect('menu-management.php');
            return;
        }
        
        $parent_items = $this->menuModel->getParentItems($_SESSION['language'] ?? 'en');
        include __DIR__ . '/../views/admin-menu/edit.php';
    }
    
    public function update($id) {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('menu-management.php?action=edit&id=' . $id);
                return;
            }
            
            // Validate required fields
            if (empty($_POST['title'])) {
                set_notification(t('msg.required_field') . ': ' . t('title'), 'error');
                redirect('menu-management.php?action=edit&id=' . $id);
                return;
            }
            
            $data = [
                'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
                'title' => trim($_POST['title']),
                'title_ar' => trim($_POST['title_ar'] ?? ''),
                'title_it' => trim($_POST['title_it'] ?? ''),
                'url' => trim($_POST['url'] ?? ''),
                'icon' => trim($_POST['icon'] ?? 'fas fa-circle'),
                'sort_order' => intval($_POST['sort_order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'permission_module' => trim($_POST['permission_module'] ?? ''),
                'permission_action' => trim($_POST['permission_action'] ?? 'view'),
                'is_separator' => isset($_POST['is_separator']) ? 1 : 0,
                'css_class' => trim($_POST['css_class'] ?? ''),
                'target' => trim($_POST['target'] ?? '_self')
            ];
            
            if ($this->menuModel->updateMenuItem($id, $data)) {
                set_notification(t('msg.updated_successfully'), 'success');
                redirect('menu-management.php');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('menu-management.php?action=edit&id=' . $id);
            }
        }
    }
    
    public function delete($id) {
        $result = $this->menuModel->deleteMenuItem($id);
        
        if (is_array($result) && isset($result['error'])) {
            set_notification($result['error'], 'error');
        } else if ($result) {
            set_notification(t('msg.deleted_successfully'), 'success');
        } else {
            set_notification(t('msg.error_occurred'), 'error');
        }
        
        redirect('menu-management.php');
    }
}