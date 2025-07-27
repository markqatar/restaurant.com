<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/process_language.php';

// Imposta il titolo della pagina
$page_title = TranslationManager::t('user.permissions') . ' - ' . TranslationManager::t('btn.add_new') . ' - Restaurant Admin';

// Avvia sessione se non esiste
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Token CSRF
if (!function_exists('get_csrf_token')) {
    function get_csrf_token() {
        return generate_csrf_token();
    }
}

require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-shield-alt me-2"></i><?php echo TranslationManager::t('user.permissions'); ?> - <?php echo TranslationManager::t('btn.add_new'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions'; ?>" 
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back_to_list'); ?>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('user.permissions'); ?> - <?php echo TranslationManager::t('msg.info'); ?></h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo get_setting('site_url', 'http://localhost'); ?>/admin/access-management/permissions/store" id="permissionForm">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label"><?php echo TranslationManager::t('name'); ?> *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="resource" class="form-label"><?php echo TranslationManager::t('resource'); ?> *</label>
                                <select class="form-control" id="resource" name="resource" required>
                                    <option value=""><?php echo TranslationManager::t('form.select_option'); ?></option>
                                    <?php
                                    // Legge i moduli installati
                                    $modulesPath = get_setting('base_path', '/var/www/html') . 'admin/modules/';
                                    if (is_dir($modulesPath)) {
                                        $modules = array_filter(scandir($modulesPath), function ($item) use ($modulesPath) {
                                            return $item !== '.' && $item !== '..' && is_dir($modulesPath . $item);
                                        });
                                        foreach ($modules as $module) {
                                            echo '<option value="' . htmlspecialchars($module) . '">' . htmlspecialchars($module) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="action" class="form-label"><?php echo TranslationManager::t('action'); ?> *</label>
                                <select class="form-control" id="action" name="action" required>
                                    <option value=""><?php echo TranslationManager::t('form.select_option'); ?></option>
                                    <option value="view"><?php echo TranslationManager::t('view'); ?></option>
                                    <option value="create"><?php echo TranslationManager::t('create'); ?></option>
                                    <option value="edit"><?php echo TranslationManager::t('edit'); ?></option>
                                    <option value="delete"><?php echo TranslationManager::t('delete'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/permissions'; ?>" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i><?php echo TranslationManager::t('cancel'); ?>
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo TranslationManager::t('create'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('msg.info'); ?></h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-1"></i><?php echo TranslationManager::t('msg.info'); ?></h6>
                    <ul class="mb-0 small">
                        <li><?php echo TranslationManager::t('required_field'); ?> (*)</li>
                        <li><?php echo TranslationManager::t('name'); ?> <?php echo TranslationManager::t('form.required'); ?></li>
                        <li><?php echo TranslationManager::t('resource'); ?> <?php echo TranslationManager::t('form.required'); ?></li>
                        <li><?php echo TranslationManager::t('action'); ?> <?php echo TranslationManager::t('form.required'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('permissionForm').addEventListener('submit', function(e) {
    if (!validateForm('permissionForm')) {
        e.preventDefault();
        Swal.fire({
            title: '<?php echo TranslationManager::t('msg.error'); ?>',
            text: '<?php echo TranslationManager::t('required_field'); ?>',
            icon: 'error'
        });
    }
});
</script>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>