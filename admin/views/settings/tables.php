<?php
$page_title = t('tables_management');
include __DIR__ . '/../admin/includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= t('settings') ?> - <?= t('tables_management') ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php"><?= t('dashboard') ?></a></li>
        <li class="breadcrumb-item"><a href="settings.php"><?= t('settings') ?></a></li>
        <li class="breadcrumb-item active"><?= t('tables') ?></li>
    </ol>

    <?php display_notification(); ?>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    <?= t('tables') ?>
                </div>
                <a href="?section=tables&action=create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i><?= t('add_table') ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($tables)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-table fa-3x text-muted mb-3"></i>
                    <p class="text-muted"><?= t('msg.no_data') ?></p>
                    <a href="?section=tables&action=create" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i><?= t('add_table') ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><?= t('table_name') ?></th>
                                <th><?= t('room') ?></th>
                                <th><?= t('branch.branch_name') ?></th>
                                <th><?= t('seats') ?></th>
                                <th><?= t('status') ?></th>
                                <th><?= t('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $table): ?>
                                <tr>
                                    <td><?= htmlspecialchars($table['name']) ?></td>
                                    <td><?= htmlspecialchars($table['room_name']) ?></td>
                                    <td><?= htmlspecialchars($table['branch_name']) ?></td>
                                    <td><?= $table['seats'] ?></td>
                                    <td>
                                        <?php if ($table['is_active']): ?>
                                            <span class="badge bg-success"><?= t('active') ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= t('inactive') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?section=tables&action=edit&id=<?= $table['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="<?= t('edit') ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?section=tables&action=delete&id=<?= $table['id'] ?>" 
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

<?php include __DIR__ . '/../admin/includes/footer.php'; ?>