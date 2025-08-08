<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>
<div class="page-breadcrumb d-flex align-items-center mb-3">
  <h1 class="h4"><i class="fas fa-inbox me-2"></i><?php echo TranslationManager::t('purchase_order.receive_title'); ?> #<?php echo (int)$order['id']; ?></h1>
  <div class="ms-auto">
    <a href="<?php echo admin_url('purchase_orders'); ?>" class="btn btn-outline-secondary btn-sm">
  <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('purchase_order.back_to_list'); ?>
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form id="receiveForm" method="post" action="<?php echo admin_url('purchase_orders/receiveSubmit'); ?>">
      <?php echo csrf_token_field(); ?>
      <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">

      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th><?php echo TranslationManager::t('purchase_order.field.product'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.sku'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.ordered_qty'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.price'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.discount'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.expiry'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.received_qty'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.barcode'); ?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?php echo htmlspecialchars($it['product_name']); ?></td>
              <td><?php echo htmlspecialchars($it['sku'] ?? ''); ?></td>
              <td><?php echo (float)$it['quantity']; ?></td>
              <td style="width:120px">
      <input type="number" step="0.01" name="price[<?php echo $it['id']; ?>]" class="form-control"
        value="<?php echo htmlspecialchars($it['price'] ?? ''); ?>" placeholder="<?php echo TranslationManager::t('purchase_order.placeholder.price_per_unit'); ?>">
              </td>
              <td style="width:120px">
      <input type="number" step="0.01" name="discount[<?php echo $it['id']; ?>]" class="form-control"
        value="<?php echo htmlspecialchars($it['discount'] ?? ''); ?>" placeholder="<?php echo TranslationManager::t('purchase_order.placeholder.discount'); ?>">
              </td>
              <td style="width:170px">
                <input type="date" name="expiry[<?php echo $it['id']; ?>]" class="form-control"
                       value="<?php echo htmlspecialchars($it['expiry_date'] ?? ''); ?>">
              </td>
              <td style="width:110px">
                <input type="number" step="1" min="0" name="received_qty[<?php echo $it['id']; ?>]" class="form-control"
                       value="<?php echo (float)($it['quantity']); ?>">
              </td>
              <td class="text-center">
                <input type="checkbox" name="gen_barcode[<?php echo $it['id']; ?>]" value="1"
                       <?php echo !empty($it['generate_barcode']) ? 'checked' : ''; ?>>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="text-end mt-3">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-check me-1"></i> <?php echo TranslationManager::t('purchase_order.btn.confirm_receive'); ?>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('receiveForm').addEventListener('submit', function(e){
  // puoi aggiungere validazioni custom se vuoi
});
</script>

<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>