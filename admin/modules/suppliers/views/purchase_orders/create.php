<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5><?php echo TranslationManager::t('purchase_order.create_title'); ?></h5>
        <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('purchase_order.back_to_list'); ?>
        </a>
    </div>

    <div class="card-body">
        <form id="purchaseOrderForm">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="supplier_id" class="form-label"><?php echo TranslationManager::t('purchase_order.field.supplier'); ?></label>
                    <select name="supplier_id" id="supplier_id" class="form-control select2" required></select>
                </div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label"><?php echo TranslationManager::t('purchase_order.field.notes'); ?></label>
                <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
            </div>

            <hr>

            <h6><?php echo TranslationManager::t('purchase_order.add_products'); ?></h6>
            <table class="table table-bordered" id="orderItemsTable">
                <thead>
                    <tr>
                        <th style="width: 40%"><?php echo TranslationManager::t('purchase_order.field.product'); ?></th>
                        <th style="width: 20%"><?php echo TranslationManager::t('purchase_order.field.quantity'); ?></th>
                        <th style="width: 20%"><?php echo TranslationManager::t('purchase_order.field.unit'); ?></th>
                        <th style="width: 10%"><?php echo TranslationManager::t('purchase_order.field.actions'); ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button type="button" id="addItem" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> <?php echo TranslationManager::t('purchase_order.btn.add_row'); ?>
            </button>

            <hr>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo TranslationManager::t('purchase_order.btn.save_draft'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const CREATE_PO_VARS = {
    urls: {
        store: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/store",
        suppliersSelect: "<?php echo get_setting('site_url'); ?>/admin/suppliers/suppliers/select",
        productsSelect: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/select",
        unitsSelect: "<?php echo get_setting('site_url'); ?>/admin/system/units/select"
    },
    csrfToken: "<?php echo generate_csrf_token(); ?>"
};
</script>

<?php
$pageScripts = [
    get_setting('site_url') . '/admin/modules/suppliers/views/purchase_orders/js/create.js'
];
require_once get_setting('base_path') . 'admin/layouts/footer.php';
?>