<?php
return [
    'configuration' => 'System Configuration',
    'global_settings_tab' => 'Global Settings',
    'languages_tab' => 'Languages',
    'site_name' => 'Site Name',
    'site_url' => 'Site URL',
    'logo' => 'Logo',
    'currency' => 'Currency',
    'currencies' => 'Currencies',
    'currencies_help' => 'List of enabled currency codes (ISO 4217), first is default',
    'timezone' => 'Timezone',
    'date_format_admin' => 'Admin Date Format',
    'date_format_public' => 'Public Date Format',
    'website_enabled' => 'Website Enabled',
    'add_language' => 'Add Language',
    'code' => 'Code',
    'name' => 'Name',
    'direction' => 'Direction',
    'admin' => 'Admin',
    'public' => 'Public',
    'actions' => 'Actions',
    'delete_language' => 'Delete Language',

    'modal' => [
        'direction_ltr' => 'LTR',
        'direction_rtl' => 'RTL'
    ],

    'success' => [
        'settings_updated' => 'Settings updated successfully',
        'language_added' => 'Language added successfully',
        'language_deleted' => 'Language deleted successfully',
        'logo_deleted' => 'Logo deleted successfully'
    ],

    'error' => [
        'invalid_csrf' => 'Invalid security token',
        'settings_update_failed' => 'Failed to update settings',
        'language_add_failed' => 'Failed to add language',
        'language_delete_failed' => 'Failed to delete language',
        'unauthorized' => 'Unauthorized access',
        'no_logo' => 'No logo to delete',
        'delete_failed' => 'Failed to delete logo file',
        'update_failed' => 'Failed to update database'
    ],

    // Activity Logs / System Logs
    'system' => [
        'activity_logs' => 'Activity Logs'
    ],
    'log' => [
        'details' => 'Log Details',
        'not_found' => 'Log not found'
    ],
    'restore' => [
        'confirm' => 'Restore this change?',
        'description' => 'This will attempt to restore the previous data.'
    ],
    'yes_restore' => 'Yes, Restore',
    'details' => 'Details',
    'restore' => 'Restore',
    // Admin Menu management keys (nested)
    'menu' => [
        'management' => 'Menu Management',
        'total_items' => 'Total Items',
        'active_items' => 'Active Items',
        'items' => 'Menu Items',
    ],
    'add' => [
        'new' => 'Add New',
        'menu' => [
            'item' => 'Add Menu Item'
        ],
    ],
    'parent' => [
        'menu' => 'Parent Menu'
    ],
    'select' => [
        'parent' => 'Select Parent'
    ],
    'sort' => [
        'order' => 'Sort Order'
    ],
    'permission' => [
        'module' => 'Permission Module',
        'action' => [
            '_value' => 'Permission Action',
            'view' => 'View',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
        ],
    ],
    'target' => 'Target',
    'css' => [
        'class' => 'CSS Class'
    ],
    'is' => [
        'active' => 'Active',
        'separator' => 'Separator'
    ],
    'example' => [
        'url' => 'e.g., users.php',
        'module' => 'e.g., users'
    ],
    'leave' => [
        'empty' => [
            'parent' => 'Leave empty for parent menu items',
            'permission' => 'Leave empty for no permission check'
        ]
    ],
    'preview' => 'Preview',
    'browse' => [
        'icons' => 'Browse FontAwesome Icons',
        'icons_info' => 'Open FontAwesome icons page to browse classes'
    ],
    'same' => [
        'window' => 'Same Window'
    ],
    'new' => [
        'window' => 'New Window'
    ],
    'icon' => [
        'placeholder' => 'fas fa-circle'
    ],
    'custom' => [
        'class' => [
            'placeholder' => 'custom-class'
        ]
    ],
    'root' => 'Root',
    'order' => 'Order',
    // 'parent' scalar replaced by nested array above; if needed root label kept.
    // (Status & confirmation keys removed - now in global file)
];