<?php require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> <?php echo TranslationManager::t('branch.new_branch'); ?>
        </h1>
        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/branches'; ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('back_to_list'); ?>
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('branch.section.branch_info'); ?></h6>
        </div>
        <div class="card-body">
            <form method="POST" action="branches/store">
                <?php echo csrf_token_field(); ?>
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-info-circle"></i> <?php echo TranslationManager::t('branch.section.general_info'); ?>
                        </h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label"><?php echo TranslationManager::t('branch.branch_name'); ?> *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="referente" class="form-label"><?php echo TranslationManager::t('branch.manager'); ?></label>
                            <input type="text" class="form-control" id="referente" name="referente">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label"><?php echo TranslationManager::t('address'); ?></label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-success">
                            <i class="fas fa-phone"></i> <?php echo TranslationManager::t('branch.section.contact_info'); ?>
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email1" class="form-label"><?php echo TranslationManager::t('branch.field.email_primary'); ?></label>
                                    <input type="email" class="form-control" id="email1" name="email1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email2" class="form-label"><?php echo TranslationManager::t('branch.field.email_secondary'); ?></label>
                                    <input type="email" class="form-control" id="email2" name="email2">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tel1" class="form-label"><?php echo TranslationManager::t('branch.field.phone_primary'); ?></label>
                                    <input type="tel" class="form-control" id="tel1" name="tel1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tel2" class="form-label"><?php echo TranslationManager::t('branch.field.phone_secondary'); ?></label>
                                    <input type="tel" class="form-control" id="tel2" name="tel2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Geographic Information -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="mb-3 text-info">
                            <i class="fas fa-map-marker-alt"></i> <?php echo TranslationManager::t('branch.section.location'); ?>
                        </h5>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="country_id" class="form-label"><?php echo TranslationManager::t('branch.field.state_region'); ?></label>
                            <select class="form-select select2" id="country_id" name="country_id">
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="city_id" class="form-label"><?php echo TranslationManager::t('branch.field.city'); ?></label>
                            <select class="form-select select2" id="city_id" name="city_id" disabled>
                                <option value=""><?php echo TranslationManager::t('branch.placeholder.select_state_first'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Status -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                <strong><?php echo TranslationManager::t('branch.field.active_branch'); ?></strong>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo TranslationManager::t('branch.btn.create_branch'); ?>
                    </button>
                    <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/shop/branches'; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> <?php echo TranslationManager::t('cancel'); ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select2 JS -->

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/shop/views/branches/js/create.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>