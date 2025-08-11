<?php // Expect $pages and $page_title provided by controller ?>
<?php include get_setting('base_path','/var/www/html') . 'admin/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo htmlspecialchars($page_title); ?></h2>
                    <?php if (has_permission($_SESSION['user_id'], 'pages', 'create')): ?>
                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/pages/page/create'; ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo TranslationManager::t('page.add'); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="pagesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th style="width:60px;">IMG</th>
                                        <th><?php echo TranslationManager::t('page.title'); ?></th>
                                        <th><?php echo TranslationManager::t('page.status'); ?></th>
                                        <th><?php echo TranslationManager::t('page.created_at'); ?></th>
                                        <th><?php echo TranslationManager::t('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pages as $page): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($page['id']); ?></td>
                                            <td>
                                                <?php if (!empty($page['featured_image'])): ?>
                                                    <img src="<?php echo htmlspecialchars($page['featured_image']); ?>" alt="" style="width:50px;height:40px;object-fit:cover;border-radius:4px;">
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($page['title']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($page['slug']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $page['is_published'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo $page['is_published'] ? TranslationManager::t('published') : TranslationManager::t('draft'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($page['created_at']); ?></td>
                                            <td>
                                                <?php if (has_permission($_SESSION['user_id'], 'pages', 'edit')): ?>
                                                    <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/pages/page/edit/' . (int)$page['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="<?php echo TranslationManager::t('edit'); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (has_permission($_SESSION['user_id'], 'pages', 'delete')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" 
                                                            onclick="confirmDelete(<?php echo $page['id']; ?>, '<?php echo htmlspecialchars(addslashes($page['title'])); ?>')" title="<?php echo TranslationManager::t('delete'); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
</div>

<script>
$(document).ready(function() {
    $('#pagesTable').DataTable({
        "order": [[ 0, "desc" ]],
        "pageLength": 25
    });
});

function confirmDelete(id, title) {
    Swal.fire({
        title: '<?php echo TranslationManager::t('confirm_delete'); ?>',
        text: title,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?php echo TranslationManager::t('yes_delete'); ?>',
        cancelButtonText: '<?php echo TranslationManager::t('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo get_setting('site_url', 'http://localhost') . '/admin/pages/page/delete'; ?>/' + id;
        }
    });
}
</script>

<?php include get_setting('base_path','/var/www/html') . 'admin/layouts/footer.php'; ?>