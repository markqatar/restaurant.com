<?php
HookManager::registerHook('users.edit.form.sections', function($user) {
    include __DIR__ . '/partials/user_branches_edit.php';
});

HookManager::registerHook('users.create.form.sections', function() {
    include __DIR__ . '/partials/user_branches_create.php';
});

HookManager::registerHook('users.table.columns', function() {
    echo '<th>' . TranslationManager::t('shops.branch') . '</th>';
});

HookManager::registerHook('users.table.rows', function($user) {
    $branches = get_user_branches($user['id']); 
    echo '<td>' . htmlspecialchars(implode(', ', $branches)) . '</td>';
});