<?php
require_once __DIR__ . '/../models/UserPreferences.php';
require_once __DIR__ . '/../includes/session.php';

class PreferencesController {
    private $userPreferencesModel;
    
    public function __construct() {
        $this->userPreferencesModel = new UserPreferences();
    }
    
    public function updateTheme() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }
        
        $theme = $_POST['theme'] ?? '';
        
        if (!in_array($theme, ['light', 'dark'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid theme']);
            return;
        }
        
        $result = $this->userPreferencesModel->updateTheme($_SESSION['user_id'], $theme);
        
        if ($result) {
            $_SESSION['theme'] = $theme;
            echo json_encode(['success' => true, 'message' => 'Theme updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update theme']);
        }
    }
    
    public function updateLanguage() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }
        
        $language = $_POST['language'] ?? '';
        
        $allowedLanguages = ['en', 'it', 'fr', 'es', 'de', 'ar'];
        if (!in_array($language, $allowedLanguages)) {
            echo json_encode(['success' => false, 'message' => 'Invalid language']);
            return;
        }
        
        $result = $this->userPreferencesModel->updateLanguage($_SESSION['user_id'], $language);
        
        if ($result) {
            $_SESSION['language'] = $language;
            echo json_encode(['success' => true, 'message' => 'Language updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update language']);
        }
    }
    
    public function updateAvatar() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }
        
        $avatar = $_POST['avatar'] ?? '';
        
        if (empty($avatar)) {
            echo json_encode(['success' => false, 'message' => 'Invalid avatar']);
            return;
        }
        
        $result = $this->userPreferencesModel->updateAvatar($_SESSION['user_id'], $avatar);
        
        if ($result) {
            $_SESSION['avatar'] = $avatar;
            echo json_encode(['success' => true, 'message' => 'Avatar updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update avatar']);
        }
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new PreferencesController();
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_theme':
            $controller->updateTheme();
            break;
        case 'update_language':
            $controller->updateLanguage();
            break;
        case 'update_avatar':
            $controller->updateAvatar();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
?>