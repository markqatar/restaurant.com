<?php

// Include required files
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/process_language.php';
// Set page title before including header
$page_title = TranslationManager::t('user.new_user') . ' - Restaurant Admin';

require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in (you may want to add this)
// if (!is_logged_in()) {
//     redirect('login.php');
// }

// Function to get CSRF token (if not defined in functions.php)
if (!function_exists('get_csrf_token')) {
    function get_csrf_token()
    {
        return generate_csrf_token();
    }
}
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-user-plus me-2"></i><?php echo TranslationManager::t('user.new_user'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/users'; ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back_to_list'); ?>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('user.account_info'); ?></h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo get_setting('site_url', 'http://localhost') . '/admin/users/store'; ?>" id="userForm">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label"><?php echo TranslationManager::t('user.username'); ?> *</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                                <div class="form-text"><?php echo TranslationManager::t('user.username'); ?> <?php echo TranslationManager::t('form.required'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label"><?php echo TranslationManager::t('email'); ?> *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label"><?php echo TranslationManager::t('user.first_name'); ?> *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label"><?php echo TranslationManager::t('user.last_name'); ?> *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <div class="form-text"><?php echo TranslationManager::t('form.required'); ?> - <?php echo TranslationManager::t('form.enter_value'); ?> (min 6 <?php echo TranslationManager::t('form.required'); ?>)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label"><?php echo TranslationManager::t('phone'); ?></label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                <?php echo TranslationManager::t('user.user_active'); ?>
                            </label>
                        </div>
                        <div class="form-text"><?php echo TranslationManager::t('inactive'); ?> <?php echo TranslationManager::t('users'); ?> <?php echo TranslationManager::t('user.password_note'); ?></div>
                    </div>

                    <div class="mb-4">
                        <h5><?php echo TranslationManager::t('user.branch_assignments'); ?></h5>
                        <p class="text-muted small"><?php echo TranslationManager::t('user.select_branches'); ?></p>

                    <?php render_hook('users.create.form.sections'); ?>


                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                            <i class="fas fa-times me-1"></i><?php echo TranslationManager::t('cancel'); ?>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo TranslationManager::t('create'); ?> <?php echo TranslationManager::t('user.new_user'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('msg.info'); ?></h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-1"></i><?php echo TranslationManager::t('msg.info'); ?></h6>
                    <ul class="mb-0 small">
                        <li><?php echo TranslationManager::t('required_field'); ?> (*)</li>
                        <li><?php echo TranslationManager::t('user.username'); ?> <?php echo TranslationManager::t('form.required'); ?></li>
                        <li>Password <?php echo TranslationManager::t('form.required'); ?> (min 6)</li>
                        <li><?php echo TranslationManager::t('user.groups'); ?> <?php echo TranslationManager::t('form.optional'); ?></li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-1"></i><?php echo TranslationManager::t('msg.warning'); ?></h6>
                    <p class="mb-0 small">
                        <?php echo TranslationManager::t('user.password_note'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation
    document.getElementById('userForm').addEventListener('submit', function(e) {
        if (!validateForm('userForm')) {
            e.preventDefault();
            Swal.fire({
                title: '<?php echo TranslationManager::t('msg.error'); ?>',
                text: '<?php echo TranslationManager::t('required_field'); ?>',
                icon: 'error'
            });
        }
    });

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