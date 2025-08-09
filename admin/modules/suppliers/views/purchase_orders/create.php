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
                <div class="col-md-4">
                    <label for="branch_id" class="form-label"><?php echo TranslationManager::t('branch.branch_name'); ?></label>
                    <?php
                        $userBranches = getUserBranches($_SESSION['user_id']);
                        if (count($userBranches) <= 1) {
                            $branchId = count($userBranches) ? (int)$userBranches[0]['id'] : '';
                            echo '<input type="hidden" name="branch_id" value="' . $branchId . '">';
                            echo '<input type="text" class="form-control" value="' . (count($userBranches) ? htmlspecialchars($userBranches[0]['name']) : '---') . '" disabled>';
                        } else {
                            echo getBranchSelector($_SESSION['user_id'], null, 'branch_id', true);
                        }
                    ?>
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
                        <th style="width:15%"><?php echo TranslationManager::t('supplier_product.form.price'); ?> (<?php echo TranslationManager::t('purchase_order.placeholder.price_per_unit'); ?>)</th>
                        <th style="width:15%">Last</th>
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
<?php
    $currenciesSetting = get_setting('currencies') ?: (get_setting('currency') ?: 'QAR');
    $currencyList = array_values(array_filter(array_map('trim', preg_split('/[,\n]+/',$currenciesSetting))));
    if(empty($currencyList)) { $currencyList=['QAR']; }
    $defaultCurrency = get_setting('currency') ?: (count($currencyList)?$currencyList[0]:'QAR');
    usort($currencyList, function($a,$b) use($defaultCurrency){ if($a===$defaultCurrency) return -1; if($b===$defaultCurrency) return 1; return 0; });
?>
const CREATE_PO_VARS = {
    urls: {
        store: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/store",
        suppliersSelect: "<?php echo get_setting('site_url'); ?>/admin/suppliers/suppliers/select",
    productsSelect: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/select",
    lastPrice: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/lastprice" // new endpoint
        unitsSelect: "<?php echo get_setting('site_url'); ?>/admin/system/units/select"
    },
        csrfToken: "<?php echo generate_csrf_token(); ?>",
        currencies: <?php echo json_encode($currencyList); ?>,
        defaultCurrency: "<?php echo $defaultCurrency; ?>"
};
</script>
<script>
// Simple translations needed in JS
CREATE_PO_VARS.translations = {
    select_supplier_first: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.select_supplier_first')); ?>",
    generic_ok: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.generic_ok')); ?>",
    generic_error: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.generic_error')); ?>",
    product_placeholder: "<?php echo addslashes(TranslationManager::t('purchase_order.field.product')); ?>",
    unit_placeholder: "<?php echo addslashes(TranslationManager::t('purchase_order.field.unit')); ?>",
    last_price: "Last price"
};
</script>

<?php
$pageScripts = [
    get_setting('site_url') . '/admin/modules/suppliers/views/purchase_orders/js/create.js'
];
require_once get_setting('base_path') . 'admin/layouts/footer.php';
?>