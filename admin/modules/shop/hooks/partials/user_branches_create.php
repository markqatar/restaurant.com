<?php
require_once admin_module_path('/models/Branch.php', 'shop');

$branch_model = new Branch();
$branches = $branch_model->read(true);
?>

<div class="mb-4">
    <h5><?php echo TranslationManager::t('user.branch_assignments'); ?></h5>
    <p class="text-muted small"><?php echo TranslationManager::t('user.select_branches'); ?></p>

    <?php if (!empty($branches)): ?>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th><?php echo TranslationManager::t('branch.name'); ?></th>
                        <th><?php echo TranslationManager::t('branch.location'); ?></th>
                        <th class="text-center"><?php echo TranslationManager::t('assign'); ?></th>
                        <th class="text-center"><?php echo TranslationManager::t('primary'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($branches as $branch): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($branch['name']); ?></td>
                            <td><?php echo htmlspecialchars($branch['city_name'] ?? $branch['address']); ?></td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input branch-checkbox" type="checkbox" name="branch_ids[]" value="<?php echo $branch['id']; ?>">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input primary-branch-radio" type="radio" name="primary_branch_id" value="<?php echo $branch['id']; ?>" disabled>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <?php echo TranslationManager::t('branch.no_branches'); ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.branch-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const row = this.closest('tr');
        const radio = row.querySelector('.primary-branch-radio');
        if (this.checked) {
            radio.disabled = false;
        } else {
            radio.disabled = true;
            radio.checked = false;
        }
    });
});
</script>