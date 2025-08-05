<?php
require_once get_setting('base_path') . 'admin/layouts/header.php';
?>

<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h5>Associa Fornitori & Unità per: <strong><?php echo htmlspecialchars($product['name']); ?></strong></h5>
    <a href="/admin/suppliers/products" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left"></i> Torna ai Prodotti
    </a>
  </div>
  <div class="card-body">
    <form id="associateForm" class="mb-4">
      <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

      <div class="row">
        <!-- Fornitore -->
        <div class="col-md-4 mb-3">
          <label>Fornitore</label>
          <select name="supplier_id" id="supplier_id" class="form-control select2" style="width: 100%" required></select>
        </div>

        <!-- Unità Principale -->
        <div class="col-md-4 mb-3">
          <label>Unità Principale</label>
          <select name="unit_id" id="unit_id" class="form-control select2" style="width: 100%" required></select>
        </div>

        <!-- Quantità Unità Principale -->
        <div class="col-md-4 mb-3">
          <label>Quantità per Unità</label>
          <input type="number" step="0.01" name="quantity" class="form-control" required>
        </div>
      </div>

      <!-- Container per le Sotto-Unità -->
      <div id="subUnitsContainer" class="row"></div>

      <!-- SKU solo per Materia Prima -->
      <div class="row">
        <div class="col-md-4 mb-3 d-flex align-items-center">
          <div class="form-check mt-4">
            <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
            <label class="form-check-label">Attivo</label>
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Associa</button>
    </form>

    <hr>

    <table id="supplierAssociationsTable" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>Fornitore</th>
          <th>Unità</th>
          <th>Quantità</th>
          <th>Sotto-Unità</th>
          <th>Attivo</th>
          <th>Azioni</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
const ASSOCIATE_VARS = {
    urls: {
        datatable: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproductassociations/datatable",
        store: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproductassociations/store",
        get: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproductassociations/get/",
        delete: "<?php echo get_setting('site_url'); ?>/admin/suppliers/supplierproductassociations/delete",
        suppliersSelect: "<?php echo get_setting('site_url'); ?>/admin/suppliers/suppliers/select",
        unitsSelect: "<?php echo get_setting('site_url'); ?>/admin/system/units/select"
    },
    csrfToken: "<?php echo generate_csrf_token(); ?>",
    productId: <?php echo (int)$product['id']; ?>,
    isRawMaterial: <?php echo (int)$product['is_raw_material']; ?>
};
</script>

<?php
$pageScripts = [
    get_setting('site_url') . '/admin/modules/suppliers/views/products/js/associate.js'
];
require_once get_setting('base_path') . 'admin/layouts/footer.php';
?>