<?php
use Luracast\Restler\RestException;

class AddressFieldRuleApi {
    /**
     * GET /address-field-rules
     */
    public function get(){
        $m = new AddressFieldRule();
        return $m->listActive();
    }
    /**
     * POST /address-field-rules (bulk upsert single rule)
     */
    public function post(){
        $input=json_decode(file_get_contents('php://input'), true) ?? [];
        if(empty($input['field_key'])) throw new RestException(400,'field_key required');
        $m=new AddressFieldRule();
        $m->upsert($input);
        return ['ok'=>true];
    }
    /**
     * GET /address-field-rules/resolve?state_id=&delivery_area_id=
     */
    public function getResolve($state_id=null,$delivery_area_id=null){
        if(!function_exists('resolve_address_field_rules')){
            require_once get_setting('base_path').'/admin/modules/customers/helpers/address_field_rules.php';
        }
        return resolve_address_field_rules($state_id,$delivery_area_id);
    }
}
