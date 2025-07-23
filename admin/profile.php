<?php
require_once __DIR__ . '/../admin/controllers/ProfileController.php';

$controller = new ProfileController();
$data = $controller->index();

// Include the view
include __DIR__ . '/../admin/views/profile/index.php';
?>