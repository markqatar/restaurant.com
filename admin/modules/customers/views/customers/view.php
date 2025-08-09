<?php include get_setting('base_path').'/admin/layouts/header.php'; ?>
<div class="container-fluid">
  <h1 class="h3 mb-3">Customer #<?php echo (int)$customer['id']; ?></h1>
  <div class="card mb-3">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9"><?php echo htmlspecialchars($customer['email']); ?></dd>
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9"><?php echo htmlspecialchars(trim(($customer['first_name']??'').' '.($customer['last_name']??''))); ?></dd>
        <dt class="col-sm-3">Phone</dt><dd class="col-sm-9"><?php echo htmlspecialchars($customer['phone']); ?></dd>
        <dt class="col-sm-3">DOB</dt><dd class="col-sm-9"><?php echo htmlspecialchars($customer['date_of_birth']); ?></dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge bg-secondary"><?php echo htmlspecialchars($customer['status']); ?></span></dd>
        <dt class="col-sm-3">Created</dt><dd class="col-sm-9"><?php echo htmlspecialchars($customer['created_at']); ?></dd>
      </dl>
    </div>
  </div>
  <a href="<?php echo get_setting('site_url').'/admin/customers/customers'; ?>" class="btn btn-outline-secondary">Back</a>
</div>
<?php include get_setting('base_path').'/admin/layouts/footer.php'; ?>
