<?php
require_once __DIR__ . '/../../admin/includes/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-user-edit me-2"></i><?php echo t('user.edit_user'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo admin_url('users'); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?php echo t('back_to_list'); ?>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo t('user.user_data'); ?></h6>
            </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo admin_url('users', 'update', $user['id']); ?>">
                                <?php echo csrf_token_field(); ?>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label"><?php echo t('user.username'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="username" name="username" 
                                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label"><?php echo t('email'); ?> <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label"><?php echo t('user.first_name'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label"><?php echo t('user.last_name'); ?> <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label"><?php echo t('phone'); ?></label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                       <?php echo $user['is_active'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">
                                                    <?php echo t('user.user_active'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong><?php echo t('msg.info'); ?>:</strong> <?php echo t('user.password_note'); ?>
                                </div>

                                <div class="mb-4">
                                    <h5><?php echo t('user.branch_assignments'); ?></h5>
                                    <p class="text-muted small"><?php echo t('user.select_branches'); ?></p>
                                    
                                    <?php
                                    // Load branches and user branch assignments
                                    require_once __DIR__ . '/../../models/Branch.php';
                                    $branch_model = new Branch();
                                    $branches = $branch_model->read(true); // Get only active branches
                                    $user_branches = $branch_model->getUserBranches($user['id']);
                                    
                                    // Create an array of assigned branch IDs for easier checking
                                    $assigned_branch_ids = array_column($user_branches, 'id');
                                    // Get primary branch ID
                                    $primary_branch_id = null;
                                    foreach ($user_branches as $ub) {
                                        if ($ub['is_primary']) {
                                            $primary_branch_id = $ub['id'];
                                            break;
                                        }
                                    }
                                    
                                    if (!empty($branches)):
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th><?php echo t('branch.name'); ?></th>
                                                    <th><?php echo t('branch.location'); ?></th>
                                                    <th class="text-center"><?php echo t('assign'); ?></th>
                                                    <th class="text-center"><?php echo t('primary'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($branches as $branch): 
                                                    $is_assigned = in_array($branch['id'], $assigned_branch_ids);
                                                    $is_primary = ($primary_branch_id == $branch['id']);
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($branch['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($branch['city_name'] ?? $branch['address']); ?></td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input branch-checkbox" type="checkbox" name="branch_ids[]" value="<?php echo $branch['id']; ?>" id="branch_<?php echo $branch['id']; ?>" <?php echo $is_assigned ? 'checked' : ''; ?>>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input primary-branch-radio" type="radio" name="primary_branch_id" value="<?php echo $branch['id']; ?>" id="primary_<?php echo $branch['id']; ?>" <?php echo $is_primary ? 'checked' : ''; ?> <?php echo $is_assigned ? '' : 'disabled'; ?>>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <?php echo t('branch.no_branches'); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?php echo admin_url('users'); ?>" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-times me-1"></i><?php echo t('cancel'); ?>
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i><?php echo t('user.save_changes'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo t('user.account_info'); ?></h6>
            </div>
                        <div class="card-body">
                            <p><strong><?php echo t('user.user_id'); ?>:</strong> <?php echo $user['id']; ?></p>
                            <p><strong><?php echo t('user.created_on'); ?>:</strong><br>
                               <?php echo format_date_localized($user['created_at']); ?></p>
                            <?php if ($user['updated_at'] && $user['updated_at'] != $user['created_at']): ?>
                            <p><strong><?php echo t('user.last_modified'); ?>:</strong><br>
                               <?php echo format_date_localized($user['updated_at']); ?></p>
                            <?php endif; ?>
                            <p><strong><?php echo t('status'); ?>:</strong><br>
                               <?php if ($user['is_active']): ?>
                                   <span class="badge bg-success"><?php echo t('active'); ?></span>
                               <?php else: ?>
                                   <span class="badge bg-danger"><?php echo t('inactive'); ?></span>
                               <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary"><?php echo t('user.quick_actions'); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetPassword(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-key me-1"></i><?php echo t('user.reset_password'); ?>
                                </button>
                                <?php if (has_permission($_SESSION['user_id'], 'users', 'delete') && $user['id'] != $_SESSION['user_id']): ?>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-trash me-1"></i><?php echo t('user.delete_user'); ?>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
        </div>
    </div>
</div>

<script>
function resetPassword(userId) {
    confirmAction('<?php echo addslashes(t('user.confirm_reset_password')); ?>')
        .then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?php echo admin_url('users', 'reset_password'); ?>/${userId}`;
            }
        });
}

function deleteUser(userId) {
    confirmDelete('<?php echo addslashes(t('user.confirm_delete')); ?>')
        .then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?php echo admin_url('users', 'delete'); ?>/${userId}`;
            }
        });
}

// Branch selection logic
document.querySelectorAll('.branch-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const branchId = this.value;
        const primaryRadio = document.getElementById('primary_' + branchId);
        
        if (this.checked) {
            primaryRadio.disabled = false;
            
            // If this is the first branch selected, automatically set as primary
            const checkedBoxes = document.querySelectorAll('.branch-checkbox:checked');
            if (checkedBoxes.length === 1) {
                primaryRadio.checked = true;
            }
        } else {
            primaryRadio.disabled = true;
            primaryRadio.checked = false;
            
            // If there's only one branch left selected, make it primary
            const checkedBoxes = document.querySelectorAll('.branch-checkbox:checked');
            if (checkedBoxes.length === 1) {
                const lastBranchId = checkedBoxes[0].value;
                document.getElementById('primary_' + lastBranchId).checked = true;
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>