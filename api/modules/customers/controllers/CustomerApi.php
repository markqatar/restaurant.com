<?php
use Luracast\Restler\RestException;

class CustomerApi {
    /**
     * POST /customers/register
     */
    public function postRegister(){
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        if(empty($input['email']) || empty($input['password'])){
            throw new RestException(400,'email and password required');
        }
        $customerModel = new Customer();
        if($customerModel->findByEmail($input['email'])){
            throw new RestException(409,'email already registered');
        }
        $id = $customerModel->create([
            'email'=>$input['email'],
            'password_hash'=>password_hash($input['password'], PASSWORD_BCRYPT),
            'status'=>'pending_profile',
            'primary_auth_method'=>'email'
        ]);
        return ['id'=>$id,'status'=>'pending_profile'];
    }

    /**
     * GET /customers/profile/{id}
     */
    public function getProfile($id){
    $this->requireCustomerAuth((int)$id);
        $customerModel = new Customer();
        $c = $customerModel->findById((int)$id);
        if(!$c){ throw new RestException(404,'not found'); }
        unset($c['password_hash']);
        return $c;
    }

    /**
     * PUT /customers/profile/{id}
     */
    public function putProfile($id){
        $this->requireCustomerAuth((int)$id);
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $customerModel = new Customer();
        $exists = $customerModel->findById((int)$id);
        if(!$exists){ throw new RestException(404,'not found'); }
        $customerModel->update((int)$id, array_intersect_key($input, array_flip(['first_name','last_name','phone','date_of_birth','meta','status'])));
        return ['updated'=>true];
    }
    /**
     * POST /customers/login
     */
    public function postLogin(){
        $input=json_decode(file_get_contents('php://input'), true) ?? [];
        if(empty($input['email'])|| empty($input['password'])) throw new RestException(400,'email/password required');
        $m=new Customer(); $c=$m->findByEmail($input['email']);
        if(!$c || empty($c['password_hash']) || !password_verify($input['password'],$c['password_hash'])) throw new RestException(401,'invalid credentials');
        $token = bin2hex(random_bytes(32));
        $db=Database::getInstance()->getConnection();
        $stmt=$db->prepare("REPLACE INTO customer_sessions (customer_id, token_hash, created_at, last_used_at, expires_at) VALUES (:cid, :th, NOW(), NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))");
        $stmt->execute([':cid'=>$c['id'], ':th'=>hash('sha256',$token)]);
        return ['token'=>$token,'customer_id'=>$c['id']];
    }
    private function requireCustomerAuth(int $customer_id){
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if(stripos($hdr,'Bearer ')!==0) throw new RestException(401,'missing bearer token');
        $token = substr($hdr,7);
        if(strlen($token)<10) throw new RestException(401,'invalid token');
        $db=Database::getInstance()->getConnection();
        $st=$db->prepare("SELECT customer_id FROM customer_sessions WHERE token_hash=:th AND customer_id=:cid AND expires_at>NOW() LIMIT 1");
        $st->execute([':th'=>hash('sha256',$token),':cid'=>$customer_id]);
        $row=$st->fetch();
        if(!$row) throw new RestException(401,'unauthorized');
        $db->prepare("UPDATE customer_sessions SET last_used_at=NOW() WHERE token_hash=:th")->execute([':th'=>hash('sha256',$token)]);
    }
}
