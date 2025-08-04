<?php
class DeliveryArea
{
    private $db;
    private $table = 'delivery_areas';

    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function getAllDeliveryAreas($branch_id = null)
    {
        if ($branch_id) {
            $stmt = $this->db->prepare("SELECT da.*, b.name FROM delivery_areas da LEFT JOIN branches b ON da.branch_id = b.id WHERE da.branch_id = ? ORDER BY da.area_name");
            $stmt->execute([$branch_id]);
        } else {
            $stmt = $this->db->prepare("SELECT da.*, b.name FROM delivery_areas da LEFT JOIN branches b ON da.branch_id = b.id ORDER BY da.area_name");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeliveryAreaById($id)
    {
        $stmt = $this->db->prepare("SELECT da.*, b.name FROM delivery_areas da LEFT JOIN branches b ON da.branch_id = b.id WHERE da.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createDeliveryArea($area_name, $branch_id)
    {
        $stmt = $this->db->prepare("INSERT INTO delivery_areas (area_name, branch_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        return $stmt->execute([$area_name, $branch_id]);
    }

    public function updateDeliveryArea($id, $area_name, $branch_id)
    {
        $stmt = $this->db->prepare("UPDATE delivery_areas SET area_name = ?, branch_id = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$area_name, $branch_id, $id]);
    }

    public function deleteDeliveryArea($id)
    {
        $stmt = $this->db->prepare("DELETE FROM delivery_areas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllBranches()
    {
        $stmt = $this->db->prepare("SELECT id, name FROM branches ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
