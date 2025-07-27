<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="container mt-5 text-center">
    <h1 class="display-4 text-warning">
        <i class="fas fa-ban"></i> <?php echo TranslationManager::t('error.403_title'); ?>
    </h1>
    <p class="lead mb-4"><?php echo TranslationManager::t('error.403_message'); ?></p>
    <a href="<?php echo get_setting('site_url'); ?>/admin" class="btn btn-primary">
        <i class="fas fa-home"></i> <?php echo TranslationManager::t('back_to_dashboard'); ?>
    </a>
</div>

<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>
