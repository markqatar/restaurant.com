<?php
return [
  'warehouse' => [
    'inventory' => [
      '_value' => 'Magazzino',
      'summary' => 'Riepilogo Inventario',
      'branch' => 'Filiale',
      'type' => 'Tipo',
      'item' => 'Articolo',
      'quantity' => 'Quantità',
      'unit' => 'Unità',
      'updated' => 'Aggiornato',
      'filters' => [
        'branch_all' => 'Tutte le filiali',
        'type_all' => 'Tutti i tipi'
      ],
      'export' => [
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Stampa'
      ]
    ],
    'transfer' => [
      'list_title' => 'Trasferimenti Inventario',
      'new_title' => 'Nuovo Trasferimento Inventario',
      'btn_new' => 'Nuovo Trasferimento',
      'fields' => [
        'from_branch' => 'Da Filiale',
        'to_branch' => 'A Filiale',
        'item_type' => 'Tipo Articolo',
        'item' => 'Articolo',
        'quantity' => 'Quantità',
        'unit' => 'Unità',
        'note' => 'Nota'
      ],
      'actions' => [
        'transfer' => 'Trasferisci',
        'cancel' => 'Annulla'
      ],
      'messages' => [
        'invalid_token' => 'Token non valido',
        'invalid_data' => 'Dati non validi',
        'completed' => 'Trasferimento completato',
        'failed' => 'Trasferimento fallito'
      ],
      'table' => [
        'id' => 'ID',
        'item' => 'Articolo',
        'from' => 'Da',
        'to' => 'A',
        'qty' => 'Qtà',
        'unit' => 'Unità',
        'note' => 'Nota',
        'at' => 'Data'
      ],
      'filter' => [
        'branch' => 'Filiale',
        'type' => 'Tipo'
      ]
    ],
    'common' => [
      'all' => 'Tutti',
      'product' => 'Prodotto',
      'recipe' => 'Ricetta'
    ]
  ]
];
