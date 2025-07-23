<?php
require_once get_setting('base_path', '/var/www/html') . 'includes/session.php';
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/functions.php';
require_once get_setting('base_path', '/var/www/html') . 'admin/controllers/BranchController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('../auth/login.php');
}

// Initialize controller
$controller = new BranchController();

// Get action and ID
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Route actions
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
            redirect('branches.php');
        }
        break;
        
    case 'update':
        if ($id) {
            $controller->update($id);
        } else {
            redirect('branches.php');
        }
        break;
        
    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            redirect('branches.php');
        }
        break;
        
    case 'manage-users':
        if ($id) {
            $controller->manageUsers($id);
        } else {
            redirect('branches.php');
        }
        break;
        
    case 'assign-user':
        $controller->assignUser();
        break;
        
    case 'remove-user':
        $controller->removeUser();
        break;
        
    case 'get-cities':
        $controller->getCities();
        break;
        
    default:
        $controller->index();
        break;
}
?>