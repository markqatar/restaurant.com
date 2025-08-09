<?php require_once get_setting('base_path').'admin/layouts/header.php'; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
  <h5><?php echo TranslationManager::t('inventory.title') ?: 'Inventory'; ?></h5>
  <a href="<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes" class="btn btn-sm btn-secondary"><?php echo TranslationManager::t('recipes.title') ?: 'Recipes'; ?></a>
  </div>
  <div class="card-body">
    <table class="table table-sm table-bordered">
  <thead><tr><th><?php echo TranslationManager::t('inventory.field.type') ?: 'Type'; ?></th><th><?php echo TranslationManager::t('recipes.field.name') ?: 'Name'; ?></th><th><?php echo TranslationManager::t('inventory.field.quantity') ?: 'Quantity'; ?></th><th><?php echo TranslationManager::t('inventory.field.unit') ?: 'Unit'; ?></th><th><?php echo TranslationManager::t('inventory.field.updated_at') ?: 'Updated'; ?></th></tr></thead>
      <tbody>
        <?php foreach($inventory as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['item_type']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo (float)$row['quantity']; ?></td>
            <td><?php echo htmlspecialchars($row['unit']); ?></td>
            <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once get_setting('base_path').'admin/layouts/footer.php'; ?>
