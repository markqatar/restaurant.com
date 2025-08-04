<?php
// Include required files
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/process_language.php';
// Set page title before including header
$page_title = TranslationManager::t('user.groups') . ' - Restaurant Admin';

require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';


// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-users-cog me-2"></i><?php echo TranslationManager::t('user.groups'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <?php if (has_permission($_SESSION['user_id'], 'user_groups', 'create')): ?>
            <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/userGroups/create'; ?>" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i><?php echo TranslationManager::t('btn.add_new'); ?>
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
                            <?php echo TranslationManager::t('user_groups.total_groups'); ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_groups ?? 0; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Groups Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('user.groups'); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo TranslationManager::t('name'); ?></th>
                        <th><?php echo TranslationManager::t('description'); ?></th>
                        <th><?php echo TranslationManager::t('users'); ?></th>
                        <th><?php echo TranslationManager::t('created'); ?></th>
                        <th><?php echo TranslationManager::t('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($user_groups)): ?>
                        <?php foreach ($user_groups as $group): ?>
                            <tr>
                                <td><?php echo $group['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($group['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($group['description'] ?? ''); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo $group['user_count'] ?? 0; ?> <?php echo TranslationManager::t('users'); ?></span>
                                </td>
                                <td><?php echo format_date($group['created_at'], 'd/m/Y H:i'); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if (has_permission($_SESSION['user_id'], 'user_groups', 'update')): ?>

                                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/userGroups/edit/' . $group['id']; ?>"
                                                class="btn btn-sm btn-outline-primary" title="<?php echo TranslationManager::t('edit'); ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            &nbsp;
                                        <?php endif; ?>

                                        <?php if (has_permission($_SESSION['user_id'], 'user_groups', 'delete')): ?>
                                            <button onclick="deleteGroup(<?php echo $group['id']; ?>)"
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
                            <td colspan="7" class="text-center"><em><?php echo TranslationManager::t('msg.no_data'); ?></em></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    const USER_GROUPS_VARS = {
        deleteUrl: '<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/userGroups/delete/'; ?>',
        translations: {
            confirmTitle: '<?php echo TranslationManager::t("user_groups.confirm_delete"); ?>',
            confirmText: '<?php echo TranslationManager::t("user_groups.confirm_delete_text"); ?>',
            yesDelete: '<?php echo TranslationManager::t("yes_delete"); ?>',
            cancel: '<?php echo TranslationManager::t("cancel"); ?>'
        }
    };
</script>
<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/access-management/views/user-groups/js/index.js',
];
?>
<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>