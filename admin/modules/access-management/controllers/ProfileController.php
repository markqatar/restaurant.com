<?php
require_once admin_module_path('/models/User.php');
require_once admin_module_path('/models/UserPreferences.php');

class ProfileController {
    private $userModel;
    private $preferencesModel;
    private $errors = [];
    private $success = [];
    
    public function __construct() {
        $this->userModel = new User();
        $this->preferencesModel = new UserPreferences();
        TranslationManager::loadModuleTranslations('access-management');

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('/admin/login');
            exit();
        }
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->readOne($userId);
        $preferences = $this->preferencesModel->getUserPreferences($userId);
        
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_password'])) {
                $this->updatePassword();
            } elseif (isset($_POST['update_username'])) {
                $this->updateUsername();
            } elseif (isset($_POST['update_avatar'])) {
                $this->updateAvatar();
            } elseif (isset($_POST['delete_avatar'])) {
                $this->deleteAvatar();
            }
            
            // Refresh data after updates
            $user = $this->userModel->readOne($userId);
            $preferences = $this->preferencesModel->getUserPreferences($userId);
        }
        
        // Prepare data for view
        $data = [
            'user' => $user,
            'preferences' => $preferences,
            'errors' => $this->errors,
            'success' => $this->success,
            'pageTitle' => TranslationManager::t('profile.page_title')
        ];

        include admin_module_path('/views/profile/index.php');
    }
    
    private function updatePassword() {
        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($currentPassword)) {
            $this->errors[] = TranslationManager::t('profile.current_password_required');
        }
        
        if (empty($newPassword)) {
            $this->errors[] = TranslationManager::t('profile.new_password_required');
        } elseif (strlen($newPassword) < 6) {
            $this->errors[] = TranslationManager::t('profile.password_min_length');
        }
        
        if ($newPassword !== $confirmPassword) {
            $this->errors[] = TranslationManager::t('profile.passwords_do_not_match');
        }
        
        // Verify current password
        if (empty($this->errors)) {
            if (!$this->userModel->verifyPassword($userId, $currentPassword)) {
                $this->errors[] = TranslationManager::t('profile.current_password_incorrect');
            }
        }
        
        // Update password if no errors
        if (empty($this->errors)) {
            if ($this->userModel->changePassword($userId, $newPassword)) {
                $this->success[] = TranslationManager::t('profile.password_updated_successfully');
            } else {
                $this->errors[] = TranslationManager::t('profile.failed_to_update_password');
            }
        }
    }
    
    private function updateUsername() {
        $userId = $_SESSION['user_id'];
        $username = $_POST['username'] ?? '';
        
        // Validation
        if (empty($username)) {
            $this->errors[] = TranslationManager::t('profile.username_required');
        } elseif (strlen($username) < 3) {
            $this->errors[] = TranslationManager::t('profile.username_min_length');
        }
        
        // Check if username exists
        if (empty($this->errors)) {
            if ($this->userModel->usernameExists($username, $userId)) {
                $this->errors[] = TranslationManager::t('profile.username_already_exists');
            }
        }
        
        // Update username if no errors
        if (empty($this->errors)) {
            if ($this->userModel->updateUsername($userId, $username)) {
                $_SESSION['username'] = $username;
                $this->success[] = TranslationManager::t('profile.username_updated_successfully');
            } else {
                $this->errors[] = TranslationManager::t('profile.failed_to_update_username');
            }
        }
    }
    
    private function updateAvatar() {
        $userId = $_SESSION['user_id'];
        
        $avatarsDir = get_setting('base_path') . '/admin/assets/images/avatars/';
        if (!is_dir($avatarsDir)) {
            mkdir($avatarsDir, 0755, true);
        }
        
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileInfo = pathinfo($_FILES['avatar']['name']);
            $extension = strtolower($fileInfo['extension']);
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($extension, $allowedExtensions)) {
                $this->errors[] = TranslationManager::t('profile.invalid_image_format');
            } else {
                $newFilename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
                $destination = $avatarsDir . $newFilename;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                    if ($this->preferencesModel->updateAvatar($userId, $newFilename)) {
                        $this->success[] = TranslationManager::t('profile.avatar_updated_successfully');
                    } else {
                        $this->errors[] = TranslationManager::t('profile.failed_to_update_avatar');
                    }
                } else {
                    $this->errors[] = TranslationManager::t('profile.failed_to_upload_avatar');
                }
            }
        } elseif ($_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $this->errors[] = TranslationManager::t('profile.error_uploading_file') . ': ' . $_FILES['avatar']['error'];
        }
    }
    
    private function deleteAvatar() {
        $userId = $_SESSION['user_id'];
        
        if ($this->preferencesModel->updateAvatar($userId, '/images/defaultavatar.jpg')) {
            $this->success[] = TranslationManager::t('profile.avatar_removed_successfully');
        } else {
            $this->errors[] = TranslationManager::t('profile.failed_to_remove_avatar');
        }
    }
}