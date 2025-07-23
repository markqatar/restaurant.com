<?php
$page_title = t('settings');
include __DIR__ . '/../../admin/includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= t('settings') ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php"><?= t('dashboard') ?></a></li>
        <li class="breadcrumb-item active"><?= t('settings') ?></li>
    </ol>

    <?php 
    if (function_exists('display_notification')) {
        display_notification(); 
    } elseif (function_exists('show_notifications')) {
        show_notifications();
    } elseif (isset($_SESSION['notifications'])) {
        foreach ($_SESSION['notifications'] as $notification) {
            echo '<div class="alert alert-' . $notification['type'] . ' alert-dismissible fade show" role="alert">';
            echo $notification['message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        $_SESSION['notifications'] = [];
    }
    ?>

    <!-- Settings Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= ($section ?? 'general') === 'general' ? 'active' : '' ?>" 
                               href="?section=general"><?= t('general_settings') ?></a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= ($section ?? '') === 'rooms' ? 'active' : '' ?>" 
                               href="?section=rooms"><?= t('rooms_management') ?></a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= ($section ?? '') === 'tables' ? 'active' : '' ?>" 
                               href="?section=tables"><?= t('tables_management') ?></a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <?php if (($section ?? 'general') === 'general'): ?>
                        <!-- General Settings Form -->
                        <form method="POST" enctype="multipart/form-data">
                            <?= csrf_token_field() ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label"><?= t('site_name') ?> *</label>
                                        <input type="text" class="form-control" id="site_name" name="site_name" 
                                               value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_url" class="form-label"><?= t('site_url') ?> *</label>
                                        <input type="url" class="form-control" id="site_url" name="site_url" 
                                               value="<?= htmlspecialchars($settings['site_url'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label"><?= t('site_logo') ?></label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <div class="form-text"><?= t('upload_logo') ?></div>
                                    </div>
                                    
                                    <?php if (!empty($settings['logo_path'])): ?>
                                        <div class="mb-3" id="current-logo-section">
                                            <label class="form-label"><?= t('current_logo') ?></label>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= site_url('uploads/' . $settings['logo_path']) ?>" 
                                                     alt="<?= t('current_logo') ?>" class="img-thumbnail" style="max-height: 100px;">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteLogo()">
                                                    <i class="fas fa-trash"></i> <?= t('delete_logo') ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="mb-3" id="no-logo-section">
                                            <small class="text-muted"><?= t('no_logo') ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i><?= t('save') ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteLogo() {
    if (confirm('<?= t('confirm_delete_logo') ?>')) {
        fetch('../../controllers/LogoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete_logo'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide current logo section and show no logo message
                document.getElementById('current-logo-section').style.display = 'none';
                
                // Create or show no-logo section
                let noLogoSection = document.getElementById('no-logo-section');
                if (!noLogoSection) {
                    noLogoSection = document.createElement('div');
                    noLogoSection.id = 'no-logo-section';
                    noLogoSection.className = 'mb-3';
                    noLogoSection.innerHTML = '<small class="text-muted"><?= t('no_logo') ?></small>';
                    document.getElementById('current-logo-section').parentNode.appendChild(noLogoSection);
                } else {
                    noLogoSection.style.display = 'block';
                }
                
                alert('<?= t('logo_deleted_successfully') ?>');
            } else {
                alert('<?= t('error') ?>: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('<?= t('error_occurred') ?>');
        });
    }
}
</script>

<?php include get_setting('base_path', '/var/www/html') . 'admin/includes/footer.php'; ?>