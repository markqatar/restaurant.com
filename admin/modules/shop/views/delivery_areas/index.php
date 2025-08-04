<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-map-marker-alt me-2"></i><?php echo TranslationManager::t('delivery_areas'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <?php if (has_permission($_SESSION['user_id'], 'delivery_areas', 'create')): ?>
            <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/deliveryarea/create'; ?>" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i><?php echo TranslationManager::t('add_delivery_area'); ?>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()" title="<?php echo TranslationManager::t('btn.refresh'); ?>">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Messaggi di successo o errore -->
<?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_GET['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_GET['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Tabella Delivery Areas -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('delivery_areas'); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" id="deliveryAreasTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php echo TranslationManager::t('id'); ?></th>
                        <th><?php echo TranslationManager::t('area_name'); ?></th>
                        <th><?php echo TranslationManager::t('branch'); ?></th>
                        <th><?php echo TranslationManager::t('created'); ?></th>
                        <th><?php echo TranslationManager::t('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($delivery_areas)): ?>
                        <?php foreach ($delivery_areas as $area): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($area['id']); ?></td>
                                <td><?php echo htmlspecialchars($area['area_name']); ?></td>
                                <td><?php echo htmlspecialchars($area['name'] ?? TranslationManager::t('no_branch')); ?></td>
                                <td><?php echo format_date($area['created_at'], 'd/m/Y H:i'); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if (has_permission($_SESSION['user_id'], 'delivery_areas', 'update')): ?>
                                            <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/deliveryarea/edit/' . $area['id']; ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               title="<?php echo TranslationManager::t('edit'); ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (has_permission($_SESSION['user_id'], 'delivery_areas', 'delete')): ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger ms-1"
                                                    onclick="confirmDelete(<?php echo $area['id']; ?>, '<?php echo htmlspecialchars(addslashes($area['area_name'])); ?>')"
                                                    title="<?php echo TranslationManager::t('delete'); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted"><?php echo TranslationManager::t('no_data_available'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const DELIVERY_AREAS_VARS = {
        urls: {
            deleteBase: '<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/deliveryarea/delete/'; ?>',
        },
        translations: {
            confirmDeleteTitle: '<?php echo TranslationManager::t("confirm_delete"); ?>',
            confirmDeleteText: '<?php echo TranslationManager::t("delete_delivery_area_confirm"); ?>',
            yesDelete: '<?php echo TranslationManager::t("yes_delete"); ?>',
            cancel: '<?php echo TranslationManager::t("cancel"); ?>'
        }
    };
</script>
<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/shop/views/delivery_areas/js/index.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>