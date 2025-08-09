<?php
return [
	'name' => 'Customers',
	'version' => '1.0.0',
	'description' => 'Gestione clienti, profili, autenticazioni social e indirizzi',
	'icon' => 'fa-user-group',
	'dependencies' => [
		'system',
		'access-management'
	],
	'extends' => 'access-management',
	'author' => 'Core',
	'license' => 'MIT',
	'permissions' => [
		// Customers core
		['customers','view','Customers View','View customers'],
		['customers','create','Customers Create','Create customers'],
		['customers','update','Customers Update','Update customers'],
		['customers','delete','Customers Delete','Delete customers'],
		// Address dynamic rules
		['address_field_rules','view','Address Field Rules View','View dynamic address field rules'],
		['address_field_rules','update','Address Field Rules Update','Manage dynamic address field rules'],
	],
	'hooks_files' => [
		// Placeholder if future hooks are added (ui / logic)
	],
];
