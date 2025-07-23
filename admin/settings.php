<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../controllers/SettingController.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('admin/login.php');
}

$settingController = new SettingController();

// Get current section
$section = $_GET['section'] ?? 'general';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($section) {
        case 'general':
            $settingController->updateGeneralSettings();
            break;
        case 'rooms':
            if ($action === 'create') {
                $settingController->storeRoom();
            } elseif ($action === 'edit' && $id) {
                $settingController->updateRoom($id);
            }
            break;
        case 'tables':
            if ($action === 'create') {
                $settingController->storeTable();
            } elseif ($action === 'edit' && $id) {
                $settingController->updateTable($id);
            }
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'delete' && $id) {
    switch ($section) {
        case 'rooms':
            $settingController->deleteRoom($id);
            break;
        case 'tables':
            $settingController->deleteTable($id);
            break;
    }
}

// Display appropriate section
switch ($section) {
    case 'rooms':
        if ($action === 'create') {
            $settingController->createRoom();
        } elseif ($action === 'edit' && $id) {
            $settingController->editRoom($id);
        } else {
            $settingController->rooms();
        }
        break;
    case 'tables':
        if ($action === 'create') {
            $settingController->createTable();
        } elseif ($action === 'edit' && $id) {
            $settingController->editTable($id);
        } else {
            $settingController->tables();
        }
        break;
    default:
        $settingController->index();
        break;
}
?>