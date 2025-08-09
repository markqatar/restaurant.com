<?php include get_setting('base_path').'/admin/layouts/header.php'; ?>
<div class="container-fluid">
  <h1 class="h3 mb-3"><?php echo htmlspecialchars($page_title); ?></h1>
  <form method="post" action="<?php echo get_setting('site_url'); ?>/admin/customers/address-field-rules/<?php echo $rule? 'update/'.(int)$rule['id'] : 'store'; ?>">
    <div class="row">
      <div class="col-md-3 mb-3">
        <label class="form-label">State ID</label>
        <input type="number" name="state_id" class="form-control" value="<?php echo $rule['state_id']??''; ?>" />
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Delivery Area ID</label>
        <input type="number" name="delivery_area_id" class="form-control" value="<?php echo $rule['delivery_area_id']??''; ?>" />
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Field Key *</label>
        <input type="text" name="field_key" required class="form-control" value="<?php echo $rule['field_key']??''; ?>" />
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Requirement</label>
        <select name="requirement" class="form-select">
          <?php $req = $rule['requirement']??'optional'; foreach(['required','optional','hidden'] as $opt): ?>
          <option value="<?php echo $opt; ?>" <?php if($req===$opt) echo 'selected'; ?>><?php echo ucfirst($opt); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">Label</label>
        <input type="text" name="label" class="form-control" value="<?php echo isset($rule['label'])? htmlspecialchars($rule['label']) : ''; ?>" />
      </div>
      <div class="col-md-2 mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" value="<?php echo $rule['sort_order']??0; ?>" />
      </div>
      <div class="col-md-2 mb-3">
        <label class="form-label">Active</label><br />
        <input type="checkbox" name="active" value="1" <?php echo (!isset($rule)|| !empty($rule['active']))? 'checked':''; ?> /> Enable
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Preview Effective (state_id & delivery_area_id)</label>
        <div class="d-flex gap-2">
          <input type="number" id="preview_state_id" class="form-control" placeholder="State" />
          <input type="number" id="preview_delivery_area_id" class="form-control" placeholder="Delivery Area" />
          <button type="button" class="btn btn-secondary" onclick="previewRules()">Preview</button>
        </div>
      </div>
    </div>
    <button class="btn btn-primary">Save</button>
    <a href="<?php echo get_setting('site_url'); ?>/admin/customers/address-field-rules" class="btn btn-outline-secondary">Cancel</a>
  </form>
  <div class="mt-4" id="preview_box" style="display:none;">
    <h5>Resolved Effective Rules</h5>
    <pre class="bg-light p-2 small" id="preview_json"></pre>
  </div>
</div>
<script>
function previewRules(){
  const s = document.getElementById('preview_state_id').value;
  const d = document.getElementById('preview_delivery_area_id').value;
  const url = '<?php echo get_setting('site_url'); ?>/admin/customers/address-field-rules/resolvePreview?'+new URLSearchParams({state_id:s||'',delivery_area_id:d||''});
  fetch(url).then(r=>r.json()).then(js=>{document.getElementById('preview_box').style.display='block';document.getElementById('preview_json').textContent=JSON.stringify(js,null,2);});
}
</script>
<?php include get_setting('base_path').'/admin/layouts/footer.php'; ?>
