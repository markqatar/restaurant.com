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
        $categories = $this->category->getAllCategories();
        // If AJAX datatable request
        if(isset($_GET['datatable'])){
            $draw = (int)($_GET['draw'] ?? 1);
            $start = (int)($_GET['start'] ?? 0);
            $length = (int)($_GET['length'] ?? 25);
            $search = trim($_GET['search']['value'] ?? '');
            $statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';
            $categoryFilter = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
            $res = $this->article->datatable($start,$length,$search,$statusFilter,$categoryFilter);
            // Add permission flags per row
            foreach($res['rows'] as &$r){
                $r['can_edit'] = can('articles','edit');
                $r['can_delete'] = can('articles','delete');
            }
            header('Content-Type: application/json');
            echo json_encode([
                'draw'=>$draw,
                'recordsTotal'=>$res['total'],
                'recordsFiltered'=>$res['filtered'],
                'data'=>$res['rows']
            ]);
            exit;
        }
        return [ 'categories'=>$categories ];
    }

    // Return revisions JSON
    public function revisions($articleId){
        if(!can('articles','edit')){ http_response_code(403); echo json_encode(['error'=>'forbidden']); return; }
        header('Content-Type: application/json');
        echo json_encode($this->article->listRevisions((int)$articleId));
    }

    // Restore a revision
    public function restoreRevision($revisionId){
        if(!can('articles','edit')){ http_response_code(403); echo json_encode(['error'=>'forbidden']); return; }
        $ok=$this->article->restoreRevision((int)$revisionId);
        header('Content-Type: application/json');
        echo json_encode(['success'=>$ok]);
    }

    // Slug availability check (per language)
    public function checkSlug(){
        if(!can('articles','edit') && !can('articles','create')){ http_response_code(403); echo json_encode(['error'=>'forbidden']); return; }
        $slug = trim($_GET['slug'] ?? '');
        $lang = trim($_GET['lang'] ?? get_default_public_language_from_db());
        $exclude = isset($_GET['exclude_id']) ? (int)$_GET['exclude_id'] : null;
        $available = $this->article->isTranslationSlugAvailable($slug,$lang,$exclude);
        header('Content-Type: application/json');
        echo json_encode(['slug'=>$slug,'language'=>$lang,'available'=>$available]);
    }
    
    public function create() {
        // Check permissions
    if (!can('articles', 'create')) {
            header('Location: /admin/articles.php?error=' . urlencode(TranslationManager::t('no_permission')));
            exit;
        }
        $languages = get_available_languages_from_db('public');
        $activeLanguages = array_filter($languages,function($l){return (int)$l['is_active_public']===1;});
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $translationsInput = $_POST['translations'] ?? [];
            $defaultLang = get_default_public_language_from_db();
            $defaultTitle = trim($translationsInput[$defaultLang]['title'] ?? '');
            if ($defaultTitle==='') {
                return [
                    'categories' => $this->category->getAllCategories(),
                    'languages'=>$languages,
                    'activeLanguages'=>$activeLanguages,
                    'error' => TranslationManager::t('title_required')
                ];
            }
            $status = $_POST['status'] ?? 'draft';
            $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $featured_image = $_POST['featured_image'] ?? null;
            $published_at = $_POST['published_at'] ?? null;
            $tagsRaw = trim($_POST['tags'] ?? '');
            $tags = $tagsRaw!=='' ? array_map('trim', explode(',', $tagsRaw)) : [];
            // Build default translation slug
            $defaultSlug = $translationsInput[$defaultLang]['slug'] ?? $this->article->generateSlug($defaultTitle);
            $baseData = [
                'title' => $defaultTitle,
                'slug' => $defaultSlug,
                'content' => $translationsInput[$defaultLang]['content'] ?? '',
                'excerpt' => $translationsInput[$defaultLang]['excerpt'] ?? '',
                'status' => $status,
                'category_id' => $category_id,
                'author_id' => $_SESSION['user_id'],
                'is_featured' => $is_featured,
                'featured_image' => $featured_image,
                'meta_title' => $translationsInput[$defaultLang]['meta_title'] ?? '',
                'meta_description' => $translationsInput[$defaultLang]['meta_description'] ?? '',
                'meta_keywords' => $translationsInput[$defaultLang]['meta_keywords'] ?? '',
                'published_at' => $published_at,
                'tags' => $tags,
                'language' => $defaultLang
            ];
            $articleId = $this->article->createArticle($baseData);
            if ($articleId) {
                // Save additional translations
                foreach ($translationsInput as $lang=>$vals) {
                    if ($lang==$defaultLang) continue;
                    $title = trim($vals['title'] ?? '');
                    if ($title==='') continue;
                    $slug = $vals['slug'] ?? $this->article->generateSlug($title,$articleId);
                    $this->article->updateArticle($articleId,[
                        'title'=>$baseData['title'], // keep base
                        'slug'=>$baseData['slug'],
                        'content'=>$baseData['content'],
                        'excerpt'=>$baseData['excerpt'],
                        'status'=>$status,
                        'category_id'=>$category_id,
                        'is_featured'=>$is_featured,
                        'featured_image'=>$featured_image,
                        'meta_title'=>$baseData['meta_title'],
                        'meta_description'=>$baseData['meta_description'],
                        'meta_keywords'=>$baseData['meta_keywords'],
                        'published_at'=>$published_at,
                        'language'=>$lang,
                        'tags'=>$tags,
                        // translation-specific fields
                        'title_override'=>$title,
                        'slug_override'=>$slug,
                        'content_override'=>$vals['content'] ?? null,
                        'excerpt_override'=>$vals['excerpt'] ?? null,
                        'meta_title_override'=>$vals['meta_title'] ?? null,
                        'meta_description_override'=>$vals['meta_description'] ?? null,
                        'meta_keywords_override'=>$vals['meta_keywords'] ?? null
                    ]); // updateArticle will upsert translation for provided language
                }
                header('Location: /admin/articles.php?success=' . urlencode(TranslationManager::t('article_created')));
                exit;
            }
            return [
                'categories' => $this->category->getAllCategories(),
                'languages'=>$languages,
                'activeLanguages'=>$activeLanguages,
                'error' => TranslationManager::t('error_occurred')
            ];
        }
        return [
            'categories' => $this->category->getAllCategories(),
            'languages'=>$languages,
            'activeLanguages'=>$activeLanguages
        ];
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
        
        $languages = get_available_languages_from_db('public');
        $activeLanguages = array_filter($languages,function($l){return (int)$l['is_active_public']===1;});
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $translationsInput = $_POST['translations'] ?? [];
            $defaultLang = get_default_public_language_from_db();
            $defaultTitle = trim($translationsInput[$defaultLang]['title'] ?? '');
            if ($defaultTitle==='') {
                return [
                    'article'=>$article,
                    'categories'=>$this->category->getAllCategories(),
                    'languages'=>$languages,
                    'activeLanguages'=>$activeLanguages,
                    'error'=>TranslationManager::t('title_required')
                ];
            }
            $status = $_POST['status'] ?? 'draft';
            $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $featured_image = $_POST['featured_image'] ?? null;
            $published_at = $_POST['published_at'] ?? null;
            $tagsRaw = trim($_POST['tags'] ?? '');
            $tags = $tagsRaw!=='' ? array_map('trim', explode(',', $tagsRaw)) : [];
            $defaultSlug = $translationsInput[$defaultLang]['slug'] ?? $this->article->generateSlug($defaultTitle,$id);
            $baseData = [
                'title'=>$defaultTitle,
                'slug'=>$defaultSlug,
                'content'=>$translationsInput[$defaultLang]['content'] ?? '',
                'excerpt'=>$translationsInput[$defaultLang]['excerpt'] ?? '',
                'status'=>$status,
                'category_id'=>$category_id,
                'is_featured'=>$is_featured,
                'featured_image'=>$featured_image,
                'meta_title'=>$translationsInput[$defaultLang]['meta_title'] ?? '',
                'meta_description'=>$translationsInput[$defaultLang]['meta_description'] ?? '',
                'meta_keywords'=>$translationsInput[$defaultLang]['meta_keywords'] ?? '',
                'published_at'=>$published_at,
                'tags'=>$tags,
                'language'=>$defaultLang
            ];
            $this->article->updateArticle($id,$baseData);
            // Additional translations
            foreach($translationsInput as $lang=>$vals){
                if($lang==$defaultLang) continue; $title=trim($vals['title']??''); if($title==='') continue; $slug=$vals['slug'] ?? $this->article->generateSlug($title,$id); $this->article->updateArticle($id,[
                    'title'=>$baseData['title'],
                    'slug'=>$baseData['slug'],
                    'content'=>$baseData['content'],
                    'excerpt'=>$baseData['excerpt'],
                    'status'=>$status,
                    'category_id'=>$category_id,
                    'is_featured'=>$is_featured,
                    'featured_image'=>$featured_image,
                    'meta_title'=>$baseData['meta_title'],
                    'meta_description'=>$baseData['meta_description'],
                    'meta_keywords'=>$baseData['meta_keywords'],
                    'published_at'=>$published_at,
                    'tags'=>$tags,
                    'language'=>$lang,
                    'title_override'=>$title,
                    'slug_override'=>$slug,
                    'content_override'=>$vals['content'] ?? null,
                    'excerpt_override'=>$vals['excerpt'] ?? null,
                    'meta_title_override'=>$vals['meta_title'] ?? null,
                    'meta_description_override'=>$vals['meta_description'] ?? null,
                    'meta_keywords_override'=>$vals['meta_keywords'] ?? null
                ]); }
            header('Location: /admin/articles.php?success=' . urlencode(TranslationManager::t('article_updated')));
            exit;
        }
        return [
            'article'=>$article,
            'categories'=>$this->category->getAllCategories(),
            'languages'=>$languages,
            'activeLanguages'=>$activeLanguages
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