<?php
return [
    'name' => 'Suppliers',
    'version' => '1.0.0',
    'dependencies' => [
        'system' => '^1.0'
    ],
    'extends' => null,
    'author' => 'Tuo Nome',
    'license' => 'MIT',
    // Module declared permissions (module, action, name, description) or short form action strings
    'permissions' => [
        // Core supplier CRUD
        ['suppliers','view','Suppliers View','View suppliers'],
        ['suppliers','create','Suppliers Create','Create suppliers'],
        ['suppliers','update','Suppliers Update','Update suppliers'],
        ['suppliers','delete','Suppliers Delete','Delete suppliers'],
        // Supplier contacts
        ['suppliers','contact.create','Supplier Contact Create','Create supplier contacts'],
        ['suppliers','contact.update','Supplier Contact Update','Update supplier contacts'],
        ['suppliers','contact.delete','Supplier Contact Delete','Delete supplier contacts'],
        // Supplier products
        ['suppliers_products','view','Supplier Products View','View supplier products'],
        ['suppliers_products','create','Supplier Products Create','Create supplier products'],
        ['suppliers_products','update','Supplier Products Update','Update supplier products'],
        ['suppliers_products','delete','Supplier Products Delete','Delete supplier products'],
        ['suppliers_products','associate.view','Supplier Products Associate View','View product-supplier associations'],
        ['suppliers_products','associate.add','Supplier Products Associate Add','Add product-supplier associations'],
        ['suppliers_products','associate.edit','Supplier Products Associate Edit','Edit product-supplier associations'],
        ['suppliers_products','associate.delete','Supplier Products Associate Delete','Delete product-supplier associations'],
        ['suppliers_products','subunit.add','Supplier Products Subunit Add','Add product sub-units'],
    ]
];