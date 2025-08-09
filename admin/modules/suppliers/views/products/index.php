<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>
<div class="card">
  <div class="card-header d-flex justify-content-between">
  <h5><?php echo TranslationManager::t('suppliers.products_title'); ?></h5>
    <?php if (can('suppliers_products','create')): ?>
    <button id="addProductBtn" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#productModal" type="button">
      <i class="fas fa-plus"></i> <?php echo TranslationManager::t('suppliers.add_product'); ?>
    </button>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <table id="productsTable" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th><?php echo TranslationManager::t('suppliers.raw_material'); ?></th>
          <th><?php echo TranslationManager::t('suppliers.generate_barcode'); ?></th>
          <th><?php echo TranslationManager::t('suppliers.requires_expiry'); ?></th>
          <th><?php echo TranslationManager::t('suppliers.unit'); ?> Base</th>
          <th><?php echo TranslationManager::t('common.actions'); ?></th>
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
          <h5 class="modal-title"><?php echo TranslationManager::t('suppliers.manage_product'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label><?php echo TranslationManager::t('common.name'); ?></label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>

          <!-- SKU (solo se materia prima) -->
          <div class="mb-3 d-none" id="skuField">
            <label>SKU</label>
            <input type="text" name="sku" id="sku" class="form-control">
          </div>

          <div class="mb-3">
            <label><?php echo TranslationManager::t('common.description'); ?></label>
            <textarea name="description" id="description" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('suppliers.unit'); ?> Base</label>
            <select name="base_unit_id" id="base_unit_id" class="form-select">
              <option value="">--</option>
              <?php
              try {
                $db = Database::getInstance()->getConnection();
                $uStmt = $db->query("SELECT id,name FROM units ORDER BY name");
                foreach ($uStmt->fetchAll(PDO::FETCH_ASSOC) as $u) {
                    echo '<option value="'.(int)$u['id'].'">'.htmlspecialchars($u['name']).'</option>';
                }
              } catch (Exception $e) {}
              ?>
            </select>
          </div>

          <!-- Checkbox -->
          <div class="form-check">
            <input type="checkbox" name="is_raw_material" id="is_raw_material" class="form-check-input">
            <label for="is_raw_material" class="form-check-label"><?php echo TranslationManager::t('suppliers.raw_material'); ?></label>
          </div>
          <div class="form-check">
            <input type="checkbox" name="generate_barcode" id="generate_barcode" class="form-check-input">
            <label for="generate_barcode" class="form-check-label"><?php echo TranslationManager::t('suppliers.generate_barcode'); ?></label>
          </div>
          <div class="form-check">
            <input type="checkbox" name="requires_expiry" id="requires_expiry" class="form-check-input">
            <label for="requires_expiry" class="form-check-label"><?php echo TranslationManager::t('suppliers.requires_expiry'); ?></label>
          </div>
        </div>
        <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="saveProductBtn">Salva</button>
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
    perms: {
      canCreate: <?php echo can('suppliers_products','create') ? 'true':'false'; ?>,
      canUpdate: <?php echo can('suppliers_products','update') ? 'true':'false'; ?>,
      canDelete: <?php echo can('suppliers_products','delete') ? 'true':'false'; ?>,
      canAssociate: <?php echo can('suppliers_products','associate.view') ? 'true':'false'; ?>
    },
        translations: {
            confirmDeleteTitle: '<?php echo TranslationManager::t("msg.confirm_delete"); ?>',
            confirmDeleteText: '<?php echo TranslationManager::t("msg.confirm_delete_text"); ?>',
            yesDelete: '<?php echo TranslationManager::t("btn.yes_delete"); ?>',
            cancel: '<?php echo TranslationManager::t("btn.cancel"); ?>',
            error: '<?php echo TranslationManager::t("msg.error_occurred"); ?>',
            recordNotFound: '<?php echo TranslationManager::t("suppliers.record_not_found"); ?>',
            associate: '<?php echo TranslationManager::t("suppliers.associate"); ?>',
            rawMaterial: '<?php echo TranslationManager::t("suppliers.raw_material"); ?>',
            generateBarcode: '<?php echo TranslationManager::t("suppliers.generate_barcode"); ?>',
            requiresExpiry: '<?php echo TranslationManager::t("suppliers.requires_expiry"); ?>'
        }
    };

  // Runtime UI adjustments based on permissions
  (function(){
    const perms = SUPPLIER_PRODUCTS_VARS.perms;
    if(!perms.canCreate){
      const btn = document.getElementById('addProductBtn');
      if(btn) btn.remove();
    }
    // Disable form fields if cannot create or update
    if(!perms.canCreate && !perms.canUpdate){
      const form = document.getElementById('productForm');
      if(form){
        [...form.querySelectorAll('input,select,textarea,button[type=submit]')].forEach(el=>{
          el.disabled = true;
        });
      }
    }
  })();
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/suppliers/views/products/js/index.js',
];
?>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>