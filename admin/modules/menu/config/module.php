<?php
return [
    'name' => 'Menu',
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
    'author' => 'Marcello Fornaciari',
    'license' => 'MIT',
    'permissions' => [
        ['menu','view','Menu View','View admin menu items'],
        ['menu','create','Menu Create','Create admin menu items'],
        ['menu','update','Menu Update','Update admin menu items'],
        ['menu','delete','Menu Delete','Delete admin menu items'],
    ]
];
