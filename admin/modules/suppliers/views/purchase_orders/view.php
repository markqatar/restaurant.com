<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<style>
/* Blocking overlay loader */
.po-overlay-loader {position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.85);z-index:1055;display:flex;align-items:center;justify-content:center;flex-direction:column;font-family:Arial, sans-serif;backdrop-filter:blur(2px);}
.po-overlay-loader.hidden {display:none;}
.po-spinner {width:70px;height:70px;border:6px solid #e0e0e0;border-top-color:#3b82f6;border-radius:50%;animation:po-spin 0.9s linear infinite;box-shadow:0 0 8px rgba(0,0,0,0.15) inset;}
@keyframes po-spin {to {transform:rotate(360deg);}}
.po-loader-text {margin-top:18px;font-size:15px;color:#333;letter-spacing:0.5px;animation:po-fade 1.2s ease-in-out infinite alternate;font-weight:500;}
@keyframes po-fade {from{opacity:.55}to{opacity:1}}
</style>

<div id="poBlockingLoader" class="po-overlay-loader hidden">
    <div class="po-spinner"></div>
    <div class="po-loader-text" id="poLoaderMessage">Loading...</div>
</div>

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
            <strong><?php echo TranslationManager::t('branch.branch_name'); ?>:</strong> <?php echo htmlspecialchars($order['branch_name'] ?? ''); ?><br>
            <strong><?php echo TranslationManager::t('purchase_order.field.status'); ?>:</strong>
            <span class="badge bg-<?php echo $order['status'] == 'draft' ? 'secondary' : ($order['status'] == 'sent' ? 'primary' : 'success'); ?>">
                <?php echo TranslationManager::t('purchase_order.status.' . $order['status']); ?>
            </span>
        </div>

        <hr>

        <h6><?php echo TranslationManager::t('purchase_order.ordered_products_title'); ?></h6>
        <p><strong><?php echo TranslationManager::t('supplier_product.form.currency'); ?> (<?php echo TranslationManager::t('purchase_order.field.order'); ?>):</strong> <?php echo htmlspecialchars($order['currency'] ?? (get_setting('currency') ?: 'QAR')); ?></p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?php echo TranslationManager::t('purchase_order.field.product'); ?></th>
                    <th><?php echo TranslationManager::t('purchase_order.field.quantity'); ?></th>
                    <th><?php echo TranslationManager::t('purchase_order.field.unit'); ?></th>
                    <th><?php echo TranslationManager::t('supplier_product.form.price'); ?></th>
                    <th><?php echo TranslationManager::t('supplier_product.form.currency'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($item['unit_name']); ?></td>
                        <td><?php echo $item['price'] !== null ? number_format($item['price'],2) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($item['currency'] ?? ($order['currency'] ?? '')); ?></td>
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
                <?php if (!empty($order['barcode_count'])): ?>
                    <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/barcodes/<?php echo (int)$order['id']; ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-barcode"></i> <?php echo TranslationManager::t('purchase_order.barcode.title'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($order['status'] == 'draft'): ?>
                    <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/edit/<?php echo (int)$order['id']; ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> <?php echo TranslationManager::t('purchase_order.btn.edit_order') ?: 'Edit'; ?>
                    </a>
                    <button id="btnSendOrder" class="btn btn-info btn-sm">
                        <i class="fas fa-paper-plane"></i> <?php echo TranslationManager::t('purchase_order.btn.send_order'); ?>
                    </button>
                <?php endif; ?>
                <?php if ($order['status'] == 'sent'): ?>
                    <button id="btnReceiveOrder" class="btn btn-success btn-sm">
                        <i class="fas fa-check"></i> <?php echo TranslationManager::t('purchase_order.btn.mark_as_received'); ?>
                    </button>
                    <button id="btnResendOrder" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-paper-plane"></i> <?php echo TranslationManager::t('purchase_order.btn.send_order'); ?>
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
            receiveOrder: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/receiveDirect", // legacy quick endpoint (unused now)
            receivePage: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/receive/<?php echo (int)$order['id']; ?>",
            resendOrder: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/resend"
        },
        csrfToken: "<?php echo generate_csrf_token(); ?>",
        poId: <?php echo (int)$order['id']; ?>,
        t: {
            confirm_send_title: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.confirm_send_title')); ?>",
            confirm_send_button: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.confirm_send_button')); ?>",
            confirm_receive_title: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.confirm_receive_title')); ?>",
            confirm_receive_button: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.confirm_receive_button')); ?>",
            sent_successfully: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.sent_successfully')); ?>",
            received_successfully: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.received_successfully')); ?>",
            generic_ok: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.generic_ok')); ?>",
            generic_error: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.generic_error')); ?>",
            resent_successfully: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.resent_successfully')); ?>"
        ,resending_email: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.resending_email')); ?>"
        ,sending_order: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.sending_order')); ?>"
        ,receiving_order: "<?php echo addslashes(TranslationManager::t('purchase_order.msg.receiving_order')); ?>"
        }
    };
</script>

<?php
$pageScripts = [
    get_setting('site_url') . '/admin/modules/suppliers/views/purchase_orders/js/view.js'
];
require_once get_setting('base_path') . 'admin/layouts/footer.php';
?>