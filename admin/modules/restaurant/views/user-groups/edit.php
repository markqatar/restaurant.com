<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/process_language.php';
$page_title = TranslationManager::t('user.groups') . ' - ' . TranslationManager::t('edit') . ' - Restaurant Admin';
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-users-cog me-2"></i><?php echo TranslationManager::t('user.groups'); ?> - <?php echo TranslationManager::t('edit'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <a href="<?php echo admin_url('user-groups'); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back_to_list'); ?>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <?php echo TranslationManager::t('user.groups'); ?> - <?php echo TranslationManager::t('edit'); ?>
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo get_setting('site_url', 'http://localhost'); ?>/admin/user-groups/update/<?php echo $user_group['id']; ?>" id="editGroupForm">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label"><?php echo TranslationManager::t('name'); ?> *</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($user_group['name']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label"><?php echo TranslationManager::t('description'); ?></label>
                                <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($user_group['description'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3"><?php echo TranslationManager::t('user.permissions'); ?></h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="40%"><?php echo TranslationManager::t('resource'); ?></th>
                                    <th><?php echo TranslationManager::t('permissions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $permissionModel = new Permission();
                                $resources = $permissionModel->getResources();
                                $groupPermissions = isset($user_group['permissions']) ? $user_group['permissions'] : [];
                                $groupPermissionIds = array_map(function ($perm) {
                                    return $perm['id'];
                                }, $groupPermissions);

                                foreach ($resources as $resourceKey):
                                    $resourcePermissions = $permissionModel->getByResource($resourceKey);
                                    $resourceName = ucfirst($resourceKey);
                                ?>
                                <tr>
                                    <td><strong><?php echo $resourceName; ?></strong></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($resourcePermissions as $permission): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" id="perm_<?php echo $permission['id']; ?>" value="<?php echo $permission['id']; ?>" <?php echo (in_array($permission['id'], $groupPermissionIds)) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="perm_<?php echo $permission['id']; ?>">
                                                    <?php echo ucfirst($permission['action']); ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo admin_url('user-groups'); ?>" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i><?php echo TranslationManager::t('cancel'); ?>
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo TranslationManager::t('save'); ?>
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
                        <li><?php echo TranslationManager::t('description'); ?> <?php echo TranslationManager::t('form.optional'); ?></li>
                        <li><?php echo TranslationManager::t('user.permissions_assigned'); ?> <?php echo TranslationManager::t('form.optional'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('editGroupForm').addEventListener('submit', function(e) {
    if (!validateForm('editGroupForm')) {
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