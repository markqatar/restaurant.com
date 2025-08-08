<?php
require_once __DIR__ . '/../models/Media.php';

class MediaController {
    private $media;
    
    public function __construct() {
        $this->media = new Media();
        TranslationManager::loadModuleTranslations('media');
    }
    
    public function index() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'media', 'view')) {
            header('Location: /admin/dashboard.php?error=' . urlencode(TranslationManager::t('no_permission')));
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
            echo json_encode(['success' => false, 'message' => TranslationManager::t('no_permission')]);
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
            echo json_encode(['success' => false, 'message' => TranslationManager::t('no_permission')]);
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
                echo json_encode(['success' => true, 'message' => TranslationManager::t('media_updated')]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => TranslationManager::t('error_occurred')]);
            }
            exit;
        }
    }
    
    public function delete($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'media', 'delete')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => TranslationManager::t('no_permission')]);
            exit;
        }
        
        if ($this->media->deleteMedia($id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => TranslationManager::t('media_deleted')]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => TranslationManager::t('error_occurred')]);
        }
        exit;
    }
    public function mediaSelector() {
        include __DIR__ . '/../views/media-library/media-selector.php';    
    }

    public function getMediaLibrary() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'media', 'view')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => TranslationManager::t('no_permission')]);
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