<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>
<div class="card" id="transfersRoot">
  <div class="card-header d-flex flex-wrap gap-2 align-items-center">
    <h5 class="mb-0 flex-grow-1"><?php echo TranslationManager::t('warehouse.transfer.list_title'); ?></h5>
    <div class="d-flex gap-2">
      <select id="filterBranch" class="form-select form-select-sm">
        <option value="">-- <?php echo TranslationManager::t('warehouse.inventory.filters.branch_all'); ?> --</option>
        <?php foreach($branches as $b): ?>
          <option value="<?php echo (int)$b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterType" class="form-select form-select-sm">
        <option value="">-- <?php echo TranslationManager::t('warehouse.inventory.filters.type_all'); ?> --</option>
        <option value="product"><?php echo TranslationManager::t('warehouse.common.product'); ?></option>
        <option value="recipe"><?php echo TranslationManager::t('warehouse.common.recipe'); ?></option>
      </select>
      <a href="<?php echo get_setting('site_url'); ?>/admin/warehouse/transfers/create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> <?php echo TranslationManager::t('warehouse.transfer.btn_new'); ?></a>
    </div>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-sm table-striped" id="transfersTable" style="width:100%">
      <thead>
        <tr>
          <th><?php echo TranslationManager::t('warehouse.transfer.table.id'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.transfer.table.item'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.transfer.table.from'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.transfer.table.to'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.transfer.table.qty'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.transfer.table.unit'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.transfer.table.note'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.transfer.table.at'); ?></th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const table = $('#transfersTable').DataTable({
    processing:true,
    serverSide:true,
    searching:true,
    lengthChange:true,
    pageLength:25,
    order:[[0,'desc']],
    ajax:{
      url:'<?php echo get_setting('site_url'); ?>/admin/warehouse/transfers/datatable',
      data:function(d){ d.branch=document.getElementById('filterBranch').value; d.type=document.getElementById('filterType').value; }
    },
    columnDefs:[{targets:[4],className:'text-end'}]
  });
  $('#filterBranch,#filterType').on('change', ()=> table.ajax.reload());
});
</script>
<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>