<?php require_once get_setting('base_path').'admin/layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><?php echo TranslationManager::t('supplier_product.inventory.title'); ?></h5>
  <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers" class="btn btn-sm btn-secondary">&larr; <?php echo TranslationManager::t('purchase_order.back_to_list'); ?></a>
</div>

<div class="card">
  <div class="card-body">
    <form id="invFilter" class="row g-2 mb-3">
      <div class="col-auto">
  <input type="number" class="form-control" name="supplier_id" placeholder="ID" value="<?php echo (int)($_GET['supplier_id']??0); ?>" required>
      </div>
      <div class="col-auto">
  <button class="btn btn-primary btn-sm"><?php echo TranslationManager::t('purchase_order.msg.confirm_send_button'); ?></button>
      </div>
    </form>
    <div class="table-responsive">
      <table class="table table-bordered table-sm" id="invTable">
        <thead class="table-light">
          <tr>
            <th><?php echo TranslationManager::t('supplier_product.form.product'); ?></th>
            <th><?php echo TranslationManager::t('supplier_product.inventory.supplier_units'); ?></th>
            <th><?php echo TranslationManager::t('supplier_product.base_unit'); ?></th>
            <th><?php echo TranslationManager::t('supplier_product.inventory.base_unit_total'); ?></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
<script>
function loadInventory(){
  const sid = document.querySelector('input[name="supplier_id"]').value;
  if(!sid) return;
  fetch('<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/inventorySummary?supplier_id='+encodeURIComponent(sid))
    .then(r=>r.json())
    .then(rows=>{
      const tb=document.querySelector('#invTable tbody'); tb.innerHTML='';
      rows.forEach(r=>{
        const tr=document.createElement('tr');
        tr.innerHTML = `<td>${r.product_name}</td><td>${(r.total_supplier_units||0)}</td><td>${r.base_unit_name||'-'}</td><td>${(r.total_base_units||0)}</td>`;
        tb.appendChild(tr);
      });
    });
}
 document.getElementById('invFilter').addEventListener('submit', e=>{e.preventDefault(); loadInventory();});
 loadInventory();
</script>
<?php require_once get_setting('base_path').'admin/layouts/footer.php'; ?>
