<?php require_once get_setting('base_path', '/var/www/html') . 'admin/includes/header.php'; ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building"></i> <?php echo t('branch.management'); ?>
        </h1>
        <?php if (has_permission($_SESSION['user_id'], 'branches', 'create')): ?>
        <a href="branches/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> <?php echo t('branch.new_branch'); ?>
        </a>
        <?php endif; ?>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <?php echo t('branch.total_branches'); ?>
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
                                <?php echo t('branch.active_branches'); ?>
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
                                <?php echo t('user.total_users'); ?>
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
            <h6 class="m-0 font-weight-bold text-primary"><?php echo t('branch.branch_list'); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="branchesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo t('branch.branch_name'); ?></th>
                            <th><?php echo t('address'); ?></th>
                            <th><?php echo t('branch.location'); ?></th>
                            <th><?php echo t('branch.contact_info'); ?></th>
                            <th><?php echo t('branch.manager'); ?></th>
                            <th><?php echo t('users'); ?></th>
                            <th><?php echo t('status'); ?></th>
                            <th><?php echo t('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($branches as $branch): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($branch['name']); ?></strong>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($branch['address'] ?? 'N/A'); ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($branch['city_name']): ?>
                                    <div><?php echo htmlspecialchars($branch['city_name']); ?></div>
                                    <?php if ($branch['state_name']): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($branch['state_name']); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
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
                                    <small class="text-muted">+ altri contatti</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($branch['referente'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?php echo $branch['users_count']; ?> utent<?php echo $branch['users_count'] == 1 ? 'e' : 'i'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($branch['is_active']): ?>
                                    <span class="badge badge-success"><?php echo t('active'); ?></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?php echo t('inactive'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if (has_permission($_SESSION['user_id'], 'branches', 'update')): ?>
                                        <a href="branches/edit/<?php echo $branch['id']; ?>" 
                                           class="btn btn-outline-primary btn-sm" title="<?php echo t('edit'); ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="branches/manage-users/<?php echo $branch['id']; ?>" 
                                           class="btn btn-outline-info btn-sm" title="<?php echo t('branch.manage_users'); ?>">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (has_permission($_SESSION['user_id'], 'branches', 'delete')): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete(<?php echo $branch['id']; ?>, '<?php echo addslashes($branch['name']); ?>')"
                                                title="<?php echo t('delete'); ?>">
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
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo t('branch.confirm_delete_title'); ?></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?php echo t('branch.confirm_delete_message'); ?> <strong id="branchName"></strong>?</p>
                <p class="text-danger"><small><?php echo t('branch.delete_warning'); ?></small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo t('cancel'); ?></button>
                <a href="#" class="btn btn-danger" id="deleteConfirm"><?php echo t('delete'); ?></a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#branchesTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/it-IT.json'
        },
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true
    });
});

function confirmDelete(id, name) {
    $('#branchName').text(name);
    $('#deleteConfirm').attr('href', 'branches/delete/' + id);
    $('#deleteModal').modal('show');
}
</script>

<?php include get_setting('base_path', '/var/www/html') . 'admin/includes/footer.php'; ?>