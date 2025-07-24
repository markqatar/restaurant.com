<?php
return [
    'name' => 'Users Management',
    'description' => 'Gestione degli utenti e gruppi utenti.',
    'version' => '1.0.0',
    'dependencies' => [
        'system' => '^1.0'
    ],
    'routes' => [
        'users' => 'UsersController@index',
        'users/create' => 'UsersController@create',
        'user-groups' => 'UserGroupsController@index',
        'user-groups/create' => 'UserGroupsController@create'
    ]
];