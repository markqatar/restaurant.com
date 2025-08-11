<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/ArticleController.php';
require_once '../includes/functions.php';
header('Content-Type: application/json');
if(!isset($_SESSION['user_id'])){ http_response_code(403); echo json_encode(['error'=>'auth']); exit; }
$articleId = (int)($_GET['article_id'] ?? 0); if(!$articleId){ echo json_encode([]); exit; }
$controller = new ArticleController($pdo); $controller->revisions($articleId);
