<?php
require_once admin_module_path('/models/SupplierProduct.php');

class SupplierProductsController
{
    private $model;

    public function __construct()
    {
        $this->model = new SupplierProduct();
    }

    public function index($supplier_id)
    {
        $supplier = (int)$supplier_id;
        $page_title = "Prodotti Fornitore";
        include admin_module_path('/views/supplier_products/index.php', 'suppliers');
    }

    public function datatable()
    {
        $product_id = (int)$_POST['product_id'];
        $data = $this->model->getByProduct($product_id);
        echo json_encode([
            "data" => $data,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data)
        ]);
    }

    public function store()
    {
        $id = $_POST['id'] ?? null;

        $data = [
            'product_id' => (int)$_POST['product_id'],
            'supplier_id' => (int)$_POST['supplier_id'],
            'supplier_name' => sanitize_input($_POST['supplier_name']),
            'sku' => sanitize_input($_POST['sku'] ?? null),
            'unit_id' => (int)$_POST['unit_id'],
            'quantity' => (float)$_POST['quantity'],
            'base_quantity' => (float)($_POST['base_quantity'] ?? 1),
            'price' => (float)$_POST['price'],
            'currency' => sanitize_input($_POST['currency'])
        ];

        if ($id) {
            $this->model->update($id, $data);
        } else {
            $this->model->create($data);
        }

        echo json_encode(['success' => true]);
    }

    public function get($id)
    {
        $product = $this->model->find((int)$id);
        echo json_encode($product);
    }

    public function delete()
    {
        $id = (int)$_POST['id'];
        $this->model->delete($id);
        echo json_encode(['success' => true]);
    }
}
