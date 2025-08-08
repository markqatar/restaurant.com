<?php
// Controllo autenticazione e permessi
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'delivery_areas', 'create')) {
    header('Location: ' . admin_url('login'));
    exit;
}


require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-map-marker-alt me-2"></i><?php echo TranslationManager::t('add_delivery_area'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/deliveryarea'; ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back_to_list'); ?>
        </a>
    </div>
</div>

<!-- Messaggio di errore -->
<?php if (isset($data['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($data['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Form aggiunta Delivery Area -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('add_delivery_area'); ?></h6>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/deliveryarea/store'; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            <div class="row">
                <!-- Area Name -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="area_name" class="form-label">
                            <?php echo TranslationManager::t('area_name'); ?> *
                        </label>
                        <input type="text"
                               class="form-control"
                               id="area_name"
                               name="area_name"
                               value="<?php echo htmlspecialchars($_POST['area_name'] ?? ''); ?>"
                               required maxlength="255">
                    </div>
                </div>
                <!-- Branch -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="shop_id" class="form-label">
                            <?php echo TranslationManager::t('branch'); ?> *
                        </label>
                        <select class="form-select" id="shop_id" name="shop_id" required>
                            <option value=""><?php echo TranslationManager::t('select_branch'); ?></option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?php echo $branch['id']; ?>"
                                    <?php echo (isset($_POST['shop_id']) && $_POST['shop_id'] == $branch['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($branch['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/deliveryarea'; ?>" class="btn btn-secondary me-2">
                    <?php echo TranslationManager::t('cancel'); ?>
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i><?php echo TranslationManager::t('save'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>