<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-truck me-2"></i><?php echo TranslationManager::t('supplier.details'); ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <a href="suppliers" class="btn btn-outline-secondary btn-sm me-2">
            <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('back'); ?>
        </a>
    <?php if (can('suppliers', 'update')): ?>
            <a href="<?php echo get_setting('site_url', 'http://localhost') ?>/admin/suppliers/suppliers/edit/<?php echo $supplier['id']; ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> <?php echo TranslationManager::t('edit'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- Colonna Principale -->
    <div class="col-lg-8">
        <!-- Informazioni Principali -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i><?php echo TranslationManager::t('supplier.main_info'); ?>
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Colonna Sinistra -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small"><?php echo TranslationManager::t('name'); ?></label>
                            <div class="fw-bold"><?php echo htmlspecialchars($supplier['name']); ?></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small"><?php echo TranslationManager::t('supplier.contact_person'); ?></label>
                            <div><?php echo $supplier['contact_person'] ? htmlspecialchars($supplier['contact_person']) : '<span class="text-muted">N/A</span>'; ?></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Email 1</label>
                            <div><?php echo $supplier['email1'] ? '<a href="mailto:' . htmlspecialchars($supplier['email1']) . '"><i class="fas fa-envelope me-1"></i>' . htmlspecialchars($supplier['email1']) . '</a>' : '<span class="text-muted">N/A</span>'; ?></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small">Email 2</label>
                            <div><?php echo $supplier['email2'] ? '<a href="mailto:' . htmlspecialchars($supplier['email2']) . '"><i class="fas fa-envelope me-1"></i>' . htmlspecialchars($supplier['email2']) . '</a>' : '<span class="text-muted">N/A</span>'; ?></div>
                        </div>
                    </div>

                    <!-- Colonna Destra -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small"><?php echo TranslationManager::t('phone'); ?> 1</label>
                            <div><?php echo $supplier['tel1'] ? '<a href="tel:' . htmlspecialchars($supplier['tel1']) . '"><i class="fas fa-phone me-1"></i>' . htmlspecialchars($supplier['tel1']) . '</a>' : '<span class="text-muted">N/A</span>'; ?></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small"><?php echo TranslationManager::t('phone'); ?> 2</label>
                            <div><?php echo $supplier['tel2'] ? '<a href="tel:' . htmlspecialchars($supplier['tel2']) . '"><i class="fas fa-phone me-1"></i>' . htmlspecialchars($supplier['tel2']) . '</a>' : '<span class="text-muted">N/A</span>'; ?></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small"><?php echo TranslationManager::t('address'); ?></label>
                            <div>
                                <?php
                                echo $supplier['address_line1'] ? htmlspecialchars($supplier['address_line1']) . '<br>' : '';
                                echo $supplier['address_line2'] ? htmlspecialchars($supplier['address_line2']) . '<br>' : '';
                                echo $supplier['zip_code'] ? htmlspecialchars($supplier['zip_code']) . '<br>' : '';
                                echo $supplier['city_name'] ? htmlspecialchars($supplier['city_name']) . '<br>' : '';
                                echo $supplier['country_name'] ? htmlspecialchars($supplier['country_name']) : '';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    <!-- Colonna Laterale -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info me-2"></i><?php echo TranslationManager::t('status'); ?>
                </h6>
            </div>
            <div class="card-body">
                <p>
                    <strong><?php echo TranslationManager::t('status'); ?>:</strong>
                    <span class="badge <?php echo $supplier['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo $supplier['is_active'] ? TranslationManager::t('active') : TranslationManager::t('inactive'); ?>
                    </span>
                </p>
                <p><strong><?php echo TranslationManager::t('created'); ?>:</strong> <?php echo format_date($supplier['created_at']); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-address-book me-2"></i><?php echo TranslationManager::t('supplier.additional_contacts'); ?>
                </h6>
                <?php if (can('suppliers', 'contact.create')): ?>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addContactModal">
                        <i class="fas fa-plus"></i> <?php echo TranslationManager::t('add_contact'); ?>
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="contactsTable" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th><?php echo TranslationManager::t('name'); ?></th>
                                <th>Email</th>
                                <th><?php echo TranslationManager::t('phone'); ?></th>
                                <th><?php echo TranslationManager::t('primary'); ?></th>
                                <th><?php echo TranslationManager::t('actions'); ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
<div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="addContactForm">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="supplier_id" value="<?php echo (int)$supplier['id']; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo TranslationManager::t('add_contact'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo TranslationManager::t('first_name'); ?></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo TranslationManager::t('last_name'); ?></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email 1</label>
                            <input type="email" name="email1" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email 2</label>
                            <input type="email" name="email2" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?php echo TranslationManager::t('phone'); ?> 1</label>
                            <input type="text" name="tel1" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?php echo TranslationManager::t('phone'); ?> 2</label>
                            <input type="text" name="tel2" class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label><?php echo TranslationManager::t('notes'); ?></label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                        <div class="col-12 mb-3 form-check">
                            <input type="checkbox" name="is_primary" class="form-check-input" value="1">
                            <label class="form-check-label"><?php echo TranslationManager::t('primary_contact'); ?></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo TranslationManager::t('save'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="viewContactModal" tabindex="-1" aria-labelledby="viewContactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo TranslationManager::t('view_contact'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Dati caricati via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo TranslationManager::t('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    const SUPPLIER_CONTACTS_VARS = {
        supplierId: <?php echo (int)$supplier['id']; ?>,
        urls: {
            datatable: "<?php echo get_setting('site_url'); ?>/admin/suppliers/suppliercontacts/datatable",
            store: "<?php echo get_setting('site_url'); ?>/admin/suppliers/suppliercontacts/store",
            delete: "<?php echo get_setting('site_url'); ?>/admin/suppliers/suppliercontacts/delete",
            getContact: "<?php echo get_setting('site_url'); ?>/admin/suppliers/suppliercontacts/get/"
        },
        csrfToken: "<?php echo generate_csrf_token(); ?>",
        translations: {
            primaryBadge: '<?php echo TranslationManager::t('primary'); ?>',
            confirmDeleteTitle: '<?php echo TranslationManager::t('confirm_delete'); ?>',
            confirmDeleteText: '<?php echo TranslationManager::t('delete_contact_confirm'); ?>',
            yesDelete: '<?php echo TranslationManager::t('yes_delete'); ?>',
            cancel: '<?php echo TranslationManager::t('cancel'); ?>',
            error: '<?php echo TranslationManager::t('error'); ?>'
        }
    };
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/suppliers/views/suppliers/js/view.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>