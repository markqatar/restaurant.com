<?php
return [
    // Root-level common navigation (migrated from module-specific files)
    'dashboard' => 'Dashboard',
    'users' => 'Users',
    'products' => 'Products',
    'orders' => 'Orders',
    'suppliers' => 'Suppliers',
    'branches' => 'Branches',
    'reports' => 'Reports',
    'settings' => 'Settings',
    'logout' => 'Logout',
    'profile' => 'Profile',
    // =======================
    // Buttons
    // =======================
    'btn' => [
        'add_new' => 'Add New',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'update' => 'Update',
        'view' => 'View',
        'refresh' => 'Refresh',
        'back' => 'Back',
        'close' => 'Close',
        'yes_delete' => 'Yes, Delete',
        'confirm' => 'Confirm'
    ],

    // =======================
    // Messages
    // =======================
    'msg' => [
        'created_successfully' => 'Created successfully',
        'updated_successfully' => 'Updated successfully',
        'deleted_successfully' => 'Deleted successfully',
        'error_occurred' => 'An error occurred',
        'not_found' => 'Item not found',
        'invalid_token' => 'Invalid security token',
        'required_field' => 'Required field',
        'confirm_delete' => 'Are you sure you want to delete this item?',
        'confirm_delete_text' => 'This action cannot be undone.',
        'saved' => 'Saved successfully',
        'loading' => 'Loading...',
        'no_data' => 'No data available'
    ],

    // =======================
    // Form
    // =======================
    'form' => [
        'required' => 'Required',
        'optional' => 'Optional',
        'select_option' => 'Select an option',
        'enter_value' => 'Enter value',
        'choose_file' => 'Choose file'
    ],

    // =======================
    // Common fields
    // =======================
    'common' => [
        'name' => 'Name',
        'description' => 'Description',
        'email' => 'Email',
        'phone' => 'Phone',
        'address' => 'Address',
        'status' => 'Status',
        'created' => 'Created',
        'updated' => 'Updated',
        'actions' => 'Actions'
    ],

    // =======================
    // Status
    // =======================
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive'
    ],

    // =======================
    // Auth
    // =======================
    'auth' => [
        'login' => 'Login',
        'logout' => 'Logout',
        'remember_me' => 'Remember Me',
        'forgot_password' => 'Forgot Password?',
        'username' => 'Username',
        'password' => 'Password'
    ],

    // Branch (moved from suppliers module)
    'branch' => [
        'management' => 'Branch Management',
        'new_branch' => 'New Branch',
        'edit_branch' => 'Edit Branch',
        'branch_list' => 'Branch List',
        'branch_name' => 'Branch Name',
        'branch_code' => 'Branch Code',
        'manager' => 'Manager',
        'location' => 'Location',
        'contact_info' => 'Contact Information',
        'total_branches' => 'Total Branches',
        'active_branches' => 'Active Branches',
        'manage_users' => 'Manage Users',
        'confirm_delete_title' => 'Confirm Deletion',
        'confirm_delete_message' => 'Are you sure you want to delete the branch',
        'delete_warning' => 'This action will also remove all user assignments.'
    ],

    // =======================
    // Suppliers module
    // =======================
    'suppliers' => [
        'products_title' => 'Products',
        'add_product' => 'Add Product',
        'manage_product' => 'Manage Product',
        'raw_material' => 'Raw Material',
        'generate_barcode' => 'Generate Barcode',
        'requires_expiry' => 'Requires Expiry Date',
        'supplier' => 'Supplier',
        'unit' => 'Unit',
        'quantity' => 'Quantity',
        'quantity_per_unit' => 'Quantity per Unit',
        'sub_unit_level' => 'Sub-Unit Level :level',
        'quantity_for_unit' => 'Quantity for this Unit',
        'active' => 'Active',
        'associate' => 'Associate',
        'associations' => 'Associations',
        'back_to_products' => 'Back to Products',
        'add_sub_unit' => 'Add Sub-Unit',
        'cancel_edit' => 'Cancel Edit',
        'confirm_delete' => 'Confirm deletion?',
        'delete_yes' => 'Yes, delete',
        'record_not_found' => 'Record not found',
        'base_quantity_gt_zero' => 'Base quantity must be > 0'
    ]
];