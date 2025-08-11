<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/CategoryController.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'categories', 'view')) {
    header('Location: /admin/login');
    exit;
}

$controller = new CategoryController($pdo);
$data = $controller->index();
$pageTitle = TranslationManager::t('categories');
include 'includes/header.php';
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2><?php echo TranslationManager::t('categories'); ?></h2>
          <div>
            <?php if (has_permission($_SESSION['user_id'], 'categories', 'create')): ?>
              <a href="categories-create.php" class="btn btn-primary"><i class="fas fa-plus"></i> <?php echo TranslationManager::t('add_category'); ?></a>
            <?php endif; ?>
            <a href="articles.php" class="btn btn-outline-secondary ms-2"><i class="fas fa-newspaper"></i> <?php echo TranslationManager::t('articles'); ?></a>
          </div>
        </div>
        <?php if (isset($_GET['success'])): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($_GET['success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if (isset($_GET['error'])): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($_GET['error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <div class="card">
          <div class="card-body table-responsive">
            <table class="table table-striped" id="categoriesTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th><?php echo TranslationManager::t('name'); ?></th>
                  <th><?php echo TranslationManager::t('slug'); ?></th>
                  <th><?php echo TranslationManager::t('articles'); ?></th>
                  <th><?php echo TranslationManager::t('status'); ?></th>
                  <th><?php echo TranslationManager::t('actions'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data['categories'] as $cat): ?>
                  <tr>
                    <td><?php echo (int)$cat['id']; ?></td>
                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($cat['slug']); ?></small></td>
                    <td><span class="badge bg-info"><?php echo (int)($cat['article_count'] ?? 0); ?></span></td>
                    <td><?php if ($cat['is_active']): ?><span class="badge bg-success">Active</span><?php else: ?><span class="badge bg-secondary">Inactive</span><?php endif; ?></td>
                    <td>
                      <?php if (has_permission($_SESSION['user_id'],'categories','edit')): ?>
                        <a href="categories-edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                      <?php endif; ?>
                      <?php if (has_permission($_SESSION['user_id'],'categories','delete')): ?>
                        <button class="btn btn-sm btn-outline-danger ms-1" onclick="confirmDelete(<?php echo $cat['id']; ?>,'<?php echo htmlspecialchars(addslashes($cat['name'])); ?>')"><i class="fas fa-trash"></i></button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$(function(){ $('#categoriesTable').DataTable({ order:[[0,'desc']], pageLength:50 }); });
function confirmDelete(id,name){
  Swal.fire({title:'<?php echo TranslationManager::t('confirm_delete'); ?>',text:'<?php echo TranslationManager::t('confirm_delete'); ?> '+name+'?',icon:'warning',showCancelButton:true,confirmButtonColor:'#d33',cancelButtonColor:'#3085d6',confirmButtonText:'<?php echo TranslationManager::t('yes_delete'); ?>'}).then(r=>{ if(r.isConfirmed){ window.location='categories.php?action=delete&id='+id; }});
}
</script>
<?php include 'includes/footer.php'; ?>
