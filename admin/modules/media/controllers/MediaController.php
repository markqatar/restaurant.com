<?php
require_once __DIR__ . '/../models/Media.php';
require_once __DIR__ . '/../includes/functions.php';

class MediaController {
    private $media;
    
    public function __construct($pdo) {
        $this->media = new Media($pdo);
    }
    
    public function index() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'media', 'view')) {
            header('Location: /admin/dashboard.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        $type = $_GET['type'] ?? null;
        $mediaFiles = $this->media->getAllMedia($type);
        
        return [
            'mediaFiles' => $mediaFiles,
            'currentType' => $type
        ];
    }
    
    public function upload() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'media', 'upload')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => translate('no_permission')]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $altText = trim($_POST['alt_text'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            $result = $this->media->uploadFile($_FILES['file'], $_SESSION['user_id'], $altText, $title, $description);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }
    
    public function update($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'media', 'edit')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => translate('no_permission')]);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $altText = trim($_POST['alt_text'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            $data = [
                'alt_text' => $altText,
                'title' => $title,
                'description' => $description
            ];
            
            if ($this->media->updateMedia($id, $data)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => translate('media_updated')]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => translate('error_occurred')]);
            }
            exit;
        }
    }
    
    public function delete($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'media', 'delete')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => translate('no_permission')]);
            exit;
        }
        
        if ($this->media->deleteMedia($id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => translate('media_deleted')]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => translate('error_occurred')]);
        }
        exit;
    }
    
    public function getMediaLibrary() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'media', 'view')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => translate('no_permission')]);
            exit;
        }
        
        $type = $_GET['type'] ?? null;
        $mediaFiles = $this->media->getAllMedia($type);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'media' => $mediaFiles
        ]);
        exit;
    }
}
?>