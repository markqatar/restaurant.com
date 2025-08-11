<?php
return [
  'name' => 'Warehouse',
  'version' => '1.0.0',
  'description' => 'Central warehouse & branch inventory management (moved from suppliers module).',
  'dependencies' => ['shop','suppliers'],
  'permissions' => [
    ['inventory','view','Inventory View','Can view warehouse inventory'],
    ['inventory','transfer','Inventory Transfer','Can transfer inventory between branches']
  ],
];
