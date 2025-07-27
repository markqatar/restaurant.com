<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/process_language.php';
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"><?php echo TranslationManager::t('settings'); ?></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?php echo admin_url('index'); ?>"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo TranslationManager::t('menu.management'); ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card border-top border-0 border-4 border-primary">
                <div class="card-body p-5">
                    <div class="card-title d-flex align-items-center">
                        <div><i class="bx bx-menu me-1 font-22 text-primary"></i></div>
                        <h5 class="mb-0 text-primary"><?php echo TranslationManager::t('menu.management'); ?></h5>
                        <div class="ms-auto">
                            <a href="?action=create" class="btn btn-primary">
                                <i class="bx bx-plus"></i> <?php echo TranslationManager::t('add.new'); ?>
                            </a>
                        </div>
                    </div>
                    <hr/>
                    
                    <?php if (isset($notification)): ?>
                    <div class="alert alert-<?php echo $notification['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $notification['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-gradient-cosmic text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-white">Total Menu Items</p>
                                            <h4 class="my-1 text-white"><?php echo $total_items; ?></h4>
                                        </div>
                                        <div class="widgets-icons-2 bg-white text-cosmic ms-auto">
                                            <i class="bx bx-menu"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-gradient-burning text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-white">Active Items</p>
                                            <h4 class="my-1 text-white"><?php echo $active_items; ?></h4>
                                        </div>
                                        <div class="widgets-icons-2 bg-white text-burning ms-auto">
                                            <i class="bx bx-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo TranslationManager::t('title'); ?></th>
                                    <th><?php echo TranslationManager::t('parent'); ?></th>
                                    <th><?php echo TranslationManager::t('url'); ?></th>
                                    <th><?php echo TranslationManager::t('icon'); ?></th>
                                    <th><?php echo TranslationManager::t('order'); ?></th>
                                    <th><?php echo TranslationManager::t('permission'); ?></th>
                                    <th><?php echo TranslationManager::t('status'); ?></th>
                                    <th><?php echo TranslationManager::t('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menu_items as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td>
                                        <?php if ($item['parent_id']): ?>
                                            <span class="ms-3">↳ </span>
                                        <?php endif; ?>
                                        <i class="<?php echo $item['icon']; ?> me-2"></i>
                                        <?php echo htmlspecialchars($item['title']); ?>
                                        <?php if ($item['is_separator']): ?>
                                            <span class="badge bg-secondary ms-2">Separator</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['parent_id']): ?>
                                            <?php
                                            $parent = array_filter($menu_items, function($i) use ($item) {
                                                return $i['id'] == $item['parent_id'];
                                            });
                                            $parent = reset($parent);
                                            echo $parent ? htmlspecialchars($parent['title']) : '-';
                                            ?>
                                        <?php else: ?>
                                            <span class="text-muted">Root</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['url']): ?>
                                            <code><?php echo htmlspecialchars($item['url']); ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="<?php echo $item['icon']; ?>" title="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $item['sort_order']; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($item['permission_module']): ?>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($item['permission_module']); ?>:<?php echo htmlspecialchars($item['permission_action']); ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex order-actions">
                                            <a href="?action=edit&id=<?php echo $item['id']; ?>" class="text-primary">
                                                <i class="bx bxs-edit"></i>
                                            </a>
                                            <a href="javascript:;" class="text-danger ms-3" onclick="confirmDelete(<?php echo $item['id']; ?>)">
                                                <i class="bx bxs-trash"></i>
                                            </a>
                                        </div>
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
function confirmDelete(id) {
    if (confirm('<?php echo TranslationManager::t("confirm.delete"); ?>')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php require_once __DIR__ . '/../../admin/includes/footer.php'; ?>