<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Purchase Orders</h5>
        <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/create" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Nuovo Ordine
        </a>
    </div>
    <div class="card-body">
        <table id="purchaseOrdersTable" class="table table-bordered table-striped w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fornitore</th>
                    <th>Stato</th>
                    <th>Totale</th>
                    <th>Data</th>
                    <th>Azioni</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
const PO_VARS = {
    urls: {
        datatable: "<?php echo get_setting('site_url'); ?>/admin/suppliers/purchaseorders/datatable"
    },
    csrfToken: "<?php echo generate_csrf_token(); ?>"
};
</script>

<?php
$pageScripts = [
    get_setting('site_url') . '/admin/modules/suppliers/views/purchase_orders/js/index.js'
];
require_once get_setting('base_path') . 'admin/layouts/footer.php';
?>