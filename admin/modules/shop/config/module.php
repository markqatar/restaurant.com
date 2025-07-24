<?php
return [
    'name' => 'Shops',
    'version' => '1.0.0',
    'description' => 'Gestione dei negozi e filiali',
    'icon' => 'fa-store', // Icona FontAwesome
    'dependencies' => [
        'system', 'access-management'
    ],
        'hooks' => [
        'users.table.columns' => function() {
            echo '<th>' . TranslationManager::t('shops.branch') . '</th>';
        },
        'users.table.rows' => function($user) {
            $branches = get_user_branches($user['id']); // Funzione helper
            echo '<td>' . htmlspecialchars(implode(', ', $branches)) . '</td>';
        }
    ],

    'extends' => 'access-management', // Questo modulo estende access-management
    'author' => 'Tuo Nome',
    'license' => 'MIT'
];