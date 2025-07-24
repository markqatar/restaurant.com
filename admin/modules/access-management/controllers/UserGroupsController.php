<?php
require_once get_setting('base_path', '/var/www/html') . 'includes/session.php';
require_once admin_module_path('/models/UserGroup.php');

class UserGroupsController {
    private $userGroupModel;
    
    public function __construct() {
        $this->userGroupModel = new UserGroup();
    }
    
    public function index() {
        // Get all user groups with user count
        $user_groups = $this->userGroupModel->getAll();
        $total_groups = count($user_groups);
        
        include __DIR__ . '/../views/user-groups/index.php';
    }
    
    public function create() {
        // Get all permissions for the form
        require_once __DIR__ . '/../models/Permission.php';
        $permissionModel = new Permission();
        $resources = $permissionModel->getResources();
        
        include __DIR__ . '/../views/user-groups/create.php';
    }
    
    public function store() {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('admin/user-groups.php?action=create');
                return;
            }
            
            // Validate required fields
            if (empty($_POST['name'])) {
                set_notification(t('msg.required_field') . ': ' . t('name'), 'error');
                redirect('admin/user-groups.php?action=create');
                return;
            }
            
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? '')
            ];
            
            $groupId = $this->userGroupModel->create($data);
            if ($groupId) {
                // Handle permissions assignment
                if (!empty($_POST['permissions'])) {
                    $this->userGroupModel->assignPermissions($groupId, $_POST['permissions']);
                }
                
                set_notification(t('msg.created_successfully'), 'success');
                redirect('admin/user-groups.php');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('admin/user-groups.php?action=create');
            }
        }
    }
    
    public function edit($id) {
        $user_group = $this->userGroupModel->getById($id);
        if (!$user_group) {
            set_notification(t('msg.not_found'), 'error');
            redirect('admin/user-groups.php');
            return;
        }
        
        // Get all permissions and assigned permissions for the form
        require_once __DIR__ . '/../models/Permission.php';
        $permissionModel = new Permission();
        $resources = $permissionModel->getResources();
        $user_group['permissions'] = $this->userGroupModel->getPermissions($id);
        
        include __DIR__ . '/../views/user-groups/edit.php';
    }
    
    public function update($id) {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('admin/user-groups.php?action=edit&id=' . $id);
                return;
            }
            
            // Validate required fields
            if (empty($_POST['name'])) {
                set_notification(t('msg.required_field') . ': ' . t('name'), 'error');
                redirect('admin/user-groups.php?action=edit&id=' . $id);
                return;
            }
            
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? '')
            ];
            
            if ($this->userGroupModel->update($id, $data)) {
                // Handle permissions assignment
                $permissions = $_POST['permissions'] ?? [];
                $this->userGroupModel->assignPermissions($id, $permissions);
                
                set_notification(t('msg.updated_successfully'), 'success');
                redirect('admin/user-groups.php');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('admin/user-groups.php?action=edit&id=' . $id);
            }
        }
    }
    
    public function delete($id) {
        $result = $this->userGroupModel->delete($id);
        
        if (is_array($result) && isset($result['error'])) {
            set_notification($result['error'], 'error');
        } else if ($result) {
            set_notification(t('msg.deleted_successfully'), 'success');
        } else {
            set_notification(t('msg.error_occurred'), 'error');
        }
        
        redirect('admin/user-groups.php');
    }
}