<?php
require_once admin_module_path('/models/UserPreferences.php');

class PreferencesController {
    private $userPreferencesModel;
    
    public function __construct() {
        $this->userPreferencesModel = new UserPreferences();
        TranslationManager::loadModuleTranslations('access-management');

    }
    
    /**
     * Update theme preference
     */
    public function updateTheme() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.user_not_logged_in')]);
            return;
        }
        
        $theme = $_POST['theme'] ?? '';
        
        if (!in_array($theme, ['light', 'dark'])) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.invalid_theme')]);
            return;
        }
        
        $result = $this->userPreferencesModel->updateTheme($_SESSION['user_id'], $theme);
        
        if ($result) {
            $_SESSION['theme'] = $theme;
            echo json_encode(['success' => true, 'message' => TranslationManager::t('msg.theme_updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.failed_to_update_theme')]);
        }
    }
    
    /**
     * Update language preference
     */
    public function updateLanguage() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.user_not_logged_in')]);
            return;
        }
        
        $language = $_POST['language'] ?? '';
        
        $allowedLanguages = ['en', 'it', 'fr', 'es', 'de', 'ar'];
        if (!in_array($language, $allowedLanguages)) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.invalid_language')]);
            return;
        }
        
        $result = $this->userPreferencesModel->updateLanguage($_SESSION['user_id'], $language);
        
        if ($result) {
            $_SESSION['language'] = $language;
            echo json_encode(['success' => true, 'message' => TranslationManager::t('msg.language_updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.failed_to_update_language')]);
        }
    }
    
    /**
     * Update avatar
     */
    public function updateAvatar() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.user_not_logged_in')]);
            return;
        }
        
        $avatar = $_POST['avatar'] ?? '';
        
        if (empty($avatar)) {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.invalid_avatar')]);
            return;
        }
        
        $result = $this->userPreferencesModel->updateAvatar($_SESSION['user_id'], $avatar);
        
        if ($result) {
            $_SESSION['avatar'] = $avatar;
            echo json_encode(['success' => true, 'message' => TranslationManager::t('msg.avatar_updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.failed_to_update_avatar')]);
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
            echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.invalid_action')]);
    }
    exit;
}