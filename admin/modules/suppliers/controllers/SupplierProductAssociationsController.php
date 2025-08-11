<?php
require_once admin_module_path('/models/SupplierProductAssociation.php');

class SupplierProductAssociationsController
{
    private $model;

    public function __construct()
    {
        $this->model = new SupplierProductAssociation();
    }

    // ✅ Lista associazioni (DataTable)
    public function datatable()
    {
        $export = $_GET['export'] ?? $_POST['export'] ?? null;
        $product_id = (int)($_POST['product_id'] ?? 0);
        $data = $this->model->getByProduct($product_id);
        if($export){
            require_once get_setting('base_path').'includes/export.php';
            $rowsExport=[]; foreach($data as $r){ $rowsExport[]=[ $r['id'], $r['supplier_name'] ?? '', $r['unit_name'] ?? '', $r['quantity'] ?? '', ($r['is_active']??0)?'YES':'NO' ]; }
            $headers=['ID','Supplier','Unit','Quantity','Active'];
            if($export==='csv') export_csv('supplier_product_associations.csv',$headers,$rowsExport); else export_pdf('supplier_product_associations.pdf','Supplier Product Associations',$headers,$rowsExport);
        }
        echo json_encode([
            'data' => $data,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data)
        ]);
    }

    // ✅ Inserimento / Aggiornamento
    public function store()
    {
        $id = $_POST['id'] ?? null;
        $supplier_id = (int)($_POST['supplier_id'] ?? 0);
        $unit_id = (int)($_POST['unit_id'] ?? 0);

        if ($supplier_id <= 0 || $unit_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Fornitore e Unità principale sono obbligatori']);
            return;
        }

        $data = [
            'supplier_id' => (int)$_POST['supplier_id'],
            'product_id' => (int)$_POST['product_id'],
            'unit_id' => (int)$_POST['unit_id'],
            'quantity' => (float)$_POST['quantity'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if ($id) {
            $this->model->update($id, $data);
            $supplier_product_id = $id;
        } else {
            $supplier_product_id = $this->model->create($data);
        }

        // Sotto-unità
        if (isset($_POST['sub_units']) && is_array($_POST['sub_units'])) {
            $this->model->clearSubUnits($supplier_product_id);
            $level = 1;
            foreach ($_POST['sub_units'] as $index => $unit_id) {
                $quantity = isset($_POST['sub_quantities'][$index]) ? (float)$_POST['sub_quantities'][$index] : 1;
                $this->model->addSubUnit($supplier_product_id, (int)$unit_id, $quantity, $level++);
            }
        }
        echo json_encode(['success' => true]);
    }
    // ✅ Recupero singolo record
    public function get($id)
    {
        $assoc = $this->model->find((int)$id);
        echo json_encode(['success' => true, 'data' => $assoc]);
    }

    // ✅ Eliminazione
    public function delete()
    {
        $id = (int)$_POST['id'];
        $this->model->delete($id);
        echo json_encode(['success' => true]);
    }
}
