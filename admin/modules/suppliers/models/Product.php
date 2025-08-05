<?php
class Product
{
    private $db;
    private $table = "products";

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function datatable($start, $length, $search)
    {
        $where = "";
        if ($search) {
            $where = "WHERE name LIKE :search OR sku LIKE :search";
        }

        $sql = "SELECT * FROM {$this->table} $where ORDER BY id DESC LIMIT :start, :length";
        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->bindValue(':search', "%$search%");
        }
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();

        if ($search) {
            $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} $where");
            $stmtCount->bindValue(':search', "%$search%");
            $stmtCount->execute();
            $filtered = $stmtCount->fetchColumn();
        } else {
            $filtered = $total;
        }

        return ['data' => $data, 'total' => $total, 'filtered' => $filtered];
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, sku, description) VALUES (:name, :sku, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET name=:name, sku=:sku, description=:description WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':sku' => $data['sku'],
            ':description' => $data['description'],
            ':id' => $id
        ]);
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

    public function searchByName($search = '')
    {
        $sql = "SELECT id, name FROM {$this->table}";
        if ($search) {
            $sql .= " WHERE name LIKE :search";
        }
        $sql .= " ORDER BY name ASC LIMIT 50";

        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->bindValue(':search', "%$search%");
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
