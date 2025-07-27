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
    'hooks_files' => [
        'ui' => admin_module_path('/hooks/ui_hooks.php', 'shop'),
        'logic' => admin_module_path('/hooks/logic_hooks.php', 'shop')
    ],
    'extends' => 'access-management', // Estende il modulo access-management
    'author' => 'Tuo Nome',
    'license' => 'MIT'
];