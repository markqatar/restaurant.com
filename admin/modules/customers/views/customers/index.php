<?php include get_setting('base_path').'/admin/layouts/header.php'; ?>
<div class="container-fluid">
  <h1 class="h3 mb-3">Customers</h1>
  <table id="customersTable" class="table table-sm table-striped table-bordered w-100">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  $('#customersTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 25,
    order: [[0,'desc']],
    ajax: {
      url: '<?php echo get_setting('site_url'); ?>/admin/customers/customers/datatable',
      type: 'POST'
    },
    columns: [
      { data: 0 },
      { data: 1 },
      { data: 2 },
      { data: 3 },
      { data: 4, orderable:false, searchable:false },
      { data: 5 },
      { data: 6, orderable:false, searchable:false }
    ]
  });
});
</script>
<?php include get_setting('base_path').'/admin/layouts/footer.php'; ?>
