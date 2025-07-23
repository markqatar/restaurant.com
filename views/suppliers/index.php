<?php
require_once '../admin/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-truck me-2"></i><?php echo t('supplier.management'); ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <?php if (has_permission($_SESSION['user_id'], 'suppliers', 'create')): ?>
                    <a href="suppliers/create" class="btn btn-primary me-2">
                        <i class="fas fa-plus me-1"></i><?php echo t('supplier.new_supplier'); ?>
                    </a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        <?php echo t('supplier.total_suppliers'); ?></div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_suppliers; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-truck fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        <?php echo t('supplier.active_suppliers'); ?></div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count(array_filter($suppliers, function($s) { return $s['is_active']; })); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suppliers Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo t('supplier.supplier_list'); ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered datatable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo t('name'); ?></th>
                                    <th><?php echo t('address'); ?></th>
                                    <th><?php echo t('supplier.city'); ?></th>
                                    <th><?php echo t('supplier.country'); ?></th>
                                    <th><?php echo t('phone'); ?></th>
                                    <th><?php echo t('email'); ?></th>
                                    <th><?php echo t('status'); ?></th>
                                    <th><?php echo t('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($suppliers as $supplier): ?>
                                <tr>
                                    <td><?php echo $supplier['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($supplier['name']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($supplier['address'] ?? 'N/A'); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($supplier['city_name'] ?? 'N/A'); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($supplier['country_name'] ?? 'N/A'); ?>
                                    </td>
                                    <td>
                                        <?php if ($supplier['phone']): ?>
                                            <a href="tel:<?php echo $supplier['phone']; ?>"><?php echo $supplier['phone']; ?></a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($supplier['email']): ?>
                                            <a href="mailto:<?php echo $supplier['email']; ?>"><?php echo $supplier['email']; ?></a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($supplier['is_active']): ?>
                                            <span class="badge bg-success"><?php echo t('active'); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><?php echo t('inactive'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="suppliers/view/<?php echo $supplier['id']; ?>" 
                                               class="btn btn-sm btn-outline-info" title="<?php echo t('btn.view'); ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if (has_permission($_SESSION['user_id'], 'suppliers', 'update')): ?>
                                            <a href="suppliers/edit/<?php echo $supplier['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="<?php echo t('edit'); ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php if (has_permission($_SESSION['user_id'], 'suppliers', 'delete')): ?>
                                            <button onclick="deleteSupplier(<?php echo $supplier['id']; ?>)" 
                                                    class="btn btn-sm btn-outline-danger" title="<?php echo t('delete'); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
function deleteSupplier(supplierId) {
    confirmDelete('<?php echo addslashes(t('supplier.confirm_delete')); ?>')
        .then((result) => {
            if (result.isConfirmed) {
                window.location.href = `suppliers/delete/${supplierId}`;
            }
        });
}
</script>

<?php include '../admin/includes/footer.php'; ?>