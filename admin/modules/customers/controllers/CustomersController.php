<?php
require_once admin_module_path('/models/Customer.php', 'customers');

class CustomersController {
    private $model;
    public function __construct(){
        $this->model = new Customer();
        TranslationManager::loadModuleTranslations('customers');
    }
    public function index(){
        if(!can('customers','view')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        // Server-side DataTable now; no prefetch needed
        $page_title = 'Customers';
        include admin_module_path('/views/customers/index.php','customers');
    }
    public function datatable(){
        if(!can('customers','view')){ header('Content-Type: application/json', true, 403); echo json_encode(['success'=>false,'message'=>'Forbidden']); return; }
    $export = $_GET['export'] ?? $_POST['export'] ?? null;
    $draw   = (int)($_POST['draw'] ?? 1);
    $start  = (int)($_POST['start'] ?? 0);
    $length = (int)($_POST['length'] ?? 25);
    $search = trim($_GET['search'] ?? ($_POST['search']['value'] ?? ''));
        // Simple column map (index => column name in SQL)
        $columns = ['id','first_name','email','phone','status','created_at'];
        $orderColumnIndex = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
        $orderDir = strtolower($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        if($export){ $start=0; $length=1000000; }
        $result = $this->model->datatable($start, $length, $search, $orderColumn, $orderDir);
        if($export){
            require_once get_setting('base_path').'includes/export.php';
            $rowsExport=[]; foreach($result['data'] as $row){ $rowsExport[]=[ $row['id'], trim(($row['first_name']??'').' '.($row['last_name']??'')), $row['email'], $row['phone'], $row['status'], $row['created_at'] ]; }
            $headers=['ID','Name','Email','Phone','Status','Created'];
            if($export==='csv') export_csv('customers.csv',$headers,$rowsExport); else export_pdf('customers.pdf','Customers',$headers,$rowsExport);
        }
        $rows = [];
        foreach($result['data'] as $row){
            $fullName = trim(($row['first_name']??'').' '.($row['last_name']??''));
            $actions = '';
            if(can('customers','view')){
                $actions .= '<a class="btn btn-sm btn-primary" href="'.get_setting('site_url').'/admin/customers/customers/view/'.(int)$row['id'].'" title="View"><i class="fas fa-eye"></i></a> ';
            }
            // (Potential future edit/delete buttons based on permissions)
            $rows[] = [
                (int)$row['id'],
                htmlspecialchars($fullName) ?: '-',
                htmlspecialchars($row['email'] ?? ''),
                htmlspecialchars($row['phone'] ?? ''),
                '<span class="badge bg-secondary">'.htmlspecialchars($row['status'] ?? '').'</span>',
                htmlspecialchars($row['created_at'] ?? ''),
                $actions
            ];
        }
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $rows
        ]);
    }
    public function view($id){
        if(!can('customers','view')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $customer = $this->model->findById((int)$id);
        if(!$customer){
            send_notification('Customer not found','danger');
            redirect(get_setting('site_url').'/admin/customers/customers');
        }
        unset($customer['password_hash']);
        $page_title = 'Customer #'.$customer['id'];
        include admin_module_path('/views/customers/view.php','customers');
    }
}
