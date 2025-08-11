<?php
return [
    // (Navigation & generic UI keys pruned - now global)
    // Keep supplier domain + purchase orders + inventory transfer + product categories + reasons + errors

    'error' => [
        '404_title' => 'الصفحة غير موجودة',
        '404_message' => 'الصفحة التي تبحث عنها غير موجودة. يرجى التحقق من العنوان أو العودة إلى لوحة التحكم.',
    ],
    'page_not_found' => 'الصفحة غير موجودة',
    'back_to_dashboard' => 'العودة إلى لوحة التحكم',

    'supplier' => [
        'management' => 'إدارة الموردين',
        'new_supplier' => 'مورد جديد',
        'edit_supplier' => 'تعديل المورد',
        'supplier_list' => 'قائمة الموردين',
        'supplier_name' => 'اسم المورد',
        'company' => 'الشركة',
        'contact_person' => 'شخص الاتصال',
        'total_suppliers' => 'إجمالي الموردين',
        'active_suppliers' => 'الموردون النشطون',
        'city' => 'المدينة',
        'country' => 'البلد',
        'confirm_delete' => 'هل أنت متأكد من رغبتك في حذف هذا المورد؟ ستؤدي هذه العملية أيضًا إلى حذف جميع جهات الاتصال المرتبطة ولا يمكن التراجع عنها.',
    ],

    // أوامر الشراء
    'purchase_order' => [
        'list_title' => 'أوامر الشراء',
        'create_title' => 'إنشاء أمر شراء جديد',
        'detail_title' => 'تفاصيل أمر الشراء',
        'receive_title' => 'استلام الأمر',
        'back_to_list' => 'العودة إلى القائمة',
        'add_products' => 'إضافة منتجات',
        'ordered_products_title' => 'المنتجات المطلوبة',
        'field' => [
            'id' => 'المعرف',
            'supplier' => 'المورد',
            'status' => 'الحالة',
            'total' => 'الإجمالي',
            'date' => 'التاريخ',
            'actions' => 'الإجراءات',
            'product' => 'المنتج',
            'quantity' => 'الكمية',
            'unit' => 'الوحدة',
            'sku' => 'SKU',
            'ordered_qty' => 'الكمية المطلوبة',
            'price' => 'السعر',
            'discount' => 'الخصم',
            'expiry' => 'الصلاحية',
            'received_qty' => 'الكمية المستلمة',
            'barcode' => 'باركود',
            'notes' => 'ملاحظات',
            'supplier_reference' => 'مرجع المورد',
            'order_discount' => 'خصم الأمر (%)',
            'supplier_invoice_pdf' => 'ملف المورد PDF',
            'view_pdf' => 'عرض PDF',
        ],
        'btn' => [
            'add_row' => 'إضافة صف',
            'save_draft' => 'حفظ كمسودة',
            'new_order' => 'أمر جديد',
            'confirm_receive' => 'تأكيد الاستلام',
            'download_pdf' => 'تنزيل PDF',
            'send_order' => 'تأكيد الطلب',
            'mark_as_received' => 'وضع علامة مستلم',
            'edit_order' => 'تعديل الطلب',
        ],
        'placeholder' => [
            'price_per_unit' => 'سعر/وحدة',
            'discount' => 'خصم',
        ],
        'status' => [
            'draft' => 'مسودة',
            'sent' => 'مؤكد',
            'received' => 'تم الاستلام',
        ],
        'msg' => [
            'add_at_least_one_product' => 'أضف منتجاً واحداً على الأقل',
            'select_supplier_first' => 'اختر المورد أولاً',
            'created_successfully' => 'تم إنشاء الأمر بنجاح',
            'not_found' => 'أمر الشراء غير موجود',
            'sent_successfully' => 'تم إرسال الأمر بنجاح',
            'invalid_token' => 'رمز غير صالح',
            'received_successfully' => 'تم استلام الأمر بنجاح',
            'not_receivable' => 'الأمر ليس في حالة قابلة للاستلام',
            'updated_successfully' => 'تم تحديث الأمر بنجاح',
            'invalid_branch' => 'فرع غير صالح',
            'confirm_send_title' => 'تأكيد الطلب؟',
            'confirm_send_button' => 'تأكيد',
            'confirm_receive_title' => 'وضع علامة تم الاستلام؟',
            'confirm_receive_button' => 'تأكيد',
            'generic_ok' => 'حسناً',
            'generic_error' => 'خطأ',
            'resent_successfully' => 'تمت إعادة إرسال البريد الإلكتروني للأمر بنجاح',
            'resending_email' => 'جاري إعادة إرسال بريد الأمر...'
            ,'sending_order' => 'جاري إرسال الأمر...'
            ,'receiving_order' => 'جاري إنهاء الاستلام...'
        ],
        'summary' => [
            'subtotal' => 'الإجمالي الفرعي',
            'line_discounts' => 'خصومات السطور',
            'order_discount_pct' => 'خصم الأمر (%)',
            'order_discount_val' => 'قيمة خصم الأمر',
            'net_total' => 'الإجمالي الصافي'
        ],
        'history' => [
            'title' => 'سجل الحالات',
            'changed_at' => 'تاريخ التغيير',
            'old_status' => 'الحالة السابقة',
            'new_status' => 'الحالة الجديدة'
        ],
        'validation' => [
            'fix_invalid_prices' => 'يرجى تصحيح الأسعار غير الصالحة',
            'expiry_required' => 'تاريخ الصلاحية مطلوب للمنتجات المحددة',
            'expiry_invalid_format' => 'تنسيق تاريخ الصلاحية غير صالح (متوقع YYYY-MM-DD)'
        ],
        'pdf' => [
            'title' => 'أمر شراء',
            'footer' => 'تم إنشاء المستند تلقائياً - الرجاء عدم الرد على هذا البريد'
        ],
        'email' => [
            'subject' => 'أمر شراء رقم {order}',
            'subject_resend' => 'أمر شراء (إعادة إرسال) رقم {order}',
            'greeting' => 'عزيزي المورد،',
            'intro' => 'مرفق أمر الشراء الخاص بنا. يمكنك أيضاً تنزيله باستخدام الزر أدناه:',
            'download_button' => 'تنزيل الأمر',
            'thanks' => 'شكراً لتعاونكم،',
            'signature' => 'قسم المشتريات'
        ],
        'barcode' => [
            'title' => 'باركود للأمر',
            'print_button' => 'طباعة',
            'generated_total' => 'الباركود المولدة',
            'none' => 'لم يتم توليد أي باركود لهذا الأمر بعد',
            'invalid_params' => 'معلمات الباركود غير صالحة',
            'regenerated' => 'تم توليد الباركود بنجاح'
        ],
        'stats' => [
            'last_price' => 'آخر سعر',
            'last_purchase_date' => 'تاريخ آخر شراء'
        ],
    ],

    // تصنيفات منتجات المورد و الوحدة الأساسية
    'supplier_product' => [
        'base_unit' => 'الوحدة الأساسية',
        'category' => 'التصنيف',
        'categories' => [
            'consumables' => 'مستهلكات',
            'food' => 'طعام',
            'raw_materials' => 'مواد خام',
            'houseware' => 'أدوات منزلية'
        ],
        'form' => [
            'product' => 'المنتج',
            'invoice_name' => 'الاسم في الفاتورة',
            'unit' => 'الوحدة',
            'quantity' => 'الكمية',
            'base_quantity' => 'الكمية بوحدة الأساس (تحويل)',
            'price' => 'السعر',
            'currency' => 'العملة'
        ],
        'inventory' => [
            'title' => 'ملخص المخزون',
            'supplier_units' => 'وحدات المورد',
            'base_unit_total' => 'إجمالي بوحدة الأساس'
        ]
    ],

    // تحويلات المخزون والأسباب
    'inventory_transfer' => [
        'menu_title' => 'تحويلات المخزون',
        'list_title' => 'تحويلات المخزون',
        'new_transfer' => 'تحويل جديد',
        'from_branch' => 'من الفرع',
        'to_branch' => 'إلى الفرع',
        'item' => 'العنصر',
        'quantity' => 'الكمية',
        'unit' => 'الوحدة',
        'note' => 'ملاحظة',
        'created_at' => 'تاريخ الإنشاء',
        'reason_out' => 'خروج تحويل',
        'reason_in' => 'دخول تحويل',
        'msg' => [
            'completed' => 'تم تنفيذ التحويل بنجاح',
            'failed' => 'فشل التحويل',
            'invalid' => 'بيانات تحويل غير صالحة'
        ]
    ],
    'inventory' => [
        'reason' => [
            'transfer_out' => 'تحويل خارج',
            'transfer_in' => 'تحويل داخل'
        ]
    ],

];
