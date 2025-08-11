<?php
require_once admin_module_path('/models/Product.php');

class ProductsController
{
    private $product_model;

    public function __construct()
    {
        $this->product_model = new Product();
    }

    public function index()
    {
        if (!can('suppliers_products', 'view')) {
            redirect('admin/unauthorized.php');
        }
        $page_title = "Products";
        include admin_module_path('/views/products/index.php', 'suppliers');
    }

    public function datatable()
    {
        $export = $_GET['export'] ?? $_POST['export'] ?? null;
        $draw = intval($_POST['draw'] ?? 1);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search = $_GET['search'] ?? ($_POST['search']['value'] ?? '');
        if($export){ $start=0; $length=1000000; }
        $data = $this->product_model->datatable($start, $length, $search);
        if($export){
            require_once get_setting('base_path').'includes/export.php';
            $rowsExport=[]; foreach($data['data'] as $r){ $rowsExport[]=[ $r['id'], $r['name'], $r['sku'], $r['base_unit_name'] ?? '', $r['is_raw_material'] ? 'RAW':'', $r['created_at'] ?? '' ]; }
            $headers=['ID','Name','SKU','Base Unit','Raw Material','Created'];
            if($export==='csv') export_csv('products.csv',$headers,$rowsExport); else export_pdf('products.pdf','Products',$headers,$rowsExport);
        }
    // datatable already returns raw rows; could enrich with base unit name if needed
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $data['total'],
            "recordsFiltered" => $data['filtered'],
            "data" => $data['data']
        ]);
    }

    public function store()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            if (!can('suppliers_products','update')) {
                echo json_encode(['success'=>false,'error'=>'forbidden']);
                return;
            }
        } else {
            if (!can('suppliers_products','create')) {
                echo json_encode(['success'=>false,'error'=>'forbidden']);
                return;
            }
        }
        $data = [
            'name' => sanitize_input($_POST['name']),
            'sku' => sanitize_input($_POST['sku']),
            'description' => sanitize_input($_POST['description']),
            'base_unit_id' => isset($_POST['base_unit_id']) && $_POST['base_unit_id'] !== '' ? (int)$_POST['base_unit_id'] : null,
            'is_raw_material' => isset($_POST['is_raw_material']) ? 1 : 0,
            'generate_barcode' => isset($_POST['generate_barcode']) ? 1 : 0,
            'requires_expiry' => isset($_POST['requires_expiry']) ? 1 : 0,
        ];

        if ($id) {
            $this->product_model->update((int)$id, $data);
        } else {
            $this->product_model->create($data);
        }

        echo json_encode(['success' => true]);
    }

    public function get($id)
    {
        $product = $this->product_model->find((int)$id);
        echo json_encode(['success' => true, 'data' => $product]);
    }

    public function delete()
    {
        $id = (int)$_POST['id'];
        if (!can('suppliers_products','delete')) {
            echo json_encode(['success'=>false,'error'=>'forbidden']);
            return;
        }
        $this->product_model->delete($id);
        echo json_encode(['success' => true]);
    }

    public function select2()
    {
        $search = $_GET['search'] ?? '';
        $products = $this->product_model->searchByName($search);
        $results = [];
        foreach ($products as $p) {
            $results[] = [
                'id' => $p['id'],
                'text' => $p['name']
            ];
        }
        echo json_encode($results);
        exit;
    }

    public function associate($product_id)
    {
    if (!can('suppliers_products','associate.view')) {
            redirect('admin/unauthorized.php');
        }

        $product_id = (int)$product_id;
        $product = $this->product_model->find($product_id);

        if (!$product) {
            redirect('admin/products');
        }

    $page_title = "Associa Fornitori - " . htmlspecialchars($product['name']);
        include admin_module_path('/views/products/associate.php', 'suppliers');
    }
}
