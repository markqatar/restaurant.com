<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../controllers/AdminMenuController.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('/admin/login');
}

$controller = new AdminMenuController();
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
            redirect('menu-management.php');
        }
        break;
        
    case 'update':
        if ($id) {
            $controller->update($id);
        } else {
            redirect('menu-management.php');
        }
        break;
        
    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            redirect('menu-management.php');
        }
        break;
        
    default:
        $controller->index();
        break;
}
?>