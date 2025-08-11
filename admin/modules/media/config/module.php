<?php
return [
    'name' => 'Media Library',
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
        ['media','view','Media View','View media library'],
        ['media','upload','Media Upload','Upload media'],
        ['media','edit','Media Edit','Edit media metadata'],
        ['media','delete','Media Delete','Delete media'],
    ]
];
