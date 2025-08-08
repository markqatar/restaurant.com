<?php
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../includes/functions.php';

class CategoryController {
    private $category;
    
    public function __construct($pdo) {
        $this->category = new Category($pdo);
    }
    
    public function index() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'categories', 'view')) {
            header('Location: /admin/dashboard.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        $categories = $this->category->getAllCategories();
        
        return [
            'categories' => $categories
        ];
    }
    
    public function create() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'categories', 'create')) {
            header('Location: /admin/categories.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            
            if (empty($name)) {
                return [
                    'parentCategories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('Category name is required')
                ];
            }
            
            $slug = $this->category->generateSlug($name);
            
            $data = [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'parent_id' => $parent_id,
                'sort_order' => $sort_order
            ];
            
            if ($this->category->createCategory($data)) {
                header('Location: /admin/categories.php?success=' . urlencode(TranslationManager::t('category_created')));
                exit;
            } else {
                return [
                    'parentCategories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('error_occurred')
                ];
            }
        }
        
        $parentCategories = $this->category->getAllCategories();
        return ['parentCategories' => $parentCategories];
    }
    
    public function edit($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'categories', 'edit')) {
            header('Location: /admin/categories.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        $category = $this->category->getCategoryById($id);
        
        if (!$category) {
            header('Location: /admin/categories.php?error=' . urlencode(TranslationManager::t('category_not_found')));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            
            if (empty($name)) {
                return [
                    'category' => $category,
                    'parentCategories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('Category name is required')
                ];
            }
            
            $slug = $this->category->generateSlug($name, $id);
            
            $data = [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'parent_id' => $parent_id,
                'sort_order' => $sort_order
            ];
            
            if ($this->category->updateCategory($id, $data)) {
                header('Location: /admin/categories.php?success=' . urlencode(TranslationManager::t('category_updated')));
                exit;
            } else {
                return [
                    'category' => $category,
                    'parentCategories' => $this->category->getAllCategories(),
                    'error' => TranslationManager::t('error_occurred')
                ];
            }
        }
        
        $parentCategories = $this->category->getAllCategories();
        return [
            'category' => $category,
            'parentCategories' => $parentCategories
        ];
    }
    
    public function delete($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'categories', 'delete')) {
            header('Location: /admin/categories.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        
        if ($this->category->deleteCategory($id)) {
            header('Location: /admin/categories.php?success=' . urlencode(TranslationManager::t('category_deleted')));
        } else {
            header('Location: /admin/categories.php?error=' . urlencode(TranslationManager::t('error_occurred')));
        }
        exit;
    }
}
?>