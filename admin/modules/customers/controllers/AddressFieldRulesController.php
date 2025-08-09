<?php
require_once admin_module_path('/models/AddressFieldRule.php','customers');

class AddressFieldRulesController {
    private $model;
    public function __construct(){
        $this->model = new AddressFieldRule();
    }
    public function index(){
        if(!can('address_field_rules','view')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $rules = $this->model->listAll();
        $page_title = 'Address Field Rules';
        include admin_module_path('/views/address_field_rules/index.php','customers');
    }
    public function create(){
        if(!can('address_field_rules','update')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $rule = null; $page_title='Create Address Field Rule';
        include admin_module_path('/views/address_field_rules/form.php','customers');
    }
    public function store(){
        if(!can('address_field_rules','update')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $data = $_POST;
        if(empty($data['field_key'])){ set_notification('field_key required','error'); redirect(get_setting('site_url').'/admin/customers/address-field-rules/create'); }
        $this->model->upsert($data);
        set_notification('Rule saved','success');
        redirect(get_setting('site_url').'/admin/customers/address-field-rules');
    }
    public function edit($id){
        if(!can('address_field_rules','update')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $rule = $this->model->getById((int)$id);
        if(!$rule){ set_notification('Rule not found','error'); redirect(get_setting('site_url').'/admin/customers/address-field-rules'); }
        $page_title='Edit Address Field Rule #'.$rule['id'];
        include admin_module_path('/views/address_field_rules/form.php','customers');
    }
    public function update($id){
        if(!can('address_field_rules','update')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $data = $_POST; $this->model->update((int)$id,$data); set_notification('Updated','success');
        redirect(get_setting('site_url').'/admin/customers/address-field-rules');
    }
    public function delete($id){
        if(!can('address_field_rules','update')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $this->model->delete((int)$id); set_notification('Deleted','success');
        redirect(get_setting('site_url').'/admin/customers/address-field-rules');
    }
    public function resolvePreview(){
        if(!can('address_field_rules','view')) redirect(get_setting('site_url').'/admin/unauthorized.php');
        $state_id = $_GET['state_id'] ?? null; $delivery_area_id = $_GET['delivery_area_id'] ?? null;
        if(!function_exists('resolve_address_field_rules')) require_once admin_module_path('/helpers/address_field_rules.php','customers');
        $effective = resolve_address_field_rules($state_id, $delivery_area_id);
        header('Content-Type: application/json'); echo json_encode($effective); exit;
    }
}
