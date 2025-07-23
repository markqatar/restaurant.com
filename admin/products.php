<?php
require_once '../includes/session.php';
require_once '../controllers/ProductController.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$controller = new ProductController();
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
            redirect('products.php');
        }
        break;
        
    case 'update':
        if ($id) {
            $controller->update($id);
        } else {
            redirect('products.php');
        }
        break;
        
    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            redirect('products.php');
        }
        break;
        
    case 'toggle-status':
        if ($id) {
            $controller->toggleStatus($id);
        } else {
            redirect('products.php');
        }
        break;
        
    case 'search':
        $controller->search();
        break;
        
    default:
        $controller->index();
        break;
}
?>