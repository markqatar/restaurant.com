<?php
require_once admin_module_path('/models/Customer.php', 'customers');

class CustomersController {
    private $model;
    public function __construct(){
        $this->model = new Customer();
        TranslationManager::loadModuleTranslations('customers');
    }
    public function index(){
        if(!can('customers','view')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, email, first_name, last_name, phone, status, created_at FROM customers ORDER BY id DESC LIMIT 100");
        $customers = $stmt->fetchAll();
        $page_title = 'Customers';
        include admin_module_path('/views/customers/index.php','customers');
    }
    public function view($id){
        if(!can('customers','view')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $customer = $this->model->findById((int)$id);
        if(!$customer){
            send_notification('Customer not found','danger');
            redirect(get_setting('site_url').'/admin/customers/customers');
        }
        unset($customer['password_hash']);
        $page_title = 'Customer #'.$customer['id'];
        include admin_module_path('/views/customers/view.php','customers');
    }
}
