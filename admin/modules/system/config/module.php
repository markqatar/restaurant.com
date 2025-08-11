<?php

return [
    'name' => 'System Core',
    'version' => '1.0.0',
    'description' => 'Funzionalità di base del sistema',
    'dependencies' => [],
    'extends' => null,
    'author' => 'Marcello Fornaciari',
    'license' => 'MIT',
    'permissions' => [
        ['system_logs','view','System Logs View','View activity logs'],
        ['system_logs','restore','System Logs Restore','Restore records from logs'],
        ['system','view','System Config View','View system configuration'],
        ['system','update','System Config Update','Update system configuration'],
    ]
];