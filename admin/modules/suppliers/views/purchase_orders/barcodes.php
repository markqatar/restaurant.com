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
        <div class="no-print print-actions">
            <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="fas fa-print"></i> <?php echo TranslationManager::t('purchase_order.barcode.print_button'); ?></button>
        </div>
        <?php if (empty($barcodes)): ?>
            <div class="alert alert-info mt-3"><?php echo TranslationManager::t('purchase_order.barcode.none'); ?></div>
        <?php else: ?>
            <div class="barcode-grid mt-3">
                <?php foreach ($barcodes as $bc): 
                    $file = get_setting('base_path') . ltrim($bc['file_path'],'/');
                    $web = get_setting('site_url') . '/' . ltrim($bc['file_path'],'/');
                    if (!is_file($file)) continue; ?>
                    <div class="barcode-item">
                        <img src="<?php echo htmlspecialchars($web); ?>" alt="<?php echo htmlspecialchars($bc['code']); ?>">
                        <div><?php echo htmlspecialchars($bc['code']); ?></div>
                        <?php if (!empty($bc['expiry_date'])): ?><div><?php echo date('d/m/Y', strtotime($bc['expiry_date'])); ?></div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>
