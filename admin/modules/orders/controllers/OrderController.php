<?php
require_once '../models/Order.php';
require_once '../includes/functions.php';

class OrderController {
    private $order_model;
    
    public function __construct() {
        $this->order_model = new Order();
    }
    
    // Display orders list
    public function index() {
        if (!has_permission($_SESSION['user_id'], 'orders', 'view')) {
            redirect('../admin/unauthorized.php');
        }
        
        $status = $_GET['status'] ?? null;
        $branch_id = $_GET['branch'] ?? null;
        
        $orders = $this->order_model->read($status, null, $branch_id, 50);
        $status_counts = $this->order_model->getStatusCounts();
        
        $page_title = "Gestione Ordini";
        include '../views/orders/index.php';
    }
    
    // Show create order form
    public function create() {
        if (!has_permission($_SESSION['user_id'], 'orders', 'create')) {
            redirect('../admin/unauthorized.php');
        }
        
        $branches = $this->getBranches();
        $customers = $this->getCustomers();
        $products = $this->getProducts();
        
        $page_title = "Nuovo Ordine";
        include '../views/orders/create.php';
    }
    
    // Store new order
    public function store() {
        if (!has_permission($_SESSION['user_id'], 'orders', 'create')) {
            redirect('../admin/unauthorized.php');
        }
        
        if ($_POST) {
            // Verify CSRF token
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('Token di sicurezza non valido', 'danger');
                redirect('orders.php?action=create');
            }
            
            // Validate and prepare order data
            $data = [
                'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'branch_id' => (int)$_POST['branch_id'],
                'order_type' => sanitize_input($_POST['order_type']),
                'customer_name' => sanitize_input($_POST['customer_name']),
                'customer_phone' => sanitize_input($_POST['customer_phone']),
                'delivery_address' => sanitize_input($_POST['delivery_address']),
                'notes' => sanitize_input($_POST['notes']),
                'payment_method' => sanitize_input($_POST['payment_method']),
                'subtotal' => (float)$_POST['subtotal'],
                'discount_amount' => (float)($_POST['discount_amount'] ?? 0),
                'delivery_fee' => (float)($_POST['delivery_fee'] ?? 0),
                'tax_amount' => (float)($_POST['tax_amount'] ?? 0),
                'total' => (float)$_POST['total']
            ];
            
            // Process order items
            if (isset($_POST['items']) && !empty($_POST['items'])) {
                $items = [];
                foreach ($_POST['items'] as $item) {
                    $items[] = [
                        'product_id' => (int)$item['product_id'],
                        'quantity' => (int)$item['quantity'],
                        'unit_price' => (float)$item['unit_price'],
                        'discount_amount' => (float)($item['discount_amount'] ?? 0),
                        'subtotal' => (float)$item['subtotal'],
                        'special_instructions' => sanitize_input($item['special_instructions'] ?? '')
                    ];
                }
                $data['items'] = $items;
            }
            
            try {
                $order_id = $this->order_model->create($data);
                if ($order_id) {
                    log_action($_SESSION['user_id'], 'create_order', 'Created order ID: ' . $order_id);
                    send_notification('Ordine creato con successo', 'success');
                    
                    // Print receipt if requested
                    if (isset($_POST['print_receipt'])) {
                        $this->printReceipt($order_id);
                    }
                    
                    redirect('orders.php?view=' . $order_id);
                } else {
                    send_notification('Errore nella creazione dell\'ordine', 'danger');
                }
            } catch (Exception $e) {
                send_notification('Errore database: ' . $e->getMessage(), 'danger');
            }
            
            redirect('orders.php?action=create');
        }
    }
    
    // Show single order details
    public function view($id) {
        if (!has_permission($_SESSION['user_id'], 'orders', 'view')) {
            redirect('../admin/unauthorized.php');
        }
        
        $order = $this->order_model->readOne($id);
        
        if (!$order) {
            send_notification('Ordine non trovato', 'danger');
            redirect('orders.php');
        }
        
        $page_title = "Ordine #" . $order['order_number'];
        include '../views/orders/view.php';
    }
    
    // Update order status
    public function updateStatus($id) {
        if (!has_permission($_SESSION['user_id'], 'orders', 'update')) {
            redirect('../admin/unauthorized.php');
        }
        
        if ($_POST && isset($_POST['status'])) {
            $status = sanitize_input($_POST['status']);
            
            try {
                if ($this->order_model->updateStatus($id, $status)) {
                    log_action($_SESSION['user_id'], 'update_order_status', "Order ID: $id, Status: $status");
                    send_notification('Status ordine aggiornato', 'success');
                    
                    // Send notification to customer if needed
                    $this->sendStatusNotification($id, $status);
                } else {
                    send_notification('Errore nell\'aggiornamento status', 'danger');
                }
            } catch (Exception $e) {
                send_notification('Errore database: ' . $e->getMessage(), 'danger');
            }
        }
        
        redirect('orders.php?view=' . $id);
    }
    
    // Assign rider to order
    public function assignRider($id) {
        if (!has_permission($_SESSION['user_id'], 'orders', 'update')) {
            redirect('../admin/unauthorized.php');
        }
        
        if ($_POST && isset($_POST['rider_id'])) {
            $rider_id = (int)$_POST['rider_id'];
            
            try {
                if ($this->order_model->assignRider($id, $rider_id)) {
                    log_action($_SESSION['user_id'], 'assign_rider', "Order ID: $id, Rider ID: $rider_id");
                    send_notification('Rider assegnato all\'ordine', 'success');
                } else {
                    send_notification('Errore nell\'assegnazione rider', 'danger');
                }
            } catch (Exception $e) {
                send_notification('Errore database: ' . $e->getMessage(), 'danger');
            }
        }
        
        redirect('orders.php?view=' . $id);
    }
    
    // Print receipt
    public function printReceipt($id) {
        $order = $this->order_model->readOne($id);
        
        if ($order) {
            // Generate receipt HTML/PDF
            include '../views/orders/receipt.php';
            
            // Send to thermal printer (implementation depends on printer library)
            $this->sendToPrinter($order);
        }
    }
    
    // Delete order
    public function delete($id) {
        if (!has_permission($_SESSION['user_id'], 'orders', 'delete')) {
            redirect('../admin/unauthorized.php');
        }
        
        try {
            if ($this->order_model->delete($id)) {
                log_action($_SESSION['user_id'], 'delete_order', 'Deleted order ID: ' . $id);
                send_notification('Ordine eliminato con successo', 'success');
            } else {
                send_notification('Errore nell\'eliminazione', 'danger');
            }
        } catch (Exception $e) {
            send_notification('Errore database: ' . $e->getMessage(), 'danger');
        }
        
        redirect('orders.php');
    }
    
    // Get daily statistics (AJAX)
    public function getDailyStats() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $stats = $this->order_model->getDailyStats($date);
        
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }
    
    // Export orders to CSV
    public function export() {
        if (!has_permission($_SESSION['user_id'], 'orders', 'view')) {
            redirect('../admin/unauthorized.php');
        }
        
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        
        // Get orders for export
        $orders = $this->getOrdersForExport($start_date, $end_date);
        
        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="orders_' . $start_date . '_' . $end_date . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Order Number', 'Date', 'Customer', 'Type', 'Status', 
            'Payment Method', 'Subtotal', 'Discount', 'Delivery Fee', 'Total'
        ]);
        
        // CSV data
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['order_number'],
                $order['order_date'],
                $order['customer_name'],
                $order['order_type'],
                $order['status'],
                $order['payment_method'],
                $order['subtotal'],
                $order['discount_amount'],
                $order['delivery_fee'],
                $order['total']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    // Helper methods
    private function getBranches() {
        global $db;
        $stmt = $db->prepare("SELECT id, name FROM branches WHERE is_active = 1 ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getCustomers() {
        global $db;
        $stmt = $db->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name, email FROM customers WHERE is_active = 1 ORDER BY first_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getProducts() {
        global $db;
        $stmt = $db->prepare("SELECT p.id, p.name, p.price, c.name as category_name FROM final_products p LEFT JOIN product_categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY c.name, p.name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function sendStatusNotification($order_id, $status) {
        // Implementation for sending notification to customer
        // Could be email, SMS, push notification, etc.
    }
    
    private function sendToPrinter($order) {
        // Implementation for thermal printer
        // This would use libraries like mike42/escpos-php
    }
    
    private function getOrdersForExport($start_date, $end_date) {
        global $db;
        $stmt = $db->prepare("
            SELECT o.*, c.first_name, c.last_name, 
                   CONCAT(c.first_name, ' ', c.last_name) as customer_name
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE DATE(o.order_date) BETWEEN ? AND ?
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$start_date, $end_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>