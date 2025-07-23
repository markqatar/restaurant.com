<?php
$page_title = t('rooms_management');
include __DIR__ . '/../../admin/includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= t('settings') ?> - <?= t('rooms_management') ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php"><?= t('dashboard') ?></a></li>
        <li class="breadcrumb-item"><a href="settings.php"><?= t('settings') ?></a></li>
        <li class="breadcrumb-item active"><?= t('rooms') ?></li>
    </ol>

    <?php 
    if (function_exists('display_notification')) {
        display_notification(); 
    } elseif (function_exists('show_notifications')) {
        show_notifications();
    } elseif (isset($_SESSION['notifications'])) {
        foreach ($_SESSION['notifications'] as $notification) {
            echo '<div class="alert alert-' . $notification['type'] . ' alert-dismissible fade show" role="alert">';
            echo $notification['message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        $_SESSION['notifications'] = [];
    }
    ?>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-door-open me-1"></i>
                    <?= t('rooms') ?>
                </div>
                <a href="?section=rooms&action=create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i><?= t('add_room') ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($rooms)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted"><?= t('msg.no_data') ?></p>
                    <a href="?section=rooms&action=create" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i><?= t('add_room') ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><?= t('room_name') ?></th>
                                <th><?= t('branch.branch_name') ?></th>
                                <th><?= t('description') ?></th>
                                <th><?= t('status') ?></th>
                                <th><?= t('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['name']) ?></td>
                                    <td><?= htmlspecialchars($room['branch_name']) ?></td>
                                    <td><?= htmlspecialchars($room['description']) ?></td>
                                    <td>
                                        <?php if ($room['is_active']): ?>
                                            <span class="badge bg-success"><?= t('active') ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= t('inactive') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?section=rooms&action=edit&id=<?= $room['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="<?= t('edit') ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?section=rooms&action=delete&id=<?= $room['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" title="<?= t('delete') ?>"
                                           onclick="return confirm('<?= t('msg.confirm_delete') ?>')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../admin/includes/footer.php'; ?>