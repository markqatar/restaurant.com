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
    /**
     * Server-side datatable provider
     */
    public function datatable(int $start,int $length,string $search='', string $orderCol='r.name', string $orderDir='ASC'){
        $orderDir = strtoupper($orderDir)==='DESC' ? 'DESC':'ASC';
        $allowedOrder = ['r.name','r.yield_quantity'];
        if(!in_array($orderCol,$allowedOrder)) $orderCol = 'r.name';
        $where=' WHERE r.is_active=1';
        $params=[];
        if($search!==''){
            $where .= ' AND r.name LIKE :s';
            $params[':s']='%'.$search.'%';
        }
        $total = (int)$this->db->query("SELECT COUNT(*) FROM recipes r WHERE r.is_active=1")->fetchColumn();
        $stCount = $this->db->prepare("SELECT COUNT(*) FROM recipes r".$where);
        $stCount->execute($params); $filtered = (int)$stCount->fetchColumn();
        $sql = "SELECT r.id,r.name,r.yield_quantity,u.name AS yield_unit_name,(SELECT COUNT(*) FROM recipe_components rc WHERE rc.recipe_id=r.id) AS components_count FROM recipes r LEFT JOIN units u ON u.id=r.yield_unit_id".$where." ORDER BY $orderCol $orderDir LIMIT :start,:len";
        $st = $this->db->prepare($sql);
        foreach($params as $k=>$v){ $st->bindValue($k,$v); }
        $st->bindValue(':start',$start,PDO::PARAM_INT);
        $st->bindValue(':len',$length,PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        return ['total'=>$total,'filtered'=>$filtered,'data'=>$rows];
    }
    public function recordBatch(int $recipe_id,float $produced_qty, ?int $unit_id, ?string $ref, ?string $notes){
        $stmt=$this->db->prepare("INSERT INTO recipe_batches (recipe_id, produced_quantity, unit_id, reference_code, notes) VALUES (:r,:q,:u,:ref,:n)");
        $stmt->execute([':r'=>$recipe_id, ':q'=>$produced_qty, ':u'=>$unit_id, ':ref'=>$ref, ':n'=>$notes]);
        return (int)$this->db->lastInsertId();
    }
    public function produce(int $recipe_id, float $desired_output_qty, ?string $reference_code=null){
        $recipe = $this->find($recipe_id); if(!$recipe) throw new Exception('Recipe not found');
        $factor = $desired_output_qty / max(0.000001,(float)$recipe['yield_quantity']);
        // Load shared inventory reasons
        $reasons = [];
    $reasonsPath = get_setting('base_path').'admin/modules/suppliers/config/inventory_reasons.php'; // TODO: move to warehouse/config in future
        if(is_file($reasonsPath)) { $reasons = require $reasonsPath; }
        $reasonConsume = $reasons['BATCH_CONSUME'] ?? 'batch_consume';
        $reasonProduce = $reasons['BATCH_PRODUCE'] ?? 'batch_produce';
        // 1. consume components
    $inventory = new Inventory();
    $branch_id = null; // Placeholder: derive from session / context if available
    if(isset($_SESSION['active_branch_id'])){ $branch_id = (int)$_SESSION['active_branch_id']; }
        foreach($recipe['components'] as $c){
            $consumeQty = (float)$c['quantity'] * $factor * -1; // negative delta
            $inventory->adjust($c['component_type'], (int)$c['component_id'], $consumeQty, $c['unit_id'], $reasonConsume, $reference_code, $branch_id);
        }
        // 2. add produced qty to recipe inventory
        $inventory->adjust('recipe', $recipe_id, $desired_output_qty, $recipe['yield_unit_id'], $reasonProduce, $reference_code, $branch_id);
        $this->recordBatch($recipe_id, $desired_output_qty, $recipe['yield_unit_id'], $reference_code, null);
        return true;
    }
    public function getInventorySummary(){
        if (file_exists(get_setting('base_path').'admin/modules/warehouse/models/Inventory.php')) {
            require_once get_setting('base_path').'admin/modules/warehouse/models/Inventory.php';
        }
        $inv = new Inventory(); return $inv->summary(); }
}
