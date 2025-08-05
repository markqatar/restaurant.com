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
        $product_id = (int)$_POST['product_id'];
        $data = $this->model->getByProduct($product_id);

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
