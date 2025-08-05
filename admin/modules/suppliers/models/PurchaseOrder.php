<?php
class PurchaseOrder
{
    private $db;
    private $table = "purchase_orders";

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function datatable($start, $length, $search)
    {
        $where = "";
        if ($search) {
            $where = "WHERE s.name LIKE :search";
        }

        $sql = "SELECT po.*, s.name AS supplier_name 
                FROM {$this->table} po
                JOIN suppliers s ON po.supplier_id = s.id
                $where
                ORDER BY po.id DESC
                LIMIT :start, :length";
        $stmt = $this->db->prepare($sql);
        if ($search) $stmt->bindValue(':search', "%$search%");
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
        $filtered = $search ? $stmt->rowCount() : $total;

        return ['data' => $data, 'total' => $total, 'filtered' => $filtered];
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (supplier_id, discount, notes) 
                VALUES (:supplier_id, :discount, :notes)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':supplier_id' => $data['supplier_id'],
            ':discount' => $data['discount'],
            ':notes' => $data['notes']
        ]);
        return $this->db->lastInsertId();
    }

    public function getSuppliers()
    {
        $stmt = $this->db->query("SELECT id, name FROM suppliers WHERE is_active = 1 ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addItems($po_id, $products, $quantities, $units)
    {
        $sql = "INSERT INTO purchase_order_items (purchase_order_id, product_id, supplier_product_id, quantity)
            VALUES (:po_id, :product_id, :supplier_product_id, :quantity)";
        $stmt = $this->db->prepare($sql);

        for ($i = 0; $i < count($products); $i++) {
            $stmt->execute([
                ':po_id' => $po_id,
                ':product_id' => (int)$products[$i],
                ':supplier_product_id' => (int)$products[$i], // provvisorio (da legare meglio in fase avanzata)
                ':quantity' => (float)$quantities[$i]
            ]);
        }
    }

        public function find($id) {
        $sql = "SELECT po.*, s.name AS supplier_name, s.email1 AS supplier_email
                FROM {$this->table} po
                JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Recupera i prodotti dell'ordine
            $sqlItems = "SELECT poi.*, p.name AS product_name, u.name AS unit_name
                         FROM purchase_order_items poi
                         JOIN products p ON poi.product_id = p.id
                         JOIN units u ON poi.unit_id = u.id
                         WHERE poi.purchase_order_id = :id";
            $stmtItems = $this->db->prepare($sqlItems);
            $stmtItems->execute([':id' => $id]);
            $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        }

        return $order ?: null;
    }
    public function updatePDF($id, $pdfPath) {
        $sql = "UPDATE {$this->table} SET pdf_path = :pdf_path WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':pdf_path' => $pdfPath, ':id' => $id]);
    }

    /**
     * Aggiorna lo stato dell'ordine
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

}
