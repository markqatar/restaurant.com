<?php include get_setting('base_path').'/admin/layouts/header.php'; ?>
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Address Field Rules</h1>
    <?php if(can('address_field_rules','update')): ?>
    <div>
      <a class="btn btn-sm btn-primary" href="<?php echo get_setting('site_url'); ?>/admin/customers/address-field-rules/create">New Rule</a>
    </div>
    <?php endif; ?>
  </div>
  <table class="table table-sm table-bordered align-middle">
    <thead>
      <tr>
        <th>ID</th><th>State</th><th>Delivery Area</th><th>Field</th><th>Requirement</th><th>Label</th><th>Sort</th><th>Active</th><?php if(can('address_field_rules','update')) echo '<th></th>'; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($rules as $r): ?>
      <tr>
        <td><?php echo (int)$r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['state_id']); ?></td>
        <td><?php echo htmlspecialchars($r['delivery_area_id']); ?></td>
        <td><code><?php echo htmlspecialchars($r['field_key']); ?></code></td>
        <td><?php echo htmlspecialchars($r['requirement']); ?></td>
        <td><?php echo htmlspecialchars($r['label']); ?></td>
        <td><?php echo (int)$r['sort_order']; ?></td>
        <td><?php echo $r['active']?'✔':'✖'; ?></td>
        <?php if(can('address_field_rules','update')): ?>
        <td class="text-nowrap">
          <a class="btn btn-sm btn-outline-secondary" href="<?php echo get_setting('site_url'); ?>/admin/customers/address-field-rules/edit/<?php echo (int)$r['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete rule?');" href="<?php echo get_setting('site_url'); ?>/admin/customers/address-field-rules/delete/<?php echo (int)$r['id']; ?>">Del</a>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include get_setting('base_path').'/admin/layouts/footer.php'; ?>
