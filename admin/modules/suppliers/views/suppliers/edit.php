<?php require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php'; ?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2"><i class="fas fa-edit me-2"></i><?php echo TranslationManager::t('supplier.edit_supplier'); ?></h1>
    <div class="btn-toolbar ms-auto">
        <a href="<?php echo admin_url('suppliers'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back_to_list'); ?>
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <?php echo TranslationManager::t('supplier.edit'); ?>: <?php echo htmlspecialchars($supplier['name']); ?>
        </h6>
        <small class="text-muted">
            <?php echo TranslationManager::t('id'); ?>: <?php echo $supplier['id']; ?> |
            <?php echo TranslationManager::t('created_at'); ?>: <?php echo htmlspecialchars($supplier['created_at']); ?>
        </small>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo get_setting('site_url', 'http://localhost') . '/admin/suppliers/suppliers/update/' . $supplier['id']; ?>">
            <?php echo csrf_token_field(); ?>

            <div class="row">
                <!-- Informazioni Generali -->
                <div class="col-md-6">
                    <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-1"></i><?php echo TranslationManager::t('general_information'); ?></h5>

                    <div class="mb-3">
                        <label for="name" class="form-label"><?php echo TranslationManager::t('supplier.name'); ?> *</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?php echo htmlspecialchars($supplier['name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="address_line1" class="form-label"><?php echo TranslationManager::t('address_line1'); ?></label>
                        <input type="text" class="form-control" id="address_line1" name="address_line1"
                               value="<?php echo htmlspecialchars($supplier['address_line1'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="address_line2" class="form-label"><?php echo TranslationManager::t('address_line2'); ?></label>
                        <input type="text" class="form-control" id="address_line2" name="address_line2"
                               value="<?php echo htmlspecialchars($supplier['address_line2'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="zip_code" class="form-label"><?php echo TranslationManager::t('zip_code'); ?></label>
                        <input type="text" class="form-control" id="zip_code" name="zip_code"
                               value="<?php echo htmlspecialchars($supplier['zip_code'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Contatti -->
                <div class="col-md-6">
                    <h5 class="text-success mb-3"><i class="fas fa-phone me-1"></i><?php echo TranslationManager::t('contact_information'); ?></h5>

                    <div class="mb-3">
                        <label for="email1" class="form-label"><?php echo TranslationManager::t('email'); ?> 1</label>
                        <input type="email" class="form-control" id="email1" name="email1"
                               value="<?php echo htmlspecialchars($supplier['email1'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email2" class="form-label"><?php echo TranslationManager::t('email'); ?> 2</label>
                        <input type="email" class="form-control" id="email2" name="email2"
                               value="<?php echo htmlspecialchars($supplier['email2'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tel1" class="form-label"><?php echo TranslationManager::t('phone'); ?> 1</label>
                        <input type="tel" class="form-control" id="tel1" name="tel1"
                               value="<?php echo htmlspecialchars($supplier['tel1'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tel2" class="form-label"><?php echo TranslationManager::t('phone'); ?> 2</label>
                        <input type="tel" class="form-control" id="tel2" name="tel2"
                               value="<?php echo htmlspecialchars($supplier['tel2'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Localizzazione -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="country_id" class="form-label"><?php echo TranslationManager::t('country'); ?></label>
                    <select class="form-select" id="country_id" name="country_id">
                        <option value=""><?php echo TranslationManager::t('select_country'); ?></option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo $country['id']; ?>" <?php echo ($supplier['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($country['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="city_id" class="form-label"><?php echo TranslationManager::t('city'); ?></label>
                    <select class="form-select" id="city_id" name="city_id">
                        <?php if (!empty($supplier['city_id']) && !empty($supplier['city_name'])): ?>
                            <option value="<?php echo $supplier['city_id']; ?>" selected>
                                <?php echo htmlspecialchars($supplier['city_name']); ?>
                            </option>
                        <?php else: ?>
                            <option value=""><?php echo TranslationManager::t('select_country_first'); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <label for="notes" class="form-label"><?php echo TranslationManager::t('notes'); ?></label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($supplier['notes'] ?? ''); ?></textarea>
            </div>

            <!-- Stato -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                       <?php echo ($supplier['is_active'] == 1) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_active">
                    <strong><?php echo TranslationManager::t('supplier.active_supplier'); ?></strong>
                </label>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i><?php echo TranslationManager::t('btn.save_changes'); ?>
                </button>
                <a href="<?php echo admin_url('suppliers'); ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i><?php echo TranslationManager::t('cancel'); ?>
                </a>
            </div>
        </form>
    </div>
</div>
<script>
    const SUPPLIERS_FORM_VARS = {
        translations: {
            loading: '<?php echo TranslationManager::t("loading"); ?>...',
            searchCity: '<?php echo TranslationManager::t("search_city"); ?>',
            selectCountryFirst: '<?php echo TranslationManager::t("select_country_first"); ?>'
        }
    };
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/suppliers/views/suppliers/js/edit.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>