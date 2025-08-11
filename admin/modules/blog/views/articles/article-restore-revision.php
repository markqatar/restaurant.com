<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/ArticleController.php';
require_once '../includes/functions.php';
header('Content-Type: application/json');
if(!isset($_SESSION['user_id'])){ http_response_code(403); echo json_encode(['error'=>'auth']); exit; }
$revisionId = (int)($_GET['revision_id'] ?? 0); if(!$revisionId){ echo json_encode(['success'=>false]); exit; }
$controller = new ArticleController($pdo); $controller->restoreRevision($revisionId);
