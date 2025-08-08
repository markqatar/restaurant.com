<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5><?php echo TranslationManager::t('purchase_order.detail_title'); ?> #<?php echo $order['id']; ?></h5>
        <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('purchase_order.back_to_list'); ?>
        </a>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <strong><?php echo TranslationManager::t('purchase_order.field.supplier'); ?>:</strong> <?php echo htmlspecialchars($order['supplier_name']); ?><br>
            <strong><?php echo TranslationManager::t('purchase_order.field.status'); ?>:</strong>
            <span class="badge bg-<?php echo $order['status'] == 'draft' ? 'secondary' : ($order['status'] == 'sent' ? 'info' : 'success'); ?>">
                <?php echo TranslationManager::t('purchase_order.status.' . $order['status']); ?>
            </span>
        </div>

        <hr>

    <h6><?php echo TranslationManager::t('purchase_order.ordered_products_title'); ?></h6>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?php echo TranslationManager::t('purchase_order.field.product'); ?></th>
                    <th><?php echo TranslationManager::t('purchase_order.field.quantity'); ?></th>
                    <th><?php echo TranslationManager::t('purchase_order.field.unit'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($item['unit_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr>

        <div class="d-flex justify-content-between">
            <div>
                <?php if ($order['pdf_path']): ?>
                    <a href="<?php echo $order['pdf_path']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-file-pdf"></i> <?php echo TranslationManager::t('purchase_order.btn.download_pdf'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($order['status'] == 'draft'): ?>
                    <button id="btnSendOrder" class="btn btn-info btn-sm">
                        <i class="fas fa-paper-plane"></i> <?php echo TranslationManager::t('purchase_order.btn.send_order'); ?>
                    </button>
                <?php endif; ?>
                <?php if ($order['status'] == 'sent'): ?>
                    <button id="btnReceiveOrder" class="btn btn-success btn-sm">
                        <i class="fas fa-check"></i> <?php echo TranslationManager::t('purchase_order.btn.mark_as_received'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const PO_DETAIL = {
    urls: {
        sendOrder: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/send",
        receiveOrder: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/receive"
    },
    csrfToken: "<?php echo generate_csrf_token(); ?>",
    poId: <?php echo (int)$order['id']; ?>
};
</script>

<?php
$pageScripts = [
    get_setting('site_url') . '/admin/modules/suppliers/views/purchase_orders/js/view.js'
];
require_once get_setting('base_path') . 'admin/layouts/footer.php';
?>