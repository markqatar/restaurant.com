<?php
require_once admin_module_path('/models/Recipe.php', 'restaurant');
// Centralized inventory model (moved from Recipe); load if present
if (file_exists(get_setting('base_path') . 'admin/modules/warehouse/models/Inventory.php')) {
    require_once get_setting('base_path') . 'admin/modules/warehouse/models/Inventory.php';
}
class RecipesController
{
    private $model;
    public function __construct()
    {
        $this->model = new Recipe();
        if (class_exists('TranslationManager')) {
            TranslationManager::loadModuleTranslations('restaurant');
        }
    }
    private function requirePerm($module, $action, $json = false)
    {
        if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], $module, $action)) {
            if ($json) {
                header('Content-Type: application/json', true, 403);
                echo json_encode(['success' => false, 'message' => 'Forbidden']);
                exit;
            }
            http_response_code(403);
            include get_setting('base_path') . 'admin/layouts/403.php';
            exit;
        }
    }
    public function index()
    {
        $this->requirePerm('recipes', 'view');
    // List now loaded via server-side DataTable
    include admin_module_path('/views/recipes/index.php', 'restaurant');
    }
    private function fetchUnits()
    {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT id,name FROM units ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }
    private function fetchProducts()
    {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT id,name FROM products ORDER BY name LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
    }
    private function fetchAllRecipes()
    {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT id,name FROM recipes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create()
    {
        $this->requirePerm('recipes', 'create');
        $units = $this->fetchUnits();
        $products = $this->fetchProducts();
        $allRecipes = $this->fetchAllRecipes();
        $components = [];
        include admin_module_path('/views/recipes/form.php', 'restaurant');
    }
    public function store()
    {
        $this->requirePerm('recipes', 'create');
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            send_notification('Invalid token', 'danger');
            redirect(get_setting('site_url') . '/admin/restaurant/recipes');
        }
        $data = ['name' => sanitize_input($_POST['name']), 'description' => sanitize_input($_POST['description'] ?? ''), 'yield_quantity' => (float)($_POST['yield_quantity'] ?? 1), 'yield_unit_id' => (int)($_POST['yield_unit_id'] ?? 0) ?: null, 'is_active' => 1];
        $components = json_decode($_POST['components_json'] ?? '[]', true) ?: [];
        // Fallback: build from parallel arrays if JSON not provided
        if (empty($components) && !empty($_POST['components']['type'])) {
            $types = $_POST['components']['type'];
            $ids = $_POST['components']['id'];
            $qtys = $_POST['components']['quantity'];
            $units = $_POST['components']['unit_id'] ?? [];
            for ($i = 0; $i < count($types); $i++) {
                if ($ids[$i] === '') continue;
                $components[] = [
                    'component_type' => $types[$i] == 'recipe' ? 'recipe' : 'product',
                    'component_id' => (int)$ids[$i],
                    'quantity' => (float)$qtys[$i],
                    'unit_id' => isset($units[$i]) && $units[$i] !== '' ? (int)$units[$i] : null
                ];
            }
        }
        $this->model->create($data, $components);
        send_notification('Recipe created', 'success');
        redirect(get_setting('site_url') . '/admin/restaurant/recipes');
    }
    public function edit($id)
    {
        $this->requirePerm('recipes', 'update');
        $recipe = $this->model->find((int)$id);
        if (!$recipe) {
            send_notification('Not found', 'danger');
            redirect(get_setting('site_url') . '/admin/restaurant/recipes');
        }
        $units = $this->fetchUnits();
        $products = $this->fetchProducts();
        $allRecipes = $this->fetchAllRecipes();
        $components = $recipe['components'] ?? [];
        include admin_module_path('/views/recipes/form.php', 'restaurant');
    }
    public function update($id)
    {
        $this->requirePerm('recipes', 'update');
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            send_notification('Invalid token', 'danger');
            redirect(get_setting('site_url') . '/admin/restaurant/recipes');
        }
        $data = ['name' => sanitize_input($_POST['name']), 'description' => sanitize_input($_POST['description'] ?? ''), 'yield_quantity' => (float)($_POST['yield_quantity'] ?? 1), 'yield_unit_id' => (int)($_POST['yield_unit_id'] ?? 0) ?: null, 'is_active' => !empty($_POST['is_active']) ? 1 : 0];
        $components = json_decode($_POST['components_json'] ?? '[]', true) ?: [];
        if (empty($components) && !empty($_POST['components']['type'])) {
            $types = $_POST['components']['type'];
            $ids = $_POST['components']['id'];
            $qtys = $_POST['components']['quantity'];
            $units = $_POST['components']['unit_id'] ?? [];
            for ($i = 0; $i < count($types); $i++) {
                if ($ids[$i] === '') continue;
                $components[] = [
                    'component_type' => $types[$i] == 'recipe' ? 'recipe' : 'product',
                    'component_id' => (int)$ids[$i],
                    'quantity' => (float)$qtys[$i],
                    'unit_id' => isset($units[$i]) && $units[$i] !== '' ? (int)$units[$i] : null
                ];
            }
        }
        $this->model->update((int)$id, $data, $components);
        send_notification('Recipe updated', 'success');
        redirect(get_setting('site_url') . '/admin/restaurant/recipes');
    }
    public function delete()
    {
        $this->requirePerm('recipes', 'delete', true);
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->model->delete($id);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    public function datatable(){
        $this->requirePerm('recipes','view', true);
    $export = $_GET['export'] ?? $_POST['export'] ?? null;
    $draw   = (int)($_POST['draw'] ?? 1);
    $start  = (int)($_POST['start'] ?? 0);
    $length = (int)($_POST['length'] ?? 25);
    $search = trim($_GET['search'] ?? ($_POST['search']['value'] ?? ''));
        $columns = ['name','yield_quantity','components_count','actions'];
        $orderColumnIndex = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
        $orderDir = strtolower($_POST['order'][0]['dir'] ?? 'asc')==='desc' ? 'DESC':'ASC';
        $orderColMap = ['name'=>'r.name','yield_quantity'=>'r.yield_quantity'];
        $orderSql = $orderColMap[$columns[$orderColumnIndex] ?? 'name'] ?? 'r.name';
        if($export){ $start=0; $length=1000000; }
        $data = $this->model->datatable($start,$length,$search,$orderSql,$orderDir);
        if($export){
            require_once get_setting('base_path').'includes/export.php';
            $rowsExport=[]; foreach($data['data'] as $r){ $rowsExport[]=[ $r['name'], (float)$r['yield_quantity'].' '.($r['yield_unit_name'] ?? ''), (int)$r['components_count'] ]; }
            $headers=['Name','Yield','Components'];
            if($export==='csv') export_csv('recipes.csv',$headers,$rowsExport); else export_pdf('recipes.pdf','Recipes',$headers,$rowsExport);
        }
        $rows = [];
        foreach($data['data'] as $r){
            $actions='';
            if(can('recipes','update')){ $actions.='<a class="btn btn-sm btn-primary" title="Edit" href="'.get_setting('site_url').'/admin/restaurant/recipes/edit/'.(int)$r['id'].'"><i class="fas fa-edit"></i></a> '; }
            if(can('recipes','delete')){ $actions.='<button data-id="'.(int)$r['id'].'" class="btn btn-sm btn-danger btn-delete" title="Delete"><i class="fas fa-trash"></i></button>'; }
            $rows[] = [
                htmlspecialchars($r['name']),
                (float)$r['yield_quantity'].' '.htmlspecialchars($r['yield_unit_name'] ?? ''),
                (int)$r['components_count'],
                $actions
            ];
        }
        header('Content-Type: application/json');
        echo json_encode([
            'draw'=>$draw,
            'recordsTotal'=>$data['total'],
            'recordsFiltered'=>$data['filtered'],
            'data'=>$rows
        ]);
    }
    public function produce()
    {
        $this->requirePerm('production', 'batch', true);
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid token']);
            return;
        }
        try {
            $this->model->produce((int)$_POST['recipe_id'], (float)$_POST['output_qty'], $_POST['reference'] ?? null);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function inventory(){
        $this->requirePerm('inventory','view');
        $db = Database::getInstance()->getConnection();
        $branches = $db->query("SELECT id,name FROM branches WHERE is_active=1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $current_branch_id = isset($_GET['branch_id']) && $_GET['branch_id']!=='' ? (int)$_GET['branch_id'] : null;
        $export = $_GET['export'] ?? null;
        $inventory = $this->model->getInventorySummary();
        if($current_branch_id !== null){
            $inventory = array_filter($inventory, function($row) use ($current_branch_id){ return (int)($row['branch_id'] ?? 0) === $current_branch_id; });
        }
        if($export){
            require_once get_setting('base_path').'includes/export.php';
            // Expected fields: recipe_id, name, branch_id, branch_name, quantity, unit_name (adjust per actual model output)
            $rowsExport=[]; foreach($inventory as $row){
                $rowsExport[]=[
                    $row['recipe_id'] ?? $row['id'] ?? '',
                    $row['name'] ?? '',
                    $row['branch_name'] ?? ($row['branch_id'] ?? ''),
                    $row['quantity'] ?? ($row['qty'] ?? ''),
                    $row['unit_name'] ?? ''
                ];
            }
            $headers=['ID','Recipe','Branch','Quantity','Unit'];
            if($export==='csv') export_csv('recipe_inventory.csv',$headers,$rowsExport); else export_pdf('recipe_inventory.pdf','Recipe Inventory',$headers,$rowsExport);
        }
        include admin_module_path('/views/recipes/inventory.php','restaurant');
    }
    // Show production batch form
    public function production()
    {
        $this->requirePerm('production', 'batch');
        $recipes = $this->fetchAllRecipes();
        include admin_module_path('/views/recipes/production.php', 'restaurant');
    }
    // JSON details for a recipe (components) used by production form
    public function details($id)
    {
        $this->requirePerm('recipes', 'view', true);
        header('Content-Type: application/json');
        $r = $this->model->find((int)$id);
        if (!$r) {
            echo json_encode(['success' => false]);
            return;
        }
        echo json_encode(['success' => true, 'recipe' => $r]);
    }
}
