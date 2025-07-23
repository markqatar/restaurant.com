<?php

class Order {
    private $db;
    private $table = 'orders';
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Create order with branch assignment
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Set default branch if not provided and user_id is available
            if (empty($data['branch_id']) && !empty($data['user_id'])) {
                $data['branch_id'] = getDefaultBranchId($data['user_id']);
            }
            
            // Generate order number if not provided
            if (!isset($data['order_number'])) {
                $data['order_number'] = $this->generateOrderNumber();
            }
            
            $query = "INSERT INTO " . $this->table . " 
                      (order_number, customer_id, branch_id, rider_id, order_type, status,
                       subtotal, discount_amount, delivery_fee, tax_amount, total,
                       payment_method, payment_status, customer_name, customer_phone, 
                       delivery_address, notes, estimated_delivery_time) 
                      VALUES (:order_number, :customer_id, :branch_id, :rider_id, :order_type, :status,
                              :subtotal, :discount_amount, :delivery_fee, :tax_amount, :total,
                              :payment_method, :payment_status, :customer_name, :customer_phone,
                              :delivery_address, :notes, :estimated_delivery_time)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':order_number' => $data['order_number'],
                ':customer_id' => $data['customer_id'] ?? null,
                ':branch_id' => $data['branch_id'],
                ':rider_id' => $data['rider_id'] ?? null,
                ':order_type' => $data['order_type'],
                ':status' => $data['status'] ?? 'pending',
                ':subtotal' => $data['subtotal'],
                ':discount_amount' => $data['discount_amount'] ?? 0,
                ':delivery_fee' => $data['delivery_fee'] ?? 0,
                ':tax_amount' => $data['tax_amount'] ?? 0,
                ':total' => $data['total'],
                ':payment_method' => $data['payment_method'],
                ':payment_status' => $data['payment_status'] ?? 'pending',
                ':customer_name' => $data['customer_name'],
                ':customer_phone' => $data['customer_phone'],
                ':delivery_address' => $data['delivery_address'] ?? null,
                ':notes' => $data['notes'] ?? null,
                ':estimated_delivery_time' => $data['estimated_delivery_time'] ?? null
            ]);
            
            $order_id = $this->db->lastInsertId();
            
            // Insert order items
            if (isset($data['items']) && !empty($data['items'])) {
                $this->insertOrderItems($order_id, $data['items']);
            }
            
            $this->db->commit();
            return $order_id;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Insert order items
    private function insertOrderItems($order_id, $items) {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, discount_amount, subtotal, special_instructions)
                  VALUES (:order_id, :product_id, :quantity, :unit_price, :discount_amount, :subtotal, :special_instructions)";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($items as $item) {
            $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':unit_price' => $item['unit_price'],
                ':discount_amount' => $item['discount_amount'] ?? 0,
                ':subtotal' => $item['subtotal'],
                ':special_instructions' => $item['special_instructions'] ?? null
            ]);
        }
    }
    
    // Read all orders with branch filtering
    public function read($status = null, $customer_id = null, $branch_id = null, $limit = null, $user_id = null) {
        $base_query = "SELECT o.*, c.first_name, c.last_name, b.name as branch_name, r.name as rider_name
                       FROM " . $this->table . " o
                       LEFT JOIN customers c ON o.customer_id = c.id
                       LEFT JOIN branches b ON o.branch_id = b.id
                       LEFT JOIN riders r ON o.rider_id = r.id";
        
        $conditions = [];
        $params = [];
        
        if ($status) {
            $conditions[] = "o.status = :status";
            $params[':status'] = $status;
        }
        
        if ($customer_id) {
            $conditions[] = "o.customer_id = :customer_id";
            $params[':customer_id'] = $customer_id;
        }
        
        if ($branch_id) {
            $conditions[] = "o.branch_id = :branch_id";
            $params[':branch_id'] = $branch_id;
        }
        
        if (!empty($conditions)) {
            $base_query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Apply branch filtering if user_id provided
        if ($user_id) {
            $filtered = addBranchFilter($base_query, $user_id, $params, 'o');
            $base_query = $filtered['query'];
            $params = $filtered['params'];
        }
        
        $base_query .= " ORDER BY o.order_date DESC";
        
        if ($limit) {
            $base_query .= " LIMIT " . $limit;
        }
        
        $stmt = $this->db->prepare($base_query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Read single order with items
    public function readOne($id) {
        // Get order details
        $query = "SELECT o.*, c.first_name, c.last_name, c.email, c.phone as customer_phone_alt,
                         b.name as branch_name, b.address as branch_address, r.name as rider_name
                  FROM " . $this->table . " o
                  LEFT JOIN customers c ON o.customer_id = c.id
                  LEFT JOIN branches b ON o.branch_id = b.id
                  LEFT JOIN riders r ON o.rider_id = r.id
                  WHERE o.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            // Get order items
            $items_query = "SELECT oi.*, p.name as product_name, p.name_ar as product_name_ar
                           FROM order_items oi
                           LEFT JOIN final_products p ON oi.product_id = p.id
                           WHERE oi.order_id = :order_id";
            
            $items_stmt = $this->db->prepare($items_query);
            $items_stmt->execute([':order_id' => $id]);
            $order['items'] = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $order;
    }
    
    // Update order status
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status
        ]);
    }
    
    // Assign rider to order
    public function assignRider($id, $rider_id) {
        $query = "UPDATE " . $this->table . " SET rider_id = :rider_id WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':rider_id' => $rider_id
        ]);
    }
    
    // Update payment status
    public function updatePaymentStatus($id, $payment_status) {
        $query = "UPDATE " . $this->table . " SET payment_status = :payment_status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':id' => $id,
            ':payment_status' => $payment_status
        ]);
    }
    
    // Generate unique order number
    private function generateOrderNumber() {
        do {
            $order_number = 'ORD' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $exists = $this->orderNumberExists($order_number);
        } while ($exists);
        
        return $order_number;
    }
    
    // Check if order number exists
    private function orderNumberExists($order_number) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE order_number = :order_number";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':order_number' => $order_number]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    // Get daily statistics
    public function getDailyStats($date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $query = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status NOT IN ('cancelled') THEN total ELSE 0 END) as total_revenue,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed_orders,
                    AVG(CASE WHEN status NOT IN ('cancelled') THEN total ELSE NULL END) as avg_order_value
                  FROM " . $this->table . "
                  WHERE DATE(order_date) = :date";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':date' => $date]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get orders by status count
    public function getStatusCounts() {
        $query = "SELECT status, COUNT(*) as count 
                  FROM " . $this->table . " 
                  WHERE DATE(order_date) = CURDATE()
                  GROUP BY status";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    // Delete order (with items)
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Delete order items first
            $items_query = "DELETE FROM order_items WHERE order_id = :order_id";
            $items_stmt = $this->db->prepare($items_query);
            $items_stmt->execute([':order_id' => $id]);
            
            // Delete order
            $order_query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $order_stmt = $this->db->prepare($order_query);
            $result = $order_stmt->execute([':id' => $id]);
            
            $this->db->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
?>