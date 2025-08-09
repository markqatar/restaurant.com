<?php
class Product
{
    private $db;
    private $table = "products";

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        // Lazy ensure base_unit_id column exists so joins using p.base_unit_id don't fail
        static $checkedUnits = false;
        if (!$checkedUnits) {
            try {
                $col = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'base_unit_id'")->fetch(PDO::FETCH_ASSOC);
                if (!$col) {
                    $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN base_unit_id INT NULL AFTER name");
                }
                // add boolean flags if missing
                $needCols = [
                    'is_raw_material' => "ALTER TABLE {$this->table} ADD COLUMN is_raw_material TINYINT(1) NOT NULL DEFAULT 0 AFTER description",
                    'generate_barcode' => "ALTER TABLE {$this->table} ADD COLUMN generate_barcode TINYINT(1) NOT NULL DEFAULT 0 AFTER is_raw_material",
                    'requires_expiry' => "ALTER TABLE {$this->table} ADD COLUMN requires_expiry TINYINT(1) NOT NULL DEFAULT 0 AFTER generate_barcode"
                ];
                foreach($needCols as $c => $ddl){
                    $col = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '$c'")->fetch(PDO::FETCH_ASSOC);
                    if(!$col){ $this->db->exec($ddl); }
                }
            } catch (Exception $e) {
                // swallow - we prefer the app to continue even if ALTER fails (permissions etc.)
            }
            $checkedUnits = true;
        }
    }

        public function searchBySupplier(int $supplier_id, string $term = ''): array {
        $sql = "
            SELECT DISTINCT p.id, p.name
            FROM products p
            JOIN supplier_products sp ON sp.product_id = p.id AND sp.supplier_id = :supplier_id
            WHERE (:term = '' OR p.name LIKE :like)
            ORDER BY p.name LIMIT 50
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':supplier_id' => $supplier_id,
            ':term'        => $term,
            ':like'        => "%{$term}%"
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Opzionale: per mostrare anche prodotti NON ancora associati a quel fornitore
    public function searchAll(string $term = ''): array {
        $sql = "SELECT id, name FROM products WHERE (:term='' OR name LIKE :like) ORDER BY name LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':term'=>$term, ':like'=>"%{$term}%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function datatable($start, $length, $search)
    {
        $where = "";
        if ($search) {
            $where = "WHERE name LIKE :search OR sku LIKE :search";
        }
    $sql = "SELECT p.*, u.name AS base_unit_name FROM {$this->table} p LEFT JOIN units u ON u.id = p.base_unit_id $where ORDER BY p.id DESC LIMIT :start, :length";
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
        // ensure base_unit_id column exists (lightweight safeguard)
        static $checked = false; if(!$checked){
            try { $c=$this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'base_unit_id'")->fetch(); if(!$c){ $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN base_unit_id INT NULL AFTER name"); } } catch(Exception $e){}
            $checked = true;
        }
    $sql = "INSERT INTO {$this->table} (name, sku, description, base_unit_id, is_raw_material, generate_barcode, requires_expiry) VALUES (:name, :sku, :description, :base_unit_id, :is_raw_material, :generate_barcode, :requires_expiry)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'=>$data['name'], ':sku'=>$data['sku'], ':description'=>$data['description'], ':base_unit_id'=>$data['base_unit_id'] ?? null,
            ':is_raw_material'=> !empty($data['is_raw_material']) ? 1:0,
            ':generate_barcode'=> !empty($data['generate_barcode']) ? 1:0,
            ':requires_expiry'=> !empty($data['requires_expiry']) ? 1:0,
        ]);
    }

    public function update($id, $data)
    {
        $cols = ['name=:name','sku=:sku','description=:description','is_raw_material=:is_raw_material','generate_barcode=:generate_barcode','requires_expiry=:requires_expiry'];
        $params = [
            ':name'=>$data['name'],
            ':sku'=>$data['sku'],
            ':description'=>$data['description'],
            ':is_raw_material'=> !empty($data['is_raw_material']) ? 1:0,
            ':generate_barcode'=> !empty($data['generate_barcode']) ? 1:0,
            ':requires_expiry'=> !empty($data['requires_expiry']) ? 1:0,
            ':id'=>$id
        ];
        if(array_key_exists('base_unit_id',$data)) { $cols[]='base_unit_id=:base_unit_id'; $params[':base_unit_id']=$data['base_unit_id']; }
        $sql = "UPDATE {$this->table} SET ".implode(',', $cols)." WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function find($id)
    {
    $stmt = $this->db->prepare("SELECT p.*, u.name AS base_unit_name FROM {$this->table} p LEFT JOIN units u ON u.id = p.base_unit_id WHERE p.id=:id");
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
