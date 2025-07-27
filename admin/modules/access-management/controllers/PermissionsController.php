<?php
require_once admin_module_path('/models/Permission.php');

class PermissionsController {
    private $permissionModel;
    
    public function __construct() {
        $this->permissionModel = new Permission();
        TranslationManager::loadModuleTranslations('access-management');
    }
    
    /**
     * Display list of all permissions
     */
    public function index() {
        $permissions = $this->permissionModel->getAll();
        $total_permissions = count($permissions);
        $modules = $this->permissionModel->getResources();
        $actions = $this->permissionModel->getActions();
        
        include __DIR__ . '/../views/permissions/index.php';
    }
    
    /**
     * Show create permission form
     */
    public function create() {
        $modules = $this->permissionModel->getResources();
        $actions = $this->permissionModel->getActions();
        
        include __DIR__ . '/../views/permissions/create.php';
    }
    
    /**
     * Store new permission
     */
    public function store() {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(TranslationManager::t('msg.invalid_token'), 'error');
                redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions/create');
                return;
            }
            
            // Validate required fields
            $required_fields = ['resource', 'action'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    set_notification(TranslationManager::t('msg.required_field') . ': ' . TranslationManager::t($field), 'error');
                    redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions/create');
                    return;
                }
            }
            
            $data = [
                'module' => trim($_POST['resource']),
                'action' => trim($_POST['action']),
            ];
            
            if ($this->permissionModel->create($data)) {
                set_notification(TranslationManager::t('msg.created_successfully'), 'success');
                redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions');
            } else {
                set_notification(TranslationManager::t('msg.error_occurred'), 'error');
                redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions/create');
            }
        }
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            set_notification(TranslationManager::t('msg.not_found'), 'error');
            redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions');
            return;
        }
        
        $modules = $this->permissionModel->getResources();
        $actions = $this->permissionModel->getActions();
        
        include __DIR__ . '/../views/permissions/edit.php';
    }
    
    /**
     * Update permission
     */
    public function update($id) {
        if ($_POST) {
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(TranslationManager::t('msg.invalid_token'), 'error');
                redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions/edit/' . $id);
                return;
            }
            
            $required_fields = ['module', 'action'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    set_notification(TranslationManager::t('msg.required_field') . ': ' . TranslationManager::t($field), 'error');
                    redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions/edit/' . $id);
                    return;
                }
            }
            
            $data = [
                'module' => trim($_POST['module']),
                'action' => trim($_POST['action']),
                'group_id' => !empty($_POST['group_id']) ? intval($_POST['group_id']) : null
            ];
            
            if ($this->permissionModel->update($id, $data)) {
                set_notification(TranslationManager::t('msg.updated_successfully'), 'success');
                redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions');
            } else {
                set_notification(TranslationManager::t('msg.error_occurred'), 'error');
                redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions/edit/' . $id);
            }
        }
    }
    
    /**
     * Delete permission
     */
    public function delete($id) {
        $result = $this->permissionModel->delete($id);
        
        if (is_array($result) && isset($result['error'])) {
            set_notification($result['error'], 'error');
        } else if ($result) {
            set_notification(TranslationManager::t('msg.deleted_successfully'), 'success');
        } else {
            set_notification(TranslationManager::t('msg.error_occurred'), 'error');
        }
        
        redirect(get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions');
    }
}