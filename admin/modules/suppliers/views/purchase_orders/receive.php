<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>
<div class="page-breadcrumb d-flex align-items-center mb-3">
  <h1 class="h4"><i class="fas fa-inbox me-2"></i><?php echo TranslationManager::t('purchase_order.receive_title'); ?> #<?php echo (int)$order['id']; ?>
    <small class="text-muted ms-2">(<?php echo htmlspecialchars($order['branch_name'] ?? ''); ?>)</small>
  </h1>
  <div class="ms-auto">
    <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/suppliers/purchaseorders'; ?>" class="btn btn-outline-secondary btn-sm">
  <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('purchase_order.back_to_list'); ?>
    </a>
    <?php if (!empty($order['barcode_count'])): ?>
    <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/barcodes/<?php echo (int)$order['id']; ?>" class="btn btn-outline-primary btn-sm ms-1">
      <i class="fas fa-barcode"></i> <?php echo TranslationManager::t('purchase_order.barcode.title'); ?>
    </a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-body">
  <form id="receiveForm" method="post" enctype="multipart/form-data" action="<?php echo get_setting('site_url', 'http://localhost') . '/admin/suppliers/purchaseorders/receiveSubmit'; ?>">
      <?php echo csrf_token_field(); ?>
      <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="form-label fw-semibold"><?php echo TranslationManager::t('purchase_order.field.supplier_reference'); ?></label>
          <input type="text" name="supplier_reference" class="form-control" value="<?php echo htmlspecialchars($order['supplier_reference'] ?? ''); ?>" placeholder="REF-1234">
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold"><?php echo TranslationManager::t('purchase_order.field.order_discount'); ?></label>
          <input type="number" step="0.01" name="order_discount" class="form-control" value="<?php echo htmlspecialchars($order['discount'] ?? ''); ?>" placeholder="0.00">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Supplier PDF</label>
          <input type="file" name="supplier_invoice_pdf" accept="application/pdf" class="form-control">
          <?php if(!empty($order['supplier_invoice_pdf'])): ?>
            <small class="d-block mt-1"><a target="_blank" href="<?php echo get_setting('site_url').'/'.ltrim($order['supplier_invoice_pdf'],'/'); ?>">Existing PDF</a></small>
          <?php endif; ?>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th><?php echo TranslationManager::t('purchase_order.field.product'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.ordered_qty'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.price'); ?> (U)</th>
              <th>Tot.</th>
              <th><?php echo TranslationManager::t('purchase_order.field.discount'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.expiry'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.received_qty'); ?></th>
              <th><?php echo TranslationManager::t('purchase_order.field.barcode'); ?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $it): ?>
            <tr data-item-id="<?php echo $it['id']; ?>" data-qty="<?php echo (float)$it['quantity']; ?>" data-requires-expiry="<?php echo (int)($it['requires_expiry'] ?? 0); ?>">
              <td><?php echo htmlspecialchars($it['product_name']); ?></td>
              <td><?php echo (float)$it['quantity']; ?></td>
              <td style="width:120px">
                <input type="number" step="0.01" name="price[<?php echo $it['id']; ?>]" class="form-control po-price"
                  value="<?php echo htmlspecialchars($it['price'] ?? ''); ?>" placeholder="<?php echo TranslationManager::t('purchase_order.placeholder.price_per_unit'); ?>">
              </td>
              <td style="width:120px">
                <input type="number" step="0.01" name="line_total[<?php echo $it['id']; ?>]" class="form-control po-line-total" value="">
              </td>
              <td style="width:150px">
                <div class="input-group">
                  <input type="number" step="0.01" name="discount[<?php echo $it['id']; ?>]" class="form-control po-discount"
                    value="<?php echo htmlspecialchars($it['discount'] ?? ''); ?>" placeholder="<?php echo TranslationManager::t('purchase_order.placeholder.discount'); ?>">
                  <select name="discount_type[<?php echo $it['id']; ?>]" class="form-select po-discount-type" style="max-width:70px">
                    <option value="val" <?php echo (isset($it['discount_type']) && $it['discount_type']==='val')? 'selected':''; ?>>€</option>
                    <option value="pct" <?php echo (isset($it['discount_type']) && $it['discount_type']==='pct')? 'selected':''; ?>>%</option>
                  </select>
                </div>
              </td>
              <td style="width:170px">
                <input type="text" name="expiry[<?php echo $it['id']; ?>]" class="form-control po-expiry"
                       value="<?php echo htmlspecialchars($it['expiry_date'] ?? ''); ?>" placeholder="YYYY-MM-DD">
              </td>
              <td style="width:110px">
                <input type="number" step="1" min="0" name="received_qty[<?php echo $it['id']; ?>]" class="form-control po-received"
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
        <div class="row g-3 mb-3 justify-content-end">
          <div class="col-md-3">
            <div class="border rounded p-2 bg-light small">
              <div class="d-flex justify-content-between"><span><?php echo TranslationManager::t('purchase_order.summary.subtotal'); ?></span><strong id="poSubtotal">0.00</strong></div>
              <div class="d-flex justify-content-between"><span><?php echo TranslationManager::t('purchase_order.summary.line_discounts'); ?></span><strong id="poLineDiscounts">0.00</strong></div>
              <div class="d-flex justify-content-between"><span><?php echo TranslationManager::t('purchase_order.summary.order_discount_pct'); ?></span><strong id="poOrderDiscountPct">0</strong></div>
              <div class="d-flex justify-content-between"><span><?php echo TranslationManager::t('purchase_order.summary.order_discount_val'); ?></span><strong id="poOrderDiscountVal">0.00</strong></div>
              <hr class="my-2" />
              <div class="d-flex justify-content-between"><span><?php echo TranslationManager::t('purchase_order.summary.net_total'); ?></span><strong id="poNetTotal" class="text-primary">0.00</strong></div>
            </div>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-check me-1"></i> <?php echo TranslationManager::t('purchase_order.btn.confirm_receive'); ?>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function initExpiryPickers(){
  document.querySelectorAll('.po-expiry').forEach(inp=>{
    inp.addEventListener('focus',()=>{ inp.type='date'; });
    inp.addEventListener('blur',()=>{ if(!inp.value) inp.type='text'; });
  });
}
function recalcLine(row){
  const qty = parseFloat(row.dataset.qty)||0;
  const priceInput = row.querySelector('.po-price');
  const totalInput = row.querySelector('.po-line-total');
  if(!priceInput || !totalInput) return;
  const price = parseFloat(priceInput.value)||0;
  const total = parseFloat(totalInput.value)||0;
  if(document.activeElement===priceInput){
    totalInput.value = (price*qty).toFixed(2);
  } else if(document.activeElement===totalInput){
    priceInput.value = qty>0 ? (total/qty).toFixed(4): priceInput.value;
  } else {
    totalInput.value = (price*qty).toFixed(2);
  }
}
function bindCalc(){
  document.querySelectorAll('tr[data-item-id]').forEach(tr=>{
    const price = tr.querySelector('.po-price');
    const total = tr.querySelector('.po-line-total');
    if(price && total){
      ['input','change','blur'].forEach(ev=>price.addEventListener(ev,()=>recalcLine(tr)));
      ['input','change','blur'].forEach(ev=>total.addEventListener(ev,()=>recalcLine(tr)));
      recalcLine(tr);
    }
    if(tr.dataset.requiresExpiry==='0'){
      const expiryCell = tr.querySelector('.po-expiry');
      if(expiryCell){ expiryCell.closest('td').style.opacity=0.35; expiryCell.placeholder='N/A'; expiryCell.disabled=true; }
    }
  });
}
function calcTotals(){
  let subtotal=0, lineDisc=0; const rows=document.querySelectorAll('tr[data-item-id]');
  rows.forEach(tr=>{
    const qty=parseFloat(tr.dataset.qty)||0;
    const price=parseFloat(tr.querySelector('.po-price')?.value)||0;
    const lineTotal=price*qty; subtotal += lineTotal;
    const discInput=tr.querySelector('.po-discount');
    const discType=tr.querySelector('.po-discount-type')?.value||'val';
    let discVal=parseFloat(discInput?.value)||0;
    if(discVal>0){
      if(discType==='pct'){
        discVal = lineTotal * (discVal/100);
      }
      lineDisc += discVal;
    }
  });
  const orderDiscPct=parseFloat(document.querySelector('input[name="order_discount"]')?.value)||0;
  const orderDiscVal = (subtotal - lineDisc) * (orderDiscPct/100);
  const net = subtotal - lineDisc - orderDiscVal;
  const f=n=>n.toFixed(2);
  document.getElementById('poSubtotal').textContent=f(subtotal);
  document.getElementById('poLineDiscounts').textContent=f(lineDisc);
  document.getElementById('poOrderDiscountPct').textContent=orderDiscPct.toFixed(2)+'%';
  document.getElementById('poOrderDiscountVal').textContent=f(orderDiscVal);
  document.getElementById('poNetTotal').textContent=f(net);
}
function bindTotals(){
  document.querySelectorAll('.po-price, .po-line-total, input[name^="discount["], input[name="order_discount"]').forEach(el=>{
    ['input','change','blur'].forEach(ev=>el.addEventListener(ev, ()=>{recalcLine(el.closest('tr[data-item-id]')); calcTotals();}));
  });
  calcTotals();
}
function highlightQtyDiff(){
  document.querySelectorAll('tr[data-item-id]').forEach(tr=>{
    const ordered = parseFloat(tr.dataset.qty)||0;
    const received = parseFloat(tr.querySelector('.po-received')?.value)||0;
    if(received !== ordered){
      tr.classList.add('table-warning');
    } else {
      tr.classList.remove('table-warning');
    }
  });
}
document.addEventListener('DOMContentLoaded', function(){
  initExpiryPickers();
  bindCalc();
  bindTotals();
  highlightQtyDiff();
  document.querySelectorAll('.po-received').forEach(inp=>['input','change','blur'].forEach(ev=>inp.addEventListener(ev,()=>highlightQtyDiff())));
});
document.getElementById('receiveForm').addEventListener('submit', function(e){
  let hasError=false; let expiryError=false;
  document.querySelectorAll('.po-price').forEach(p=>{if(p.value!=='' && parseFloat(p.value)<0){hasError=true; p.classList.add('is-invalid');}});
  // expiry required check
  document.querySelectorAll('tr[data-item-id]').forEach(tr=>{
    if(tr.dataset.requiresExpiry==='1'){
      const exp = tr.querySelector('.po-expiry');
      if(exp && !exp.disabled){
        const v = exp.value.trim();
        if(!v){ expiryError=true; exp.classList.add('is-invalid'); }
      }
    }
  });
  if(hasError || expiryError){
    e.preventDefault();
    const msg = (hasError? '<?php echo addslashes(TranslationManager::t('purchase_order.validation.fix_invalid_prices')); ?>' : '')
      + (expiryError? '\n<?php echo addslashes(TranslationManager::t('purchase_order.validation.expiry_required')); ?>' : '');
    alert(msg.trim());
  }
});
</script>

<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>