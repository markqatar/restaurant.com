<?php require_once get_setting('base_path').'admin/layouts/header.php'; ?>
<?php $isEdit = isset($recipe); ?>
<div class="card">
  <div class="card-header"><h5><?php echo $isEdit ? (TranslationManager::t('recipes.action.edit') ?: 'Edit') : (TranslationManager::t('recipes.action.new') ?: 'Create'); ?> <?php echo TranslationManager::t('recipes.title') ?: 'Recipe'; ?></h5></div>
  <div class="card-body">
    <form method="post" action="" id="recipe-form">
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
      <div class="form-group mb-2">
  <label><?php echo TranslationManager::t('recipes.field.name') ?: 'Name'; ?></label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($recipe['name'] ?? ''); ?>" required>
      </div>
      <div class="form-row row mb-2">
        <div class="col">
          <label><?php echo TranslationManager::t('recipes.field.yield') ?: 'Yield Quantity'; ?></label>
          <input type="number" step="0.0001" name="yield_quantity" class="form-control" value="<?php echo htmlspecialchars($recipe['yield_quantity'] ?? ''); ?>" required>
        </div>
        <div class="col">
          <label><?php echo TranslationManager::t('recipes.field.unit') ?: 'Yield Unit'; ?></label>
          <select name="yield_unit_id" class="form-control" required>
            <option value="">-- unit --</option>
            <?php foreach($units as $u): ?>
              <option value="<?php echo (int)$u['id']; ?>" <?php if(($recipe['yield_unit_id'] ?? null)==$u['id']) echo 'selected'; ?>><?php echo htmlspecialchars($u['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <hr>
  <h6><?php echo TranslationManager::t('recipes.field.components') ?: 'Components'; ?></h6>
      <table class="table table-sm" id="components-table">
        <thead><tr><th>Type</th><th>Item</th><th>Qty</th><th>Unit</th><th></th></tr></thead>
        <tbody>
        <?php if(!empty($components)): foreach($components as $c): ?>
          <tr>
            <td>
              <select name="components[type][]" class="form-control form-control-sm type-select">
                <option value="product" <?php if($c['component_type']==='product') echo 'selected'; ?>>Product</option>
                <option value="recipe" <?php if($c['component_type']==='recipe') echo 'selected'; ?>>Recipe</option>
              </select>
            </td>
            <td>
              <select name="components[id][]" class="form-control form-control-sm item-select" data-selected="<?php echo $c['component_type']==='product' ? $c['product_id'] : $c['child_recipe_id']; ?>"></select>
            </td>
            <td><input type="number" step="0.0001" name="components[quantity][]" class="form-control form-control-sm" value="<?php echo (float)$c['quantity']; ?>" required></td>
            <td><input type="text" name="components[unit][]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c['unit']); ?>" required></td>
            <td><button type="button" class="btn btn-sm btn-danger btn-remove">&times;</button></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
  <button type="button" class="btn btn-sm btn-secondary" id="btn-add-component"><?php echo TranslationManager::t('recipes.action.add_component') ?: 'Add Component'; ?></button>
      <hr>
  <?php if(can('recipes', $isEdit? 'update':'create')): ?>
  <button class="btn btn-primary">Save</button>
  <?php endif; ?>
  <a href="<?php echo get_setting('site_url'); ?>/admin/restaurant/recipes" class="btn btn-secondary"><?php echo TranslationManager::t('cancel') ?: 'Cancel'; ?></a>
    </form>
  </div>
</div>
<script>
const products = <?php echo json_encode($products); ?>; // id,name
const recipes = <?php echo json_encode($allRecipes); ?>; // id,name
function buildOptions(list, selected){
  return '<option value="">-- choose --</option>'+list.map(i=>`<option value="${i.id}" ${selected==i.id?'selected':''}>${i.name}</option>`).join('');
}
function populateRow(row){
  const typeSel = row.querySelector('.type-select');
  const itemSel = row.querySelector('.item-select');
  const selected = itemSel.getAttribute('data-selected');
  if(typeSel.value==='product') itemSel.innerHTML = buildOptions(products, selected); else itemSel.innerHTML = buildOptions(recipes.filter(r=>r.id != <?php echo $recipe['id'] ?? 'null'; ?>), selected);
}
function addRow(prefill){
  const tbody = document.querySelector('#components-table tbody');
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><select name="components[type][]" class="form-control form-control-sm type-select"><option value="product">Product</option><option value="recipe">Recipe</option></select></td>
    <td><select name="components[id][]" class="form-control form-control-sm item-select"></select></td>
    <td><input type="number" step="0.0001" name="components[quantity][]" class="form-control form-control-sm" required></td>
    <td><input type="text" name="components[unit][]" class="form-control form-control-sm" required></td>
    <td><button type="button" class="btn btn-sm btn-danger btn-remove">&times;</button></td>`;
  tbody.appendChild(tr);
  if(prefill){
    tr.querySelector('.type-select').value = prefill.type;
    tr.querySelector('.item-select').setAttribute('data-selected', prefill.id);
    tr.querySelector('[name="components[quantity][]"]').value = prefill.quantity;
    tr.querySelector('[name="components[unit][]"]').value = prefill.unit;
  }
  populateRow(tr);
}
// Initialize existing rows
Array.from(document.querySelectorAll('#components-table tbody tr')).forEach(populateRow);

document.getElementById('btn-add-component').addEventListener('click', ()=> addRow());

document.addEventListener('change', function(e){
  if(e.target.classList.contains('type-select')){
    populateRow(e.target.closest('tr'));
  }
});

document.addEventListener('click', function(e){
  if(e.target.classList.contains('btn-remove')){
    e.target.closest('tr').remove();
  }
});
</script>
<?php require_once get_setting('base_path').'admin/layouts/footer.php'; ?>
