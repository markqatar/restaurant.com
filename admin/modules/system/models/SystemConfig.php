<?php
require_once get_setting('base_path', '/var/www/html') . '/admin/includes/functions.php';

class SystemConfig {
    private $db;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function getAllSettings() {
        $stmt = $this->db->prepare("SELECT setting_key, setting_value FROM settings");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function updateSettings($data) {
        try {
            foreach ($data as $key => $value) {
                $stmt = $this->db->prepare("
                    INSERT INTO settings (setting_key, setting_value) 
                    VALUES (:key, :value)
                    ON DUPLICATE KEY UPDATE setting_value = :value
                ");
                $stmt->execute(['key' => $key, 'value' => $value]);
            }
            return true;
        } catch (Exception $e) {
            error_log("Errore updateSettings: " . $e->getMessage());
            return false;
        }
    }
}