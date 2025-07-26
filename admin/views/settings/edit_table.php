<?php
$page_title = TranslationManager::t('edit_table');
include __DIR__ . '/../admin/layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= TranslationManager::t('edit_table') ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php"><?= TranslationManager::t('dashboard') ?></a></li>
        <li class="breadcrumb-item"><a href="settings.php"><?= TranslationManager::t('settings') ?></a></li>
        <li class="breadcrumb-item"><a href="settings.php?section=tables"><?= TranslationManager::t('tables') ?></a></li>
        <li class="breadcrumb-item active"><?= TranslationManager::t('edit_table') ?></li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    <?= TranslationManager::t('edit_table') ?>: <?= htmlspecialchars($table['name']) ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?= csrf_token_field() ?>
                        
                        <div class="mb-3">
                            <label for="room_id" class="form-label"><?= TranslationManager::t('select_room') ?> *</label>
                            <select class="form-select" id="room_id" name="room_id" required>
                                <option value=""><?= TranslationManager::t('select_room') ?></option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= $room['id'] ?>" <?= $room['id'] == $table['room_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($room['branch_name'] . ' - ' . $room['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label"><?= TranslationManager::t('table_name') ?> *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($table['name']) ?>" required>
                            <div class="form-text"><?= TranslationManager::t('table_examples') ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="seats" class="form-label"><?= TranslationManager::t('seats') ?> *</label>
                            <input type="number" class="form-control" id="seats" name="seats" min="1" max="20" 
                                   value="<?= $table['seats'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= $table['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <?= TranslationManager::t('active') ?>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i><?= TranslationManager::t('update') ?>
                            </button>
                            <a href="settings.php?section=tables" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i><?= TranslationManager::t('cancel') ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../admin/includes/footer.php'; ?>