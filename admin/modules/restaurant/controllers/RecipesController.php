<?php
require_once admin_module_path('/models/Recipe.php','restaurant');
class RecipesController {
    private $model;
    public function __construct(){
        $this->model = new Recipe();
        if(class_exists('TranslationManager')){ TranslationManager::loadModuleTranslations('restaurant'); }
        $this->ensureMenu();
    }
    private function ensureMenu(){
        static $done=false; if($done) return; $done=true;
        try {
            $db=Database::getInstance()->getConnection();
            // Minimal ensure (structure per spec: widths reduced to 100)
            $db->exec("CREATE TABLE IF NOT EXISTS admin_menu_items (
              id INT NOT NULL AUTO_INCREMENT,
              parent_id INT DEFAULT NULL,
              title VARCHAR(100) NOT NULL,
              title_ar VARCHAR(100) DEFAULT NULL,
              title_it VARCHAR(100) DEFAULT NULL,
              url VARCHAR(255) DEFAULT NULL,
              icon VARCHAR(50) DEFAULT 'fas fa-circle',
              sort_order INT DEFAULT 0,
              is_active TINYINT(1) DEFAULT 1,
              permission_module VARCHAR(50) DEFAULT NULL,
              permission_action VARCHAR(20) DEFAULT 'view',
              is_separator TINYINT(1) DEFAULT 0,
              css_class VARCHAR(100) DEFAULT NULL,
              target VARCHAR(20) DEFAULT '_self',
              created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              KEY parent_id (parent_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Top-level Restaurant
            $stmt=$db->prepare("SELECT id FROM admin_menu_items WHERE parent_id IS NULL AND title=:t LIMIT 1");
            $stmt->execute([':t'=>'Restaurant']);
            $parentId=$stmt->fetchColumn();
            if(!$parentId){
                $ins=$db->prepare("INSERT INTO admin_menu_items (parent_id,title,url,icon,sort_order,is_active,permission_module,permission_action,is_separator,css_class,target) VALUES (NULL,'Restaurant','/admin/restaurant/recipes','fas fa-utensils',90,1,NULL,'view',0,NULL,'_self')");
                $ins->execute();
                $parentId=$db->lastInsertId();
            }
            // Helper
            $ensure=function($title,$url,$icon,$sort,$permModule,$permAction) use ($db,$parentId){
                $s=$db->prepare("SELECT id,title FROM admin_menu_items WHERE parent_id=:p AND (url=:u OR title=:t) LIMIT 1");
                $s->execute([':p'=>$parentId,':u'=>$url,':t'=>$title]);
                $row=$s->fetch(PDO::FETCH_ASSOC);
                if($row){
                    // Normalize legacy translation-key titles
                    if(strpos($row['title'],'restaurant.')===0){
                        $up=$db->prepare("UPDATE admin_menu_items SET title=:t WHERE id=:id");
                        $up->execute([':t'=>$title,':id'=>$row['id']]);
                    }
                    return;
                }
                $ins=$db->prepare("INSERT INTO admin_menu_items (parent_id,title,url,icon,sort_order,is_active,permission_module,permission_action,is_separator,css_class,target) VALUES (:p,:t,:u,:i,:s,1,:pm,:pa,0,NULL,'_self')");
                $ins->execute([':p'=>$parentId,':t'=>$title,':u'=>$url,':i'=>$icon,':s'=>$sort,':pm'=>$permModule,':pa'=>$permAction]);
            };
            $ensure('Recipes','/admin/restaurant/recipes','fas fa-book',1,'recipes','view');
            $ensure('Production','/admin/restaurant/recipes/production','fas fa-industry',2,'production','batch');
        } catch(Exception $e){ /* ignore */ }
    }
    private function requirePerm($module,$action,$json=false){
        if(!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'],$module,$action)){
            if($json){
                header('Content-Type: application/json', true, 403);
                echo json_encode(['success'=>false,'message'=>'Forbidden']);
                exit;
            }
            http_response_code(403);
            include get_setting('base_path').'admin/layouts/403.php';
            exit;
        }
    }
    public function index(){ $this->requirePerm('recipes','view'); $recipes = $this->model->listAll(); include admin_module_path('/views/recipes/index.php','restaurant'); }
    private function fetchUnits(){
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT id,name FROM units ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }
    private function fetchProducts(){
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT id,name FROM products ORDER BY name LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
    }
    private function fetchAllRecipes(){
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT id,name FROM recipes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create(){
    $this->requirePerm('recipes','create');
        $units = $this->fetchUnits();
        $products = $this->fetchProducts();
        $allRecipes = $this->fetchAllRecipes();
        $components = [];
        include admin_module_path('/views/recipes/form.php','restaurant');
    }
    public function store(){
    $this->requirePerm('recipes','create');
        if(!verify_csrf_token($_POST['csrf_token']??'')){ send_notification('Invalid token','danger'); redirect(get_setting('site_url').'/admin/restaurant/recipes'); }
        $data = [ 'name'=>sanitize_input($_POST['name']), 'description'=>sanitize_input($_POST['description']??''), 'yield_quantity'=>(float)($_POST['yield_quantity']??1), 'yield_unit_id'=> (int)($_POST['yield_unit_id']??0)?:null, 'is_active'=>1 ];
        $components = json_decode($_POST['components_json']??'[]', true) ?: [];
        // Fallback: build from parallel arrays if JSON not provided
        if(empty($components) && !empty($_POST['components']['type'])){
            $types = $_POST['components']['type'];
            $ids = $_POST['components']['id'];
            $qtys = $_POST['components']['quantity'];
            $units = $_POST['components']['unit_id'] ?? [];
            for($i=0;$i<count($types);$i++){
                if($ids[$i]==='') continue;
                $components[] = [
                    'component_type'=> $types[$i]=='recipe'?'recipe':'product',
                    'component_id'=> (int)$ids[$i],
                    'quantity'=> (float)$qtys[$i],
                    'unit_id'=> isset($units[$i]) && $units[$i]!=='' ? (int)$units[$i] : null
                ];
            }
        }
        $this->model->create($data,$components);
        send_notification('Recipe created','success');
        redirect(get_setting('site_url').'/admin/restaurant/recipes');
    }
    public function edit($id){
        $this->requirePerm('recipes','update');
        $recipe = $this->model->find((int)$id);
        if(!$recipe){ send_notification('Not found','danger'); redirect(get_setting('site_url').'/admin/restaurant/recipes'); }
        $units = $this->fetchUnits();
        $products = $this->fetchProducts();
        $allRecipes = $this->fetchAllRecipes();
        $components = $recipe['components'] ?? [];
        include admin_module_path('/views/recipes/form.php','restaurant');
    }
    public function update($id){ $this->requirePerm('recipes','update'); if(!verify_csrf_token($_POST['csrf_token']??'')){ send_notification('Invalid token','danger'); redirect(get_setting('site_url').'/admin/restaurant/recipes'); }
        $data = [ 'name'=>sanitize_input($_POST['name']), 'description'=>sanitize_input($_POST['description']??''), 'yield_quantity'=>(float)($_POST['yield_quantity']??1), 'yield_unit_id'=> (int)($_POST['yield_unit_id']??0)?:null, 'is_active'=> !empty($_POST['is_active'])?1:0 ];
        $components = json_decode($_POST['components_json']??'[]', true) ?: [];
        if(empty($components) && !empty($_POST['components']['type'])){
            $types = $_POST['components']['type'];
            $ids = $_POST['components']['id'];
            $qtys = $_POST['components']['quantity'];
            $units = $_POST['components']['unit_id'] ?? [];
            for($i=0;$i<count($types);$i++){
                if($ids[$i]==='') continue;
                $components[] = [
                    'component_type'=> $types[$i]=='recipe'?'recipe':'product',
                    'component_id'=> (int)$ids[$i],
                    'quantity'=> (float)$qtys[$i],
                    'unit_id'=> isset($units[$i]) && $units[$i]!=='' ? (int)$units[$i] : null
                ];
            }
        }
        $this->model->update((int)$id,$data,$components);
        send_notification('Recipe updated','success');
        redirect(get_setting('site_url').'/admin/restaurant/recipes');
    }
    public function delete(){ $this->requirePerm('recipes','delete',true); $id=(int)($_POST['id']??0); if($id){ $this->model->delete($id); echo json_encode(['success'=>true]); } else { echo json_encode(['success'=>false]); } }
    public function produce(){ $this->requirePerm('production','batch',true); if(!verify_csrf_token($_POST['csrf_token']??'')){ echo json_encode(['success'=>false,'message'=>'Invalid token']); return; } try { $this->model->produce((int)$_POST['recipe_id'], (float)$_POST['output_qty'], $_POST['reference']??null); echo json_encode(['success'=>true]); } catch(Exception $e){ echo json_encode(['success'=>false,'message'=>$e->getMessage()]); } }
    public function inventory(){ $this->requirePerm('inventory','view'); $inventory = $this->model->getInventorySummary(); include admin_module_path('/views/recipes/inventory.php','restaurant'); }
    // Show production batch form
    public function production(){
        $this->requirePerm('production','batch');
        $recipes = $this->fetchAllRecipes();
        include admin_module_path('/views/recipes/production.php','restaurant');
    }
    // JSON details for a recipe (components) used by production form
    public function details($id){
        $this->requirePerm('recipes','view',true);
        header('Content-Type: application/json');
        $r = $this->model->find((int)$id);
        if(!$r){ echo json_encode(['success'=>false]); return; }
        echo json_encode(['success'=>true,'recipe'=>$r]);
    }
}
