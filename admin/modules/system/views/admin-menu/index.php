<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-bars me-2"></i><?php echo TranslationManager::t('menu.management'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <?php if (has_permission($_SESSION['user_id'], 'menu', 'create')): ?>
            <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/system/adminmenu/create'; ?>" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i><?php echo TranslationManager::t('add.new'); ?>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()" title="<?php echo TranslationManager::t('btn.refresh'); ?>">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Messaggi di notifica -->
<?php if (isset($notification)): ?>
    <div class="alert alert-<?php echo $notification['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($notification['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <?php echo TranslationManager::t('menu.total_items'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_items; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <?php echo TranslationManager::t('menu.active_items'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $active_items; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Items Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('menu.items'); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo TranslationManager::t('title'); ?></th>
                        <th><?php echo TranslationManager::t('parent'); ?></th>
                        <th><?php echo TranslationManager::t('url'); ?></th>
                        <th><?php echo TranslationManager::t('icon'); ?></th>
                        <th><?php echo TranslationManager::t('order'); ?></th>
                        <th><?php echo TranslationManager::t('permission'); ?></th>
                        <th><?php echo TranslationManager::t('status'); ?></th>
                        <th><?php echo TranslationManager::t('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menu_items as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td>
                                <?php if ($item['parent_id']): ?>
                                    <span class="ms-3">↳ </span>
                                <?php endif; ?>
                                <i class="<?php echo htmlspecialchars($item['icon']); ?> me-2"></i>
                                <?php echo htmlspecialchars($item['title']); ?>
                                <?php if ($item['is_separator']): ?>
                                    <span class="badge bg-secondary ms-2"><?php echo TranslationManager::t('separator'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item['parent_id']): ?>
                                    <?php
                                    $parent = array_filter($menu_items, fn($i) => $i['id'] == $item['parent_id']);
                                    $parent = reset($parent);
                                    echo $parent ? htmlspecialchars($parent['title']) : '-';
                                    ?>
                                <?php else: ?>
                                    <span class="text-muted"><?php echo TranslationManager::t('root'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $item['url'] ? '<code>' . htmlspecialchars($item['url']) . '</code>' : '<span class="text-muted">-</span>'; ?>
                            </td>
                            <td>
                                <i class="<?php echo htmlspecialchars($item['icon']); ?>" title="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                            </td>
                            <td><span class="badge bg-info"><?php echo $item['sort_order']; ?></span></td>
                            <td>
                                <?php if ($item['permission_module']): ?>
                                    <small class="text-muted"><?php echo htmlspecialchars($item['permission_module'] . ':' . $item['permission_action']); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item['is_active']): ?>
                                    <span class="badge bg-success"><?php echo TranslationManager::t('active'); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?php echo TranslationManager::t('inactive'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <?php if (has_permission($_SESSION['user_id'], 'menu', 'update')): ?>
                                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/system/adminmenu/edit/' . $item['id']; ?>"
                                           class="btn btn-sm btn-outline-primary" title="<?php echo TranslationManager::t('edit'); ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (has_permission($_SESSION['user_id'], 'menu', 'delete')): ?>
                                        <button onclick="confirmDelete(<?php echo $item['id']; ?>)"
                                                class="btn btn-sm btn-outline-danger" title="<?php echo TranslationManager::t('delete'); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo TranslationManager::t("confirm_delete"); ?>',
        text: '<?php echo TranslationManager::t("delete_menu_item_confirm"); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?php echo TranslationManager::t("yes_delete"); ?>',
        cancelButtonText: '<?php echo TranslationManager::t("cancel"); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo get_setting('site_url', 'http://localhost') . '/admin/system/adminmenu/delete'; ?>/' + id;
        }
    });
}
</script>

<?php
$pageScripts = [
    admin_module_path('/views/menu/js/index.js'),
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>