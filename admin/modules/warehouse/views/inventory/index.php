<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>
<div class="card" id="inventorySummaryRoot">
  <div class="card-header">
    <div class="d-flex flex-wrap gap-2 align-items-end">
      <div>
  <label class="form-label mb-0 small"><?php echo TranslationManager::t('warehouse.inventory.branch'); ?></label>
        <select id="filterBranch" class="form-select form-select-sm">
          <option value="">-- <?php echo TranslationManager::t('warehouse.inventory.filters.branch_all'); ?> --</option>
          <?php
            $db=Database::getInstance()->getConnection();
            foreach($db->query("SELECT id,name FROM branches ORDER BY name") as $b){
              echo '<option value="'.(int)$b['id'].'">'.htmlspecialchars($b['name']).'</option>';
            }
          ?>
        </select>
      </div>
      <div>
  <label class="form-label mb-0 small"><?php echo TranslationManager::t('warehouse.inventory.type'); ?></label>
        <select id="filterType" class="form-select form-select-sm">
          <option value="">-- <?php echo TranslationManager::t('warehouse.inventory.filters.type_all'); ?> --</option>
          <option value="product"><?php echo TranslationManager::t('warehouse.common.product'); ?></option>
          <option value="recipe"><?php echo TranslationManager::t('warehouse.common.recipe'); ?></option>
        </select>
      </div>
      <div class="flex-grow-1"></div>
      <div class="btn-group btn-group-sm" role="group">
  <button class="btn btn-outline-secondary" id="btnExportCsv"><i class="fas fa-file-csv"></i> <?php echo TranslationManager::t('warehouse.inventory.export.csv'); ?></button>
  <button class="btn btn-outline-secondary" id="btnExportPdf"><i class="fas fa-file-pdf"></i> <?php echo TranslationManager::t('warehouse.inventory.export.pdf'); ?></button>
  <button class="btn btn-outline-secondary" id="btnPrint"><i class="fas fa-print"></i> <?php echo TranslationManager::t('warehouse.inventory.export.print'); ?></button>
      </div>
    </div>
  </div>
  <div class="card-body table-responsive pt-2">
    <table class="table table-striped table-sm" id="inventoryTable" style="width:100%">
      <thead>
        <tr>
          <th><?php echo TranslationManager::t('warehouse.inventory.branch'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.inventory.type'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.inventory.item'); ?></th>
          <th class="text-end"><?php echo TranslationManager::t('warehouse.inventory.quantity'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.inventory.unit'); ?></th>
          <th><?php echo TranslationManager::t('warehouse.inventory.updated'); ?></th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const table = $('#inventoryTable').DataTable({
    processing:true,
    serverSide:true,
    searching:true,
    lengthChange:true,
    pageLength:25,
    order:[],
    ajax:{
      url: '<?php echo get_setting('site_url'); ?>/admin/warehouse/inventory/datatable',
      data: function(d){
        d.branch = document.getElementById('filterBranch').value;
        d.type = document.getElementById('filterType').value;
      }
    },
    columnDefs:[
      { targets:[3], className:'text-end' },
      { targets:'_all', defaultContent:'' }
    ],
    drawCallback:function(){ if(window.markInstance){ window.markInstance.unmark(); window.markInstance.mark($('.dataTables_filter input').val()); } }
  });
  $('#filterBranch,#filterType').on('change', ()=> table.ajax.reload());
  // CSV Export
  document.getElementById('btnExportCsv').addEventListener('click', function(){
    const data = table.rows({search:'applied'}).data().toArray();
  let csv = '"<?php echo TranslationManager::t('warehouse.inventory.branch'); ?>","<?php echo TranslationManager::t('warehouse.inventory.type'); ?>","<?php echo TranslationManager::t('warehouse.inventory.item'); ?>","<?php echo TranslationManager::t('warehouse.inventory.quantity'); ?>","<?php echo TranslationManager::t('warehouse.inventory.unit'); ?>","<?php echo TranslationManager::t('warehouse.inventory.updated'); ?>"\n';
    data.forEach(row=>{ csv += row.map(c=> '"'+$('<div>').html(c).text().replace(/"/g,'""')+'"').join(',')+'\n'; });
    const blob = new Blob([csv],{type:'text/csv;charset=utf-8;'}); const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='inventory_summary.csv'; a.click(); URL.revokeObjectURL(a.href);
  });
  // PDF Export via simple print to new window (fallback)
  document.getElementById('btnExportPdf').addEventListener('click', function(){
    const data = table.rows({search:'applied'}).data().toArray();
      let html = '<h3><?php echo addslashes(TranslationManager::t('warehouse.inventory.summary')); ?></h3><table border="1" cellspacing="0" cellpadding="4" style="width:100%;font-size:12px;border-collapse:collapse"><thead><tr>'+
        ['<?php echo addslashes(TranslationManager::t('warehouse.inventory.branch')); ?>','<?php echo addslashes(TranslationManager::t('warehouse.inventory.type')); ?>','<?php echo addslashes(TranslationManager::t('warehouse.inventory.item')); ?>','<?php echo addslashes(TranslationManager::t('warehouse.inventory.quantity')); ?>','<?php echo addslashes(TranslationManager::t('warehouse.inventory.unit')); ?>','<?php echo addslashes(TranslationManager::t('warehouse.inventory.updated')); ?>'].map(h=>'<th>'+h+'</th>').join('')+
        '</tr></thead><tbody>';
    data.forEach(row=>{ html += '<tr>'+row.map(c=>'<td>'+$('<div>').html(c).text()+'</td>').join('')+'</tr>'; });
    html+='</tbody></table>';
    const w=window.open('about:blank'); w.document.write('<html><head><title>PDF</title></head><body>'+html+'</body></html>'); w.document.close(); w.print();
  });
  document.getElementById('btnPrint').addEventListener('click', ()=> window.print());
});
</script>
<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>