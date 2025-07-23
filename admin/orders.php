<?php
require_once '../includes/session.php';
require_once '../controllers/OrderController.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$controller = new OrderController();
$action = $_GET['action'] ?? ($_GET['view'] ? 'view' : 'index');
$id = $_GET['id'] ?? $_GET['view'] ?? null;

// Route to appropriate method
switch ($action) {
    case 'create':
        $controller->create();
        break;
        
    case 'store':
        $controller->store();
        break;
        
    case 'view':
        if ($id) {
            $controller->view($id);
        } else {
            redirect('orders.php');
        }
        break;
        
    case 'update-status':
        if ($id) {
            $controller->updateStatus($id);
        } else {
            redirect('orders.php');
        }
        break;
        
    case 'assign-rider':
        if ($id) {
            $controller->assignRider($id);
        } else {
            redirect('orders.php');
        }
        break;
        
    case 'print':
        if ($id) {
            $controller->printReceipt($id);
        } else {
            redirect('orders.php');
        }
        break;
        
    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            redirect('orders.php');
        }
        break;
        
    case 'export':
        $controller->export();
        break;
        
    case 'stats':
        $controller->getDailyStats();
        break;
        
    default:
        $controller->index();
        break;
}
?>