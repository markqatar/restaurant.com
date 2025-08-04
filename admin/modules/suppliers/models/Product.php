<?php
class Product {
    private $db;
    private $table = "products";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function datatable($start, $length, $search) {
        $where = "";
        if ($search) {
            $where = "WHERE name LIKE :search";
        }
        $sql = "SELECT * FROM {$this->table} $where LIMIT :start, :length";
        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->bindValue(':search', "%$search%");
        }
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
        $filtered = $search ? $stmt->rowCount() : $total;

        return ['data' => $data, 'total' => $total, 'filtered' => $filtered];
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, sku, description) VALUES (:name, :sku, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':sku' => $data['sku'],
            ':description' => $data['description']
        ]);
    }
}