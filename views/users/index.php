<?php
require_once __DIR__ . '/../../admin/includes/header.php';
?>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

    <h1 class="h2">
        <i class="fas fa-users me-2"></i><?php echo t('user.management'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <?php if (has_permission($_SESSION['user_id'], 'users', 'create')): ?>
            <a href="<?php echo admin_url('users', 'create'); ?>" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i><?php echo t('user.new_user'); ?>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()" title="<?php echo t('btn.refresh'); ?>">
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
                            <?php echo t('user.total_users'); ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_users; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <?php echo t('user.active_users'); ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count(array_filter($users, function ($u) {
                                return $u['is_active'];
                            })); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo t('user.user_list'); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo t('user.username'); ?></th>
                        <th><?php echo t('user.full_name'); ?></th>
                        <th><?php echo t('email'); ?></th>
                        <th><?php echo t('branch.branches'); ?></th>
                        <th><?php echo t('user.groups'); ?></th>
                        <th><?php echo t('status'); ?></th>
                        <th><?php echo t('created'); ?></th>
                        <th><?php echo t('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                            </td>
                            <td>
                                <a href="mailto:<?php echo $user['email']; ?>">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                // Get user branches
                                require_once __DIR__ . '/../../models/Branch.php';
                                $branch_model = new Branch();
                                $user_branches = $branch_model->getUserBranches($user['id']);

                                if (!empty($user_branches)):
                                    foreach ($user_branches as $branch):
                                        $badge_class = $branch['is_primary'] ? 'bg-success' : 'bg-secondary';
                                        $title = $branch['is_primary'] ? t('primary') : '';
                                ?>
                                        <span class="badge <?php echo $badge_class; ?> me-1" <?php if ($title) echo 'title="' . $title . '"'; ?>>
                                            <?php echo htmlspecialchars($branch['name']); ?>
                                        </span>
                                    <?php
                                    endforeach;
                                else:
                                    ?>
                                    <span class="text-muted"><?php echo t('branch.no_assigned_branches'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['user_groups']): ?>
                                    <?php foreach (explode(', ', $user['user_groups']) as $group): ?>
                                        <span class="badge bg-info me-1"><?php echo htmlspecialchars($group); ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted"><?php echo t('user.no_groups'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="badge bg-success"><?php echo t('active'); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?php echo t('inactive'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo format_date($user['created_at'], 'd/m/Y H:i'); ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <?php if (has_permission($_SESSION['user_id'], 'users', 'update')): ?>
                                        <a href="<?php echo admin_url('users', 'edit', $user['id']); ?>"
                                            class="btn btn-sm btn-outline-primary" title="<?php echo t('edit'); ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (has_permission($_SESSION['user_id'], 'users', 'delete') && $user['id'] != $_SESSION['user_id']): ?>
                                        <button onclick="deleteUser(<?php echo $user['id']; ?>)"
                                            class="btn btn-sm btn-outline-danger" title="<?php echo t('delete'); ?>">
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
    function deleteUser(userId) {
        confirmDelete('<?php echo addslashes(t('user.confirm_delete')); ?>')
            .then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?php echo admin_url('users', 'delete'); ?>/${userId}`;
                }
            });
    }
</script>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>