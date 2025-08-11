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

    /**
     * Server-side DataTable helper
     * @return array [total=>int, filtered=>int, data=>array]
     */
    public function datatable(int $start, int $length, string $search='', string $orderColumn='id', string $orderDir='DESC'){
        $allowed = ['id','first_name','email','phone','status','created_at'];
        if(!in_array($orderColumn,$allowed)) $orderColumn = 'id';
        $orderDir = strtoupper($orderDir)==='ASC' ? 'ASC':'DESC';
        // Total
        $total = (int)$this->db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
        $where = '';
        $params = [];
        if($search !== ''){
            $where = " WHERE (first_name LIKE :s OR last_name LIKE :s OR email LIKE :s OR phone LIKE :s)";
            $params[':s'] = '%'.$search.'%';
        }
        $sqlFiltered = "SELECT COUNT(*) FROM customers".$where;
        $st = $this->db->prepare($sqlFiltered); $st->execute($params); $filtered = (int)$st->fetchColumn();
        $sqlData = "SELECT id, email, first_name, last_name, phone, status, created_at FROM customers".$where." ORDER BY $orderColumn $orderDir LIMIT :start,:len";
        $st2 = $this->db->prepare($sqlData);
        foreach($params as $k=>$v){ $st2->bindValue($k,$v); }
        $st2->bindValue(':start',$start, PDO::PARAM_INT);
        $st2->bindValue(':len',$length, PDO::PARAM_INT);
        $st2->execute();
        $rows = $st2->fetchAll(PDO::FETCH_ASSOC);
        return ['total'=>$total,'filtered'=>$filtered,'data'=>$rows];
    }
}
