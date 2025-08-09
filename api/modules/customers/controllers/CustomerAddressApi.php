<?php
use Luracast\Restler\RestException;

class CustomerAddressApi {
    /**
     * GET /customers/{customer_id}/addresses
     */
    public function get($customer_id){
    $this->requireCustomerAuth((int)$customer_id);
        $m = new CustomerAddress();
        return $m->listByCustomer((int)$customer_id);
    }
    /**
     * POST /customers/{customer_id}/addresses
     */
    public function post($customer_id){
    $this->requireCustomerAuth((int)$customer_id);
        $input=json_decode(file_get_contents('php://input'), true) ?? [];
        foreach(['line1'] as $req){ if(empty($input[$req])) throw new RestException(400, "$req required"); }
            $state_id = $input['state_id'] ?? null;
            $delivery_area_id = $input['delivery_area_id'] ?? null;
            if(!function_exists('resolve_address_field_rules')){
                require_once get_setting('base_path').'/admin/modules/customers/helpers/address_field_rules.php';
            }
            $rules = resolve_address_field_rules($state_id, $delivery_area_id);
            $extra = $input['extra'] ?? [];
            if(!is_array($extra)) $extra = [];
            [$errors, $sanitizedExtra] = validate_address_extra($extra, $rules);
            if($errors){ throw new RestException(422, implode('; ',$errors)); }
            $input['extra'] = $sanitizedExtra;
            $m = new CustomerAddress();
            $id = $m->create((int)$customer_id,$input);
            return ['id'=>$id,'rules_applied'=>$rules];
    }
    /**
     * PUT /customers/{customer_id}/addresses/{id}
     */
    public function put($customer_id,$id){
    $this->requireCustomerAuth((int)$customer_id);
        $input=json_decode(file_get_contents('php://input'), true) ?? [];
            $state_id = $input['state_id'] ?? null;
            $delivery_area_id = $input['delivery_area_id'] ?? null;
            if(!function_exists('resolve_address_field_rules')){
                require_once get_setting('base_path').'/admin/modules/customers/helpers/address_field_rules.php';
            }
            $rules = resolve_address_field_rules($state_id, $delivery_area_id);
            if(isset($input['extra'])){
                if(!is_array($input['extra'])) $input['extra'] = [];
                [$errors,$sanitizedExtra] = validate_address_extra($input['extra'],$rules);
                if($errors){ throw new RestException(422, implode('; ',$errors)); }
                $input['extra'] = $sanitizedExtra;
            }
            $m=new CustomerAddress();
            $m->update((int)$id,(int)$customer_id,$input);
            return ['updated'=>true,'rules_applied'=>$rules];
    }
    /**
     * DELETE /customers/{customer_id}/addresses/{id}
     */
    public function delete($customer_id,$id){
        $this->requireCustomerAuth((int)$customer_id);
        $m=new CustomerAddress();
        $m->delete((int)$id,(int)$customer_id);
        return ['deleted'=>true];
    }
    private function requireCustomerAuth(int $customer_id){
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if(stripos($hdr,'Bearer ')!==0) throw new RestException(401,'missing bearer token');
        $token = substr($hdr,7);
        $db=Database::getInstance()->getConnection();
        $st=$db->prepare("SELECT customer_id FROM customer_sessions WHERE token_hash=:th AND customer_id=:cid AND expires_at>NOW() LIMIT 1");
        $st->execute([':th'=>hash('sha256',$token),':cid'=>$customer_id]);
        if(!$st->fetch()) throw new RestException(401,'unauthorized');
        $db->prepare("UPDATE customer_sessions SET last_used_at=NOW() WHERE token_hash=:th")->execute([':th'=>hash('sha256',$token)]);
    }
}
