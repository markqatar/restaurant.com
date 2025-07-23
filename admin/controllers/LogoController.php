<?php
require_once __DIR__ . '/../models/Setting.php';

class LogoController {
    private $settingModel;
    
    public function __construct() {
        $this->settingModel = new Setting();
    }
    
    public function deleteLogo() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }
        
        // Get current logo path
        $logoPath = $this->settingModel->get('logo_path');
        
        if (empty($logoPath)) {
            echo json_encode(['success' => false, 'message' => 'No logo to delete']);
            return;
        }
        
        // Full path to logo file
        $fullPath = __DIR__ . '/../' . $logoPath;
        
        try {
            // Delete file if exists
            if (file_exists($fullPath)) {
                if (!unlink($fullPath)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete logo file']);
                    return;
                }
            }
            
            // Clear logo path from database
            $result = $this->settingModel->set('logo_path', '');
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Logo deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update database']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_logo') {
    $controller = new LogoController();
    $controller->deleteLogo();
    exit;
}
?>