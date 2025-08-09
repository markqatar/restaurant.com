<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="card">
  <div class="card-header d-flex justify-content-between">
  <h5><?php echo TranslationManager::t('supplier_product.form.product'); ?> - <?php echo htmlspecialchars($supplier_id); ?></h5>
  <button id="addSupplierProductBtn" type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierProductModal">
  <i class="fas fa-plus"></i> <?php echo TranslationManager::t('purchase_order.btn.add_row') ?: 'Add'; ?>
    </button>
  </div>
  <div class="card-body">
    <table id="supplierProductsTable" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th><?php echo TranslationManager::t('supplier_product.form.product'); ?></th>
          <th><?php echo TranslationManager::t('supplier_product.form.invoice_name'); ?></th>
          <th><?php echo TranslationManager::t('supplier_product.form.unit'); ?></th>
          <th><?php echo TranslationManager::t('supplier_product.form.quantity'); ?></th>
          <th><?php echo TranslationManager::t('supplier_product.form.price'); ?></th>
          <th><?php echo TranslationManager::t('purchase_order.field.actions'); ?></th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="supplierProductModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="supplierProductForm">
      <input type="hidden" name="id" id="spId">
      <input type="hidden" name="supplier_id" value="<?php echo $supplier_id; ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo TranslationManager::t('supplier_product.form.product'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label><?php echo TranslationManager::t('supplier_product.form.product'); ?></label>
            <select name="product_id" id="product_id" class="form-control select2" style="width:100%"></select>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('supplier_product.form.invoice_name'); ?></label>
            <input type="text" name="supplier_name" id="supplier_name" class="form-control">
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('supplier_product.form.unit'); ?></label>
            <select name="unit_id" id="unit_id" class="form-control select2" style="width:100%"></select>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('supplier_product.category'); ?></label>
            <select name="category_id" id="category_id" class="form-select">
              <option value="">--</option>
              <?php
              try {
                $db = Database::getInstance()->getConnection();
                $cStmt = $db->query("SELECT id, slug, name_en FROM supplier_product_categories WHERE is_active=1 ORDER BY name_en");
                foreach ($cStmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
                    echo '<option value="'.(int)$c['id'].'">'.htmlspecialchars($c['name_en']).'</option>';
                }
              } catch (Exception $e) {}
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('supplier_product.form.base_quantity'); ?></label>
            <input type="number" step="0.0001" min="0.0001" name="base_quantity" id="base_quantity" class="form-control" value="1">
            <small class="text-muted">1 box = 12 pcs</small>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('supplier_product.form.quantity'); ?></label>
            <input type="number" step="0.01" name="quantity" id="quantity" class="form-control">
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('supplier_product.form.price'); ?></label>
            <input type="number" step="0.01" name="price" id="price" class="form-control">
          </div>
          <?php
            $currenciesSetting = get_setting('currencies') ?: (get_setting('currency') ?: 'QAR');
            $currencyList = array_values(array_filter(array_map('trim', preg_split('/[,\n]+/',$currenciesSetting))));
            if(empty($currencyList)) { $currencyList=['QAR']; }
            $defaultCurrency = get_setting('currency') ?: (count($currencyList)?$currencyList[0]:'QAR');
            // Reorder to make default first
            usort($currencyList, function($a,$b) use($defaultCurrency){
              if($a===$defaultCurrency) return -1; if($b===$defaultCurrency) return 1; return 0;
            });
          ?>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('supplier_product.form.currency'); ?></label>
            <select name="currency" id="currency" class="form-select">
              <?php foreach($currencyList as $c): ?>
                <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $c===$defaultCurrency? 'selected':''; ?>><?php echo htmlspecialchars($c); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><?php echo TranslationManager::t('purchase_order.msg.confirm_send_button'); ?></button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
    const SUPPLIER_PRODUCTS_LINK_VARS = {
        supplierId: <?php echo (int)$supplier; ?>,
        urls: {
            datatable: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/datatable",
            store: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/store",
            get: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/get/",
            delete: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/delete",
            selectProducts: "<?php echo get_setting('site_url'); ?>/admin/suppliers/products/select2",
            selectUnits: "<?php echo get_setting('site_url'); ?>/admin/system/units/select2"
        },
        csrfToken: "<?php echo generate_csrf_token(); ?>",
        translations: {
            confirmDeleteTitle: '<?php echo TranslationManager::t("confirm_delete"); ?>',
            confirmDeleteText: '<?php echo TranslationManager::t("delete_supplier_product_confirm"); ?>',
            yesDelete: '<?php echo TranslationManager::t("yes_delete"); ?>',
            cancel: '<?php echo TranslationManager::t("cancel"); ?>',
            error: '<?php echo TranslationManager::t("error"); ?>'
        }
    };
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/suppliers/views/supplier_products/js/index.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>
