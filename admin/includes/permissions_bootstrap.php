<?php
// Centralized permission registration
// Call include_once early in admin bootstrap AFTER DB connection & session.
if (!function_exists('ensure_permissions')) {
    return; // helpers not loaded yet
}
// TODO: Gradually migrate these centralized declarations into each module's config/module.php 'permissions' array.
// When migration complete, this file can be trimmed or removed. For now it co-exists; ensure_permissions() is idempotent.
ensure_permissions([
    // Suppliers core
    ['suppliers','view','Suppliers View','View suppliers'],
    ['suppliers','create','Suppliers Create','Create suppliers'],
    ['suppliers','update','Suppliers Update','Update suppliers'],
    ['suppliers','delete','Suppliers Delete','Delete suppliers'],
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
    // Pages
    ['pages','view','Pages View','View pages'],
    ['pages','create','Pages Create','Create pages'],
    ['pages','edit','Pages Edit','Edit pages'],
    ['pages','delete','Pages Delete','Delete pages'],
    // Articles / Blog
    ['articles','view','Articles View','View articles'],
    ['articles','create','Articles Create','Create articles'],
    ['articles','edit','Articles Edit','Edit articles'],
    ['articles','delete','Articles Delete','Delete articles'],
    ['categories','view','Categories View','View categories'],
    ['categories','create','Categories Create','Create categories'],
    ['categories','update','Categories Update','Update categories'],
    ['categories','delete','Categories Delete','Delete categories'],
    // Media
    ['media','view','Media View','View media library'],
    ['media','upload','Media Upload','Upload media'],
    ['media','edit','Media Edit','Edit media metadata'],
    ['media','delete','Media Delete','Delete media'],
    // System logs
    ['system_logs','view','System Logs View','View activity logs'],
    ['system_logs','restore','System Logs Restore','Restore records from logs'],
    // Users & Groups
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
    // Orders
    ['orders','view','Orders View','View orders'],
    ['orders','create','Orders Create','Create orders'],
    ['orders','update','Orders Update','Update orders'],
    ['orders','delete','Orders Delete','Delete orders'],
    // Branches (shop)
    ['branches','view','Branches View','View branches'],
    ['branches','create','Branches Create','Create branches'],
    ['branches','update','Branches Update','Update branches'],
    ['branches','delete','Branches Delete','Delete branches'],
    // Menu (admin navigation builder)
    ['menu','view','Menu View','View admin menu items'],
    ['menu','create','Menu Create','Create admin menu items'],
    ['menu','update','Menu Update','Update admin menu items'],
    ['menu','delete','Menu Delete','Delete admin menu items'],
    // System config
    ['system','view','System Config View','View system configuration'],
    ['system','update','System Config Update','Update system configuration'],
]);
