<?php
require_once admin_module_path('/models/Supplier.php');

class SuppliersController
{
    private $supplier_model;
    private $db;

    public function __construct()
    {
        $this->supplier_model = new Supplier();
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        TranslationManager::loadModuleTranslations('suppliers');
    }

    /**
     * Display suppliers list
     */
    public function index()
    {
        if (!can('suppliers', 'view')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $suppliers = $this->supplier_model->read();
        $total_suppliers = $this->supplier_model->count();

        $page_title = TranslationManager::t('suppliers.page_title');
        include admin_module_path('/views/suppliers/index.php');
    }

    /**
     * Show create supplier form
     */
    public function create()
    {
    if (!can('suppliers', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $countries = $this->supplier_model->getCountries();

        $page_title = TranslationManager::t('suppliers.create_title');
        include admin_module_path('/views/suppliers/create.php');
    }

    /**
     * Store new supplier
     */
    public function store()
    {
    if (!can('suppliers', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification(TranslationManager::t('msg.invalid_token'), 'danger');
                redirect(get_setting('site_url') . '/admin/suppliers/suppliers/create');
            }

            $errors = $this->validateSupplierData($_POST);

            if (empty($errors)) {
                $data = [
                    'name' => sanitize_input($_POST['name']),
                    'address_line1' => sanitize_input($_POST['address_line1']),
                    'address_line2' => sanitize_input($_POST['address_line2']),
                    'zip_code' => sanitize_input($_POST['zip_code']),
                    'country_id' => !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null,
                    'city_id' => !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null,
                    'tel1' => sanitize_input($_POST['tel1']),
                    'tel2' => sanitize_input($_POST['tel2']),
                    'email1' => sanitize_input($_POST['email1']),
                    'email2' => sanitize_input($_POST['email2']),
                    'notes' => sanitize_input($_POST['notes']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                try {
                    $this->db->beginTransaction();
                    $supplier_id = $this->supplier_model->create($data);

                    if ($supplier_id) {
                        log_action('suppliers', 'suppliers', 'create', $supplier_id, null, $data);
                        $this->db->commit();
                        send_notification(TranslationManager::t('msg.created_successfully'), 'success');
                        redirect(get_setting('site_url') . '/admin/suppliers/suppliers');
                    } else {
                        $this->db->rollBack();
                        send_notification(TranslationManager::t('msg.error_occurred'), 'danger');
                    }
                } catch (Exception $e) {
                    $this->db->rollBack();
                    send_notification(TranslationManager::t('msg.db_error') . ': ' . $e->getMessage(), 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    send_notification($error, 'danger');
                }
            }

            redirect(get_setting('site_url') . '/admin/suppliers/suppliers/create');
        }
    }

    /**
     * Show edit supplier form
     */
    public function edit($id)
    {
    if (!can('suppliers', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $supplier = $this->supplier_model->readOne($id);

        if (!$supplier) {
            send_notification(TranslationManager::t('msg.not_found'), 'danger');
            redirect(get_setting('site_url') . '/admin/suppliers/suppliers');
        }

        $countries = $this->supplier_model->getCountries();
        $cities = $supplier['country_id'] ? $this->supplier_model->getCitiesByCountry($supplier['country_id']) : [];

        $page_title = TranslationManager::t('suppliers.edit_title');
        include admin_module_path('/views/suppliers/edit.php');
    }

    /**
     * Update supplier
     */
    public function update($id)
    {
    if (!can('suppliers', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification(TranslationManager::t('msg.invalid_token'), 'danger');
                redirect(get_setting('site_url') . '/admin/suppliers/suppliers/edit/' . $id);
            }

            $errors = $this->validateSupplierData($_POST);

            if (empty($errors)) {
                $data = [
                    'name' => sanitize_input($_POST['name']),
                    'address_line1' => sanitize_input($_POST['address_line1']),
                    'address_line2' => sanitize_input($_POST['address_line2']),
                    'zip_code' => sanitize_input($_POST['zip_code']),
                    'country_id' => !empty($_POST['country_id']) ? (int)$_POST['country_id'] : null,
                    'city_id' => !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null,
                    'tel1' => sanitize_input($_POST['tel1']),
                    'tel2' => sanitize_input($_POST['tel2']),
                    'email1' => sanitize_input($_POST['email1']),
                    'email2' => sanitize_input($_POST['email2']),
                    'notes' => sanitize_input($_POST['notes']),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];

                try {
                    $this->db->beginTransaction();
                    $old_data = $this->supplier_model->readOne($id);

                    if ($this->supplier_model->update($id, $data)) {
                        log_action('suppliers', 'suppliers', 'update', $id, $old_data, $data);
                        $this->db->commit();
                        send_notification(TranslationManager::t('msg.updated_successfully'), 'success');
                        redirect(get_setting('site_url') . '/admin/suppliers/suppliers');
                    } else {
                        $this->db->rollBack();
                        send_notification(TranslationManager::t('msg.error_occurred'), 'danger');
                    }
                } catch (Exception $e) {
                    $this->db->rollBack();
                    send_notification(TranslationManager::t('msg.db_error') . ': ' . $e->getMessage(), 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    send_notification($error, 'danger');
                }
            }
        }

        redirect(get_setting('site_url') . '/admin/suppliers/suppliers/edit/' . $id);
    }

    /**
     * Delete supplier
     */
    public function delete($id)
    {
    if (!can('suppliers', 'delete')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        try {
            $old_data = $this->supplier_model->readOne($id);

            if ($this->supplier_model->delete($id)) {
                log_action('suppliers', 'suppliers', 'delete', $id, $old_data, null);
                send_notification(TranslationManager::t('msg.deleted_successfully'), 'success');
            } else {
                send_notification(TranslationManager::t('msg.error_occurred'), 'danger');
            }
        } catch (Exception $e) {
            send_notification(TranslationManager::t('msg.db_error') . ': ' . $e->getMessage(), 'danger');
        }

        redirect(get_setting('site_url') . '/admin/suppliers/suppliers');
    }


    public function view($id)
    {
    if (!can('suppliers', 'view')) {
            redirect(get_setting('site_url') . '/admin/unauthorized.php');
        }

        $supplier = $this->supplier_model->readOne($id);
        if (!$supplier) {
            send_notification(TranslationManager::t('msg.not_found'), 'danger');
            redirect(get_setting('site_url') . '/admin/suppliers/suppliers');
        }

        $contacts = $this->supplier_model->getContacts($id);

        $page_title = TranslationManager::t('suppliers.view_title');
        include admin_module_path('/views/suppliers/view.php');
    }

    /**
     * Validate supplier input
     */
    private function validateSupplierData($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = TranslationManager::t('validation.supplier_name_required');
        }

        if (!empty($data['email1']) && !filter_var($data['email1'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = TranslationManager::t('validation.email_invalid');
        }

        if (!empty($data['email2']) && !filter_var($data['email2'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = TranslationManager::t('validation.email_invalid');
        }

        return $errors;
    }

    public function select()
    {
        $search = $_POST['search'] ?? '';
        $sql = "SELECT id, name FROM suppliers WHERE name LIKE :search ORDER BY name ASC LIMIT 20";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':search' => "%$search%"]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    }
}
