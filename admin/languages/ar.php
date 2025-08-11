<?php
return [
    // عناصر التنقل العامة (محولة من ملفات الوحدات)
    'dashboard' => 'لوحة التحكم',
    'users' => 'المستخدمون',
    'products' => 'المنتجات',
    'orders' => 'الطلبات',
    'suppliers' => 'الموردون',
    'branches' => 'الفروع',
    'reports' => 'التقارير',
    'settings' => 'الإعدادات',
    'logout' => 'تسجيل الخروج',
    'profile' => 'الملف الشخصي',
    'btn' => [
        'add_new' => 'إضافة جديد',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'delete' => 'حذف',
        'edit' => 'تعديل',
        'update' => 'تحديث',
        'view' => 'عرض',
        'refresh' => 'تحديث',
        'back' => 'رجوع',
        'close' => 'إغلاق',
        'yes_delete' => 'نعم، احذف',
        'confirm' => 'تأكيد'
    ],

    'msg' => [
        'created_successfully' => 'تم الإنشاء بنجاح',
        'updated_successfully' => 'تم التحديث بنجاح',
        'deleted_successfully' => 'تم الحذف بنجاح',
        'error_occurred' => 'حدث خطأ',
        'not_found' => 'العنصر غير موجود',
        'invalid_token' => 'رمز الأمان غير صالح',
        'required_field' => 'حقل مطلوب',
        'confirm_delete' => 'هل أنت متأكد أنك تريد حذف هذا العنصر؟',
        'confirm_delete_text' => 'لا يمكن التراجع عن هذا الإجراء.',
        'saved' => 'تم الحفظ بنجاح',
        'loading' => 'جارٍ التحميل...',
        'no_data' => 'لا توجد بيانات متاحة'
    ],

    'form' => [
        'required' => 'إلزامي',
        'optional' => 'اختياري',
        'select_option' => 'اختر خيارًا',
        'enter_value' => 'أدخل قيمة',
        'choose_file' => 'اختر ملف'
    ],

    'common' => [
        'name' => 'الاسم',
        'description' => 'الوصف',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'address' => 'العنوان',
        'status' => 'الحالة',
        'created' => 'تاريخ الإنشاء',
        'updated' => 'تم التحديث',
        'actions' => 'الإجراءات'
    ],

    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط'
    ],

    'auth' => [
        'login' => 'تسجيل الدخول',
        'logout' => 'تسجيل الخروج',
        'remember_me' => 'تذكرني',
        'forgot_password' => 'هل نسيت كلمة المرور؟',
        'username' => 'اسم المستخدم',
        'password' => 'كلمة المرور'
    ],

    // الفروع (تم نقلها إلى الملف العالمي)
    'branch' => [
        'management' => 'إدارة الفروع',
        'new_branch' => 'فرع جديد',
        'edit_branch' => 'تعديل الفرع',
        'branch_list' => 'قائمة الفروع',
        'branch_name' => 'اسم الفرع',
        'branch_code' => 'رمز الفرع',
        'manager' => 'المدير',
        'location' => 'الموقع',
        'contact_info' => 'معلومات الاتصال',
        'total_branches' => 'إجمالي الفروع',
        'active_branches' => 'الفروع النشطة',
        'manage_users' => 'إدارة المستخدمين',
        'confirm_delete_title' => 'تأكيد الحذف',
        'confirm_delete_message' => 'هل أنت متأكد أنك تريد حذف الفرع',
        'delete_warning' => 'سيتم أيضًا إزالة كافة تخصيصات المستخدمين.'
    ],

    // =======================
    // الموردون (وحدات التوريد)
    // =======================
    'suppliers' => [
        'products_title' => 'المنتجات',
        'add_product' => 'إضافة منتج',
        'manage_product' => 'إدارة المنتج',
        'raw_material' => 'مادة أولية',
        'generate_barcode' => 'توليد باركود',
        'requires_expiry' => 'يتطلب تاريخ صلاحية',
        'supplier' => 'المورد',
        'unit' => 'الوحدة',
        'quantity' => 'الكمية',
        'quantity_per_unit' => 'الكمية لكل وحدة',
        'sub_unit_level' => 'وحدة فرعية مستوى :level',
        'quantity_for_unit' => 'الكمية لهذه الوحدة',
        'active' => 'نشط',
        'associate' => 'ربط',
        'associations' => 'الروابط',
        'back_to_products' => 'العودة إلى المنتجات',
        'add_sub_unit' => 'إضافة وحدة فرعية',
        'cancel_edit' => 'إلغاء التعديل',
        'confirm_delete' => 'تأكيد الحذف؟',
        'delete_yes' => 'نعم، احذف',
        'record_not_found' => 'السجل غير موجود',
        'base_quantity_gt_zero' => 'يجب أن تكون الكمية الأساسية > 0'
    ]
];