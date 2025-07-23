<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../controllers/SupplierController.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$controller = new SupplierController();
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Route to appropriate method
switch ($action) {
    case 'create':
        $controller->create();
        break;
        
    case 'store':
        $controller->store();
        break;
        
    case 'edit':
        if ($id) {
            $controller->edit($id);
        } else {
            redirect('suppliers.php');
        }
        break;
        
    case 'update':
        if ($id) {
            $controller->update($id);
        } else {
            redirect('suppliers.php');
        }
        break;
        
    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            redirect('suppliers.php');
        }
        break;
        
    case 'get-cities':
        $controller->getCities();
        break;
        
    case 'add-contact':
        $controller->addContact();
        break;
        
    case 'delete-contact':
        $controller->deleteContact();
        break;
        
    default:
        $controller->index();
        break;
}
?>