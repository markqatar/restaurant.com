<?php
require_once __DIR__ . '/../models/Page.php';

class PageController {
    private $page_model;

    public function __construct() {
        $this->page_model = new Page();
        TranslationManager::loadModuleTranslations('pages');
    }

    // List pages
    public function index() {
        if (!can('pages', 'view')) {
            send_notification('msg.no_permission', 'danger');
            redirect(get_setting('site_url','/admin') . '/admin/dashboard');
        }
        $pages = $this->page_model->getAll();
        $page_title = TranslationManager::t('page.management');
        include __DIR__ . '/../views/pages/index.php';
    }

    // Show create form
    public function create() {
    if (!can('pages', 'create')) {
            send_notification('msg.no_permission', 'danger');
            redirect(get_setting('site_url','/admin') . '/admin/pages');
        }
    $page_title = TranslationManager::t('page.add');
    $languages = get_available_languages_from_db('public');
    $activeLanguages = array_filter($languages, function($l){return (int)$l['is_active_public'] === 1;});
        include __DIR__ . '/../views/pages/create.php';
    }

    // Store page
    public function store() {
    if (!can('pages', 'create')) {
            send_notification('msg.no_permission', 'danger');
            redirect(get_setting('site_url','/admin') . '/admin/pages');
        }
        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('msg.invalid_token','danger');
                redirect(get_setting('site_url','/admin') . '/admin/pages/create');
            }
            $is_published = isset($_POST['is_published']) ? 1 : 0; // maintained naming
            $sort_order = (int)($_POST['sort_order'] ?? 0);

            $translationsInput = $_POST['translations'] ?? [];
            // Ensure default language title exists
            $defaultLang = get_default_public_language_from_db();
            $defaultTitle = trim($translationsInput[$defaultLang]['title'] ?? '');
            if ($defaultTitle === '') {
                send_notification('page.title_required','danger');
                redirect(get_setting('site_url','/admin') . '/admin/pages/create');
            }

            // Prepare translations array with slug generation
            $translations = [];
            foreach ($translationsInput as $lang => $vals) {
                $title = trim($vals['title'] ?? '');
                if ($title === '') continue; // skip empties
                $slug = $vals['slug'] ?? $this->page_model->generateSlug($title, $lang);
                $translations[$lang] = [
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $vals['content'] ?? null,
                    'meta_title' => $vals['meta_title'] ?? null,
                    'meta_description' => $vals['meta_description'] ?? null,
                ];
            }

            $baseData = [ 'is_published' => $is_published, 'sort_order' => $sort_order ];
            $pageId = $this->page_model->create($baseData, $translations);
            if ($pageId) {
                send_notification('page.saved_success','success');
                redirect(get_setting('site_url','/admin') . '/admin/pages');
            }
            send_notification('page.error_process','danger');
            redirect(get_setting('site_url','/admin') . '/admin/pages/create');
        }
    }

    // Edit form
    public function edit($id) {
    if (!can('pages', 'edit')) {
            send_notification('msg.no_permission', 'danger');
            redirect(get_setting('site_url','/admin') . '/admin/pages');
        }
    $page = $this->page_model->find($id);
        if (!$page) {
            send_notification('page.page_not_found','danger');
            redirect(get_setting('site_url','/admin') . '/admin/pages');
        }
    $page_title = TranslationManager::t('page.edit');
    $languages = get_available_languages_from_db('public');
    $activeLanguages = array_filter($languages, function($l){return (int)$l['is_active_public'] === 1;});
        include __DIR__ . '/../views/pages/edit.php';
    }

    // Update page
    public function update($id) {
    if (!can('pages', 'edit')) {
            send_notification('msg.no_permission', 'danger');
            redirect(get_setting('site_url','/admin') . '/admin/pages');
        }
        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification('msg.invalid_token','danger');
                redirect(get_setting('site_url','/admin') . '/admin/pages/edit/' . $id);
            }
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            $sort_order = (int)($_POST['sort_order'] ?? 0);
            $translationsInput = $_POST['translations'] ?? [];
            $defaultLang = get_default_public_language_from_db();
            $defaultTitle = trim($translationsInput[$defaultLang]['title'] ?? '');
            if ($defaultTitle === '') {
                send_notification('page.title_required','danger');
                redirect(get_setting('site_url','/admin') . '/admin/pages/edit/' . $id);
            }
            $translations = [];
            foreach ($translationsInput as $lang => $vals) {
                $title = trim($vals['title'] ?? '');
                if ($title === '') continue;
                // If slug present keep, else generate unique (exclude current page id)
                $slug = $vals['slug'] ?? $this->page_model->generateSlug($title, $lang, $id);
                $translations[$lang] = [
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $vals['content'] ?? null,
                    'meta_title' => $vals['meta_title'] ?? null,
                    'meta_description' => $vals['meta_description'] ?? null,
                ];
            }
            $baseData = [ 'is_published' => $is_published, 'sort_order' => $sort_order ];
            if ($this->page_model->update($id, $baseData, $translations)) {
                send_notification('page.updated_success','success');
                redirect(get_setting('site_url','/admin') . '/admin/pages');
            }
            send_notification('page.error_process','danger');
            redirect(get_setting('site_url','/admin') . '/admin/pages/edit/' . $id);
        }
    }

    // Delete
    public function delete($id) {
    if (!can('pages', 'delete')) {
            send_notification('msg.no_permission', 'danger');
            redirect(get_setting('site_url','/admin') . '/admin/pages');
        }
        if ($this->page_model->delete($id)) {
            send_notification('page.deleted_success','success');
        } else {
            send_notification('page.error_process','danger');
        }
        redirect(get_setting('site_url','/admin') . '/admin/pages');
    }
}
?>