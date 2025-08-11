<?php

return [
    'name' => 'Users Management',
    'description' => 'Gestione degli utenti e gruppi utenti.',
    'version' => '1.0.0',
    'dependencies' => [
        'system' => '^1.0'
    ],
    'extends' => null,
    'author' => 'Marcello Fornaciari',
    'license' => 'MIT',
    'permissions' => [
        ['users','view','Users View','View users'],
        ['users','create','Users Create','Create users'],
        ['users','update','Users Update','Update users'],
        ['users','delete','Users Delete','Delete users'],
        ['user_groups','view','User Groups View','View user groups'],
        ['user_groups','create','User Groups Create','Create user groups'],
        ['user_groups','update','User Groups Update','Update user groups'],
        ['user_groups','delete','User Groups Delete','Delete user groups'],
        ['permissions','view','Permissions View','View permissions'],
        ['permissions','create','Permissions Create','Create permissions'],
        ['permissions','update','Permissions Update','Update permissions'],
        ['permissions','delete','Permissions Delete','Delete permissions'],
    ]
];