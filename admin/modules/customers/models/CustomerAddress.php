<?php
class CustomerAddress {
    private $db;
    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }
    public function listByCustomer(int $customer_id){
        $stmt=$this->db->prepare("SELECT * FROM customer_addresses WHERE customer_id=:cid ORDER BY is_default DESC, id ASC");
        $stmt->execute([':cid'=>$customer_id]);
        return $stmt->fetchAll();
    }
    public function create(int $customer_id,array $data){
        if(!empty($data['is_default'])){
            $this->db->prepare("UPDATE customer_addresses SET is_default=0 WHERE customer_id=:cid")->execute([':cid'=>$customer_id]);
        }
        $sql="INSERT INTO customer_addresses (customer_id,label,line1,line2,bell_name,phone_alt,city_id,state_id,postal_code,delivery_area_id,is_default,extra) VALUES (:customer_id,:label,:line1,:line2,:bell_name,:phone_alt,:city_id,:state_id,:postal_code,:delivery_area_id,:is_default,:extra)";
        $stmt=$this->db->prepare($sql);
        $stmt->execute([
            ':customer_id'=>$customer_id,
            ':label'=>$data['label']??null,
            ':line1'=>$data['line1'],
            ':line2'=>$data['line2']??null,
            ':bell_name'=>$data['bell_name']??null,
            ':phone_alt'=>$data['phone_alt']??null,
            ':city_id'=>$data['city_id']??null,
            ':state_id'=>$data['state_id']??null,
            ':postal_code'=>$data['postal_code']??null,
            ':delivery_area_id'=>$data['delivery_area_id']??null,
            ':is_default'=>!empty($data['is_default'])?1:0,
            ':extra'=> isset($data['extra']) ? json_encode($data['extra']) : null,
        ]);
        return $this->db->lastInsertId();
    }
    public function update(int $id,int $customer_id,array $data){
        $fields=[];$params=[':id'=>$id,':cid'=>$customer_id];
        foreach(['label','line1','line2','bell_name','phone_alt','city_id','state_id','postal_code','delivery_area_id','extra'] as $f){
            if(array_key_exists($f,$data)){
                $fields[]="$f=:$f";
                $params[":$f"] = ($f==='extra'? json_encode($data[$f]): $data[$f]);
            }
        }
        if(isset($data['is_default'])){
            if($data['is_default']){
                $this->db->prepare("UPDATE customer_addresses SET is_default=0 WHERE customer_id=:cid")->execute([':cid'=>$customer_id]);
                $fields[]='is_default=1';
            } else {
                $fields[]='is_default=0';
            }
        }
        if(!$fields) return false;
        $sql="UPDATE customer_addresses SET ".implode(',', $fields).", updated_at=NOW() WHERE id=:id AND customer_id=:cid";
        $stmt=$this->db->prepare($sql);
        return $stmt->execute($params);
    }
    public function delete(int $id,int $customer_id){
        $stmt=$this->db->prepare("DELETE FROM customer_addresses WHERE id=:id AND customer_id=:cid");
        return $stmt->execute([':id'=>$id,':cid'=>$customer_id]);
    }
}
