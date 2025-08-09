<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/functions.php';

$database = Database::getInstance();
$db = $database->getConnection();

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS `settings` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `setting_key` VARCHAR(100) NOT NULL UNIQUE,
        `setting_value` TEXT,
        `branch_id` INT DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $db->exec($sql);

    // Insert default settings if not exist
    $defaults = [
        'site_name' => 'Restaurant Management System',
        'site_url' => 'http://localhost',
        'logo_path' => '',
        'currency' => 'USD',
    'currencies' => 'QAR,EUR,USD',
        'timezone' => 'UTC',
        'base_path' => '/opt/homebrew/var/www/restaurant.com/'
    ];

    foreach ($defaults as $key => $value) {
        $stmt = $db->prepare("
            INSERT INTO settings (setting_key, setting_value) 
            VALUES (:key, :value)
            ON DUPLICATE KEY UPDATE setting_value = :value
        ");
        $stmt->execute(['key' => $key, 'value' => $value]);
    }

    echo "✅ System module installed successfully!";
} catch (Exception $e) {
    echo "❌ Error installing system module: " . $e->getMessage();
}