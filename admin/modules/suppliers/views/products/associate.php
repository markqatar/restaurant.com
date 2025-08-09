<?php
require_once get_setting('base_path') . 'admin/layouts/header.php';
?>

<div class="card">
  <div class="card-header d-flex justify-content-between">
  <h5><?php echo TranslationManager::t('suppliers.associate'); ?> <?php echo TranslationManager::t('suppliers.supplier'); ?> & <?php echo TranslationManager::t('suppliers.unit'); ?> - <strong><?php echo htmlspecialchars($product['name']); ?></strong></h5>
    <div class="d-flex gap-2">
      <a href="/admin/suppliers/products" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('suppliers.back_to_products'); ?>
      </a>
      <?php if (can('suppliers_products','associate.add')): ?>
      <button type="button" id="quickAddAssociationBtn" class="btn btn-success btn-sm">
        <i class="fas fa-plus"></i> <?php echo TranslationManager::t('suppliers.add_association'); ?>
      </button>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <form id="associateForm" class="mb-4">
      <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

      <div class="row">
        <!-- Fornitore -->
        <div class="col-md-4 mb-3">
          <label><?php echo TranslationManager::t('suppliers.supplier'); ?></label>
          <select name="supplier_id" id="supplier_id" class="form-control select2" style="width: 100%" required></select>
        </div>

        <!-- Unità Principale -->
        <div class="col-md-4 mb-3">
          <label><?php echo TranslationManager::t('suppliers.unit'); ?> Principale</label>
          <select name="unit_id" id="unit_id" class="form-control select2" style="width: 100%" required></select>
        </div>

        <!-- Quantità Unità Principale -->
        <div class="col-md-4 mb-3">
          <label><?php echo TranslationManager::t('suppliers.quantity_per_unit'); ?></label>
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
            <label class="form-check-label"><?php echo TranslationManager::t('status.active'); ?></label>
          </div>
        </div>
      </div>

  <?php if (can('suppliers_products','associate.add')): ?>
    <button type="submit" class="btn btn-primary" id="associateSubmitBtn"><?php echo TranslationManager::t('suppliers.associate'); ?></button>
  <?php endif; ?>
  <?php if (can('suppliers_products','subunit.add')): ?>
    <button type="button" id="addSubUnitBtn" class="btn btn-outline-secondary ms-2"><?php echo TranslationManager::t('suppliers.add_sub_unit'); ?></button>
  <?php endif; ?>
  <button type="button" id="cancelEditBtn" class="btn btn-warning ms-2 d-none"><?php echo TranslationManager::t('suppliers.cancel_edit'); ?></button>
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
  isRawMaterial: <?php echo (int)$product['is_raw_material']; ?>,
  perms: {
    canAssociateView: <?php echo can('suppliers_products','associate.view') ? 'true':'false'; ?>,
    canAssociateAdd: <?php echo can('suppliers_products','associate.add') ? 'true':'false'; ?>,
    canAssociateEdit: <?php echo can('suppliers_products','associate.edit') ? 'true':'false'; ?>,
    canAssociateDelete: <?php echo can('suppliers_products','associate.delete') ? 'true':'false'; ?>,
    canAddSubUnit: <?php echo can('suppliers_products','subunit.add') ? 'true':'false'; ?>
  },
  translations: {
    confirmDelete: '<?php echo TranslationManager::t("suppliers.confirm_delete"); ?>',
    deleteYes: '<?php echo TranslationManager::t("suppliers.delete_yes"); ?>',
    error: '<?php echo TranslationManager::t("msg.error_occurred"); ?>',
    recordNotFound: '<?php echo TranslationManager::t("suppliers.record_not_found"); ?>',
    quantityForUnit: '<?php echo TranslationManager::t("suppliers.quantity_for_unit"); ?>',
    subUnitLevel: '<?php echo TranslationManager::t("suppliers.sub_unit_level"); ?>',
    quantityPerUnit: '<?php echo TranslationManager::t("suppliers.quantity_per_unit"); ?>'
  }
};

// Disable form elements if user cannot add/edit associations
(function(){
  const p = ASSOCIATE_VARS.perms;
  if(!p.canAssociateAdd && !p.canAssociateEdit){
    const form = document.getElementById('associateForm');
    if(form){
      [...form.querySelectorAll('input,select,textarea,button[type=submit]')].forEach(el=>{
        el.disabled = true;
      });
    }
  }
  if(!p.canAddSubUnit){
    const subBtn = document.getElementById('addSubUnitBtn');
    if(subBtn) subBtn.remove();
  }
})();
</script>

<?php
$pageScripts = [
    get_setting('site_url') . '/admin/modules/suppliers/views/products/js/associate.js'
];
require_once get_setting('base_path') . 'admin/layouts/footer.php';
?>