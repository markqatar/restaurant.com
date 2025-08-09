<?php
class Customer {
    private $db;
    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }
    public function create(array $data){
        $sql = "INSERT INTO customers (email,password_hash,first_name,last_name,phone,date_of_birth,status,primary_auth_method,meta) VALUES (:email,:password_hash,:first_name,:last_name,:phone,:date_of_birth,:status,:primary_auth_method,:meta)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => $data['email'] ?? null,
            ':password_hash' => $data['password_hash'] ?? null,
            ':first_name' => $data['first_name'] ?? null,
            ':last_name' => $data['last_name'] ?? null,
            ':phone' => $data['phone'] ?? null,
            ':date_of_birth' => $data['date_of_birth'] ?? null,
            ':status' => $data['status'] ?? 'pending_profile',
            ':primary_auth_method' => $data['primary_auth_method'] ?? 'email',
            ':meta' => isset($data['meta']) ? json_encode($data['meta']) : null,
        ]);
        return $this->db->lastInsertId();
    }
    public function findByEmail(string $email){
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE email=:email LIMIT 1");
        $stmt->execute([':email'=>$email]);
        return $stmt->fetch();
    }
    public function findById(int $id){
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }
    public function update(int $id,array $data){
        $fields=[];$params=[':id'=>$id];
        foreach(['first_name','last_name','phone','date_of_birth','status','meta'] as $f){
            if(array_key_exists($f,$data)){
                $fields[]="$f=:$f";
                $params[":$f"] = ($f==='meta' ? json_encode($data[$f]) : $data[$f]);
            }
        }
        if(!$fields) return false;
        $sql="UPDATE customers SET ".implode(',', $fields).", updated_at=NOW() WHERE id=:id";
        $stmt=$this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
