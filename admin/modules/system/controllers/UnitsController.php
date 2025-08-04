<?php
require_once admin_module_path('/models/Unit.php');

class UnitsController {
    private $unit_model;

    public function __construct() {
        $this->unit_model = new Unit();
        TranslationManager::loadModuleTranslations('system');
    }

    public function index() {
        $page_title = TranslationManager::t('units.page_title');
        include admin_module_path('/views/units/index.php', 'system');
    }

    public function datatable() {
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $search = $_POST['search']['value'] ?? '';
        $data = $this->unit_model->datatable($start, $length, $search);
        echo json_encode([
            'draw' => intval($draw),
            'recordsTotal' => $data['recordsTotal'],
            'recordsFiltered' => $data['recordsFiltered'],
            'data' => array_map(function($row){
                return [
                    $row['id'],
                    htmlspecialchars($row['name']),
                    htmlspecialchars($row['short_name']),
                    $row['factor'],
                    ucfirst($row['type']),
                    $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>',
                    '<button class="btn btn-sm btn-primary editUnit" data-id="'.$row['id'].'"><i class="fa fa-edit"></i></button>
                     <button class="btn btn-sm btn-danger deleteUnit" data-id="'.$row['id'].'"><i class="fa fa-trash"></i></button>'
                ];
            }, $data['data'])
        ]);
    }

    public function get() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $unit = $this->unit_model->get($id);
            $translations = $this->unit_model->getTranslations($id);
            echo json_encode(['success'=>true,'data'=>$unit,'translations'=>$translations]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Invalid ID']);
        }
    }

    public function save() {
        $id = $_POST['id'] ?? null;
        $data = [
            'name'=>sanitize_input($_POST['name']),
            'short_name'=>sanitize_input($_POST['short_name']),
            'factor'=>(float)$_POST['factor'],
            'type'=>sanitize_input($_POST['type']),
            'is_active'=>isset($_POST['is_active']) ? 1 : 0
        ];
        $translations = $_POST['translations'] ?? [];
        if ($id) {
            $this->unit_model->update($id, $data);
        } else {
            $id = $this->unit_model->create($data);
        }
        $this->unit_model->saveTranslations($id, $translations);
        echo json_encode(['success'=>true,'message'=>'Saved successfully']);
    }

    public function delete() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->unit_model->delete($id);
            echo json_encode(['success'=>true]);
        } else {
            echo json_encode(['success'=>false]);
        }
    }
}