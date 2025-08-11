<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>
<style>
.barcode-grid {display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-top:20px;}
.barcode-item {border:1px solid #ccc;padding:8px;text-align:center;font-size:11px;border-radius:4px;background:#fff;}
.barcode-item img {max-width:100%;height:auto;}
.print-actions {margin-top:15px;display:flex;gap:10px;}
@media print {
    body {background:#fff;}
    .no-print {display:none !important;}
    .barcode-item {page-break-inside:avoid;}
}
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo TranslationManager::t('purchase_order.barcode.title') . ' #' . (int)$order['id']; ?></h5>
        <div class="no-print">
            <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/view/<?php echo (int)$order['id']; ?>" class="btn btn-secondary btn-sm">&larr; <?php echo TranslationManager::t('purchase_order.back_to_list'); ?></a>
        </div>
    </div>
    <div class="card-body">
        <p class="no-print mb-2 text-muted">
            <?php echo TranslationManager::t('purchase_order.barcode.generated_total'); ?>: <strong><?php echo (int)$order['barcode_count']; ?></strong>
        </p>
            <div class="no-print print-actions flex-wrap">
                <button class="btn btn-primary btn-sm" id="btnPrintSelected"><i class="fas fa-print"></i> <?php echo TranslationManager::t('purchase_order.barcode.print_button'); ?></button>
                <button class="btn btn-outline-secondary btn-sm" id="btnSelectAll">Select All</button>
                <button class="btn btn-outline-secondary btn-sm" id="btnClearSelection">Clear</button>
                <div class="d-flex align-items-center gap-2 flex-wrap ms-2">
                    <input type="text" id="filterProduct" class="form-control form-control-sm" placeholder="Product" style="max-width:160px;">
                    <input type="text" id="filterSupplier" class="form-control form-control-sm" placeholder="Supplier" style="max-width:160px;">
                    <input type="date" id="filterExpiry" class="form-control form-control-sm" style="max-width:150px;">
                </div>
            </div>
        <?php if (empty($barcodes)): ?>
            <div class="alert alert-info mt-3"><?php echo TranslationManager::t('purchase_order.barcode.none'); ?></div>
        <?php else: ?>
            <div class="barcode-grid mt-3">
                <?php foreach ($barcodes as $bc): 
                    $file = get_setting('base_path') . ltrim($bc['file_path'],'/');
                    $web = get_setting('site_url') . '/' . ltrim($bc['file_path'],'/');
                    if (!is_file($file)) continue; 
                                        $copies = max(1, (int)($bc['quantity'] ?? 1));
                                        for($i=0;$i<$copies;$i++): 
                                                $dataAttr = 'data-code="'.htmlspecialchars($bc['code']).'" data-product="'.htmlspecialchars($bc['product_name']??'').'" data-supplier="'.htmlspecialchars($bc['supplier_name']??'').'" data-expiry="'.htmlspecialchars($bc['expiry_date'] ?? '').'"';
                                                ?>
                                                <div class="barcode-item" <?php echo $dataAttr; ?>>
                                                        <div class="form-check text-start no-print mb-1">
                                                                <input class="form-check-input select-barcode" type="checkbox">
                                                        </div>
                                                        <img src="<?php echo htmlspecialchars($web); ?>" alt="<?php echo htmlspecialchars($bc['code']); ?>">
                                                        <div><?php echo htmlspecialchars($bc['code']); ?></div>
                                                        <?php if (!empty($bc['expiry_date'])): ?><div><?php echo date('d/m/Y', strtotime($bc['expiry_date'])); ?></div><?php endif; ?>
                                                </div>
                                        <?php endfor; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
// Filtering & selective print
(function(){
    const items = Array.from(document.querySelectorAll('.barcode-item'));
    const fProd = document.getElementById('filterProduct');
    const fSupp = document.getElementById('filterSupplier');
    const fExp  = document.getElementById('filterExpiry');
    function applyFilters(){
        const p = (fProd.value||'').toLowerCase();
        const s = (fSupp.value||'').toLowerCase();
        const e = (fExp.value||'');
        items.forEach(it=>{
            const mp = (it.dataset.product||'').toLowerCase();
            const ms = (it.dataset.supplier||'').toLowerCase();
            const me = (it.dataset.expiry||'');
            const ok = (!p || mp.includes(p)) && (!s || ms.includes(s)) && (!e || me===e);
            it.style.display = ok? '':'none';
        });
    }
    [fProd,fSupp,fExp].forEach(inp=>inp && inp.addEventListener('input',applyFilters));
    document.getElementById('btnSelectAll').addEventListener('click',e=>{e.preventDefault();items.forEach(i=>{ if(i.style.display!=='none'){ const cb=i.querySelector('.select-barcode'); if(cb) cb.checked=true; }});});
    document.getElementById('btnClearSelection').addEventListener('click',e=>{e.preventDefault();items.forEach(i=>{ const cb=i.querySelector('.select-barcode'); if(cb) cb.checked=false; });});
    document.getElementById('btnPrintSelected').addEventListener('click',e=>{
        e.preventDefault();
        const selected = items.filter(i=>{ const cb=i.querySelector('.select-barcode'); return cb && cb.checked && i.style.display!=='none'; });
        if(!selected.length){ window.print(); return; }
        // Clone only selected into a print window
        const w = window.open('', '_blank');
        w.document.write('<html><head><title>Print</title><style>.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;} .barcode-item{border:1px solid #ccc;padding:8px;text-align:center;font-size:11px;border-radius:4px;} img{max-width:100%;height:auto;}</style></head><body><div class="grid">');
        selected.forEach(i=>{ const clone = i.cloneNode(true); const chk = clone.querySelector('.select-barcode'); if(chk) chk.remove(); const fc = clone.querySelector('.form-check'); if(fc) fc.remove(); w.document.body.querySelector('.grid').appendChild(clone); });
        w.document.write('</div><script>window.onload=function(){window.print();};<\/script></body></html>');
        w.document.close();
    });
})();
</script>
<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>
