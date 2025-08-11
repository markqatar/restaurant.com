<?php
class TransfersController {
    private $inventory;
    public function __construct(){
        require_once get_setting('base_path').'admin/modules/warehouse/models/Inventory.php';
        $this->inventory = new Inventory();
        if(function_exists('ensure_permissions')){
            ensure_permissions([
                ['inventory','transfer','Inventory Transfer','Can transfer inventory between branches']
            ]);
        }
    }
    private function requirePerm(){
        if(!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'],'inventory','transfer')){
            http_response_code(403); include get_setting('base_path').'admin/layouts/403.php'; exit;
        }
    }
    public function index(){
        $this->requirePerm();
        if(class_exists('TranslationManager')){ TranslationManager::loadModuleTranslations('warehouse'); }
        $db = Database::getInstance()->getConnection();
        $branches = $db->query("SELECT id,name FROM branches ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        include admin_module_path('/views/transfers/index.php','warehouse');
    }
    public function datatable(){
        $this->requirePerm();
        $db = Database::getInstance()->getConnection();
        $start = intval($_GET['start']??0); $length = intval($_GET['length']??25); $search = trim($_GET['search']['value']??'');
        $branch = isset($_GET['branch']) && $_GET['branch']!=='' ? intval($_GET['branch']) : null;
        $type = isset($_GET['type']) && $_GET['type']!=='' ? ($_GET['type']=='recipe'?'recipe':'product') : null;
        $where=[]; $params=[];
        if($branch!==null){ $where[]='(t.from_branch_id = :b OR t.to_branch_id = :b)'; $params[':b']=$branch; }
        if($type){ $where[]='t.item_type = :type'; $params[':type']=$type; }
        if($search!==''){
            $where[]='(p.name LIKE :s OR r.name LIKE :s OR rb.name LIKE :s OR tb.name LIKE :s OR t.note LIKE :s)';
            $params[':s']='%'.$search.'%';
        }
        $whereSql = $where?('WHERE '.implode(' AND ',$where)) : '';
        $total = (int)$db->query('SELECT COUNT(*) FROM inventory_transfers')->fetchColumn();
        $countSql = "SELECT COUNT(*) FROM inventory_transfers t LEFT JOIN products p ON (t.item_type='product' AND p.id=t.item_id) LEFT JOIN recipes r ON (t.item_type='recipe' AND r.id=t.item_id) LEFT JOIN branches rb ON rb.id=t.from_branch_id LEFT JOIN branches tb ON tb.id=t.to_branch_id $whereSql";
        $st=$db->prepare($countSql); $st->execute($params); $filtered=(int)$st->fetchColumn();
        $sql = "SELECT t.*, p.name AS product_name, rb.name AS from_branch_name, tb.name AS to_branch_name, u.name AS unit_name, r.name AS recipe_name FROM inventory_transfers t LEFT JOIN products p ON (t.item_type='product' AND p.id=t.item_id) LEFT JOIN recipes r ON (t.item_type='recipe' AND r.id=t.item_id) LEFT JOIN branches rb ON rb.id=t.from_branch_id LEFT JOIN branches tb ON tb.id=t.to_branch_id LEFT JOIN units u ON u.id=t.unit_id $whereSql ORDER BY t.id DESC LIMIT :start,:len";
        $st2=$db->prepare($sql); foreach($params as $k=>$v){ $st2->bindValue($k,$v);} $st2->bindValue(':start',$start,PDO::PARAM_INT); $st2->bindValue(':len',$length,PDO::PARAM_INT); $st2->execute();
        $rows=$st2->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode([
            'draw'=>intval($_GET['draw']??1),
            'recordsTotal'=>$total,
            'recordsFiltered'=>$filtered,
            'data'=>array_map(function($r){
                return [
                    (int)$r['id'],
                    htmlspecialchars($r['item_type']=='recipe'?$r['recipe_name']:$r['product_name']),
                    htmlspecialchars($r['from_branch_name']),
                    htmlspecialchars($r['to_branch_name']),
                    (float)$r['quantity'],
                    htmlspecialchars($r['unit_name']),
                    htmlspecialchars($r['note']),
                    htmlspecialchars($r['created_at'])
                ];
            },$rows)
        ]);
        exit;
    }
    public function create(){
        $this->requirePerm();
        $db = Database::getInstance()->getConnection();
        $branches = $db->query("SELECT id,name FROM branches WHERE is_active=1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $products = $db->query("SELECT id,name FROM products ORDER BY name LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
        $recipes = $db->query("SELECT id,name FROM recipes ORDER BY name LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
        $units = $db->query("SELECT id,name FROM units ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        include admin_module_path('/views/transfers/create.php','warehouse');
    }
    public function store(){
        $this->requirePerm();
        if(!verify_csrf_token($_POST['csrf_token']??'')){ send_notification('Invalid token','danger'); redirect(get_setting('site_url').'/admin/warehouse/transfers'); }
        $from = (int)($_POST['from_branch_id']??0); $to = (int)($_POST['to_branch_id']??0); $type = $_POST['item_type']=='recipe'?'recipe':'product'; $item = (int)($_POST['item_id']??0); $qty=(float)($_POST['quantity']??0); $unit_id = (int)($_POST['unit_id']??0)?:null; $note=trim($_POST['note']??'');
        if($from<=0||$to<=0||$from==$to||$item<=0||$qty<=0){ send_notification('Invalid data','danger'); redirect(get_setting('site_url').'/admin/warehouse/transfers/create'); }
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        try {
            $this->inventory->adjust($type,$item, -1*$qty,$unit_id,'transfer_out',$note,$from);
            $this->inventory->adjust($type,$item, $qty,$unit_id,'transfer_in',$note,$to);
            $ins=$db->prepare("INSERT INTO inventory_transfers (item_type,item_id,from_branch_id,to_branch_id,quantity,unit_id,note,created_by) VALUES (?,?,?,?,?,?,?,?)");
            $ins->execute([$type,$item,$from,$to,$qty,$unit_id,$note,$_SESSION['user_id']??null]);
            $db->commit();
            send_notification('Transfer completed','success');
        } catch(Exception $e){ $db->rollBack(); send_notification('Transfer failed','danger'); }
        redirect(get_setting('site_url').'/admin/warehouse/transfers');
    }
}
