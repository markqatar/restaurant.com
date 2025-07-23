<?php

class UserPreferences {
    private $db;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        
        // Create table if it doesn't exist
        $this->createTable();
    }
    
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS user_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            theme VARCHAR(20) DEFAULT 'light',
            language VARCHAR(10) DEFAULT 'en',
            avatar VARCHAR(255) DEFAULT 'default.png',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_pref (user_id)
        )";
        
        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creating user_preferences table: " . $e->getMessage());
        }
    }
    
    public function getUserPreferences($userId) {
        $stmt = $this->db->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        $prefs = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Return defaults if no preferences found
        if (!$prefs) {
            return [
                'theme' => 'light',
                'language' => 'en',
                'avatar' => 'default.png'
            ];
        }
        
        return $prefs;
    }
    
    public function updateTheme($userId, $theme) {
        $sql = "INSERT INTO user_preferences (user_id, theme) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE theme = ?, updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $theme, $theme]);
    }
    
    public function updateLanguage($userId, $language) {
        $sql = "INSERT INTO user_preferences (user_id, language) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE language = ?, updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $language, $language]);
    }
    
    public function updateAvatar($userId, $avatar) {
        $sql = "INSERT INTO user_preferences (user_id, avatar) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE avatar = ?, updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$userId, $avatar, $avatar]);
        
        if ($result) {
            // Update session if it belongs to the current user
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
                $_SESSION['avatar'] = $avatar;
            }
        }
        
        return $result;
    }
    
    public function updatePreferences($userId, $theme = null, $language = null, $avatar = null) {
        $prefs = $this->getUserPreferences($userId);
        
        $newTheme = $theme ?? $prefs['theme'];
        $newLanguage = $language ?? $prefs['language'];
        $newAvatar = $avatar ?? $prefs['avatar'];
        
        $sql = "INSERT INTO user_preferences (user_id, theme, language, avatar) VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                theme = ?, 
                language = ?, 
                avatar = ?, 
                updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $newTheme, $newLanguage, $newAvatar, $newTheme, $newLanguage, $newAvatar]);
    }
}
?>