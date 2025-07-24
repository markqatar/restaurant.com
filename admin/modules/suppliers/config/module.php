<?php
return [
    'name' => 'Shops',
    'icon' => 'fa-store', // Icona FontAwesome
    'menu' => [
        [
            'title' => 'Shops',
            'route' => '/admin/shops',
            'permissions' => ['view_shops']
        ],
        [
            'title' => 'Branches',
            'route' => '/admin/shops/branches',
            'permissions' => ['view_branches']
        ],
        [
            'title' => 'Shop Settings',
            'route' => '/admin/shops/settings',
            'permissions' => ['manage_shop_settings']
        ]
    ],
    'dependencies' => [
        'system' // Il modulo system è necessario per i settings globali
    ]
];