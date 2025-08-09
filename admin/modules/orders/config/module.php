<?php
return [
    'name' => 'Orders',
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
    'license' => 'MIT',
    'permissions' => [
        ['orders','view','Orders View','View orders'],
        ['orders','create','Orders Create','Create orders'],
        ['orders','update','Orders Update','Update orders'],
        ['orders','delete','Orders Delete','Delete orders'],
    ]
];
