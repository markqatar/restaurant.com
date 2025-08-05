<?php
require_once admin_module_path('/models/Unit.php');

class UnitsController
{
    private $unit_model;

    public function __construct()
    {
        $this->unit_model = new Unit();
        TranslationManager::loadModuleTranslations('system');
    }

    public function index()
    {
        $page_title = TranslationManager::t('units.page_title');
        include admin_module_path('/views/units/index.php', 'system');
    }

    public function datatable()
    {
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $search = $_POST['search']['value'] ?? '';

        // Legge parametri di ordinamento
        $orderColumnIndex = $_POST['order'][0]['column'] ?? 0; // indice colonna
        $orderDir = $_POST['order'][0]['dir'] ?? 'asc'; // asc/desc

        // Colonne mappate ai campi DB
        $columns = ['u.id', 'u.name', 'u.short_name', 'u.factor', 'u.type', 'u.is_active'];

        // Colonna scelta oppure fallback a id
        $orderColumn = $columns[$orderColumnIndex] ?? 'u.id';

        $data = $this->unit_model->datatable($start, $length, $search, $orderColumn, $orderDir);

        echo json_encode([
            'draw' => intval($draw),
            'recordsTotal' => $data['recordsTotal'],
            'recordsFiltered' => $data['recordsFiltered'],
            'data' => array_map(function ($row) {
                return [
                    $row['id'],
                    htmlspecialchars($row['name']),
                    htmlspecialchars($row['short_name']),
                    $row['factor'],
                    $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>',
                    '<button class="btn btn-sm btn-primary editUnit" data-id="' . $row['id'] . '"><i class="fa fa-edit"></i></button>
                 <button class="btn btn-sm btn-danger deleteUnit" data-id="' . $row['id'] . '"><i class="fa fa-trash"></i></button>'
                ];
            }, $data['data'])
        ]);
    }
    public function get()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $unit = $this->unit_model->get($id);
            $translations = $this->unit_model->getTranslations($id);
            echo json_encode(['success' => true, 'data' => $unit, 'translations' => $translations]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        }
    }

    public function save()
    {
        $id = $_POST['id'] ?? null;
        $data = [
            'name' => sanitize_input($_POST['name']),
            'short_name' => sanitize_input($_POST['short_name']),
            'factor' => (float)$_POST['factor'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        $translations = $_POST['translations'] ?? [];
        if ($id) {
            $this->unit_model->update($id, $data);
        } else {
            $id = $this->unit_model->create($data);
        }
        $this->unit_model->saveTranslations($id, $translations);
        echo json_encode(['success' => true, 'message' => 'Saved successfully']);
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->unit_model->delete($id);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function relations()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $relations = $this->unit_model->getRelations($id);
            $reverseRelations = $this->unit_model->getReverseRelations($id);
            echo json_encode(['success' => true, 'main' => $relations, 'sub' => $reverseRelations]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function saveRelation()
    {
        $parent_id = $_POST['parent_id'] ?? null;
        $child_id = $_POST['child_id'] ?? null;

        if ($parent_id && $child_id) {
            $this->unit_model->addRelation($parent_id, $child_id);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function deleteRelation()
    {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->unit_model->deleteRelation($id);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function availableUnits()
    {
        $current_id = $_POST['id'] ?? null;
        if (!$current_id) {
            echo json_encode([]);
            return;
        }

        // Escludi l'unità stessa e quelle già collegate
        $sql = "SELECT id, name FROM units 
            WHERE id != :id 
            AND id NOT IN (SELECT child_unit_id FROM unit_relations WHERE parent_unit_id = :id)
            ORDER BY name ASC";

        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute([':id' => $current_id]);
        $units = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($units);
    }

    public function select()
    {
        $search = $_POST['search'] ?? '';
        $parent_id = isset($_POST['base_unit_id']) ? (int)$_POST['base_unit_id'] : null;

        if ($parent_id) {
            // Recupera unità figlie da unit_relations
            $sql = "SELECT u.id, u.name FROM unit_relations ur
                JOIN units u ON ur.child_unit_id = u.id
                WHERE ur.parent_unit_id = :parent_id";
            if ($search) {
                $sql .= " AND u.name LIKE :search";
            }
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $params = [':parent_id' => $parent_id];
            if ($search) {
                $params[':search'] = "%$search%";
            }
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            return;
        }

        // Se non c'è parent_id, ritorna solo le unità radice
        $sql = "SELECT id, name FROM units WHERE 1";
        $params = [];
        if ($search) {
            $sql .= " AND name LIKE :search";
            $params[':search'] = "%$search%";
        }
        $sql .= " ORDER BY name ASC LIMIT 20";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
