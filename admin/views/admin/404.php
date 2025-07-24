<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i><?php echo t('page_not_found'); ?>
                </h1>
            </div>
            
            <div class="alert alert-warning">
                <h4 class="alert-heading"><?php echo t('error.404_title'); ?></h4>
                <p><?php echo t('error.404_message'); ?></p>
                <hr>
                <p class="mb-0">
                    <a href="<?php echo admin_url('index'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-home me-1"></i><?php echo t('back_to_dashboard'); ?>
                    </a>
                </p>
            </div>
        </main>
    </div>
</div>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>