<?php
return [
    'name' => 'Restaurant',
    'version' => '1.0.0',
    'description' => 'Modulo per la gestione ristoranti (menu, tavoli, prenotazioni)',
    'icon' => 'fa-utensils', // Icona FontAwesome
    'dependencies' => [
        'shops'
    ],
    'hooks' => [
        'after_create' => function ($module, $record_id, $new_data) {
            log_action($module, 'create', $record_id, null, $new_data);
        },
        'after_update' => function ($module, $record_id, $old_data, $new_data) {
            log_action($module, 'update', $record_id, $old_data, $new_data);
        },
        'after_delete' => function ($module, $record_id, $old_data) {
            log_action($module, 'delete', $record_id, $old_data, null);
        }
    ],

    'extends' => 'shops', // Questo modulo estende Shops
    'author' => 'Tuo Nome',
    'license' => 'MIT',
    'permissions' => [
        // Placeholder restaurant permissions (adjust with real features)
        ['restaurant','view','Restaurant View','View restaurant data'],
        ['restaurant','update','Restaurant Update','Update restaurant data'],
    ]
];
