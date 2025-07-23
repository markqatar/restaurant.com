<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Permission.php';

class PermissionController {
    private $permissionModel;
    
    public function __construct() {
        $this->permissionModel = new Permission();
    }
    
    public function index() {
        // Get all permissions with user groups
        $permissions = $this->permissionModel->getAll();
        $total_permissions = count($permissions);
        $modules = $this->permissionModel->getResources();
        $actions = $this->permissionModel->getActions();
        
        include __DIR__ . '/../views/permissions/index.php';
    }
    
    public function create() {
        $modules = $this->permissionModel->getResources();
        $actions = $this->permissionModel->getActions();
        
        include __DIR__ . '/../views/permissions/create.php';
    }
    
    public function store() {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('permissions.php?action=create');
                return;
            }
            
            // Validate required fields
            $required_fields = ['module', 'action'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    set_notification(t('msg.required_field') . ': ' . t($field), 'error');
                    redirect('permissions.php?action=create');
                    return;
                }
            }
            
            $data = [
                'module' => trim($_POST['module']),
                'action' => trim($_POST['action']),
                'group_id' => !empty($_POST['group_id']) ? intval($_POST['group_id']) : null
            ];
            
            if ($this->permissionModel->create($data)) {
                set_notification(t('msg.created_successfully'), 'success');
                redirect('permissions.php');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('permissions.php?action=create');
            }
        }
    }
    
    public function edit($id) {
        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            set_notification(t('msg.not_found'), 'error');
            redirect('permissions.php');
            return;
        }
        
        $modules = $this->permissionModel->getResources();
        $actions = $this->permissionModel->getActions();
        
        include __DIR__ . '/../views/permissions/edit.php';
    }
    
    public function update($id) {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('permissions.php?action=edit&id=' . $id);
                return;
            }
            
            // Validate required fields
            $required_fields = ['module', 'action'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    set_notification(t('msg.required_field') . ': ' . t($field), 'error');
                    redirect('permissions.php?action=edit&id=' . $id);
                    return;
                }
            }
            
            $data = [
                'module' => trim($_POST['module']),
                'action' => trim($_POST['action']),
                'group_id' => !empty($_POST['group_id']) ? intval($_POST['group_id']) : null
            ];
            
            if ($this->permissionModel->update($id, $data)) {
                set_notification(t('msg.updated_successfully'), 'success');
                redirect('permissions.php');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('permissions.php?action=edit&id=' . $id);
            }
        }
    }
    
    public function delete($id) {
        $result = $this->permissionModel->delete($id);
        
        if (is_array($result) && isset($result['error'])) {
            set_notification($result['error'], 'error');
        } else if ($result) {
            set_notification(t('msg.deleted_successfully'), 'success');
        } else {
            set_notification(t('msg.error_occurred'), 'error');
        }
        
        redirect('permissions.php');
    }
}