<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-truck me-2"></i><?php echo TranslationManager::t('supplier.management'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
    <?php if (can('suppliers', 'create')): ?>
            <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/suppliers/suppliers/create'; ?>" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i><?php echo TranslationManager::t('supplier.new_supplier'); ?>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()" title="<?php echo TranslationManager::t('btn.refresh'); ?>">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Statistiche -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <?php echo TranslationManager::t('supplier.total_suppliers'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_suppliers; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                            <?php echo TranslationManager::t('supplier.active_suppliers'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count(array_filter($suppliers, fn($s) => $s['is_active'])); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabella fornitori -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('supplier.supplier_list'); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo TranslationManager::t('name'); ?></th>
                        <th><?php echo TranslationManager::t('address'); ?></th>
                        <th><?php echo TranslationManager::t('supplier.city'); ?></th>
                        <th><?php echo TranslationManager::t('supplier.country'); ?></th>
                        <th><?php echo TranslationManager::t('phone'); ?></th>
                        <th><?php echo TranslationManager::t('email'); ?></th>
                        <th><?php echo TranslationManager::t('status'); ?></th>
                        <th><?php echo TranslationManager::t('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?php echo $supplier['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($supplier['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($supplier['address_line1'] . ' ' . $supplier['address_line2'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($supplier['city_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($supplier['country_name'] ?? '-'); ?></td>
                            <td>
                                <?php if ($supplier['tel1']): ?>
                                    <a href="tel:<?php echo $supplier['tel1']; ?>"><?php echo $supplier['tel1']; ?></a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($supplier['email1']): ?>
                                    <a href="mailto:<?php echo $supplier['email1']; ?>"><?php echo $supplier['email1']; ?></a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($supplier['is_active']): ?>
                                    <span class="badge bg-success"><?php echo TranslationManager::t('active'); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?php echo TranslationManager::t('inactive'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/suppliers/suppliers/view'; ?>/<?php echo $supplier['id']; ?>"
                                       class="btn btn-sm btn-outline-info" title="<?php echo TranslationManager::t('btn.view'); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>&nbsp;

                                    <?php if (can('suppliers', 'update')): ?>
                                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/suppliers/suppliers/edit'; ?>/<?php echo $supplier['id']; ?>"
                                           class="btn btn-sm btn-outline-primary" title="<?php echo TranslationManager::t('edit'); ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>&nbsp;
                                    <?php endif; ?>

                                    <?php if (can('suppliers', 'delete')): ?>
                                        <button onclick="deleteSupplier(<?php echo $supplier['id']; ?>)"
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
    const SUPPLIERS_VARS = {
        urls: {
            deleteBase: '<?php echo get_setting("site_url", "http://localhost") . "/admin/suppliers/suppliers/delete"; ?>/'
        },
        translations: {
            confirmDeleteTitle: '<?php echo TranslationManager::t("confirm_delete"); ?>',
            confirmDeleteText: '<?php echo TranslationManager::t("supplier.confirm_delete"); ?>',
            yesDelete: '<?php echo TranslationManager::t("yes_delete"); ?>',
            cancel: '<?php echo TranslationManager::t("cancel"); ?>'
        }
    };
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/suppliers/views/suppliers/js/index.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>