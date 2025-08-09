<?php require_once get_setting('base_path').'admin/layouts/header.php'; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
  <h5><?php echo TranslationManager::t('recipes.title') ?: 'Recipes'; ?></h5>
    <div class="btn-group">
  <?php if(can('production','batch')): ?><a href="<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/production" class="btn btn-sm btn-warning"><i class="fas fa-industry"></i> <?php echo TranslationManager::t('recipes.action.production') ?: 'Production'; ?></a><?php endif; ?>
  <?php if(can('inventory','view')): ?><a href="<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/inventory" class="btn btn-sm btn-info"><i class="fas fa-boxes"></i> <?php echo TranslationManager::t('inventory.title') ?: 'Inventory'; ?></a><?php endif; ?>
  <?php if(can('recipes','create')): ?><a href="<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/create" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> <?php echo TranslationManager::t('recipes.action.new') ?: 'New'; ?></a><?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <table class="table table-bordered table-sm">
  <thead><tr><th><?php echo TranslationManager::t('recipes.field.name') ?: 'Name'; ?></th><th><?php echo TranslationManager::t('recipes.field.yield') ?: 'Yield'; ?></th><th><?php echo TranslationManager::t('recipes.field.components') ?: 'Components'; ?></th><th><?php echo TranslationManager::t('recipes.field.actions') ?: 'Actions'; ?></th></tr></thead>
      <tbody>
        <?php foreach($recipes as $r): ?>
        <tr>
          <td><?php echo htmlspecialchars($r['name']); ?></td>
          <td><?php echo (float)$r['yield_quantity'].' '.htmlspecialchars($r['yield_unit_name']); ?></td>
          <td><?php
            $count = Database::getInstance()->getConnection()->prepare("SELECT COUNT(*) FROM recipe_components WHERE recipe_id=?");
            $count->execute([$r['id']]);
            echo (int)$count->fetchColumn();
          ?></td>
          <td>
            <?php if(can('recipes','update')): ?><a class="btn btn-sm btn-primary" title="<?php echo TranslationManager::t('recipes.action.edit') ?: 'Edit'; ?>" href="<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/edit/<?php echo (int)$r['id']; ?>"><i class="fas fa-edit"></i></a><?php endif; ?>
            <?php if(can('recipes','delete')): ?><button data-id="<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-danger btn-delete" title="<?php echo TranslationManager::t('recipes.action.delete') ?: 'Delete'; ?>"><i class="fas fa-trash"></i></button><?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
 <script>
 <?php if(can('recipes','delete')): ?>
 document.addEventListener('click', function(e){
  if(e.target.closest('.btn-delete')){
  if(confirm('<?php echo addslashes(TranslationManager::t('recipes.confirm.delete') ?: 'Delete recipe?'); ?>')){
      fetch('<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/delete', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+e.target.closest('.btn-delete').dataset.id+'&csrf_token=<?php echo generate_csrf_token(); ?>'}).then(r=>r.json()).then(j=>{ if(j.success) location.reload(); else alert('Error'); });
    }
  }
 });
 <?php endif; ?>
 </script>
<?php require_once get_setting('base_path').'admin/layouts/footer.php'; ?>
