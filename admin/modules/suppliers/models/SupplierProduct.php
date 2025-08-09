<?php
class SupplierProduct
{
    private $db;
    private $table = "supplier_products";

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getBySupplier($supplier_id)
    {
        $sql = "SELECT sp.*, p.name AS product_name, u.name AS unit_name
                FROM supplier_products sp
                JOIN products p ON sp.product_id = p.id
                JOIN units u ON sp.unit_id = u.id
                WHERE sp.supplier_id = :supplier_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':supplier_id' => $supplier_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $this->ensureCategoryColumn();
        $sql = "INSERT INTO {$this->table} 
                (supplier_id, product_id, category_id, supplier_name, unit_id, quantity, base_quantity, price, currency) 
                VALUES (:supplier_id, :product_id, :category_id, :supplier_name, :unit_id, :quantity, :base_quantity, :price, :currency)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $this->ensureCategoryColumn();
        $sql = "UPDATE {$this->table} SET
                supplier_id=:supplier_id, product_id=:product_id, category_id=:category_id, supplier_name=:supplier_name,
                unit_id=:unit_id, quantity=:quantity, base_quantity=:base_quantity, price=:price, currency=:currency
                WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function find($id)
    {
        $this->ensureCategoryColumn();
        $stmt = $this->db->prepare("SELECT sp.*, cat.slug AS category_slug FROM {$this->table} sp LEFT JOIN supplier_product_categories cat ON cat.id = sp.category_id WHERE sp.id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function getByProduct($product_id)
    {
        $sql = "SELECT sp.*, s.name AS supplier_name_real, u.name AS unit_name, cat.slug AS category_slug
            FROM {$this->table} sp
            JOIN suppliers s ON sp.supplier_id = s.id
            JOIN units u ON sp.unit_id = u.id
            LEFT JOIN supplier_product_categories cat ON cat.id = sp.category_id
            WHERE sp.product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectProductsBySupplier(int $supplier_id, string $term = ''): array
    {
        $this->ensureCategoryColumn();
        $sql = "
        SELECT DISTINCT p.id, p.name, sp.unit_id, u.name AS unit_name, p.base_unit_id, bu.name AS base_unit_name
        FROM supplier_products sp
        JOIN products p ON p.id = sp.product_id
        JOIN units u ON u.id = sp.unit_id
        LEFT JOIN units bu ON bu.id = p.base_unit_id
        WHERE sp.supplier_id = :supplier_id
          AND (:term = '' OR p.name LIKE :like)
        ORDER BY p.name
        LIMIT 50
    ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':supplier_id' => $supplier_id,
            ':term'        => $term,
            ':like'        => "%{$term}%"
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function inventorySummaryBySupplier(int $supplier_id): array {
        $this->ensureCategoryColumn();
        $sql = "SELECT p.id AS product_id, p.name AS product_name, p.base_unit_id, bu.name AS base_unit_name,
                       SUM(sp.quantity * sp.base_quantity) AS total_base_units,
                       SUM(sp.quantity) AS total_supplier_units
                FROM supplier_products sp
                JOIN products p ON p.id = sp.product_id
                LEFT JOIN units bu ON bu.id = p.base_unit_id
                WHERE sp.supplier_id = :sid
                GROUP BY p.id, p.name, p.base_unit_id, bu.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sid'=>$supplier_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function ensureCategoryColumn() {
        static $done = false; if($done) return; $done = true;
        try { $c=$this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'category_id'")->fetch(); if(!$c){ $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN category_id INT NULL AFTER product_id"); } } catch(Exception $e) {}
    }
}
