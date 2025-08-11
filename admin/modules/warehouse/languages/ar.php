<?php
return [
  'warehouse' => [
    'inventory' => [
      '_value' => 'المخزون',
      'summary' => 'ملخص المخزون',
      'branch' => 'الفرع',
      'type' => 'النوع',
      'item' => 'الصنف',
      'quantity' => 'الكمية',
      'unit' => 'الوحدة',
      'updated' => 'آخر تحديث',
      'filters' => [
        'branch_all' => 'كل الفروع',
        'type_all' => 'كل الأنواع'
      ],
      'export' => [
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'طباعة'
      ]
    ],
    'transfer' => [
      'list_title' => 'تحويلات المخزون',
      'new_title' => 'تحويل مخزون جديد',
      'btn_new' => 'تحويل جديد',
      'fields' => [
        'from_branch' => 'من الفرع',
        'to_branch' => 'إلى الفرع',
        'item_type' => 'نوع الصنف',
        'item' => 'الصنف',
        'quantity' => 'الكمية',
        'unit' => 'الوحدة',
        'note' => 'ملاحظة'
      ],
      'actions' => [
        'transfer' => 'تحويل',
        'cancel' => 'إلغاء'
      ],
      'messages' => [
        'invalid_token' => 'رمز غير صالح',
        'invalid_data' => 'بيانات غير صالحة',
        'completed' => 'اكتمل التحويل',
        'failed' => 'فشل التحويل'
      ],
      'table' => [
        'id' => 'المعرف',
        'item' => 'الصنف',
        'from' => 'من',
        'to' => 'إلى',
        'qty' => 'الكمية',
        'unit' => 'الوحدة',
        'note' => 'ملاحظة',
        'at' => 'في'
      ],
      'filter' => [
        'branch' => 'الفرع',
        'type' => 'النوع'
      ]
    ],
    'common' => [
      'all' => 'الكل',
      'product' => 'منتج',
      'recipe' => 'وصفة'
    ]
  ]
];
