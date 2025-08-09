<?php
class SupplierProductAssociation
{
    private $db;
    private $table = "supplier_products";

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByProduct($product_id)
    {
        $sql = "SELECT sp.*, s.name AS supplier_name, u.name AS unit_name
                FROM {$this->table} sp
                JOIN suppliers s ON sp.supplier_id = s.id
                JOIN units u ON sp.unit_id = u.id
                WHERE sp.product_id = :product_id
                ORDER BY s.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $product_id]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Aggiungo sub-unità
        foreach ($data as &$row) {
            $row['sub_units'] = $this->getSubUnitsBySupplierProduct($row['id']);
        }
        return $data;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (product_id, supplier_id, unit_id, quantity, is_active)
                VALUES (:product_id, :supplier_id, :unit_id, :quantity, :is_active)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':product_id' => $data['product_id'],
            ':supplier_id' => $data['supplier_id'],
            ':unit_id' => $data['unit_id'],
            ':quantity' => $data['quantity'],
            ':is_active' => $data['is_active']
        ]);
        return $this->db->lastInsertId(); // FIX
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET
                supplier_id = :supplier_id,
                unit_id = :unit_id,
                quantity = :quantity,
                is_active = :is_active
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':supplier_id' => $data['supplier_id'],
            ':unit_id' => $data['unit_id'],
            ':quantity' => $data['quantity'],
            ':is_active' => $data['is_active'],
            ':id' => $id
        ]);
    }

    public function find($id)
    {
    $sql = "SELECT sp.*, s.name AS supplier_name, u.name AS unit_name
        FROM {$this->table} sp
        JOIN suppliers s ON sp.supplier_id = s.id
        JOIN units u ON sp.unit_id = u.id
        WHERE sp.id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($record) {
            $record['sub_units'] = $this->getSubUnitsBySupplierProduct($id);
        }
        return $record;
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function addSubUnit($supplier_product_id, $unit_id, $quantity, $level)
    {
        $sql = "INSERT INTO supplier_product_sub_units 
                (supplier_product_id, unit_id, quantity, level) 
                VALUES (:spid, :uid, :qty, :lvl)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':spid' => $supplier_product_id, ':uid' => $unit_id, ':qty' => $quantity, ':lvl' => $level]);
    }

    public function clearSubUnits($supplier_product_id)
    {
        $sql = "DELETE FROM supplier_product_sub_units WHERE supplier_product_id = :spid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':spid' => $supplier_product_id]);
    }

    public function getSubUnitsBySupplierProduct($supplier_product_id)
    {
        $sql = "SELECT su.unit_id, u.name, su.quantity, su.level
                FROM supplier_product_sub_units su
                JOIN units u ON u.id = su.unit_id
                WHERE su.supplier_product_id = :spid
                ORDER BY su.level ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':spid' => $supplier_product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}