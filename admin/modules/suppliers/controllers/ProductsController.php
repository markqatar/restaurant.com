<?php
require_once admin_module_path('/models/Product.php');

class ProductsController {
    private $product_model;

    public function __construct() {
        $this->product_model = new Product();
        TranslationManager::loadModuleTranslations('suppliers');
    }

    public function index() {
        if (!has_permission($_SESSION['user_id'], 'suppliers', 'view')) {
            redirect('admin/unauthorized.php');
        }
        $page_title = "Products";
        include admin_module_path('/views/products/index.php', 'suppliers');
    }

    public function datatable() {
        $draw = $_POST['draw'];
        $start = $_POST['start'];
        $length = $_POST['length'];
        $search = $_POST['search']['value'] ?? '';

        $data = $this->product_model->datatable($start, $length, $search);
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $data['total'],
            "recordsFiltered" => $data['filtered'],
            "data" => $data['data']
        ]);
        exit;
    }

    public function store() {
        $name = sanitize_input($_POST['name']);
        $sku = sanitize_input($_POST['sku']);
        $description = sanitize_input($_POST['description']);
        $this->product_model->create(['name' => $name, 'sku' => $sku, 'description' => $description]);
        echo json_encode(['success' => true]);
    }
}