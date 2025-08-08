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
        $sql = "INSERT INTO {$this->table} 
                (supplier_id, product_id, supplier_name, unit_id, quantity, base_quantity, price, currency) 
                VALUES (:supplier_id, :product_id, :supplier_name, :unit_id, :quantity, :base_quantity, :price, :currency)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET
                supplier_id=:supplier_id, product_id=:product_id, supplier_name=:supplier_name,
                unit_id=:unit_id, quantity=:quantity, base_quantity=:base_quantity, price=:price, currency=:currency
                WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=:id");
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
        $sql = "SELECT sp.*, s.name AS supplier_name_real, u.name AS unit_name
            FROM {$this->table} sp
            JOIN suppliers s ON sp.supplier_id = s.id
            JOIN units u ON sp.unit_id = u.id
            WHERE sp.product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectProductsBySupplier(int $supplier_id, string $term = ''): array
    {
        $sql = "
        SELECT DISTINCT p.id, p.name
        FROM supplier_products sp
        JOIN products p ON p.id = sp.product_id
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
}
