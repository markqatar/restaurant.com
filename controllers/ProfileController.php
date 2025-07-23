<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserPreferences.php';
require_once __DIR__ . '/../includes/functions.php';

class ProfileController {
    private $userModel;
    private $preferencesModel;
    private $errors = [];
    private $success = [];
    
    public function __construct() {
        $this->userModel = new User();
        $this->preferencesModel = new UserPreferences();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
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
            'pageTitle' => 'User Profile'
        ];
        
        return $data;
    }
    
    private function updatePassword() {
        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($currentPassword)) {
            $this->errors[] = 'Current password is required';
        }
        
        if (empty($newPassword)) {
            $this->errors[] = 'New password is required';
        } elseif (strlen($newPassword) < 6) {
            $this->errors[] = 'Password must be at least 6 characters';
        }
        
        if ($newPassword !== $confirmPassword) {
            $this->errors[] = 'New passwords do not match';
        }
        
        // Verify current password
        if (empty($this->errors)) {
            if (!$this->userModel->verifyPassword($userId, $currentPassword)) {
                $this->errors[] = 'Current password is incorrect';
            }
        }
        
        // Update password if no errors
        if (empty($this->errors)) {
            if ($this->userModel->changePassword($userId, $newPassword)) {
                $this->success[] = 'Password updated successfully';
            } else {
                $this->errors[] = 'Failed to update password';
            }
        }
    }
    
    private function updateUsername() {
        $userId = $_SESSION['user_id'];
        $username = $_POST['username'] ?? '';
        
        // Validation
        if (empty($username)) {
            $this->errors[] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $this->errors[] = 'Username must be at least 3 characters';
        }
        
        // Check if username exists
        if (empty($this->errors)) {
            if ($this->userModel->usernameExists($username, $userId)) {
                $this->errors[] = 'Username already exists';
            }
        }
        
        // Update username if no errors
        if (empty($this->errors)) {
            if ($this->userModel->updateUsername($userId, $username)) {
                $_SESSION['username'] = $username;
                $this->success[] = 'Username updated successfully';
            } else {
                $this->errors[] = 'Failed to update username';
            }
        }
    }
    
    private function updateAvatar() {
        $userId = $_SESSION['user_id'];
        
        // Make sure avatars directory exists
        $avatarsDir = __DIR__ . '/../assets/images/avatars/';
        if (!is_dir($avatarsDir)) {
            mkdir($avatarsDir, 0755, true);
        }
        
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileInfo = pathinfo($_FILES['avatar']['name']);
            $extension = strtolower($fileInfo['extension']);
            
            // Validate file type
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($extension, $allowedExtensions)) {
                $this->errors[] = 'Only JPG, PNG, and GIF images are allowed';
            } else {
                // Generate unique filename
                $newFilename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
                $destination = $avatarsDir . $newFilename;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                    // Update user preferences
                    if ($this->preferencesModel->updateAvatar($userId, $newFilename)) {
                        $this->success[] = 'Avatar updated successfully';
                    } else {
                        $this->errors[] = 'Failed to update avatar in database';
                    }
                } else {
                    $this->errors[] = 'Failed to upload avatar';
                }
            }
        } else if ($_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $this->errors[] = 'Error uploading file: ' . $_FILES['avatar']['error'];
        }
    }
    
    private function deleteAvatar() {
        $userId = $_SESSION['user_id'];
        
        if ($this->preferencesModel->updateAvatar($userId, '/images/defaultavatar.jpg')) {
            $this->success[] = 'Avatar removed successfully';
        } else {
            $this->errors[] = 'Failed to remove avatar';
        }
    }
}
?>