<?php
return [
    'name' => 'Blog',
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
        ['articles','view','Articles View','View articles'],
        ['articles','create','Articles Create','Create articles'],
        ['articles','edit','Articles Edit','Edit articles'],
        ['articles','delete','Articles Delete','Delete articles'],
        ['categories','view','Categories View','View categories'],
        ['categories','create','Categories Create','Create categories'],
        ['categories','update','Categories Update','Update categories'],
        ['categories','delete','Categories Delete','Delete categories'],
    ]
];
