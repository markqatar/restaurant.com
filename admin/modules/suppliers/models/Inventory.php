<?php
class Inventory {
    private $db;
    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
        $this->migrate();
    }
    private function migrate(){
        // Centralized inventory tables (moved from Recipe model) with branch scope
        $this->db->exec("CREATE TABLE IF NOT EXISTS ingredient_inventory (id INT AUTO_INCREMENT PRIMARY KEY, item_type ENUM('product','recipe') NOT NULL, item_id INT NOT NULL, branch_id INT NULL, quantity DECIMAL(18,6) NOT NULL DEFAULT 0, unit_id INT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY uniq_item (item_type,item_id,branch_id), INDEX(branch_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->exec("CREATE TABLE IF NOT EXISTS inventory_movements (id INT AUTO_INCREMENT PRIMARY KEY, item_type ENUM('product','recipe') NOT NULL, item_id INT NOT NULL, branch_id INT NULL, delta DECIMAL(18,6) NOT NULL, unit_id INT NULL, reason VARCHAR(50) NOT NULL, context VARCHAR(190) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(item_type,item_id,branch_id), INDEX(reason), INDEX(branch_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        // Transfers log
        $this->db->exec("CREATE TABLE IF NOT EXISTS inventory_transfers (\n            id INT AUTO_INCREMENT PRIMARY KEY,\n            item_type ENUM('product','recipe') NOT NULL,\n            item_id INT NOT NULL,\n            from_branch_id INT NOT NULL,\n            to_branch_id INT NOT NULL,\n            quantity DECIMAL(18,6) NOT NULL,\n            unit_id INT NULL,\n            note VARCHAR(255) NULL,\n            created_by INT NULL,\n            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n            INDEX(item_type,item_id), INDEX(from_branch_id), INDEX(to_branch_id)\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        // Legacy upgrade: add branch_id columns if tables existed without them
        try {
            $col = $this->db->query("SHOW COLUMNS FROM ingredient_inventory LIKE 'branch_id'")->fetch();
            if(!$col){
                $this->db->exec("ALTER TABLE ingredient_inventory ADD COLUMN branch_id INT NULL AFTER item_id");
                // Rebuild unique index
                $this->db->exec("ALTER TABLE ingredient_inventory DROP INDEX uniq_item");
                $this->db->exec("ALTER TABLE ingredient_inventory ADD UNIQUE KEY uniq_item (item_type,item_id,branch_id)");
                $this->db->exec("ALTER TABLE ingredient_inventory ADD INDEX(branch_id)");
            }
        } catch(Exception $e) { /* ignore */ }
        try {
            $col2 = $this->db->query("SHOW COLUMNS FROM inventory_movements LIKE 'branch_id'")->fetch();
            if(!$col2){
                $this->db->exec("ALTER TABLE inventory_movements ADD COLUMN branch_id INT NULL AFTER item_id");
                $this->db->exec("ALTER TABLE inventory_movements ADD INDEX(branch_id)");
                $this->db->exec("ALTER TABLE inventory_movements DROP INDEX item_type"); // may not exist; ignore errors
                // Recreate composite index with branch
                try { $this->db->exec("ALTER TABLE inventory_movements ADD INDEX(item_type,item_id,branch_id)"); } catch(Exception $ie) {}
            }
        } catch(Exception $e){ /* ignore */ }
    }
    public function adjust(string $type,int $id,float $delta, ?int $unit_id, string $reason, ?string $context=null, ?int $branch_id=null){
        $this->db->beginTransaction();
        try {
            $sel = $this->db->prepare("SELECT id, quantity FROM ingredient_inventory WHERE item_type=:t AND item_id=:i AND ((branch_id IS NULL AND :b IS NULL) OR branch_id = :b) FOR UPDATE");
            $sel->execute([':t'=>$type, ':i'=>$id, ':b'=>$branch_id]);
            $row=$sel->fetch(PDO::FETCH_ASSOC);
            if($row){
                $newQ = (float)$row['quantity'] + $delta;
                if($newQ < -0.000001) $newQ = 0; // no negatives
                $up = $this->db->prepare("UPDATE ingredient_inventory SET quantity=:q, unit_id=COALESCE(:u,unit_id) WHERE id=:id");
                $up->execute([':q'=>$newQ, ':u'=>$unit_id, ':id'=>$row['id']]);
            } else {
                $ins = $this->db->prepare("INSERT INTO ingredient_inventory (item_type,item_id,branch_id,quantity,unit_id) VALUES (:t,:i,:b,:q,:u)");
                $ins->execute([':t'=>$type,':i'=>$id,':b'=>$branch_id,':q'=>$delta,':u'=>$unit_id]);
            }
                $mov = $this->db->prepare("INSERT INTO inventory_movements (item_type,item_id,branch_id,delta,unit_id,reason,context) VALUES (:t,:i,:b,:d,:u,:r,:c)");
            $mov->execute([':t'=>$type,':i'=>$id,':b'=>$branch_id,':d'=>$delta,':u'=>$unit_id,':r'=>$reason,':c'=>$context]);
            $this->db->commit();
        } catch(Exception $e){ $this->db->rollBack(); throw $e; }
    }
    public function summary(){
        $sql = "SELECT ii.*, CASE WHEN ii.item_type='product' THEN p.name ELSE r.name END AS name, u.name AS unit_name, b.name AS branch_name FROM ingredient_inventory ii LEFT JOIN products p ON (ii.item_type='product' AND p.id=ii.item_id) LEFT JOIN recipes r ON (ii.item_type='recipe' AND r.id=ii.item_id) LEFT JOIN units u ON u.id=ii.unit_id LEFT JOIN branches b ON b.id = ii.branch_id ORDER BY b.name IS NULL, b.name, name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
