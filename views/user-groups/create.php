<?php
// Process language changes first to prevent header issues
require_once __DIR__ . '/../../admin/includes/process_language.php';

// Set page title before including header
$page_title = t('user.groups') . ' - ' . t('btn.add_new') . ' - Restaurant Admin';

// Include required files
require_once __DIR__ . '/../../includes/functions.php';

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

require_once __DIR__ . '/../../admin/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-users-cog me-2"></i><?php echo t('user.groups'); ?> - <?php echo t('btn.add_new'); ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="user-groups.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i><?php echo t('back_to_list'); ?>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><?php echo t('user.groups'); ?> - <?php echo t('msg.info'); ?></h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo get_setting('site_url', 'http://restaurant.com'); ?>/admin/user-groups/tore" id="groupForm">
                                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label"><?php echo t('name'); ?> *</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="description" class="form-label"><?php echo t('description'); ?></label>
                                            <input type="text" class="form-control" id="description" name="description">
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3"><?php echo t('user.permissions'); ?></h5>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="40%"><?php echo t('resource'); ?></th>
                                                <th><?php echo t('permissions'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            // Get all resources and their permissions
                                            require_once __DIR__ . '/../../models/Permission.php';
                                            $permissionModel = new Permission();
                                            $resources = $permissionModel->getResources();
                                            
                                            foreach ($resources as $resourceKey): 
                                                $resourcePermissions = $permissionModel->getByResource($resourceKey);
                                                $resourceName = ucfirst($resourceKey);
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo $resourceName; ?></strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <?php foreach ($resourcePermissions as $permission): ?>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permissions[]" 
                                                                   id="perm_<?php echo $permission['id']; ?>" 
                                                                   value="<?php echo $permission['id']; ?>">
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

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                        <i class="fas fa-times me-1"></i><?php echo t('cancel'); ?>
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i><?php echo t('create'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><?php echo t('msg.info'); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-1"></i><?php echo t('msg.info'); ?></h6>
                                <ul class="mb-0 small">
                                    <li><?php echo t('required_field'); ?> (*)</li>
                                    <li><?php echo t('name'); ?> <?php echo t('form.required'); ?></li>
                                    <li><?php echo t('description'); ?> <?php echo t('form.optional'); ?></li>
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
document.getElementById('groupForm').addEventListener('submit', function(e) {
    if (!validateForm('groupForm')) {
        e.preventDefault();
        Swal.fire({
            title: '<?php echo t('msg.error'); ?>',
            text: '<?php echo t('required_field'); ?>',
            icon: 'error'
        });
    }
});
</script>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>