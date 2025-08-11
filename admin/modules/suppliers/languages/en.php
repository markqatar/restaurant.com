<?php
return [
    // (Navigation & generic UI keys pruned - now global)
    // Keep domain-specific: supplier CRUD, purchase orders, supplier products, inventory transfer, reasons, and minimal error handling

    // Error messages (domain specific 404 useful if module routed directly)
    'error' => [
        '404_title' => 'Page Not Found',
        '404_message' => 'The page you are looking for could not be found. Please check the URL or return to the dashboard.',
    ],
    'page_not_found' => 'Page Not Found',
    'back_to_dashboard' => 'Back to Dashboard',

    // Supplier Management (module domain)
    'supplier' => [
        'management' => 'Supplier Management',
        'new_supplier' => 'New Supplier',
        'edit_supplier' => 'Edit Supplier',
        'supplier_list' => 'Supplier List',
        'supplier_name' => 'Supplier Name',
        'company' => 'Company',
        'contact_person' => 'Contact Person',
        'total_suppliers' => 'Total Suppliers',
        'active_suppliers' => 'Active Suppliers',
        'city' => 'City',
        'country' => 'Country',
        'confirm_delete' => 'Are you sure you want to delete this supplier? This action will also delete all associated contacts and cannot be undone.',
    ],

    // Purchase Orders
    'purchase_order' => [
        'list_title' => 'Purchase Orders',
        'create_title' => "Create Purchase Order",
        'detail_title' => 'Purchase Order Details',
        'receive_title' => 'Receive Order',
        'back_to_list' => 'Back to list',
        'add_products' => 'Add Products',
        'ordered_products_title' => 'Ordered Products',
        'field' => [
            'id' => 'ID',
            'supplier' => 'Supplier',
            'status' => 'Status',
            'total' => 'Total',
            'date' => 'Date',
            'actions' => 'Actions',
            'product' => 'Product',
            'quantity' => 'Quantity',
            'unit' => 'Unit',
            'sku' => 'SKU',
            'ordered_qty' => 'Ordered Qty',
            'price' => 'Price',
            'discount' => 'Discount',
            'expiry' => 'Expiry',
            'received_qty' => 'Received Qty',
            'barcode' => 'Barcode',
            'notes' => 'Notes',
            'supplier_reference' => 'Supplier Ref.',
            'order_discount' => 'Order Discount (%)',
            'supplier_invoice_pdf' => 'Supplier PDF',
            'view_pdf' => 'View PDF',
        ],
        'btn' => [
            'add_row' => 'Add Row',
            'save_draft' => 'Save Draft',
            'new_order' => 'New Order',
            'confirm_receive' => 'Confirm Receipt',
            'download_pdf' => 'Download PDF',
            'send_order' => 'Confirm Order',
            'mark_as_received' => 'Mark as Received',
            'edit_order' => 'Edit Order',
        ],
        'placeholder' => [
            'price_per_unit' => 'Price/unit',
            'discount' => 'discount',
        ],
        'status' => [
            'draft' => 'Draft',
            'sent' => 'Confirmed',
            'received' => 'Received',
        ],
        'msg' => [
            'add_at_least_one_product' => 'Add at least one product',
            'select_supplier_first' => 'Select a supplier first',
            'created_successfully' => 'Order created successfully',
            'not_found' => 'Order not found',
            'sent_successfully' => 'Order sent successfully',
            'invalid_token' => 'Invalid token',
            'received_successfully' => 'Order received successfully',
            'not_receivable' => 'The order is not in a receivable state',
            'updated_successfully' => 'Order updated successfully',
            'invalid_branch' => 'Invalid branch selection',
            'confirm_send_title' => 'Confirm order?',
            'confirm_send_button' => 'Confirm',
            'confirm_receive_title' => 'Mark as received?',
            'confirm_receive_button' => 'Confirm',
            'generic_ok' => 'OK',
            'generic_error' => 'Error',
            'resent_successfully' => 'Order email resent successfully',
            'resending_email' => 'Resending order email...'
            ,'sending_order' => 'Sending order...'
            ,'receiving_order' => 'Finalizing receipt...'
        ],
        'summary' => [
            'subtotal' => 'Subtotal',
            'line_discounts' => 'Line Discounts',
            'order_discount_pct' => 'Order Discount (%)',
            'order_discount_val' => 'Order Discount Val.',
            'net_total' => 'Net Total'
        ],
        'history' => [
            'title' => 'Status History',
            'changed_at' => 'Changed At',
            'old_status' => 'Old Status',
            'new_status' => 'New Status'
        ],
        'validation' => [
            'fix_invalid_prices' => 'Fix invalid prices',
            'expiry_required' => 'Expiry date is required for the indicated products',
            'expiry_invalid_format' => 'Invalid expiry date format (expected YYYY-MM-DD)'
        ],
        'pdf' => [
            'title' => 'Purchase Order',
            'footer' => 'Automatically generated document - Do not reply to this email'
        ],
        'email' => [
            'subject' => 'Purchase Order #{order}',
            'subject_resend' => 'Purchase Order (Resent) #{order}',
            'greeting' => 'Dear Supplier,',
            'intro' => 'Please find attached our purchase order. You can also download it using the button below:',
            'download_button' => 'Download Order',
            'thanks' => 'Thank you for your cooperation,',
            'signature' => 'Purchasing Department'
        ],
        'barcode' => [
            'title' => 'Barcodes for Order',
            'print_button' => 'Print',
            'generated_total' => 'Generated Barcodes',
            'none' => 'No barcodes generated for this order yet',
            'invalid_params' => 'Invalid barcode parameters',
            'regenerated' => 'Barcodes generated successfully'
        ],
        'stats' => [
            'last_price' => 'Last Price',
            'last_purchase_date' => 'Last Purchase Date'
        ],
    ],

    // Supplier Product Categories & Base Units
    'supplier_product' => [
        'base_unit' => 'Base Unit',
        'category' => 'Category',
        'categories' => [
            'consumables' => 'Consumables',
            'food' => 'Food',
            'raw_materials' => 'Raw Materials',
            'houseware' => 'Houseware'
        ],
        'form' => [
            'product' => 'Product',
            'invoice_name' => 'Invoice Name',
            'unit' => 'Unit',
            'quantity' => 'Quantity',
            'base_quantity' => 'Quantity in Base Unit (conversion)',
            'price' => 'Price',
            'currency' => 'Currency'
        ],
        'inventory' => [
            'title' => 'Inventory Summary',
            'supplier_units' => 'Supplier Units',
            'base_unit_total' => 'Total Base Units'
        ]
    ],

    // Inventory Transfer & Reasons
    'inventory_transfer' => [
        'menu_title' => 'Inventory Transfers',
        'list_title' => 'Inventory Transfers',
        'new_transfer' => 'New Transfer',
        'from_branch' => 'From Branch',
        'to_branch' => 'To Branch',
        'item' => 'Item',
        'quantity' => 'Quantity',
        'unit' => 'Unit',
        'note' => 'Note',
        'created_at' => 'Created At',
        'reason_out' => 'Transfer Out',
        'reason_in' => 'Transfer In',
        'msg' => [
            'completed' => 'Transfer completed successfully',
            'failed' => 'Transfer failed',
            'invalid' => 'Invalid transfer data'
        ]
    ],
    'inventory' => [
        'reason' => [
            'transfer_out' => 'Transfer Out',
            'transfer_in' => 'Transfer In'
        ]
    ],

];
?>