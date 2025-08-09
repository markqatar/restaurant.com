<?php
class Recipe {
    private $db;
    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
        $this->migrate();
    }
    private function migrate(){
        $this->db->exec("CREATE TABLE IF NOT EXISTS recipes (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(190) NOT NULL, description TEXT NULL, yield_quantity DECIMAL(12,4) NOT NULL DEFAULT 1, yield_unit_id INT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX(is_active)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        // Backfill missing columns for legacy installations (table may predate is_active)
        try {
            $col = $this->db->query("SHOW COLUMNS FROM recipes LIKE 'is_active'")->fetch();
            if(!$col){
                $this->db->exec("ALTER TABLE recipes ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1, ADD INDEX (is_active)");
            }
            $col2 = $this->db->query("SHOW COLUMNS FROM recipes LIKE 'yield_unit_id'")->fetch();
            if(!$col2){
                // Add yield_unit_id where absent (legacy installs) and an index for JOIN performance
                $this->db->exec("ALTER TABLE recipes ADD COLUMN yield_unit_id INT NULL AFTER yield_quantity, ADD INDEX (yield_unit_id)");
            }
        } catch(Exception $e){ /* ignore - migration best effort */ }
        $this->db->exec("CREATE TABLE IF NOT EXISTS recipe_components (id INT AUTO_INCREMENT PRIMARY KEY, recipe_id INT NOT NULL, component_type ENUM('product','recipe') NOT NULL, component_id INT NOT NULL, quantity DECIMAL(14,6) NOT NULL, unit_id INT NULL, INDEX(recipe_id), INDEX(component_type), FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->exec("CREATE TABLE IF NOT EXISTS recipe_batches (id INT AUTO_INCREMENT PRIMARY KEY, recipe_id INT NOT NULL, produced_quantity DECIMAL(14,6) NOT NULL, unit_id INT NULL, reference_code VARCHAR(190) NULL, notes TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE, INDEX(recipe_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        // Simple inventory table for raw materials & produced recipe outputs (aggregated by product or recipe)
        $this->db->exec("CREATE TABLE IF NOT EXISTS ingredient_inventory (id INT AUTO_INCREMENT PRIMARY KEY, item_type ENUM('product','recipe') NOT NULL, item_id INT NOT NULL, quantity DECIMAL(18,6) NOT NULL DEFAULT 0, unit_id INT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY uniq_item (item_type,item_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->db->exec("CREATE TABLE IF NOT EXISTS inventory_movements (id INT AUTO_INCREMENT PRIMARY KEY, item_type ENUM('product','recipe') NOT NULL, item_id INT NOT NULL, delta DECIMAL(18,6) NOT NULL, unit_id INT NULL, reason VARCHAR(50) NOT NULL, context VARCHAR(190) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(item_type,item_id), INDEX(reason)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
    public function listAll(){
        $stmt = $this->db->query("SELECT r.*, u.name AS yield_unit_name FROM recipes r LEFT JOIN units u ON u.id = r.yield_unit_id WHERE r.is_active=1 ORDER BY r.name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find(int $id){
        $stmt = $this->db->prepare("SELECT r.*, u.name AS yield_unit_name FROM recipes r LEFT JOIN units u ON u.id = r.yield_unit_id WHERE r.id=:id");
        $stmt->execute([':id'=>$id]);
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$rec) return null;
        $c = $this->db->prepare("SELECT rc.*, pu.name AS product_name, rr.name AS recipe_name, uu.name AS unit_name FROM recipe_components rc LEFT JOIN products pu ON (rc.component_type='product' AND pu.id=rc.component_id) LEFT JOIN recipes rr ON (rc.component_type='recipe' AND rr.id=rc.component_id) LEFT JOIN units uu ON uu.id = rc.unit_id WHERE rc.recipe_id=:rid ORDER BY rc.id");
        $c->execute([':rid'=>$id]);
        $rec['components'] = $c->fetchAll(PDO::FETCH_ASSOC);
        return $rec;
    }
    public function create(array $data, array $components){
        $stmt = $this->db->prepare("INSERT INTO recipes (name, description, yield_quantity, yield_unit_id, is_active) VALUES (:name,:description,:yield_quantity,:yield_unit_id,:is_active)");
        $stmt->execute([
            ':name'=>$data['name'], ':description'=>$data['description']??null, ':yield_quantity'=>$data['yield_quantity']??1, ':yield_unit_id'=>$data['yield_unit_id']??null, ':is_active'=> !empty($data['is_active'])?1:0
        ]);
        $rid = (int)$this->db->lastInsertId();
        $this->saveComponents($rid,$components);
        return $rid;
    }
    public function update(int $id, array $data, array $components){
        $stmt=$this->db->prepare("UPDATE recipes SET name=:name, description=:description, yield_quantity=:yield_quantity, yield_unit_id=:yield_unit_id, is_active=:is_active WHERE id=:id");
        $stmt->execute([':name'=>$data['name'],':description'=>$data['description']??null,':yield_quantity'=>$data['yield_quantity']??1,':yield_unit_id'=>$data['yield_unit_id']??null,':is_active'=> !empty($data['is_active'])?1:0,':id'=>$id]);
        $this->db->prepare("DELETE FROM recipe_components WHERE recipe_id=:id")->execute([':id'=>$id]);
        $this->saveComponents($id,$components);
        return true;
    }
    private function saveComponents(int $recipe_id, array $components){
        if(empty($components)) return;
        $sql = "INSERT INTO recipe_components (recipe_id, component_type, component_id, quantity, unit_id) VALUES (:recipe_id,:component_type,:component_id,:quantity,:unit_id)";
        $st = $this->db->prepare($sql);
        foreach($components as $c){
            if(empty($c['component_type'])||empty($c['component_id'])||empty($c['quantity'])) continue;
            $st->execute([
                ':recipe_id'=>$recipe_id,
                ':component_type'=>$c['component_type']=='recipe'?'recipe':'product',
                ':component_id'=>(int)$c['component_id'],
                ':quantity'=>(float)$c['quantity'],
                ':unit_id'=> $c['unit_id']??null
            ]);
        }
    }
    public function delete(int $id){
        $this->db->prepare("DELETE FROM recipes WHERE id=:id")->execute([':id'=>$id]);
        return true;
    }
    // Inventory helpers
    public function adjustInventory(string $type,int $id,float $delta, ?int $unit_id, string $reason, ?string $context=null){
        $this->db->beginTransaction();
        try {
            $sel = $this->db->prepare("SELECT id, quantity FROM ingredient_inventory WHERE item_type=:t AND item_id=:i FOR UPDATE");
            $sel->execute([':t'=>$type, ':i'=>$id]);
            $row=$sel->fetch(PDO::FETCH_ASSOC);
            if($row){
                $newQ = (float)$row['quantity'] + $delta;
                if($newQ < -0.000001) $newQ = 0; // floor to zero
                $up = $this->db->prepare("UPDATE ingredient_inventory SET quantity=:q, unit_id=COALESCE(:u,unit_id) WHERE id=:id");
                $up->execute([':q'=>$newQ, ':u'=>$unit_id, ':id'=>$row['id']]);
            } else {
                $ins = $this->db->prepare("INSERT INTO ingredient_inventory (item_type,item_id,quantity,unit_id) VALUES (:t,:i,:q,:u)");
                $ins->execute([':t'=>$type,':i'=>$id,':q'=>$delta,':u'=>$unit_id]);
            }
            $mov = $this->db->prepare("INSERT INTO inventory_movements (item_type,item_id,delta,unit_id,reason,context) VALUES (:t,:i,:d,:u,:r,:c)");
            $mov->execute([':t'=>$type,':i'=>$id,':d'=>$delta,':u'=>$unit_id,':r'=>$reason,':c'=>$context]);
            $this->db->commit();
        } catch(Exception $e){ $this->db->rollBack(); throw $e; }
    }
    public function recordBatch(int $recipe_id,float $produced_qty, ?int $unit_id, ?string $ref, ?string $notes){
        $stmt=$this->db->prepare("INSERT INTO recipe_batches (recipe_id, produced_quantity, unit_id, reference_code, notes) VALUES (:r,:q,:u,:ref,:n)");
        $stmt->execute([':r'=>$recipe_id, ':q'=>$produced_qty, ':u'=>$unit_id, ':ref'=>$ref, ':n'=>$notes]);
        return (int)$this->db->lastInsertId();
    }
    public function produce(int $recipe_id, float $desired_output_qty, ?string $reference_code=null){
        $recipe = $this->find($recipe_id); if(!$recipe) throw new Exception('Recipe not found');
        $factor = $desired_output_qty / max(0.000001,(float)$recipe['yield_quantity']);
        // 1. consume components
        foreach($recipe['components'] as $c){
            $consumeQty = (float)$c['quantity'] * $factor * -1; // negative delta
            $this->adjustInventory($c['component_type'], (int)$c['component_id'], $consumeQty, $c['unit_id'], 'batch_consume', $reference_code);
        }
        // 2. add produced qty to recipe inventory (as aggregated item_type=recipe)
        $this->adjustInventory('recipe', $recipe_id, $desired_output_qty, $recipe['yield_unit_id'], 'batch_produce', $reference_code);
        $this->recordBatch($recipe_id, $desired_output_qty, $recipe['yield_unit_id'], $reference_code, null);
        return true;
    }
    public function getInventorySummary(){
        $sql = "SELECT ii.*, CASE WHEN ii.item_type='product' THEN p.name ELSE r.name END AS name, u.name AS unit_name FROM ingredient_inventory ii LEFT JOIN products p ON (ii.item_type='product' AND p.id=ii.item_id) LEFT JOIN recipes r ON (ii.item_type='recipe' AND r.id=ii.item_id) LEFT JOIN units u ON u.id=ii.unit_id ORDER BY name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
