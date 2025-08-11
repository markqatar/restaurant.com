<div class="card">
  <div class="card-header"><h5 class="mb-0">New Inventory Transfer</h5></div>
  <div class="card-body">
    <form method="post" action="<?php echo get_setting('site_url'); ?>/admin/suppliers/inventory-transfers/store">
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>" />
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">From Branch</label>
          <select name="from_branch_id" class="form-select" required>
            <option value="">--</option>
            <?php foreach($branches as $b): ?>
              <option value="<?php echo (int)$b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">To Branch</label>
          <select name="to_branch_id" class="form-select" required>
            <option value="">--</option>
            <?php foreach($branches as $b): ?>
              <option value="<?php echo (int)$b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Item Type</label>
          <select name="item_type" class="form-select">
            <option value="product">Product</option>
            <option value="recipe">Recipe</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Item</label>
          <select name="item_id" class="form-select" required>
            <option value="">-- Product --</option>
            <?php foreach($products as $p): ?>
              <option value="<?php echo (int)$p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
            <?php endforeach; ?>
            <option disabled>-- Recipes --</option>
            <?php foreach($recipes as $p): ?>
              <option value="<?php echo (int)$p['id']; ?>" data-type="recipe">&#127859; <?php echo htmlspecialchars($p['name']); ?></option>
            <?php endforeach; ?>
          </select>
          <small class="text-muted">Switch type if selecting a recipe.</small>
        </div>
        <div class="col-md-3">
          <label class="form-label">Quantity</label>
          <input type="number" step="0.0001" name="quantity" class="form-control" required />
        </div>
        <div class="col-md-3">
          <label class="form-label">Unit</label>
          <select name="unit_id" class="form-select">
            <option value="">(auto)</option>
            <?php foreach($units as $u): ?>
              <option value="<?php echo (int)$u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Note</label>
          <input type="text" name="note" class="form-control" maxlength="255" />
        </div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary">Transfer</button>
        <a href="<?php echo get_setting('site_url'); ?>/admin/suppliers/inventory-transfers" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<script>
// Auto change item_type based on selection group (optional aid)
</script>
