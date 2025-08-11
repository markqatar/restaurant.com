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
    <table id="recipesTable" class="table table-bordered table-sm w-100">
      <thead>
        <tr>
          <th><?php echo TranslationManager::t('recipes.field.name') ?: 'Name'; ?></th>
          <th><?php echo TranslationManager::t('recipes.field.yield') ?: 'Yield'; ?></th>
          <th><?php echo TranslationManager::t('recipes.field.components') ?: 'Components'; ?></th>
          <th><?php echo TranslationManager::t('recipes.field.actions') ?: 'Actions'; ?></th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
 <script>
 $(function(){
   $('#recipesTable').DataTable({
     processing:true,
     serverSide:true,
     pageLength:25,
     order:[[0,'asc']],
     ajax:{
       url:'<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/datatable',
       type:'POST'
     },
     columns:[
       {data:0},
       {data:1},
       {data:2, orderable:false},
       {data:3, orderable:false, searchable:false}
     ]
   });
   document.addEventListener('click', function(e){
     const btn = e.target.closest('.btn-delete');
     if(btn){
       if(confirm('<?php echo addslashes(TranslationManager::t('recipes.confirm.delete') ?: 'Delete recipe?'); ?>')){
         fetch('<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/delete', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+btn.dataset.id+'&csrf_token=<?php echo generate_csrf_token(); ?>'}).then(r=>r.json()).then(j=>{ if(j.success){ $('#recipesTable').DataTable().ajax.reload(null,false); } else alert('Error'); });
       }
     }
   });
 });
 </script>
<?php require_once get_setting('base_path').'admin/layouts/footer.php'; ?>
