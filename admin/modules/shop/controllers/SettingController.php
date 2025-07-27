<?php
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/Room.php';
require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/Branch.php';

class SettingController {
    private $settingModel;
    private $roomModel;
    private $tableModel;
    private $branchModel;
    
    public function __construct() {
        $this->settingModel = new Setting();
        $this->roomModel = new Room();
        $this->tableModel = new Table();
        $this->branchModel = new Branch();
    }
    
    public function index() {
        $settings = $this->settingModel->getAll();
        $branches = $this->branchModel->getAll();
        include __DIR__ . '/../views/settings/index.php';
    }
    
    public function updateGeneralSettings() {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('admin/settings.php');
                return;
            }
            
            // Update site settings
            $this->settingModel->set('site_name', trim($_POST['site_name'] ?? ''));
            $this->settingModel->set('site_url', rtrim(trim($_POST['site_url'] ?? ''), '/'));
            
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($_FILES['logo']['type'], $allowedTypes)) {
                    $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $filename = 'logo_' . time() . '.' . $extension;
                    $filepath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $filepath)) {
                        // Delete old logo if exists
                        $oldLogo = $this->settingModel->get('logo_path');
                        if ($oldLogo && file_exists($uploadDir . $oldLogo)) {
                            unlink($uploadDir . $oldLogo);
                        }
                        
                        $this->settingModel->set('logo_path', $filename);
                    }
                }
            }
            
            set_notification(t('msg.settings_updated_successfully'), 'success');
            redirect('admin/settings.php');
        }
    }
    
    public function rooms($branch_id = null) {
        $branches = $this->branchModel->getAll();
        $rooms = $this->roomModel->getAll($branch_id);
        include __DIR__ . '/../views/settings/rooms.php';
    }
    
    public function createRoom() {
        $branches = $this->branchModel->getAll();
        include __DIR__ . '/../views/settings/create_room.php';
    }
    
    public function storeRoom() {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('admin/settings.php?section=rooms&action=create');
                return;
            }
            
            // Validate required fields
            if (empty($_POST['name']) || empty($_POST['branch_id'])) {
                set_notification(t('msg.required_fields'), 'error');
                redirect('admin/settings.php?section=rooms&action=create');
                return;
            }
            
            $data = [
                'branch_id' => (int)$_POST['branch_id'],
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if ($this->roomModel->create($data)) {
                set_notification(t('msg.room_created_successfully'), 'success');
                redirect('admin/settings.php?section=rooms');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('admin/settings.php?section=rooms&action=create');
            }
        }
    }
    
    public function editRoom($id) {
        $room = $this->roomModel->getById($id);
        if (!$room) {
            set_notification(t('msg.not_found'), 'error');
            redirect('admin/settings.php?section=rooms');
            return;
        }
        
        $branches = $this->branchModel->getAll();
        include __DIR__ . '/../views/settings/edit_room.php';
    }
    
    public function updateRoom($id) {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('admin/settings.php?section=rooms&action=edit&id=' . $id);
                return;
            }
            
            $data = [
                'branch_id' => (int)$_POST['branch_id'],
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if ($this->roomModel->update($id, $data)) {
                set_notification(t('msg.room_updated_successfully'), 'success');
                redirect('admin/settings.php?section=rooms');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('admin/settings.php?section=rooms&action=edit&id=' . $id);
            }
        }
    }
    
    public function deleteRoom($id) {
        $result = $this->roomModel->delete($id);
        
        if (is_array($result) && isset($result['error'])) {
            set_notification($result['error'], 'error');
        } else if ($result) {
            set_notification(t('msg.room_deleted_successfully'), 'success');
        } else {
            set_notification(t('msg.error_occurred'), 'error');
        }
        
        redirect('admin/settings.php?section=rooms');
    }
    
    public function tables($room_id = null) {
        $rooms = $this->roomModel->getAll();
        $tables = $this->tableModel->getAll($room_id);
        include __DIR__ . '/../views/settings/tables.php';
    }
    
    public function createTable() {
        $rooms = $this->roomModel->getAll();
        include __DIR__ . '/../views/settings/create_table.php';
    }
    
    public function storeTable() {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('admin/settings.php?section=tables&action=create');
                return;
            }
            
            $data = [
                'room_id' => (int)$_POST['room_id'],
                'name' => trim($_POST['name']),
                'seats' => (int)$_POST['seats'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if ($this->tableModel->create($data)) {
                set_notification(t('msg.table_created_successfully'), 'success');
                redirect('admin/settings.php?section=tables');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('admin/settings.php?section=tables&action=create');
            }
        }
    }
    
    public function editTable($id) {
        $table = $this->tableModel->getById($id);
        if (!$table) {
            set_notification(t('msg.not_found'), 'error');
            redirect('admin/settings.php?section=tables');
            return;
        }
        
        $rooms = $this->roomModel->getAll();
        include __DIR__ . '/../views/settings/edit_table.php';
    }
    
    public function updateTable($id) {
        if ($_POST) {
            // Validate CSRF token
            if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
                set_notification(t('msg.invalid_token'), 'error');
                redirect('admin/settings.php?section=tables&action=edit&id=' . $id);
                return;
            }
            
            $data = [
                'room_id' => (int)$_POST['room_id'],
                'name' => trim($_POST['name']),
                'seats' => (int)$_POST['seats'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if ($this->tableModel->update($id, $data)) {
                set_notification(t('msg.table_updated_successfully'), 'success');
                redirect('admin/settings.php?section=tables');
            } else {
                set_notification(t('msg.error_occurred'), 'error');
                redirect('admin/settings.php?section=tables&action=edit&id=' . $id);
            }
        }
    }
    
    public function deleteTable($id) {
        if ($this->tableModel->delete($id)) {
            set_notification(t('msg.table_deleted_successfully'), 'success');
        } else {
            set_notification(t('msg.error_occurred'), 'error');
        }
        
        redirect('admin/settings.php?section=tables');
    }
}