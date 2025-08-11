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
        TranslationManager::loadModuleTranslations('blog');
        $categories = $this->category->getAllCategories();
        return ['categories' => $categories];
    }
    
    public function create() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'categories', 'create')) {
            header('Location: /admin/categories.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        $languages = get_available_languages_from_db('public');
        $activeLanguages = array_filter($languages, function($l){ return (int)$l['is_active_public'] === 1; });
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $translationsInput = $_POST['translations'] ?? [];
            $defaultLang = get_default_public_language_from_db();
            $defaultName = trim($translationsInput[$defaultLang]['name'] ?? '');
            if ($defaultName === '') {
                return [
                    'parentCategories' => $this->category->getAllCategories(),
                    'languages' => $languages,
                    'activeLanguages' => $activeLanguages,
                    'error' => TranslationManager::t('category_name_required')
                ];
            }
            // build translations array
            $translations = [];
            foreach ($translationsInput as $lang => $vals) {
                $name = trim($vals['name'] ?? '');
                if ($name === '') continue;
                $slug = $vals['slug'] ?? $this->category->generateSlug($name, $lang);
                $translations[$lang] = [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $vals['description'] ?? null,
                    'meta_title' => $vals['meta_title'] ?? null,
                    'meta_description' => $vals['meta_description'] ?? null,
                ];
            }
            $baseData = [
                'parent_id' => !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null,
                'sort_order' => intval($_POST['sort_order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            $newId = $this->category->createCategory($baseData, $translations);
            if ($newId) {
                header('Location: /admin/categories.php?success=' . urlencode(TranslationManager::t('category_created')));
                exit;
            }
            return [
                'parentCategories' => $this->category->getAllCategories(),
                'languages' => $languages,
                'activeLanguages' => $activeLanguages,
                'error' => TranslationManager::t('error_occurred')
            ];
        }
        return [
            'parentCategories' => $this->category->getAllCategories(),
            'languages' => $languages,
            'activeLanguages' => $activeLanguages
        ];
    }
    
    public function edit($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'categories', 'edit')) {
            header('Location: /admin/categories.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        $languages = get_available_languages_from_db('public');
        $activeLanguages = array_filter($languages, function($l){ return (int)$l['is_active_public'] === 1; });
        $category = $this->category->getCategoryById($id);
        if (!$category) {
            header('Location: /admin/categories.php?error=' . urlencode(TranslationManager::t('category_not_found')));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $translationsInput = $_POST['translations'] ?? [];
            $defaultLang = get_default_public_language_from_db();
            $defaultName = trim($translationsInput[$defaultLang]['name'] ?? '');
            if ($defaultName === '') {
                return [
                    'category' => $category,
                    'parentCategories' => $this->category->getAllCategories(),
                    'languages' => $languages,
                    'activeLanguages' => $activeLanguages,
                    'error' => TranslationManager::t('category_name_required')
                ];
            }
            $translations = [];
            foreach ($translationsInput as $lang => $vals) {
                $name = trim($vals['name'] ?? '');
                if ($name === '') continue;
                $slug = $vals['slug'] ?? $this->category->generateSlug($name, $lang, $id);
                $translations[$lang] = [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $vals['description'] ?? null,
                    'meta_title' => $vals['meta_title'] ?? null,
                    'meta_description' => $vals['meta_description'] ?? null,
                ];
            }
            $baseData = [
                'parent_id' => !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null,
                'sort_order' => intval($_POST['sort_order'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            if ($this->category->updateCategory($id, $baseData, $translations)) {
                header('Location: /admin/categories.php?success=' . urlencode(TranslationManager::t('category_updated')));
                exit;
            }
            return [
                'category' => $category,
                'parentCategories' => $this->category->getAllCategories(),
                'languages' => $languages,
                'activeLanguages' => $activeLanguages,
                'error' => TranslationManager::t('error_occurred')
            ];
        }
        return [
            'category' => $category,
            'parentCategories' => $this->category->getAllCategories(),
            'languages' => $languages,
            'activeLanguages' => $activeLanguages
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