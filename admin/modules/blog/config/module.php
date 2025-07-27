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
    ],

    'extends' => 'access-management', // Questo modulo estende access-management
    'author' => 'Tuo Nome',
    'license' => 'MIT'
];
