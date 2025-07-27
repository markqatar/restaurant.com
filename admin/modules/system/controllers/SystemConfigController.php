<?php
require_once admin_module_path('/models/SystemConfig.php');

class SystemConfigController
{
    private $config_model;

    public function __construct()
    {
        $this->config_model = new SystemConfig();
        TranslationManager::init();
        TranslationManager::loadModuleTranslations('system');
    }

    public function index()
    {
        if (!has_permission($_SESSION['user_id'], 'system', 'view')) {
            redirect(get_setting('site_url') . '/admin/unauthorized.php');
        }

        $settings = $this->config_model->getAllSettings();
        $languages = get_available_languages_from_db('admin'); // Carica lingue dal DB
        $page_title = TranslationManager::t('system.configuration');

        load_admin_view('edit_settings', [
            'page_title' => $page_title,
            'settings' => $settings,
            'languages' => $languages
        ], 'system');
    }

    public function update()
    {
        if (!has_permission($_SESSION['user_id'], 'system', 'update')) {
            redirect(get_setting('site_url') . '/admin/unauthorized.php');
        }

        if ($_POST) {
            if (!verify_csrf_token($_POST['csrf_token'])) {
                send_notification(TranslationManager::t('system.error.invalid_csrf'), 'danger');
                redirect(get_setting('site_url') . '/admin/system/SystemConfig');
            }

            $data = [
                'site_name' => sanitize_input($_POST['site_name']),
                'site_url' => sanitize_input($_POST['site_url']),
                'currency' => sanitize_input($_POST['currency']),
                'timezone' => sanitize_input($_POST['timezone']),
                'date_format_admin' => sanitize_input($_POST['date_format_admin']),
                'date_format_public' => sanitize_input($_POST['date_format_public']),
                'website_enabled' => isset($_POST['website_enabled']) ? 1 : 0
            ];

            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $logo_path = upload_file($_FILES['logo'], get_setting('base_path') . '/public/assets/images/logo/');
                if ($logo_path) {
                    $data['logo_path'] = $logo_path;
                }
            }

            if ($this->config_model->updateSettings($data)) {
                send_notification(TranslationManager::t('system.success.settings_updated'), 'success');
            } else {
                send_notification(TranslationManager::t('system.error.settings_update_failed'), 'danger');
            }

            redirect(get_setting('site_url') . '/admin/system/SystemConfig');
        }
    }

    public function updateLanguages()
    {
        if (!verify_csrf_token($_POST['csrf_token'])) {
            send_notification(TranslationManager::t('system.error.invalid_csrf'), 'danger');
            redirect(get_setting('site_url') . '/admin/system/SystemConfig');
        }

        $active_admin = $_POST['active_admin'] ?? [];
        $active_public = $_POST['active_public'] ?? [];

        $db = Database::getInstance()->getConnection();

        $db->exec("UPDATE languages SET is_active_admin = 0, is_active_public = 0");

        $stmt = $db->prepare("UPDATE languages SET is_active_admin = 1 WHERE code = ?");
        foreach ($active_admin as $code) {
            $stmt->execute([$code]);
        }

        $stmt = $db->prepare("UPDATE languages SET is_active_public = 1 WHERE code = ?");
        foreach ($active_public as $code) {
            $stmt->execute([$code]);
        }

        send_notification(TranslationManager::t('system.success.settings_updated'), 'success');
        redirect(get_setting('site_url') . '/admin/system/SystemConfig');
    }

    public function addLanguage()
    {
        if (!verify_csrf_token($_POST['csrf_token'])) {
            send_notification(TranslationManager::t('system.error.invalid_csrf'), 'danger');
            redirect(get_setting('site_url') . '/admin/system/SystemConfig');
        }

        $code = sanitize_input($_POST['code']);
        $name = sanitize_input($_POST['name']);
        $direction = $_POST['direction'] === 'RTL' ? 'RTL' : 'LTR';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO languages (code, name, direction) VALUES (?, ?, ?)");

        if ($stmt->execute([$code, $name, $direction])) {
            send_notification(TranslationManager::t('system.success.language_added'), 'success');
        } else {
            send_notification(TranslationManager::t('system.error.language_add_failed'), 'danger');
        }

        redirect(get_setting('site_url') . '/admin/system/SystemConfig');
    }

    public function deleteLanguage($code)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM languages WHERE code = ?");
        if ($stmt->execute([$code])) {
            send_notification(TranslationManager::t('system.success.language_deleted'), 'success');
        } else {
            send_notification(TranslationManager::t('system.error.language_delete_failed'), 'danger');
        }
        redirect(get_setting('site_url') . '/admin/system/SystemConfig');
    }
}