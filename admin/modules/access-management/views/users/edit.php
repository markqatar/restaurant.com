<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-user-edit me-2"></i><?php echo TranslationManager::t('user.edit_user'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/users'; ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back_to_list'); ?>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('user.user_data'); ?></h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/users/update/' . $user['id']; ?>">
                    <?php echo csrf_token_field(); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label"><?php echo TranslationManager::t('user.username'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label"><?php echo TranslationManager::t('email'); ?> <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label"><?php echo TranslationManager::t('user.first_name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label"><?php echo TranslationManager::t('user.last_name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label"><?php echo TranslationManager::t('phone'); ?></label>
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
                                        <?php echo TranslationManager::t('user.user_active'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
<div class="mb-3">
    <label for="group_id" class="form-label"><?php echo TranslationManager::t('user.group'); ?></label>
    <select class="form-select" id="group_id" name="group_id">
        <option value=""><?php echo TranslationManager::t('form.select_option'); ?></option>
        <?php foreach ($userGroups as $group): ?>
            <option value="<?php echo $group['id']; ?>" 
                <?php echo (in_array($group['id'], array_column($assignedGroups, 'id'))) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($group['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong><?php echo TranslationManager::t('msg.info'); ?>:</strong> <?php echo TranslationManager::t('user.password_note'); ?>
                    </div>

                    <?php render_hook('users.edit.form.sections', $user); ?>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/users'; ?>" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i><?php echo TranslationManager::t('cancel'); ?>
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo TranslationManager::t('user.save_changes'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('user.account_info'); ?></h6>
            </div>
            <div class="card-body">
                <p><strong><?php echo TranslationManager::t('user.user_id'); ?>:</strong> <?php echo $user['id']; ?></p>
                <p><strong><?php echo TranslationManager::t('user.created_on'); ?>:</strong><br>
                    <?php echo TranslationManager::format_date_localized($user['created_at']); ?></p>
                <?php if ($user['updated_at'] && $user['updated_at'] != $user['created_at']): ?>
                    <p><strong><?php echo TranslationManager::t('user.last_modified'); ?>:</strong><br>
                        <?php echo TranslationManager::format_date_localized($user['updated_at']); ?></p>
                <?php endif; ?>
                <p><strong><?php echo TranslationManager::t('status'); ?>:</strong><br>
                    <?php if ($user['is_active']): ?>
                        <span class="badge bg-success"><?php echo TranslationManager::t('active'); ?></span>
                    <?php else: ?>
                        <span class="badge bg-danger"><?php echo TranslationManager::t('inactive'); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('user.quick_actions'); ?></h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetPassword(<?php echo $user['id']; ?>)">
                        <i class="fas fa-key me-1"></i><?php echo TranslationManager::t('user.reset_password'); ?>
                    </button>
                    <?php if (has_permission($_SESSION['user_id'], 'users', 'delete') && $user['id'] != $_SESSION['user_id']): ?>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteUser(<?php echo $user['id']; ?>)">
                            <i class="fas fa-trash me-1"></i><?php echo TranslationManager::t('user.delete_user'); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function resetPassword(userId) {
        confirmAction('<?php echo addslashes(TranslationManager::t('user.confirm_reset_password')); ?>')
            .then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/users/reset_password'; ?>/' + userId;
                }
            });
    }

    function deleteUser(userId) {
        confirmDelete('<?php echo addslashes(TranslationManager::t('user.confirm_delete')); ?>')
            .then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo get_setting('site_url', 'http://localhost') . '/admin/access-management/users/delete'; ?>/' + userId;
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

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>