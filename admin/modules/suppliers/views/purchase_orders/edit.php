<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h5><?php echo TranslationManager::t('purchase_order.create_title'); ?> #<?php echo (int)$order['id']; ?></h5>
    <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('purchase_order.back_to_list'); ?>
    </a>
  </div>
  <div class="card-body">
    <form id="purchaseOrderEditForm">
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label"><?php echo TranslationManager::t('purchase_order.field.supplier'); ?></label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['supplier_name']); ?>" disabled>
          <input type="hidden" name="supplier_id" value="<?php echo (int)$order['supplier_id']; ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label"><?php echo TranslationManager::t('branch.branch_name'); ?></label>
          <?php
            $userBranches = getUserBranches($_SESSION['user_id']);
            $currentBranchId = $order['branch_id'] ?? null;
            if (count($userBranches) <= 1) {
                $branchId = $currentBranchId ?: (count($userBranches)? (int)$userBranches[0]['id']: '');
                echo '<input type="hidden" name="branch_id" value="' . $branchId . '">';
                $branchName = '';
                if ($branchId) {
                    foreach ($userBranches as $b) { if ($b['id']==$branchId) { $branchName = $b['name']; break; } }
                }
                echo '<input type="text" class="form-control" value="' . htmlspecialchars($branchName) . '" disabled>';
            } else {
                echo getBranchSelector($_SESSION['user_id'], $currentBranchId, 'branch_id', true);
            }
          ?>
        </div>
      </div>

      <hr>
      <h6><?php echo TranslationManager::t('purchase_order.add_products'); ?></h6>
      <table class="table table-bordered" id="orderItemsTable">
        <thead>
          <tr>
            <th style="width:40%"><?php echo TranslationManager::t('purchase_order.field.product'); ?></th>
            <th style="width:20%"><?php echo TranslationManager::t('purchase_order.field.quantity'); ?></th>
            <th style="width:20%"><?php echo TranslationManager::t('purchase_order.field.unit'); ?></th>
            <th style="width:10%"><?php echo TranslationManager::t('supplier_product.form.price'); ?></th>
            <th style="width:10%"><?php echo TranslationManager::t('supplier_product.form.currency'); ?></th>
            <th style="width:10%"><?php echo TranslationManager::t('purchase_order.field.actions'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items as $it): ?>
            <tr>
              <td>
                <select name="products[]" class="form-control select2 product-select" required data-initial-name="<?php echo htmlspecialchars($it['product_name']); ?>" data-initial-id="<?php echo (int)$it['product_id']; ?>">
                  <option value="<?php echo (int)$it['product_id']; ?>" selected><?php echo htmlspecialchars($it['product_name']); ?></option>
                </select>
              </td>
              <td>
                <input type="number" step="0.01" name="quantities[]" class="form-control" value="<?php echo (float)$it['quantity']; ?>" required>
              </td>
              <td>
                <select name="units[]" class="form-control select2 unit-select" disabled>
                  <option selected><?php echo htmlspecialchars($it['unit_name']); ?></option>
                </select>
                <input type="hidden" name="units[]" value="<?php echo (int)($it['unit_id'] ?? 0); ?>">
              </td>
              <td>
                 <input type="number" step="0.01" name="prices[]" class="form-control" value="<?php echo isset($it['price']) ? htmlspecialchars($it['price']) : ''; ?>">
              </td>
              <td>
                <select name="currencies[]" class="form-select">
                  <?php
                    $currenciesSetting = get_setting('currencies') ?: (get_setting('currency') ?: 'QAR');
                    $currencyList = array_values(array_filter(array_map('trim', preg_split('/[,\n]+/',$currenciesSetting))));
                    if(empty($currencyList)) $currencyList=['QAR'];
                    $defaultCurrency = get_setting('currency') ?: $currencyList[0];
                    usort($currencyList, function($a,$b) use($defaultCurrency){ if($a===$defaultCurrency) return -1; if($b===$defaultCurrency) return 1; return 0; });
                    $rowCurrency = $it['currency'] ?? $defaultCurrency;
                    foreach($currencyList as $c){
                      echo '<option value="'.htmlspecialchars($c).'" '.($c===$rowCurrency?'selected':'').'>'.htmlspecialchars($c).'</option>';
                    }
                  ?>
                </select>
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <button type="button" id="addItem" class="btn btn-sm btn-success">
        <i class="fas fa-plus"></i> <?php echo TranslationManager::t('purchase_order.btn.add_row'); ?>
      </button>
      <hr>
      <div class="text-end">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo TranslationManager::t('purchase_order.btn.save_draft'); ?></button>
      </div>
    </form>
  </div>
</div>
<script>
<?php
  $currenciesSetting = get_setting('currencies') ?: (get_setting('currency') ?: 'QAR');
  $currencyList = array_values(array_filter(array_map('trim', preg_split('/[,\n]+/',$currenciesSetting))));
  if(empty($currencyList)) $currencyList=['QAR'];
  $defaultCurrency = get_setting('currency') ?: $currencyList[0];
  usort($currencyList, function($a,$b) use($defaultCurrency){ if($a===$defaultCurrency) return -1; if($b===$defaultCurrency) return 1; return 0; });
?>
const EDIT_PO_VARS = {
  urls: {
    update: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/update/<?php echo (int)$order['id']; ?>",
    productsSelect: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/select",
  },
  csrfToken: "<?php echo generate_csrf_token(); ?>",
  supplierId: <?php echo (int)$order['supplier_id']; ?>,
  currencies: <?php echo json_encode($currencyList); ?>,
  defaultCurrency: "<?php echo $defaultCurrency; ?>",
  translations: {
  select_supplier_first: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.select_supplier_first')); ?>",
  generic_ok: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.generic_ok')); ?>",
  generic_error: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.generic_error')); ?>",
  product_placeholder: "<?php echo addslashes(TranslationManager::t('purchase_order.field.product')); ?>",
  unit_placeholder: "<?php echo addslashes(TranslationManager::t('purchase_order.field.unit')); ?>"
  }
};
</script>
<?php
$pageScripts = [
    get_setting('site_url') . '/admin/modules/suppliers/views/purchase_orders/js/edit.js'
];
require_once get_setting('base_path') . 'admin/layouts/footer.php';
?>
