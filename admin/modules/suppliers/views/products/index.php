<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h5>Prodotti</h5>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#productModal">
      <i class="fas fa-plus"></i> Aggiungi Prodotto
    </button>
  </div>
  <div class="card-body">
    <table id="productsTable" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Materia Prima</th>
          <th>Genera Barcode</th>
          <th>Scadenza</th>
          <th>Azioni</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modal Prodotto -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="productForm">
      <input type="hidden" name="id" id="productId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Gestione Prodotto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>

          <!-- SKU (solo se materia prima) -->
          <div class="mb-3 d-none" id="skuField">
            <label>SKU</label>
            <input type="text" name="sku" id="sku" class="form-control">
          </div>

          <div class="mb-3">
            <label>Descrizione</label>
            <textarea name="description" id="description" class="form-control"></textarea>
          </div>

          <!-- Checkbox -->
          <div class="form-check">
            <input type="checkbox" name="is_raw_material" id="is_raw_material" class="form-check-input">
            <label for="is_raw_material" class="form-check-label">Materia Prima</label>
          </div>
          <div class="form-check">
            <input type="checkbox" name="generate_barcode" id="generate_barcode" class="form-check-input">
            <label for="generate_barcode" class="form-check-label">Genera Barcode</label>
          </div>
          <div class="form-check">
            <input type="checkbox" name="requires_expiry" id="requires_expiry" class="form-check-input">
            <label for="requires_expiry" class="form-check-label">Richiede Data di Scadenza</label>
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
    const SUPPLIER_PRODUCTS_VARS = {
        urls: {
            datatable: "<?php echo get_setting('site_url'); ?>/admin/suppliers/products/datatable",
            store: "<?php echo get_setting('site_url'); ?>/admin/suppliers/products/store",
            get: "<?php echo get_setting('site_url'); ?>/admin/suppliers/products/get/",
            delete: "<?php echo get_setting('site_url'); ?>/admin/suppliers/products/delete"
        },
        csrfToken: "<?php echo generate_csrf_token(); ?>",
        translations: {
            confirmDeleteTitle: '<?php echo TranslationManager::t("confirm_delete"); ?>',
            confirmDeleteText: '<?php echo TranslationManager::t("delete_product_confirm"); ?>',
            yesDelete: '<?php echo TranslationManager::t("yes_delete"); ?>',
            cancel: '<?php echo TranslationManager::t("cancel"); ?>',
            error: '<?php echo TranslationManager::t("error"); ?>'
        }
    };
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/suppliers/views/products/js/index.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>