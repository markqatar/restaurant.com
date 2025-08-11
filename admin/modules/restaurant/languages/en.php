<?php
return [

    // (Navigation keys removed - now in global file)
    
    // Error messages
    'error' => [
        '404_title' => 'Page Not Found',
        '404_message' => 'The page you are looking for could not be found. Please check the URL or return to the dashboard.',
    ],
    
    'page_not_found' => 'Page Not Found',
    'back_to_dashboard' => 'Back to Dashboard',
    
    // (User management keys removed - now in dedicated module/global)
    
    // (Branch management keys removed - now global)
    
    // (Supplier management keys removed - belongs to suppliers module)
    
    // (Product management keys removed - not restaurant-specific)
    
    // (Order management keys removed - not restaurant-specific)
    
    // (Generic button keys removed - now global)
    
    // (Generic message keys removed - now global)
    
    // Forms
    'form' => [
        'required' => 'Required',
        'optional' => 'Optional',
        'select_option' => 'Select an option',
        'enter_value' => 'Enter value',
        'choose_file' => 'Choose file',
    ],
    
    // Additional missing translations
    'password' => 'Password',
    'min_characters' => 'Minimum characters',
    'instructions' => 'Instructions',
    'important_info' => 'Important Information',
    'security' => 'Security',
    'ensure_secure_passwords' => 'Ensure you use secure passwords and assign only necessary permissions.',
    'fields_marked_required' => 'Fields marked with * are required',
    'username_must_be_unique' => 'Username must be unique in the system',
    'password_min_length' => 'Password must be at least 6 characters',
    'assign_groups_after_creation' => 'After creation, you can assign the user to groups',
    'validation_error' => 'Validation Error',
    'fill_required_fields' => 'Please fill in all required fields',
    
    // Settings specific
    'general_settings' => 'General Settings',
    'site_name' => 'Site Name',
    'site_url' => 'Site URL',
    'site_logo' => 'Site Logo',
    'upload_logo' => 'Upload Logo',
    'current_logo' => 'Current Logo',
    'no_logo' => 'No logo uploaded',
    'rooms_management' => 'Rooms Management',
    'tables_management' => 'Tables Management',
    'room' => 'Room',
    'rooms' => 'Rooms',
    'table' => 'Table',
    'tables' => 'Tables',
    'add_room' => 'Add Room',
    'edit_room' => 'Edit Room',
    'add_table' => 'Add Table',
    'edit_table' => 'Edit Table',
    'room_name' => 'Room Name',
    'room_description' => 'Room Description',
    'table_name' => 'Table Name',
    'seats' => 'Seats',
    'select_branch' => 'Select Branch',
    'select_room' => 'Select Room',
    'room_examples' => 'Examples: Main Hall, Terrace, Balcony, Private Room, Garden, etc.',
    'table_examples' => 'Examples: Table 1, A1, Terrace-1, etc.',
    
    // Messages for new features (nested)
    'msg' => [
        'settings_updated_successfully' => 'Settings updated successfully',
        'room_created_successfully' => 'Room created successfully',
        'room_updated_successfully' => 'Room updated successfully',
        'room_deleted_successfully' => 'Room deleted successfully',
        'table_created_successfully' => 'Table created successfully',
        'table_updated_successfully' => 'Table updated successfully',
        'table_deleted_successfully' => 'Table deleted successfully',
        'cannot_delete_room_has_tables' => 'Cannot delete room that has tables',
        'required_fields' => 'Required fields are missing',
        'invalid_token' => 'Invalid security token',
        'not_found' => 'Item not found',
        'created_successfully' => 'Created successfully',
        'updated_successfully' => 'Updated successfully',
        'deleted_successfully' => 'Deleted successfully',
        'error_occurred' => 'An error occurred',
    ],
    
    // Logo management
    'delete_logo' => 'Delete Logo',
    'confirm_delete_logo' => 'Are you sure you want to delete the current logo?',
    'logo_deleted_successfully' => 'Logo deleted successfully',
    'error_occurred' => 'An error occurred',

        // Delivery Areas Management
    'add_delivery_area' => 'Add Delivery Area',
    'edit_delivery_area' => 'Edit Delivery Area',
    'area_name' => 'Area Name',
    'area_name_required' => 'Area name is required',
    'branch_required' => 'Branch selection is required',
    'no_branch' => 'No Branch',
    'delivery_area_created' => 'Delivery area created successfully',
    'delivery_area_updated' => 'Delivery area updated successfully',
    'delivery_area_deleted' => 'Delivery area deleted successfully',
    'delivery_area_not_found' => 'Delivery area not found',
    'error_creating_delivery_area' => 'Error creating delivery area',
    'error_updating_delivery_area' => 'Error updating delivery area',
    'error_deleting_delivery_area' => 'Error deleting delivery area',
    'delete_delivery_area_confirm' => 'Are you sure you want to delete the delivery area "%s"?',
    'branch' => 'Branch',
    'created_at' => 'Created At',
    'id' => 'ID',
    'back' => 'Back',
    'invalid_request' => 'Invalid request',
    'no_permission' => 'You do not have permission to access this page',
    'confirm_delete' => 'Confirm Delete',
    'yes_delete' => 'Yes, Delete',

    // Recipes Module (nested)
    'restaurant' => [
        'menu' => 'Restaurant',
        'recipes' => 'Recipes',
        'production' => 'Production',
    ],
    'recipes' => [
        'title' => 'Recipes',
        'action' => [
            'new' => 'New',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'production' => 'Production',
            'add_component' => 'Add Component',
        ],
        'field' => [
            'name' => 'Name',
            'yield' => 'Yield',
            'components' => 'Components',
            'actions' => 'Actions',
            'output_quantity' => 'Output Quantity',
            'reference_code' => 'Reference Code',
            'base_qty' => 'Base Qty',
            'scaled_qty' => 'Scaled Qty',
            'unit' => 'Unit',
            'yield_label' => 'Yield',
        ],
        'production' => [
            'batch_title' => 'Production Batch',
        ],
        'msg' => [
            'batch_success' => 'Batch produced successfully',
            'batch_error' => 'Batch production failed',
        ],
        'components' => [
            'preview' => 'Components Preview',
        ],
        'confirm' => [
            'delete' => 'Delete recipe?',
        ],
    ],

    // Inventory (nested)
    'inventory' => [
        'title' => 'Inventory',
        'field' => [
            'type' => 'Type',
            'updated_at' => 'Updated',
            'quantity' => 'Quantity',
            'unit' => 'Unit',
        ],
        'reason' => [
            'batch_consume' => 'Batch Consumption',
            'batch_produce' => 'Batch Production',
            'po_receive' => 'Purchase Order Receive',
        ],
    ],
    'cancel' => 'Cancel',

];
?>