<?php
require_once __DIR__ . '/../models/Page.php';
require_once __DIR__ . '/../includes/functions.php';

class PageController {
    private $page;
    
    public function __construct($pdo) {
        $this->page = new Page($pdo);
    }
    
    public function index() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'pages', 'view')) {
            header('Location: /admin/dashboard.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        $pages = $this->page->getAllPages();
        
        return [
            'pages' => $pages
        ];
    }
    
    public function create() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'pages', 'create')) {
            header('Location: /admin/pages.php?error=' . urlencode(translate('no_permission')));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';
            $excerpt = trim($_POST['excerpt'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            $template = $_POST['template'] ?? 'default';
            $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            $featured_image = $_POST['featured_image'] ?? null;
            $meta_title = trim($_POST['meta_title'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');
            $meta_keywords = trim($_POST['meta_keywords'] ?? '');
            
            if (empty($title)) {
                return ['error' => translate('title_required')];
            }
            
            $slug = $this->page->generateSlug($title);
            
            $data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'status' => $status,
                'template' => $template,
                'parent_id' => $parent_id,
                'sort_order' => $sort_order,
                'author_id' => $_SESSION['user_id'],
                'featured_image' => $featured_image,
                'meta_title' => $meta_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords
            ];
            
            if ($this->page->createPage($data)) {
                header('Location: /admin/pages.php?success=' . urlencode(translate('page_created')));
                exit;
            } else {
                return ['error' => translate('error_occurred')];
            }
        }
        
        $parentPages = $this->page->getAllPages('published');
        return ['parentPages' => $parentPages];
    }
    
    public function edit($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'pages', 'edit')) {
            header('Location: /admin/pages.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        $page = $this->page->getPageById($id);
        
        if (!$page) {
            header('Location: /admin/pages.php?error=' . urlencode(translate('page_not_found')));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';
            $excerpt = trim($_POST['excerpt'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            $template = $_POST['template'] ?? 'default';
            $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            $featured_image = $_POST['featured_image'] ?? null;
            $meta_title = trim($_POST['meta_title'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');
            $meta_keywords = trim($_POST['meta_keywords'] ?? '');
            
            if (empty($title)) {
                return [
                    'page' => $page,
                    'parentPages' => $this->page->getAllPages('published'),
                    'error' => translate('title_required')
                ];
            }
            
            $slug = $this->page->generateSlug($title, $id);
            
            $data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'status' => $status,
                'template' => $template,
                'parent_id' => $parent_id,
                'sort_order' => $sort_order,
                'featured_image' => $featured_image,
                'meta_title' => $meta_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords
            ];
            
            if ($this->page->updatePage($id, $data)) {
                header('Location: /admin/pages.php?success=' . urlencode(translate('page_updated')));
                exit;
            } else {
                return [
                    'page' => $page,
                    'parentPages' => $this->page->getAllPages('published'),
                    'error' => translate('error_occurred')
                ];
            }
        }
        
        $parentPages = $this->page->getAllPages('published');
        return [
            'page' => $page,
            'parentPages' => $parentPages
        ];
    }
    
    public function delete($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'pages', 'delete')) {
            header('Location: /admin/pages.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        if ($this->page->deletePage($id)) {
            header('Location: /admin/pages.php?success=' . urlencode(translate('page_deleted')));
        } else {
            header('Location: /admin/pages.php?error=' . urlencode(translate('error_occurred')));
        }
        exit;
    }
}
?>