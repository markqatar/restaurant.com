<?php

class SupplierContact
{
    private $db;
    private $table = 'supplier_contacts';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getBySupplier($supplier_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE supplier_id = :supplier_id ORDER BY is_primary DESC, first_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':supplier_id' => $supplier_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBySupplierPaginated($supplier_id, $start, $length, $search = '')
    {
        $sql = "SELECT * FROM {$this->table} WHERE supplier_id = :supplier_id";
        $params = [':supplier_id' => $supplier_id];

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :search OR last_name LIKE :search OR email1 LIKE :search OR tel1 LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY is_primary DESC, first_name ASC LIMIT :start, :length";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countBySupplier($supplier_id, $search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE supplier_id = :supplier_id";
        $params = [':supplier_id' => $supplier_id];

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :search OR last_name LIKE :search OR email1 LIKE :search OR tel1 LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
    public function create($supplier_id, $data)
    {
        $sql = "INSERT INTO {$this->table} (supplier_id, first_name, last_name, tel1, tel2, email1, email2, notes, is_primary, is_active)
                VALUES (:supplier_id, :first_name, :last_name, :tel1, :tel2, :email1, :email2, :notes, :is_primary, :is_active)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':supplier_id' => $supplier_id,
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':tel1' => $data['tel1'] ?? null,
            ':tel2' => $data['tel2'] ?? null,
            ':email1' => $data['email1'] ?? null,
            ':email2' => $data['email2'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':is_primary' => $data['is_primary'] ?? 0,
            ':is_active' => 1
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET
                first_name = :first_name, last_name = :last_name, tel1 = :tel1, tel2 = :tel2,
                email1 = :email1, email2 = :email2, notes = :notes, is_primary = :is_primary,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':tel1' => $data['tel1'] ?? null,
            ':tel2' => $data['tel2'] ?? null,
            ':email1' => $data['email1'] ?? null,
            ':email2' => $data['email2'] ?? null,
            ':notes' => $data['notes'] ?? null,
            ':is_primary' => $data['is_primary'] ?? 0
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM supplier_contacts WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
