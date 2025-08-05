<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h5>Prodotti di <?php echo htmlspecialchars($supplier_id); ?></h5>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierProductModal">
      <i class="fas fa-plus"></i> Aggiungi Prodotto
    </button>
  </div>
  <div class="card-body">
    <table id="supplierProductsTable" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>Prodotto</th>
          <th>Nome Fattura</th>
          <th>Unità</th>
          <th>Quantità</th>
          <th>Prezzo</th>
          <th>Azioni</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="supplierProductModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="supplierProductForm">
      <input type="hidden" name="id" id="spId">
      <input type="hidden" name="supplier_id" value="<?php echo $supplier_id; ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Associa Prodotto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Prodotto</label>
            <select name="product_id" id="product_id" class="form-control select2" style="width:100%"></select>
          </div>
          <div class="mb-3">
            <label>Nome in Fattura</label>
            <input type="text" name="supplier_name" id="supplier_name" class="form-control">
          </div>
          <div class="mb-3">
            <label>Unità</label>
            <select name="unit_id" id="unit_id" class="form-control select2" style="width:100%"></select>
          </div>
          <div class="mb-3">
            <label>Quantità</label>
            <input type="number" step="0.01" name="quantity" id="quantity" class="form-control">
          </div>
          <div class="mb-3">
            <label>Prezzo</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control">
          </div>
          <div class="mb-3">
            <label>Valuta</label>
            <input type="text" name="currency" id="currency" class="form-control" value="EUR">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salva</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
    const SUPPLIER_PRODUCTS_LINK_VARS = {
        supplierId: <?php echo (int)$supplier; ?>,
        urls: {
            datatable: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/datatable",
            store: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/store",
            get: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/get/",
            delete: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproducts/delete",
            selectProducts: "<?php echo get_setting('site_url'); ?>/admin/suppliers/products/select2",
            selectUnits: "<?php echo get_setting('site_url'); ?>/admin/system/units/select2"
        },
        csrfToken: "<?php echo generate_csrf_token(); ?>",
        translations: {
            confirmDeleteTitle: '<?php echo TranslationManager::t("confirm_delete"); ?>',
            confirmDeleteText: '<?php echo TranslationManager::t("delete_supplier_product_confirm"); ?>',
            yesDelete: '<?php echo TranslationManager::t("yes_delete"); ?>',
            cancel: '<?php echo TranslationManager::t("cancel"); ?>',
            error: '<?php echo TranslationManager::t("error"); ?>'
        }
    };
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/suppliers/views/supplier_products/js/index.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>
