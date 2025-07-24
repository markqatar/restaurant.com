<?php
class DeliveryArea {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllDeliveryAreas($branch_id = null) {
        try {
            if ($branch_id) {
                $stmt = $this->pdo->prepare("SELECT da.*, b.branch_name FROM delivery_areas da LEFT JOIN branches b ON da.shop_id = b.id WHERE da.shop_id = ? ORDER BY da.area_name");
                $stmt->execute([$branch_id]);
            } else {
                $stmt = $this->pdo->prepare("SELECT da.*, b.branch_name FROM delivery_areas da LEFT JOIN branches b ON da.shop_id = b.id ORDER BY da.area_name");
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching delivery areas: " . $e->getMessage());
            return [];
        }
    }
    
    public function getDeliveryAreaById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT da.*, b.branch_name FROM delivery_areas da LEFT JOIN branches b ON da.shop_id = b.id WHERE da.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching delivery area: " . $e->getMessage());
            return false;
        }
    }
    
    public function createDeliveryArea($area_name, $shop_id) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO delivery_areas (area_name, shop_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            return $stmt->execute([$area_name, $shop_id]);
        } catch (PDOException $e) {
            error_log("Error creating delivery area: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateDeliveryArea($id, $area_name, $shop_id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE delivery_areas SET area_name = ?, shop_id = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$area_name, $shop_id, $id]);
        } catch (PDOException $e) {
            error_log("Error updating delivery area: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteDeliveryArea($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM delivery_areas WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting delivery area: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllBranches() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, branch_name FROM branches ORDER BY branch_name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching branches: " . $e->getMessage());
            return [];
        }
    }
}
?>