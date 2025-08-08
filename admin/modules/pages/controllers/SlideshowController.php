<?php
require_once __DIR__ . '/../models/Slideshow.php';
require_once __DIR__ . '/../includes/functions.php';

class SlideshowController {
    private $slideshow;
    
    public function __construct($pdo) {
        $this->slideshow = new Slideshow($pdo);
    }
    
    public function index() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'slideshows', 'view')) {
            header('Location: /admin/dashboard.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        $slides = $this->slideshow->getAllSlides();
        $pages = $this->slideshow->getPages();
        $articles = $this->slideshow->getArticles();
        
        return [
            'slides' => $slides,
            'pages' => $pages,
            'articles' => $articles
        ];
    }
    
    public function create() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'slideshows', 'create')) {
            header('Location: /admin/slideshows.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $image = trim($_POST['image'] ?? '');
            $caption = trim($_POST['caption'] ?? '');
            $link_url = trim($_POST['link_url'] ?? '');
            $link_text = trim($_POST['link_text'] ?? '');
            $page_id = !empty($_POST['page_id']) ? intval($_POST['page_id']) : null;
            $article_id = !empty($_POST['article_id']) ? intval($_POST['article_id']) : null;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            
            if (empty($title)) {
                return [
                    'pages' => $this->slideshow->getPages(),
                    'articles' => $this->slideshow->getArticles(),
                    'error' => TranslationManager::t('title_required')
                ];
            }
            
            if (empty($image)) {
                return [
                    'pages' => $this->slideshow->getPages(),
                    'articles' => $this->slideshow->getArticles(),
                    'error' => TranslationManager::t('image_required')
                ];
            }
            
            $data = [
                'title' => $title,
                'image' => $image,
                'caption' => $caption,
                'link_url' => $link_url,
                'link_text' => $link_text,
                'page_id' => $page_id,
                'article_id' => $article_id,
                'sort_order' => $sort_order,
                'status' => $status
            ];
            
            if ($this->slideshow->createSlide($data)) {
                header('Location: /admin/slideshows.php?success=' . urlencode(TranslationManager::t('slide_created')));
                exit;
            } else {
                return [
                    'pages' => $this->slideshow->getPages(),
                    'articles' => $this->slideshow->getArticles(),
                    'error' => TranslationManager::t('error_occurred')
                ];
            }
        }
        
        $pages = $this->slideshow->getPages();
        $articles = $this->slideshow->getArticles();
        return [
            'pages' => $pages,
            'articles' => $articles
        ];
    }
    
    public function edit($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'slideshows', 'edit')) {
            header('Location: /admin/slideshows.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        $slide = $this->slideshow->getSlideById($id);
        
        if (!$slide) {
            header('Location: /admin/slideshows.php?error=' . urlencode(TranslationManager::t('slide_not_found')));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $image = trim($_POST['image'] ?? '');
            $caption = trim($_POST['caption'] ?? '');
            $link_url = trim($_POST['link_url'] ?? '');
            $link_text = trim($_POST['link_text'] ?? '');
            $page_id = !empty($_POST['page_id']) ? intval($_POST['page_id']) : null;
            $article_id = !empty($_POST['article_id']) ? intval($_POST['article_id']) : null;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            
            if (empty($title)) {
                return [
                    'slide' => $slide,
                    'pages' => $this->slideshow->getPages(),
                    'articles' => $this->slideshow->getArticles(),
                    'error' => TranslationManager::t('title_required')
                ];
            }
            
            if (empty($image)) {
                return [
                    'slide' => $slide,
                    'pages' => $this->slideshow->getPages(),
                    'articles' => $this->slideshow->getArticles(),
                    'error' => TranslationManager::t('image_required')
                ];
            }
            
            $data = [
                'title' => $title,
                'image' => $image,
                'caption' => $caption,
                'link_url' => $link_url,
                'link_text' => $link_text,
                'page_id' => $page_id,
                'article_id' => $article_id,
                'sort_order' => $sort_order,
                'status' => $status
            ];
            
            if ($this->slideshow->updateSlide($id, $data)) {
                header('Location: /admin/slideshows.php?success=' . urlencode(TranslationManager::t('slide_updated')));
                exit;
            } else {
                return [
                    'slide' => $slide,
                    'pages' => $this->slideshow->getPages(),
                    'articles' => $this->slideshow->getArticles(),
                    'error' => TranslationManager::t('error_occurred')
                ];
            }
        }
        
        $pages = $this->slideshow->getPages();
        $articles = $this->slideshow->getArticles();
        return [
            'slide' => $slide,
            'pages' => $pages,
            'articles' => $articles
        ];
    }
    
    public function delete($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'slideshows', 'delete')) {
            header('Location: /admin/slideshows.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        if ($this->slideshow->deleteSlide($id)) {
            header('Location: /admin/slideshows.php?success=' . urlencode(TranslationManager::t('slide_deleted')));
        } else {
            header('Location: /admin/slideshows.php?error=' . urlencode(TranslationManager::t('error_occurred')));
        }
        exit;
    }
}
?>