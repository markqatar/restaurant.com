<?php
return [
    'name' => 'Shop',
    'version' => '1.0.0',
    'description' => 'Gestione dei negozi e filiali',
    'icon' => 'fa-store', // Icona FontAwesome
    'dependencies' => [
        'system',
        'access-management'
    ],
    'hooks' => [
        'users.table.columns' => function () {
            echo '<th>' . TranslationManager::t('shops.branch') . '</th>';
        },
        'users.table.rows' => function ($user) {
            $branches = get_user_branches($user['id']); // Funzione helper
            echo '<td>' . htmlspecialchars(implode(', ', $branches)) . '</td>';
        },
        'users.edit.form.sections' => function ($user) {
            require_once admin_module_path('/models/Branch.php', 'shop');

            $branch_model = new Branch();
            $branches = $branch_model->read(true);
            $user_branches = $branch_model->getUserBranches($user['id']);

            $assigned_branch_ids = array_column($user_branches, 'id');
            $primary_branch_id = null;
            foreach ($user_branches as $ub) {
                if ($ub['is_primary']) {
                    $primary_branch_id = $ub['id'];
                    break;
                }
            }

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
                        <?php foreach ($branches as $branch):
                            $is_assigned = in_array($branch['id'], $assigned_branch_ids);
                            $is_primary = ($primary_branch_id == $branch['id']);
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($branch['name']); ?></td>
                                <td><?php echo htmlspecialchars($branch['city_name'] ?? $branch['address']); ?></td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input branch-checkbox" type="checkbox" name="branch_ids[]" value="<?php echo $branch['id']; ?>" <?php echo $is_assigned ? 'checked' : ''; ?>>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input primary-branch-radio" type="radio" name="primary_branch_id" value="<?php echo $branch['id']; ?>" <?php echo $is_primary ? 'checked' : ''; ?> <?php echo $is_assigned ? '' : 'disabled'; ?>>
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
<?php
        }
    ],
    'logicHooks' => [
        'user.after_create' => function ($user_id, $post_data) {
            $branch_ids = $post_data['branch_ids'] ?? [];
            $primary_branch_id = $post_data['primary_branch_id'] ?? null;

            require_once admin_module_path('/models/Branch.php', 'shop');
            $branch_model = new Branch();

            foreach ($branch_ids as $branch_id) {
                $is_primary = ($branch_id == $primary_branch_id);
                $branch_model->assignUser($user_id, $branch_id, $is_primary);
            }
        },
        'user.after_update' => function ($user_id, $post_data) {
            $branch_ids = $post_data['branch_ids'] ?? [];
            $primary_branch_id = $post_data['primary_branch_id'] ?? null;

            require_once admin_module_path('/models/Branch.php', 'shop');
            $branch_model = new Branch();

            $current_branches = $branch_model->getUserBranches($user_id);
            $current_branch_ids = array_column($current_branches, 'id');

            foreach ($current_branch_ids as $current_branch_id) {
                if (!in_array($current_branch_id, $branch_ids)) {
                    $branch_model->removeUser($user_id, $current_branch_id);
                }
            }

            foreach ($branch_ids as $branch_id) {
                $is_primary = ($branch_id == $primary_branch_id);
                $branch_model->assignUser($user_id, $branch_id, $is_primary);
            }
        }
    ],

    'extends' => 'access-management', // Questo modulo estende access-management
    'author' => 'Tuo Nome',
    'license' => 'MIT'
];
