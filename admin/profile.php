<?php
require_once __DIR__ . '/../controllers/ProfileController.php';

$controller = new ProfileController();
$data = $controller->index();

// Include the view
include __DIR__ . '/../views/profile/index.php';
?>