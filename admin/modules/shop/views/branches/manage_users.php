<?php require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> <?php echo TranslationManager::t('branch.manage_users'); ?> - <?php echo htmlspecialchars($branch['name']); ?>
        </h1>
        <a href="branches" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('back_to_list'); ?>
        </a>
    </div>

    <div class="row">
        <!-- Current Users -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?php echo TranslationManager::t('branch.assigned_users'); ?> (<?php echo count($branch_users); ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($branch_users)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted"><?php echo TranslationManager::t('branch.no_assigned_users'); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo TranslationManager::t('users'); ?></th>
                                        <th><?php echo TranslationManager::t('email'); ?></th>
                                        <th><?php echo TranslationManager::t('type'); ?></th>
                                        <th><?php echo TranslationManager::t('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="assignedUsers">
                                    <?php foreach ($branch_users as $user): ?>
                                    <tr id="user-row-<?php echo $user['id']; ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm rounded-circle bg-primary text-white me-2">
                                                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                                    <br><small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <?php if ($user['is_primary']): ?>
                                                <span class="badge badge-primary"><?php echo TranslationManager::t('branch.primary_branch'); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><?php echo TranslationManager::t('branch.secondary_branch'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <?php if (!$user['is_primary']): ?>
                        <button class="btn btn-outline-primary btn-sm" 
                            onclick="setPrimary(<?php echo $user['id']; ?>)"
                            title="<?php echo TranslationManager::t('branch.set_as_primary'); ?>">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                <?php endif; ?>
                        <button class="btn btn-outline-danger btn-sm" 
                            onclick="removeUser(<?php echo $user['id']; ?>)"
                            title="<?php echo TranslationManager::t('branch.remove_from_branch'); ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Add User -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success"><?php echo TranslationManager::t('branch.add_user'); ?></h6>
                </div>
                <div class="card-body">
                    <form id="assignUserForm">
                        <input type="hidden" name="branch_id" value="<?php echo $branch['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label"><?php echo TranslationManager::t('branch.select_user'); ?></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value=""><?php echo TranslationManager::t('branch.choose_user_placeholder'); ?></option>
                                <?php foreach ($all_users as $user): ?>
                                    <?php 
                                    // Check if user is already assigned
                                    $already_assigned = false;
                                    foreach ($branch_users as $assigned_user) {
                                        if ($assigned_user['id'] == $user['id']) {
                                            $already_assigned = true;
                                            break;
                                        }
                                    }
                                    if (!$already_assigned): 
                                    ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['display_name']); ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary">
                                <label class="form-check-label" for="is_primary">
                                    <?php echo TranslationManager::t('branch.set_as_primary'); ?>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> <?php echo TranslationManager::t('branch.assign_user'); ?>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Branch Info -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info"><?php echo TranslationManager::t('branch.branch_info'); ?></h6>
                </div>
                <div class="card-body">
                    <p><strong><?php echo TranslationManager::t('branch.branch_name'); ?>:</strong> <?php echo htmlspecialchars($branch['name']); ?></p>
                    <?php if ($branch['address']): ?>
                    <p><strong><?php echo TranslationManager::t('address'); ?>:</strong> <?php echo htmlspecialchars($branch['address']); ?></p>
                    <?php endif; ?>
                    <?php if ($branch['city_name']): ?>
                    <p><strong><?php echo TranslationManager::t('branch.location'); ?>:</strong> <?php echo htmlspecialchars($branch['city_name']); ?></p>
                    <?php endif; ?>
                    <?php if ($branch['referente']): ?>
                    <p><strong><?php echo TranslationManager::t('branch.manager'); ?>:</strong> <?php echo htmlspecialchars($branch['referente']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle form submission
    $('#assignUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'branches/assign-user',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', '<?php echo TranslationManager::t('branch.msg.server_error'); ?>');
            }
        });
    });
});

function removeUser(userId) {
    if (confirm('<?php echo TranslationManager::t('branch.confirm.remove_user'); ?>')) {
        $.ajax({
            url: 'branches/remove-user',
            method: 'POST',
            data: {
                user_id: userId,
                branch_id: <?php echo $branch['id']; ?>
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#user-row-' + userId).fadeOut(300, function() {
                        $(this).remove();
                    });
                    // Add user back to dropdown
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', '<?php echo TranslationManager::t('branch.msg.server_error'); ?>');
            }
        });
    }
}

function setPrimary(userId) {
    if (confirm('<?php echo TranslationManager::t('branch.confirm.set_primary'); ?>')) {
        $.ajax({
            url: 'branches/assign-user',
            method: 'POST',
            data: {
                user_id: userId,
                branch_id: <?php echo $branch['id']; ?>,
                is_primary: 1
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', '<?php echo TranslationManager::t('branch.primary_updated'); ?>');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', '<?php echo TranslationManager::t('branch.msg.server_error'); ?>');
            }
        });
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    $('.container-fluid').prepend(alertHtml);
    
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}
</script>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>