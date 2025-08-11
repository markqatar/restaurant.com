<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h5 class="mb-0">Inventory Transfers</h5>
    <a href="<?php echo get_setting('site_url', 'http://localhost').'admin/suppliers/inventory-transfers/create'; ?>" class="btn btn-primary btn-sm">New Transfer</a>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-sm table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Item</th>
            <th>From</th>
            <th>To</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Note</th>
            <th>At</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?php echo (int)$r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['item_type']=='recipe'?$r['recipe_name']:$r['product_name']); ?></td>
            <td><?php echo htmlspecialchars($r['from_branch_name']); ?></td>
            <td><?php echo htmlspecialchars($r['to_branch_name']); ?></td>
            <td><?php echo (float)$r['quantity']; ?></td>
            <td><?php echo htmlspecialchars($r['unit_name']); ?></td>
            <td><?php echo htmlspecialchars($r['note']); ?></td>
            <td><?php echo htmlspecialchars($r['created_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
