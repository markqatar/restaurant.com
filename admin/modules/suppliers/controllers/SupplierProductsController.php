<?php
require_once admin_module_path('/models/SupplierProduct.php');

class SupplierProductsController {
    private $model;

    public function __construct() {
        $this->model = new SupplierProduct();
    }

    public function index($supplier_id) {
        $supplier_id = (int)$supplier_id;
        $page_title = "Supplier Products";
        include admin_module_path('/views/supplier_products/index.php', 'suppliers');
    }

    public function datatable() {
        $supplier_id = (int)$_POST['supplier_id'];
        $data = $this->model->getBySupplier($supplier_id);
        echo json_encode([
            "data" => $data,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data)
        ]);
    }

    public function store() {
        $data = [
            'supplier_id' => (int)$_POST['supplier_id'],
            'product_id' => (int)$_POST['product_id'],
            'supplier_name' => sanitize_input($_POST['supplier_name']),
            'unit_id' => (int)$_POST['unit_id'],
            'quantity' => (float)$_POST['quantity'],
            'base_quantity' => (float)$_POST['base_quantity'],
            'price' => (float)$_POST['price'],
            'currency' => sanitize_input($_POST['currency'])
        ];
        $this->model->create($data);
        echo json_encode(['success' => true]);
    }
}