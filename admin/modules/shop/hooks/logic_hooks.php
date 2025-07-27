<?php
HookManager::registerLogicHook('user.after_create', function($user_id, $post_data) {
    $branch_ids = $post_data['branch_ids'] ?? [];
    $primary_branch_id = $post_data['primary_branch_id'] ?? null;

    require_once admin_module_path('/models/Branch.php', 'shop');
    $branch_model = new Branch();

    foreach ($branch_ids as $branch_id) {
        $is_primary = ($branch_id == $primary_branch_id);
        $branch_model->assignUser($user_id, $branch_id, $is_primary);
    }
});

HookManager::registerLogicHook('user.after_update', function($user_id, $post_data) {
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
});