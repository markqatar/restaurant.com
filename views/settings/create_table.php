<?php
$page_title = t('add_table');
include __DIR__ . '/../admin/includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= t('add_table') ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php"><?= t('dashboard') ?></a></li>
        <li class="breadcrumb-item"><a href="settings.php"><?= t('settings') ?></a></li>
        <li class="breadcrumb-item"><a href="settings.php?section=tables"><?= t('tables') ?></a></li>
        <li class="breadcrumb-item active"><?= t('add_table') ?></li>
    </ol>

    <?php display_notification(); ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    <?= t('add_table') ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?= csrf_token_field() ?>
                        
                        <div class="mb-3">
                            <label for="room_id" class="form-label"><?= t('select_room') ?> *</label>
                            <select class="form-select" id="room_id" name="room_id" required>
                                <option value=""><?= t('select_room') ?></option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= $room['id'] ?>">
                                        <?= htmlspecialchars($room['branch_name'] . ' - ' . $room['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label"><?= t('table_name') ?> *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="form-text"><?= t('table_examples') ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="seats" class="form-label"><?= t('seats') ?> *</label>
                            <input type="number" class="form-control" id="seats" name="seats" min="1" max="20" value="4" required>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    <?= t('active') ?>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i><?= t('save') ?>
                            </button>
                            <a href="settings.php?section=tables" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i><?= t('cancel') ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../admin/includes/footer.php'; ?>