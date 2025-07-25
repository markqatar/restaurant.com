<?php
// Process language changes first to prevent header issues
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/process_language.php';

// Set page title before including header
$page_title = TranslationManager::t('user.permissions') . ' - ' . TranslationManager::t('btn.add_new') . ' - Restaurant Admin';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get CSRF token (if not defined in functions.php)
if (!function_exists('get_csrf_token')) {
    function get_csrf_token() {
        return generate_csrf_token();
    }
}

require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-shield-alt me-2"></i><?php echo TranslationManager::t('user.permissions'); ?> - <?php echo TranslationManager::t('btn.add_new'); ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
                    <a href="permissions.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back_to_list'); ?>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('user.permissions'); ?> - <?php echo TranslationManager::t('msg.info'); ?></h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="permissions.php?action=store" id="permissionForm">
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
                                                <option value="users"><?php echo TranslationManager::t('users'); ?></option>
                                                <option value="branches"><?php echo TranslationManager::t('branches'); ?></option>
                                                <option value="suppliers"><?php echo TranslationManager::t('suppliers'); ?></option>
                                                <option value="products"><?php echo TranslationManager::t('products'); ?></option>
                                                <option value="orders"><?php echo TranslationManager::t('orders'); ?></option>
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="description" class="form-label"><?php echo TranslationManager::t('description'); ?></label>
                                            <input type="text" class="form-control" id="description" name="description">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                        <i class="fas fa-times me-1"></i><?php echo TranslationManager::t('cancel'); ?>
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i><?php echo TranslationManager::t('create'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
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

        </main>
    </div>
</div>

<script>
// Form validation
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