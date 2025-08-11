<?php
return [
    'name' => 'Pages',
    'version' => '1.0.0',
    'description' => 'Gestione pagine contenuto statico',
    'icon' => 'fa-file-alt',
    'dependencies' => [
        'system',
        'access-management'
    ],
    'hooks' => [
    ],
    'extends' => 'access-management',
    'author' => 'Marcello Fornaciari',
    'license' => 'MIT',
    'permissions' => [
        ['pages','view','Pages View','View pages'],
        ['pages','create','Pages Create','Create pages'],
        ['pages','edit','Pages Edit','Edit pages'],
        ['pages','delete','Pages Delete','Delete pages'],
    ]
];
