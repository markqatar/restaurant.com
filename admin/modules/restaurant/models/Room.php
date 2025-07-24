<?php

class Room {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->createTableIfNotExists();
    }
    
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS rooms (
            id INT AUTO_INCREMENT PRIMARY KEY,
            branch_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
        )";
        
        $this->db->exec($sql);
    }
    
    public function getAll($branch_id = null) {
        if ($branch_id) {
            $stmt = $this->db->prepare("
                SELECT r.*, b.name as branch_name 
                FROM rooms r 
                JOIN branches b ON r.branch_id = b.id 
                WHERE r.branch_id = ? 
                ORDER BY r.name
            ");
            $stmt->execute([$branch_id]);
        } else {
            $stmt = $this->db->prepare("
                SELECT r.*, b.name as branch_name 
                FROM rooms r 
                JOIN branches b ON r.branch_id = b.id 
                ORDER BY b.name, r.name
            ");
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, b.name as branch_name 
            FROM rooms r 
            JOIN branches b ON r.branch_id = b.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO rooms (branch_id, name, description, is_active) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['branch_id'],
            $data['name'],
            $data['description'] ?? '',
            $data['is_active'] ?? 1
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE rooms 
            SET branch_id = ?, name = ?, description = ?, is_active = ? 
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['branch_id'],
            $data['name'],
            $data['description'] ?? '',
            $data['is_active'] ?? 1,
            $id
        ]);
    }
    
    public function delete($id) {
        // Check if room has tables
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tables WHERE room_id = ?");
        $stmt->execute([$id]);
        $tableCount = $stmt->fetchColumn();
        
        if ($tableCount > 0) {
            return ['error' => t('msg.cannot_delete_room_has_tables')];
        }
        
        $stmt = $this->db->prepare("DELETE FROM rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }
}