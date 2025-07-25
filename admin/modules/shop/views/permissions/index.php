<?php
// Process language changes
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/process_language.php';

// Set page title
$page_title = TranslationManager::t('user.permissions') . ' - Restaurant Admin';

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include header
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-shield-alt me-2"></i><?php echo TranslationManager::t('user.permissions'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <?php if (has_permission($_SESSION['user_id'], 'permissions', 'create')): ?>
            <a href="<?php echo admin_url('permissions', 'create'); ?>" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i><?php echo TranslationManager::t('btn.add_new'); ?>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()" title="<?php echo TranslationManager::t('btn.refresh'); ?>">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <?php echo TranslationManager::t('user.permissions'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_permissions ?? 0; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
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
                            <?php echo TranslationManager::t('resource'); ?>s
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($resources ?? []); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('user.permissions'); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo TranslationManager::t('name'); ?></th>
                        <th><?php echo TranslationManager::t('module'); ?></th>
                        <th><?php echo TranslationManager::t('action'); ?></th>
                        <th><?php echo TranslationManager::t('user.groups'); ?></th>
                        <th><?php echo TranslationManager::t('description'); ?></th>
                        <th><?php echo TranslationManager::t('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($permissions)): ?>
                        <?php foreach ($permissions as $permission): ?>
                            <tr>
                                <td><?php echo $permission['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($permission['module'] . ' - ' . $permission['action']); ?></strong></td>
                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($permission['module']); ?></span></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($permission['action']); ?></span></td>
                                <td>
                                    <?php if (!empty($permission['user_groups'])): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($permission['user_groups']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted"><?php echo TranslationManager::t('msg.no_data'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="text-muted">-</span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if (has_permission($_SESSION['user_id'], 'permissions', 'update')): ?>
                                            <a href="<?php echo admin_url('permissions', 'edit', $permission['id']); ?>" 
                                               class="btn btn-sm btn-outline-primary" title="<?php echo TranslationManager::t('edit'); ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (has_permission($_SESSION['user_id'], 'permissions', 'delete')): ?>
                                            <button onclick="deletePermission(<?php echo $permission['id']; ?>)" 
                                                    class="btn btn-sm btn-outline-danger" title="<?php echo TranslationManager::t('delete'); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                <em><?php echo TranslationManager::t('msg.no_data'); ?></em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function deletePermission(permissionId) {
        confirmDelete('<?php echo addslashes(TranslationManager::t('msg.confirm_delete')); ?>')
            .then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?php echo admin_url('permissions', 'delete'); ?>/${permissionId}`;
                }
            });
    }
</script>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>