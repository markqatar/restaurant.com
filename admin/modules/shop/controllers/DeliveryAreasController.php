<?php
require_once admin_module_path('/models/DeliveryArea.php');

class DeliveryAreasController
{
    private $delivery_area_model;
    private $db;

    public function __construct()
    {
        $this->delivery_area_model = new DeliveryArea();
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        TranslationManager::loadModuleTranslations('shops'); // Assumendo che faccia parte del modulo Shops
    }

    /**
     * Display list of delivery areas
     */
    public function index()
    {
        if (!has_permission($_SESSION['user_id'], 'delivery_areas', 'view')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $delivery_areas = $this->delivery_area_model->getAllDeliveryAreas();
        $branches = $this->delivery_area_model->getAllBranches();

        $page_title = TranslationManager::t('delivery_areas.page_title');

        include admin_module_path('/views/delivery_areas/index.php');
    }

    /**
     * Show create delivery area form
     */
    public function create()
    {
        if (!has_permission($_SESSION['user_id'], 'delivery_areas', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $branches = $this->delivery_area_model->getAllBranches();
        $page_title = TranslationManager::t('delivery_areas.create_title');
        include admin_module_path('/views/delivery_areas/create.php');
    }

    /**
     * Store new delivery area
     */
    public function store()
    {
        if (!has_permission($_SESSION['user_id'], 'delivery_areas', 'create')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification(TranslationManager::t('msg.invalid_token'), 'danger');
                redirect(get_setting('site_url') . '/admin/shop/deliveryarea/create');
            }

            $errors = $this->validateDeliveryAreaData($_POST);

            if (empty($errors)) {
                $data = [
                    'area_name' => sanitize_input($_POST['area_name']),
                    'shop_id' => intval($_POST['shop_id'])
                ];

                try {
                    $newId = $this->delivery_area_model->createDeliveryArea($data['area_name'], $data['shop_id']);
                    if ($newId) {
                        log_action('shops', 'delivery_areas', 'create', $newId, null, $data);
                        send_notification(TranslationManager::t('msg.created_successfully'), 'success');
                        redirect(get_setting('site_url') . '/admin/shop/deliveryarea');
                    } else {
                        send_notification(TranslationManager::t('msg.error_occurred'), 'danger');
                    }
                } catch (Exception $e) {
                    send_notification(TranslationManager::t('msg.db_error') . ': ' . $e->getMessage(), 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    send_notification(TranslationManager::t($error), 'danger');
                }
            }

            redirect(get_setting('site_url') . '/admin/shop/deliveryarea/create');
        }
    }

    /**
     * Show edit delivery area form
     */
    public function edit($id)
    {
        if (!has_permission($_SESSION['user_id'], 'delivery_areas', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        $data = $this->delivery_area_model->getDeliveryAreaById($id);
        if (!$data) {
            send_notification(TranslationManager::t('msg.not_found'), 'danger');
            redirect(get_setting('site_url') . '/admin/shop/deliveryarea');
        }

        $branches = $this->delivery_area_model->getAllBranches();
        $page_title = TranslationManager::t('delivery_areas.edit_title');
        include admin_module_path('/views/delivery_areas/edit.php');
    }

    /**
     * Update delivery area
     */
    public function update($id)
    {
        if (!has_permission($_SESSION['user_id'], 'delivery_areas', 'update')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification(TranslationManager::t('msg.invalid_token'), 'danger');
                redirect(get_setting('site_url') . '/admin/shop/deliveryarea/edit/' . $id);
            }

            $errors = $this->validateDeliveryAreaData($_POST);

            if (empty($errors)) {
                $data = [
                    'area_name' => sanitize_input($_POST['area_name']),
                    'shop_id' => intval($_POST['shop_id'])
                ];

                try {
                    $this->db->beginTransaction();
                    $old_data = $this->delivery_area_model->getDeliveryAreaById($id);

                    if ($this->delivery_area_model->updateDeliveryArea($id, $data['area_name'], $data['shop_id'])) {
                        log_action('shops', 'delivery_areas', 'update', $id, $old_data, $data);
                        $this->db->commit();
                        send_notification(TranslationManager::t('msg.updated_successfully'), 'success');
                        redirect(get_setting('site_url') . '/admin/shop/deliveryarea');
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
                    send_notification(TranslationManager::t($error), 'danger');
                }
            }

            redirect(get_setting('site_url') . '/admin/shop/deliveryarea/edit/' . $id);
        }
    }

    /**
     * Delete delivery area
     */
    public function delete($id)
    {
        if (!has_permission($_SESSION['user_id'], 'delivery_areas', 'delete')) {
            redirect(__DIR__ . '/../admin/unauthorized.php');
        }

        try {
            $old_data = $this->delivery_area_model->getDeliveryAreaById($id);

            if ($this->delivery_area_model->deleteDeliveryArea($id)) {
                log_action('shops', 'delivery_areas', 'delete', $id, $old_data, null);
                send_notification(TranslationManager::t('msg.deleted_successfully'), 'success');
            } else {
                send_notification(TranslationManager::t('msg.error_occurred'), 'danger');
            }
        } catch (Exception $e) {
            send_notification(TranslationManager::t('msg.db_error') . ': ' . $e->getMessage(), 'danger');
        }

        redirect(get_setting('site_url') . '/admin/shop/deliveryarea');
    }

    /**
     * Validate delivery area input
     */
    private function validateDeliveryAreaData($data)
    {
        $errors = [];

        if (empty($data['area_name'])) {
            $errors[] = 'validation.area_name_required';
        }

        if (empty($data['shop_id']) || intval($data['shop_id']) <= 0) {
            $errors[] = 'validation.branch_required';
        }

        return $errors;
    }
}