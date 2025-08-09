<?php require_once get_setting('base_path').'admin/layouts/header.php'; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
  <h5><?php echo TranslationManager::t('recipes.production.batch_title') ?: 'Production Batch'; ?></h5>
    <div>
      <a href="<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes" class="btn btn-sm btn-secondary">Back</a>
    </div>
  </div>
  <div class="card-body">
  <?php if(can('production','batch')): ?>
  <form id="production-form">
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <div class="row mb-3">
        <div class="col-md-5">
          <label><?php echo TranslationManager::t('recipes.title') ?: 'Recipe'; ?></label>
          <select name="recipe_id" id="recipe_id" class="form-control" required>
            <option value="">-- choose recipe --</option>
            <?php foreach($recipes as $r): ?>
              <option value="<?php echo (int)$r['id']; ?>" data-yield="<?php echo htmlspecialchars($r['yield_quantity']??''); ?>" data-unit="<?php echo htmlspecialchars($r['yield_unit_name']??''); ?>"><?php echo htmlspecialchars($r['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label><?php echo TranslationManager::t('recipes.field.output_quantity') ?: 'Output Quantity'; ?></label>
          <input type="number" step="0.0001" name="output_qty" id="output_qty" class="form-control" required>
          <small class="text-muted" id="yieldInfo"></small>
        </div>
        <div class="col-md-4">
          <label><?php echo TranslationManager::t('recipes.field.reference_code') ?: 'Reference Code'; ?> (opt)</label>
          <input type="text" name="reference" class="form-control" placeholder="Batch code / note">
        </div>
      </div>
      <div class="mb-2">
  <button class="btn btn-primary"><?php echo TranslationManager::t('recipes.action.production') ?: 'Produce'; ?></button>
      </div>
    </form>
    <?php else: ?>
      <div class="alert alert-danger">You don't have permission to run production batches.</div>
    <?php endif; ?>
    <div id="result" class="mt-3"></div>
    <hr>
  <h6><?php echo TranslationManager::t('recipes.components.preview') ?: 'Components Preview'; ?></h6>
    <table class="table table-sm" id="componentsPreview" style="display:none;">
      <thead><tr><th>Type</th><th>Name</th><th>Base Qty</th><th>Scaled Qty</th><th>Unit</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>
</div>
<script>
const recipeSelect = document.getElementById('recipe_id');
const outputQty = document.getElementById('output_qty');
const yieldInfo = document.getElementById('yieldInfo');
const table = document.getElementById('componentsPreview');
const tbody = table.querySelector('tbody');
let currentRecipe = null;

function fetchDetails(id){
  if(!id){ currentRecipe=null; tbody.innerHTML=''; table.style.display='none'; return; }
  fetch('<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/details/'+id)
    .then(r=>r.json())
    .then(j=>{ if(j.success){ currentRecipe=j.recipe; renderPreview(); } });
}
function renderPreview(){
  if(!currentRecipe) return;
  const desired = parseFloat(outputQty.value||0);
  const baseYield = parseFloat(currentRecipe.yield_quantity||1);
  const factor = baseYield ? desired / baseYield : 0;
  tbody.innerHTML='';
  (currentRecipe.components||[]).forEach(c=>{
    const scaled = factor * parseFloat(c.quantity||0);
    const name = c.component_type==='product'? c.product_name : c.recipe_name;
    const unit = c.unit_name || '';
    const tr = document.createElement('tr');
  tr.innerHTML = `<td>${c.component_type}</td><td>${name||''}</td><td>${parseFloat(c.quantity||0)}</td><td>${scaled.toFixed(4)}</td><td>${unit}</td>`;
    tbody.appendChild(tr);
  });
  table.style.display='';
  yieldInfo.textContent = 'Yield: '+baseYield+' '+(currentRecipe.yield_unit_name||'');
}
recipeSelect.addEventListener('change', e=> fetchDetails(e.target.value));
outputQty.addEventListener('input', renderPreview);

// Submit production
const form = document.getElementById('production-form');
form.addEventListener('submit', function(e){
  e.preventDefault();
  const fd = new FormData(form);
  fetch('<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes/produce', {method:'POST', body: fd})
    .then(r=>r.json())
    .then(j=>{
      if(j.success){
  document.getElementById('result').innerHTML = '<div class="alert alert-success"><?php echo addslashes(TranslationManager::t('recipes.msg.batch_success') ?: 'Batch produced successfully'); ?></div>';
        // reset? keep recipe selection for multiple batches
      } else {
  document.getElementById('result').innerHTML = '<div class="alert alert-danger">'+(j.message||'<?php echo addslashes(TranslationManager::t('recipes.msg.batch_error') ?: 'Error'); ?>')+'</div>';
      }
    });
});
</script>
<?php require_once get_setting('base_path').'admin/layouts/footer.php'; ?>
