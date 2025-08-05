<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Dettaglio Ordine #<?php echo $order['id']; ?></h5>
        <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Torna alla lista
        </a>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <strong>Fornitore:</strong> <?php echo htmlspecialchars($order['supplier_name']); ?><br>
            <strong>Stato:</strong>
            <span class="badge bg-<?php echo $order['status'] == 'draft' ? 'secondary' : ($order['status'] == 'sent' ? 'info' : 'success'); ?>">
                <?php echo ucfirst($order['status']); ?>
            </span>
        </div>

        <hr>

        <h6>Prodotti Ordinati</h6>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Prodotto</th>
                    <th>Quantità</th>
                    <th>Unità</th>
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
                        <i class="fas fa-file-pdf"></i> Scarica PDF
                    </a>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($order['status'] == 'draft'): ?>
                    <button id="btnSendOrder" class="btn btn-info btn-sm">
                        <i class="fas fa-paper-plane"></i> Invia Ordine
                    </button>
                <?php endif; ?>
                <?php if ($order['status'] == 'sent'): ?>
                    <button id="btnReceiveOrder" class="btn btn-success btn-sm">
                        <i class="fas fa-check"></i> Segna come Ricevuto
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