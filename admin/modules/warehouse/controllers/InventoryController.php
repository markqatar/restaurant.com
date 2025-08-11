<?php
class InventoryController {
    private $inventory;
    public function __construct(){
        require_once get_setting('base_path').'admin/modules/warehouse/models/Inventory.php';
        $this->inventory = new Inventory();
        if(function_exists('ensure_permissions')){
            ensure_permissions([
                ['inventory','view','Inventory View','Can view warehouse inventory']
            ]);
        }
    }
    public function datatable(){
        $this->requirePerm();
        $start = intval($_GET['start'] ?? 0);
        $length = intval($_GET['length'] ?? 25);
        $search = trim($_GET['search']['value'] ?? '');
        $branch = isset($_GET['branch']) && $_GET['branch'] !== '' ? intval($_GET['branch']) : null;
        $type = isset($_GET['type']) && $_GET['type'] !== '' ? $_GET['type'] : null;
        $result = $this->inventory->datatable($start,$length,$search,$branch,$type);
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => intval($_GET['draw'] ?? 1),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => array_map(function($r){
                return [
                    htmlspecialchars($r['branch_name'] ?? '-'),
                    '<span class="badge bg-'+($r['item_type']==='product'?'primary':'info')+'">'+htmlspecialchars($r['item_type'])+'</span>',
                    htmlspecialchars($r['name']),
                    number_format((float)$r['quantity'],4),
                    htmlspecialchars($r['unit_name'] ?? ''),
                    htmlspecialchars($r['updated_at'])
                ];
            }, $result['rows'])
        ]);
        exit;
    }
    private function requirePerm(){
        if(!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'],'inventory','view')){
            http_response_code(403); include get_setting('base_path').'admin/layouts/403.php'; exit;
        }
    }
    public function index(){
        $this->requirePerm();
    if(class_exists('TranslationManager')){ TranslationManager::loadModuleTranslations('warehouse'); }
        $rows = $this->inventory->summary();
        // Ensure menu item(s)
        try {
            $db = Database::getInstance()->getConnection();
            $db->exec("CREATE TABLE IF NOT EXISTS admin_menu_items (id INT AUTO_INCREMENT PRIMARY KEY, parent_id INT NULL, title VARCHAR(100) NOT NULL, icon VARCHAR(50) NULL, url VARCHAR(255) NULL, sort_order INT DEFAULT 0, is_active TINYINT(1) DEFAULT 1, permission_code VARCHAR(100) NULL, INDEX(parent_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            // Parent Warehouse group (if desired)
            $parentId = null;
            $chk = $db->prepare("SELECT id FROM admin_menu_items WHERE url='warehouse' LIMIT 1");
            $chk->execute();
            if($p=$chk->fetch(PDO::FETCH_ASSOC)){ $parentId = (int)$p['id']; } else {
                $ins=$db->prepare("INSERT INTO admin_menu_items (parent_id,title,icon,url,sort_order,permission_code) VALUES (NULL,'Warehouse','fas fa-warehouse','warehouse',50,'inventory.view')");
                $ins->execute(); $parentId = (int)$db->lastInsertId();
            }
            $chk2 = $db->prepare("SELECT id FROM admin_menu_items WHERE url='warehouse/inventory' LIMIT 1");
            $chk2->execute();
            if(!$chk2->fetch()){
                $ins2=$db->prepare("INSERT INTO admin_menu_items (parent_id,title,icon,url,sort_order,permission_code) VALUES (?,?,?,?,?,?)");
                $ins2->execute([$parentId,'Inventory Summary','fas fa-boxes','warehouse/inventory',51,'inventory.view']);
            }
            $chk3 = $db->prepare("SELECT id FROM admin_menu_items WHERE url='warehouse/transfers' LIMIT 1");
            $chk3->execute();
            if(!$chk3->fetch()){
                $ins3=$db->prepare("INSERT INTO admin_menu_items (parent_id,title,icon,url,sort_order,permission_code) VALUES (?,?,?,?,?,?)");
                $ins3->execute([$parentId,'Transfers','fas fa-exchange-alt','warehouse/transfers',52,'inventory.transfer']);
            }
        } catch(Exception $e) {}
        include admin_module_path('/views/inventory/index.php','warehouse');
    }
}
