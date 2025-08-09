<?php
require_once admin_module_path('/models/SupplierContact.php');

class SupplierContactsController
{
    private $contact_model;

    public function __construct()
    {
        $this->contact_model = new SupplierContact();
        TranslationManager::loadModuleTranslations('suppliers');
    }

    /**
     * Visualizza contatti del fornitore
     */
    public function datatable()
    {
    if (!can('suppliers', 'view')) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }

        $supplier_id = (int)($_POST['supplier_id'] ?? 0);
        $start = (int)($_POST['start'] ?? 0);
        $length = (int)($_POST['length'] ?? 10);
        $search = trim($_POST['search']['value'] ?? '');
        $draw = (int)($_POST['draw'] ?? 1);

        // Calcolo totale contatti per il fornitore (senza filtro)
        $recordsTotal = $this->contact_model->countBySupplier($supplier_id);

        // Ottieni contatti con ricerca e paginazione
        $contacts = $this->contact_model->getBySupplierPaginated($supplier_id, $start, $length, $search);

        // Conta i contatti filtrati
        $recordsFiltered = $this->contact_model->countBySupplier($supplier_id, $search);

        // Prepara i dati per DataTables
        $data = [];
        foreach ($contacts as $contact) {
            $data[] = [
                'name' => htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']),
                'email' => $contact['email1'] ?: '',
                'phone' => $contact['tel1'] ?: '',
                'is_primary' => $contact['is_primary'] ? '<span class="badge bg-primary">' . TranslationManager::t('primary') . '</span>' : '',
                'actions' => '
                <button class="btn btn-sm btn-info view-contact" data-id="' . $contact['id'] . '"><i class="fas fa-eye"></i></button>&nbsp;
                <button class="btn btn-sm btn-warning edit-contact" data-id="' . $contact['id'] . '"><i class="fas fa-edit"></i></button>&nbsp;
                <button class="btn btn-sm btn-danger delete-contact" data-id="' . $contact['id'] . '"><i class="fas fa-trash"></i></button>
            '
            ];
        }

        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
        exit;
    }
    /**
     * Crea contatto (AJAX)
     */
    public function store()
    {
    if (!can('suppliers', 'contact.create') && !can('suppliers','contact.update')) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }

        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.invalid_token')]);
                exit;
            }
            if (isset($_POST['contact_id']) && !empty($_POST['contact_id'])) {
                return $this->update();
            }

            $supplier_id = (int)$_POST['supplier_id'];
            $data = [
                'first_name' => sanitize_input($_POST['first_name']),
                'last_name' => sanitize_input($_POST['last_name']),
                'tel1' => sanitize_input($_POST['tel1']),
                'tel2' => sanitize_input($_POST['tel2']),
                'email1' => sanitize_input($_POST['email1']),
                'email2' => sanitize_input($_POST['email2']),
                'notes' => sanitize_input($_POST['notes']),
                'is_primary' => isset($_POST['is_primary']) ? 1 : 0
            ];

            try {
                if ($this->contact_model->create($supplier_id, $data)) {
                    echo json_encode(['success' => true, 'message' => TranslationManager::t('msg.created_successfully')]);
                } else {
                    echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.error_occurred')]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    /**
     * Aggiorna contatto (AJAX)
     */
    public function update()
    {
    if (!can('suppliers', 'contact.update')) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }

        if ($_POST && isset($_POST['contact_id'])) {
            $contact_id = (int)$_POST['contact_id'];
            $data = [
                'first_name' => sanitize_input($_POST['first_name']),
                'last_name' => sanitize_input($_POST['last_name']),
                'tel1' => sanitize_input($_POST['tel1']),
                'tel2' => sanitize_input($_POST['tel2']),
                'email1' => sanitize_input($_POST['email1']),
                'email2' => sanitize_input($_POST['email2']),
                'notes' => sanitize_input($_POST['notes']),
                'is_primary' => isset($_POST['is_primary']) ? 1 : 0
            ];

            try {
                if ($this->contact_model->update($contact_id, $data)) {
                    echo json_encode(['success' => true, 'message' => TranslationManager::t('msg.updated_successfully')]);
                } else {
                    echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.error_occurred')]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    /**
     * Elimina contatto (AJAX)
     */
    public function delete()
    {
    if (!can('suppliers', 'contact.delete')) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }

        if ($_POST && isset($_POST['contact_id'])) {
            $contact_id = (int)$_POST['contact_id'];

            try {
                if ($this->contact_model->delete($contact_id)) {
                    echo json_encode(['success' => true, 'message' => TranslationManager::t('msg.deleted_successfully')]);
                } else {
                    echo json_encode(['success' => false, 'message' => TranslationManager::t('msg.error_occurred')]);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function get($id)
    {
    if (!can('suppliers', 'view')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            exit;
        }

        if (empty($id) || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid contact ID']);
            exit;
        }

        try {
            $contact = $this->contact_model->findById($id);

            if ($contact) {
                echo json_encode(['success' => true, 'data' => $contact]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Contact not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}
