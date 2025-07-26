<?php
$page_title = t('edit_room');
include __DIR__ . '/../admin/layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= t('edit_room') ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php"><?= t('dashboard') ?></a></li>
        <li class="breadcrumb-item"><a href="settings.php"><?= t('settings') ?></a></li>
        <li class="breadcrumb-item"><a href="settings.php?section=rooms"><?= t('rooms') ?></a></li>
        <li class="breadcrumb-item active"><?= t('edit_room') ?></li>
    </ol>

    <?php display_notification(); ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-door-open me-1"></i>
                    <?= t('edit_room') ?>: <?= htmlspecialchars($room['name']) ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?= csrf_token_field() ?>
                        
                        <div class="mb-3">
                            <label for="branch_id" class="form-label"><?= t('select_branch') ?> *</label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value=""><?= t('select_branch') ?></option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= $branch['id'] == $room['branch_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($branch['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label"><?= t('room_name') ?> *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($room['name']) ?>" required>
                            <div class="form-text"><?= t('room_examples') ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><?= t('room_description') ?></label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($room['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= $room['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <?= t('active') ?>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i><?= t('update') ?>
                            </button>
                            <a href="settings.php?section=rooms" class="btn btn-secondary">
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