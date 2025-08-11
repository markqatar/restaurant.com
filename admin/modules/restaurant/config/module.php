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
    'author' => 'Marcello Fornaciari',
    'license' => 'MIT',
    'permissions' => [
        // Placeholder restaurant permissions (adjust with real features)
        ['restaurant', 'view', 'Restaurant View', 'View restaurant data'],
        ['restaurant', 'update', 'Restaurant Update', 'Update restaurant data'],
        // Recipes
        ['recipes', 'view', 'Recipes View', 'List & view recipes'],
        ['recipes', 'create', 'Recipes Create', 'Create new recipes'],
        ['recipes', 'update', 'Recipes Update', 'Edit recipes'],
        ['recipes', 'delete', 'Recipes Delete', 'Delete recipes'],
        // Production (batch making of recipes)
        ['production', 'batch', 'Production Batch', 'Execute recipe batch production'],
        // Inventory
        ['inventory', 'view', 'Inventory View', 'View ingredient inventory']
    ]
];
