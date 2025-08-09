<?php
class AddressFieldRule {
    private $db;
    public function __construct(){ $this->db = Database::getInstance()->getConnection(); }
    public function listActive(){
        return $this->db->query("SELECT * FROM address_field_rules WHERE active=1 ORDER BY sort_order, id")->fetchAll();
    }
    public function listAll(){
        return $this->db->query("SELECT * FROM address_field_rules ORDER BY sort_order, id")->fetchAll();
    }
    public function getById(int $id){
        $st=$this->db->prepare("SELECT * FROM address_field_rules WHERE id=:id");
        $st->execute([':id'=>$id]);
        return $st->fetch();
    }
    public function upsert(array $data){
        $stmt=$this->db->prepare("INSERT INTO address_field_rules (state_id,delivery_area_id,field_key,requirement,label,sort_order,active) VALUES (:state_id,:delivery_area_id,:field_key,:requirement,:label,:sort_order,:active)
        ON DUPLICATE KEY UPDATE requirement=VALUES(requirement), label=VALUES(label), sort_order=VALUES(sort_order), active=VALUES(active)");
        $stmt->execute([
            ':state_id'=>$data['state_id']??null,
            ':delivery_area_id'=>$data['delivery_area_id']??null,
            ':field_key'=>$data['field_key'],
            ':requirement'=>$data['requirement']??'optional',
            ':label'=>$data['label']??ucfirst(str_replace('_',' ',$data['field_key'])),
            ':sort_order'=>$data['sort_order']??0,
            ':active'=>isset($data['active'])? (int)$data['active'] : 1,
        ]);
        return $this->db->lastInsertId();
    }
    public function update(int $id,array $data){
        $fields=[];$params=[':id'=>$id];
        foreach(['state_id','delivery_area_id','field_key','requirement','label','sort_order','active'] as $f){
            if(array_key_exists($f,$data)){
                $fields[]="$f=:$f";
                $params[":$f"] = $data[$f];
            }
        }
        if(!$fields) return false;
        $sql="UPDATE address_field_rules SET ".implode(',', $fields)." WHERE id=:id";
        $st=$this->db->prepare($sql);
        return $st->execute($params);
    }
    public function delete(int $id){
        $st=$this->db->prepare("DELETE FROM address_field_rules WHERE id=:id");
        return $st->execute([':id'=>$id]);
    }
}
