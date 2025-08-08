<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-building me-2"></i><?php echo TranslationManager::t('branch.management'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <?php if (has_permission($_SESSION['user_id'], 'branches', 'create')): ?>
            <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/branches/create'; ?>" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i><?php echo TranslationManager::t('branch.new_branch'); ?>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()" title="<?php echo TranslationManager::t('btn.refresh'); ?>">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <?php echo TranslationManager::t('branch.total_branches'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_branches; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
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
                            <?php echo TranslationManager::t('branch.active_branches'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count(array_filter($branches, function($b) { return $b['is_active']; })); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <?php echo TranslationManager::t('user.total_users'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo array_sum(array_column($branches, 'users_count')); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Branches Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('branch.branch_list'); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php echo TranslationManager::t('branch.branch_name'); ?></th>
                        <th><?php echo TranslationManager::t('address'); ?></th>
                        <th><?php echo TranslationManager::t('branch.location'); ?></th>
                        <th><?php echo TranslationManager::t('branch.contact_info'); ?></th>
                        <th><?php echo TranslationManager::t('branch.manager'); ?></th>
                        <th><?php echo TranslationManager::t('users'); ?></th>
                        <th><?php echo TranslationManager::t('status'); ?></th>
                        <th><?php echo TranslationManager::t('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($branches as $branch): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($branch['name']); ?></strong></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($branch['address'] ?? TranslationManager::t('na')); ?></small></td>
                            <td>
                                <?php if ($branch['city_name']): ?>
                                    <div><?php echo htmlspecialchars($branch['city_name']); ?></div>
                                    <?php if ($branch['state_name']): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($branch['state_name']); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted"><?php echo TranslationManager::t('na'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($branch['email1']): ?>
                                    <div><i class="fas fa-envelope fa-sm text-primary"></i>
                                        <a href="mailto:<?php echo $branch['email1']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($branch['email1']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($branch['tel1']): ?>
                                    <div><i class="fas fa-phone fa-sm text-success"></i>
                                        <a href="tel:<?php echo $branch['tel1']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($branch['tel1']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($branch['email2'] || $branch['tel2']): ?>
                                    <small class="text-muted">+ <?php echo TranslationManager::t('branch.more_contacts'); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($branch['referente'] ?? TranslationManager::t('na')); ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $branch['users_count']; ?> <?php echo $branch['users_count'] == 1 ? TranslationManager::t('branch.user_singular') : TranslationManager::t('branch.user_plural'); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($branch['is_active']): ?>
                                    <span class="badge bg-success"><?php echo TranslationManager::t('active'); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?php echo TranslationManager::t('inactive'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <?php if (has_permission($_SESSION['user_id'], 'branches', 'update')): ?>
                                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/branches/edit/' . $branch['id']; ?>"
                                           class="btn btn-sm btn-outline-primary" title="<?php echo TranslationManager::t('edit'); ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/branches/manage-users/' . $branch['id']; ?>"
                                           class="btn btn-sm btn-outline-info" title="<?php echo TranslationManager::t('branch.manage_users'); ?>">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (has_permission($_SESSION['user_id'], 'branches', 'delete')): ?>
                                        <button onclick="confirmDelete(<?php echo $branch['id']; ?>, '<?php echo addslashes($branch['name']); ?>')"
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo TranslationManager::t('branch.confirm_delete_title'); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p><?php echo TranslationManager::t('branch.confirm_delete_message'); ?> <strong id="branchName"></strong>?</p>
                <p class="text-danger"><small><?php echo TranslationManager::t('branch.delete_warning'); ?></small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo TranslationManager::t('cancel'); ?></button>
                <a href="#" class="btn btn-danger" id="deleteConfirm"><?php echo TranslationManager::t('delete'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php
$pageScripts = [
    admin_module_path('/views/branches/js/index.js'),
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>