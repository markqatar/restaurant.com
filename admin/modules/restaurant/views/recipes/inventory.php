<?php require_once get_setting('base_path').'admin/layouts/header.php'; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
  <h5><?php echo TranslationManager::t('inventory.title') ?: 'Inventory'; ?></h5>
  <a href="<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes" class="btn btn-sm btn-secondary"><?php echo TranslationManager::t('recipes.title') ?: 'Recipes'; ?></a>
  </div>
  <div class="card-body">
    <form method="get" class="row g-2 mb-3">
      <div class="col-auto">
        <label class="form-label mb-0 small"><?php echo TranslationManager::t('inventory.field.branch') ?: 'Branch'; ?></label>
        <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">-- <?php echo TranslationManager::t('inventory.filter.all_branches') ?: 'All'; ?> --</option>
          <?php foreach(($branches ?? []) as $b): ?>
            <option value="<?php echo (int)$b['id']; ?>" <?php echo isset($current_branch_id) && (int)$current_branch_id === (int)$b['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </form>
    <table class="table table-sm table-bordered">
  <thead><tr><th><?php echo TranslationManager::t('inventory.field.branch') ?: 'Branch'; ?></th><th><?php echo TranslationManager::t('inventory.field.type') ?: 'Type'; ?></th><th><?php echo TranslationManager::t('recipes.field.name') ?: 'Name'; ?></th><th><?php echo TranslationManager::t('inventory.field.quantity') ?: 'Quantity'; ?></th><th><?php echo TranslationManager::t('inventory.field.unit') ?: 'Unit'; ?></th><th><?php echo TranslationManager::t('inventory.field.updated_at') ?: 'Updated'; ?></th></tr></thead>
      <tbody>
        <?php foreach($inventory as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['branch_name'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['item_type']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo (float)$row['quantity']; ?></td>
            <td><?php echo htmlspecialchars($row['unit_name'] ?? $row['unit']); ?></td>
            <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once get_setting('base_path').'admin/layouts/footer.php'; ?>
