<?php include get_setting('base_path').'/admin/layouts/header.php'; ?>
<div class="container-fluid">
  <h1 class="h3 mb-3">Customers</h1>
  <table class="table table-sm table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Created</th><th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($customers as $c): ?>
      <tr>
        <td><?php echo (int)$c['id']; ?></td>
        <td><?php echo htmlspecialchars(trim(($c['first_name']??'').' '.($c['last_name']??''))); ?></td>
        <td><?php echo htmlspecialchars($c['email']); ?></td>
        <td><?php echo htmlspecialchars($c['phone']); ?></td>
        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($c['status']); ?></span></td>
        <td><?php echo htmlspecialchars($c['created_at']); ?></td>
        <td><a class="btn btn-sm btn-primary" href="<?php echo get_setting('site_url').'/admin/customers/customers/view/'.$c['id']; ?>">View</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include get_setting('base_path').'/admin/layouts/footer.php'; ?>
