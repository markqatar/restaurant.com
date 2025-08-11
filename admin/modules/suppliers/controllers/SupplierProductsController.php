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
        $export = $_GET['export'] ?? $_POST['export'] ?? null;
        $product_id = (int)($_POST['product_id'] ?? 0);
        $data = $this->model->getByProduct($product_id); // consider adding supplier filter if needed
        if($export){
            require_once get_setting('base_path').'includes/export.php';
            $rowsExport=[]; foreach($data as $r){ $rowsExport[]=[ $r['id'], $r['supplier_name'] ?? '', $r['sku'] ?? '', $r['unit_name'] ?? '', $r['quantity'] ?? '', $r['price'] ?? '', $r['currency'] ?? '' ]; }
            $headers=['ID','Supplier','SKU','Unit','Quantity','Price','Currency'];
            if($export==='csv') export_csv('supplier_products.csv',$headers,$rowsExport); else export_pdf('supplier_products.pdf','Supplier Products',$headers,$rowsExport);
        }
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
            // Currency now selected from configured list in settings UI
            'currency' => sanitize_input($_POST['currency']),
            'category_id' => isset($_POST['category_id']) ? (int)$_POST['category_id'] : null
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
    $row = $this->model->find((int)$id);
    echo json_encode($row ? array_merge(['success'=>true], $row) : ['success'=>false]);
    }

    public function delete()
    {
        $id = (int)$_POST['id'];
        $this->model->delete($id);
        echo json_encode(['success' => true]);
    }

    public function select()
    {
        $supplier_id = (int)($_POST['supplier_id'] ?? 0);
        $search      = trim($_POST['search'] ?? '');
        if ($supplier_id <= 0) {
            echo json_encode([]);
            return;
        }

        // ritorna SOLO i prodotti già associati a quel fornitore
        $rows = $this->model->selectProductsBySupplier($supplier_id, $search);

        // formato Select2 con metadati unità
        $out = array_map(fn($r) => [
            'id' => $r['id'],
            'text' => $r['name'],
            'unit_id' => $r['unit_id'] ?? null,
            'unit_name' => $r['unit_name'] ?? null,
            'base_unit_id' => $r['base_unit_id'] ?? null,
            'base_unit_name' => $r['base_unit_name'] ?? null,
        ], $rows);
        echo json_encode($out);
    }

    // Inventory summary in base units for a supplier
    public function inventorySummary()
    {
        $supplier_id = (int)($_GET['supplier_id'] ?? 0);
        if ($supplier_id <= 0) { echo json_encode([]); return; }
        $export = $_GET['export'] ?? null;
        $rows = $this->model->inventorySummaryBySupplier($supplier_id);
        if($export){
            require_once get_setting('base_path').'includes/export.php';
            $rowsExport=[]; foreach($rows as $r){ $rowsExport[]=[ $r['product_id'] ?? $r['id'] ?? '', $r['product_name'] ?? $r['name'] ?? '', $r['quantity'] ?? '', $r['unit_name'] ?? '', $r['base_quantity'] ?? '', $r['base_unit_name'] ?? '' ]; }
            $headers=['Product ID','Product','Quantity','Unit','Base Qty','Base Unit'];
            if($export==='csv') export_csv('supplier_inventory.csv',$headers,$rowsExport); else export_pdf('supplier_inventory.pdf','Supplier Inventory',$headers,$rowsExport);
        }
        echo json_encode($rows);
    }
}
