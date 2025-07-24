<?php

class Table {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->createTableIfNotExists();
    }
    
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS tables (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            seats INT NOT NULL DEFAULT 4,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
        )";
        
        $this->db->exec($sql);
    }
    
    public function getAll($room_id = null) {
        if ($room_id) {
            $stmt = $this->db->prepare("
                SELECT t.*, r.name as room_name, b.name as branch_name 
                FROM tables t 
                JOIN rooms r ON t.room_id = r.id 
                JOIN branches b ON r.branch_id = b.id 
                WHERE t.room_id = ? 
                ORDER BY t.name
            ");
            $stmt->execute([$room_id]);
        } else {
            $stmt = $this->db->prepare("
                SELECT t.*, r.name as room_name, b.name as branch_name 
                FROM tables t 
                JOIN rooms r ON t.room_id = r.id 
                JOIN branches b ON r.branch_id = b.id 
                ORDER BY b.name, r.name, t.name
            ");
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT t.*, r.name as room_name, r.branch_id, b.name as branch_name 
            FROM tables t 
            JOIN rooms r ON t.room_id = r.id 
            JOIN branches b ON r.branch_id = b.id 
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO tables (room_id, name, seats, is_active) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['room_id'],
            $data['name'],
            $data['seats'] ?? 4,
            $data['is_active'] ?? 1
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE tables 
            SET room_id = ?, name = ?, seats = ?, is_active = ? 
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['room_id'],
            $data['name'],
            $data['seats'] ?? 4,
            $data['is_active'] ?? 1,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tables WHERE id = ?");
        return $stmt->execute([$id]);
    }
}