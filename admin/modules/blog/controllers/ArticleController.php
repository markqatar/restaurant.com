<?php
require_once __DIR__ . '/../models/Article.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../includes/functions.php';

class ArticleController {
    private $article;
    private $category;
    
    public function __construct($pdo) {
        $this->article = new Article($pdo);
        $this->category = new Category($pdo);
    }
    
    public function index() {
        // Check permissions
        if (!can('articles', 'view')) {
            header('Location: /admin/dashboard.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        $articles = $this->article->getAllArticles();
        $categories = $this->category->getAllCategories();
        
        return [
            'articles' => $articles,
            'categories' => $categories
        ];
    }
    
    public function create() {
        // Check permissions
    if (!can('articles', 'create')) {
            header('Location: /admin/articles.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';
            $excerpt = trim($_POST['excerpt'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $featured_image = $_POST['featured_image'] ?? null;
            $meta_title = trim($_POST['meta_title'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');
            $meta_keywords = trim($_POST['meta_keywords'] ?? '');
            $published_at = $_POST['published_at'] ?? null;
            
            if (empty($title)) {
                return [
                    'categories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('title_required')
                ];
            }
            
            if (empty($content)) {
                return [
                    'categories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('content_required')
                ];
            }
            
            $slug = $this->article->generateSlug($title);
            
            $data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'status' => $status,
                'category_id' => $category_id,
                'author_id' => $_SESSION['user_id'],
                'is_featured' => $is_featured,
                'featured_image' => $featured_image,
                'meta_title' => $meta_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords,
                'published_at' => $published_at
            ];
            
            if ($this->article->createArticle($data)) {
                header('Location: /admin/articles.php?success=' . urlencode(TranslationManager::t('article_created')));
                exit;
            } else {
                return [
                    'categories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('error_occurred')
                ];
            }
        }
        
        $categories = $this->category->getAllCategories();
        return ['categories' => $categories];
    }
    
    public function edit($id) {
        // Check permissions
    if (!can('articles', 'edit')) {
            header('Location: /admin/articles.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        $article = $this->article->getArticleById($id);
        
        if (!$article) {
            header('Location: /admin/articles.php?error=' . urlencode(TranslationManager::t('article_not_found')));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';
            $excerpt = trim($_POST['excerpt'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $featured_image = $_POST['featured_image'] ?? null;
            $meta_title = trim($_POST['meta_title'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');
            $meta_keywords = trim($_POST['meta_keywords'] ?? '');
            $published_at = $_POST['published_at'] ?? null;
            
            if (empty($title)) {
                return [
                    'article' => $article,
                    'categories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('title_required')
                ];
            }
            
            if (empty($content)) {
                return [
                    'article' => $article,
                    'categories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('content_required')
                ];
            }
            
            $slug = $this->article->generateSlug($title, $id);
            
            $data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'status' => $status,
                'category_id' => $category_id,
                'is_featured' => $is_featured,
                'featured_image' => $featured_image,
                'meta_title' => $meta_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords,
                'published_at' => $published_at
            ];
            
            if ($this->article->updateArticle($id, $data)) {
                header('Location: /admin/articles.php?success=' . urlencode(TranslationManager::t('article_updated')));
                exit;
            } else {
                return [
                    'article' => $article,
                    'categories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('error_occurred')
                ];
            }
        }
        
        $categories = $this->category->getAllCategories();
        return [
            'article' => $article,
            'categories' => $categories
        ];
    }
    
    public function delete($id) {
        // Check permissions
    if (!can('articles', 'delete')) {
            header('Location: /admin/articles.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        if ($this->article->deleteArticle($id)) {
            header('Location: /admin/articles.php?success=' . urlencode(TranslationManager::t('article_deleted')));
        } else {
            header('Location: /admin/articles.php?error=' . urlencode(TranslationManager::t('error_occurred')));
        }
        exit;
    }
}
?>