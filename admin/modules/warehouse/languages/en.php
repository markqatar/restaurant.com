<?php
return [
  'warehouse' => [
    'inventory' => [
      '_value' => 'Inventory', // optional group label
      'summary' => 'Inventory Summary',
      'branch' => 'Branch',
      'type' => 'Type',
      'item' => 'Item',
      'quantity' => 'Quantity',
      'unit' => 'Unit',
      'updated' => 'Updated',
      'filters' => [
        'branch_all' => 'All Branches',
        'type_all' => 'All Types'
      ],
      'export' => [
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Print'
      ]
    ],
    'transfer' => [
      'list_title' => 'Inventory Transfers',
      'new_title' => 'New Inventory Transfer',
      'btn_new' => 'New Transfer',
      'fields' => [
        'from_branch' => 'From Branch',
        'to_branch' => 'To Branch',
        'item_type' => 'Item Type',
        'item' => 'Item',
        'quantity' => 'Quantity',
        'unit' => 'Unit',
        'note' => 'Note'
      ],
      'actions' => [
        'transfer' => 'Transfer',
        'cancel' => 'Cancel'
      ],
      'messages' => [
        'invalid_token' => 'Invalid token',
        'invalid_data' => 'Invalid data',
        'completed' => 'Transfer completed',
        'failed' => 'Transfer failed'
      ],
      'table' => [
        'id' => 'ID',
        'item' => 'Item',
        'from' => 'From',
        'to' => 'To',
        'qty' => 'Qty',
        'unit' => 'Unit',
        'note' => 'Note',
        'at' => 'At'
      ],
      'filter' => [
        'branch' => 'Branch',
        'type' => 'Type'
      ]
    ],
    'common' => [
      'all' => 'All',
      'product' => 'Product',
      'recipe' => 'Recipe'
    ]
  ]
];
