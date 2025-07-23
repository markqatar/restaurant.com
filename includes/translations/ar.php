<?php
return [
    // Common
    'yes' => 'نعم',
    'no' => 'لا',
    'save' => 'حفظ',
    'cancel' => 'إلغاء',
    'delete' => 'حذف',
    'edit' => 'تعديل',
    'create' => 'إنشاء',
    'update' => 'تحديث',
    'search' => 'بحث',
    'actions' => 'الإجراءات',
    'status' => 'الحالة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'created' => 'تم الإنشاء',
    'updated' => 'تم التحديث',
    'name' => 'الاسم',
    'email' => 'البريد الإلكتروني',
    'phone' => 'الهاتف',
    'address' => 'العنوان',
    'description' => 'الوصف',
    'notes' => 'ملاحظات',
    'back_to_list' => 'العودة إلى القائمة',
    'required_field' => 'حقل مطلوب',
    'optional' => 'اختياري',
    
    // Navigation & Menu
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
    
    // Error messages
    'error' => [
        '404_title' => 'الصفحة غير موجودة',
        '404_message' => 'الصفحة التي تبحث عنها غير موجودة. يرجى التحقق من العنوان أو العودة إلى لوحة التحكم.',
    ],
    
    'page_not_found' => 'الصفحة غير موجودة',
    'back_to_dashboard' => 'العودة إلى لوحة التحكم',
    
    // User Management
    'user' => [
        'management' => 'إدارة المستخدمين',
        'new_user' => 'مستخدم جديد',
        'edit_user' => 'تعديل المستخدم',
        'user_list' => 'قائمة المستخدمين',
        'total_users' => 'إجمالي المستخدمين',
        'active_users' => 'المستخدمون النشطون',
        'username' => 'اسم المستخدم',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'full_name' => 'الاسم الكامل',
        'groups' => 'مجموعات المستخدمين',
        'permissions' => 'الأذونات',
        'no_groups' => 'لا توجد مجموعات',
        'user_data' => 'بيانات المستخدم',
        'account_info' => 'معلومات الحساب',
        'user_id' => 'معرف المستخدم',
        'created_on' => 'تم الإنشاء في',
        'last_modified' => 'آخر تعديل',
        'quick_actions' => 'إجراءات سريعة',
        'reset_password' => 'إعادة تعيين كلمة المرور',
        'delete_user' => 'حذف المستخدم',
        'user_active' => 'المستخدم نشط',
        'save_changes' => 'حفظ التغييرات',
        'password_note' => 'لأسباب أمنية، لا يمكن تعديل كلمة المرور من هذا القسم. استخدم وظيفة "إعادة تعيين كلمة المرور" إذا لزم الأمر.',
        'confirm_delete' => 'هل أنت متأكد من رغبتك في حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.',
        'confirm_reset_password' => 'هل أنت متأكد من رغبتك في إعادة تعيين كلمة مرور هذa المستخدم؟ سيتم إرسال كلمة مرور مؤقتة.',
    ],
    
    // Branch Management
    'branch' => [
        'management' => 'إدارة الفروع',
        'new_branch' => 'فرع جديد',
        'edit_branch' => 'تعديل الفرع',
        'branch_list' => 'قائمة الفروع',
        'branch_name' => 'اسم الفرع',
        'branch_code' => 'كود الفرع',
        'manager' => 'المدير',
        'location' => 'الموقع',
        'contact_info' => 'معلومات الاتصال',
        'total_branches' => 'إجمالي الفروع',
        'active_branches' => 'الفروع النشطة',
        'manage_users' => 'إدارة المستخدمين',
        'confirm_delete_title' => 'تأكيد الحذف',
        'confirm_delete_message' => 'هل أنت متأكد من رغبتك في حذف الفرع',
        'delete_warning' => 'هذا الإجراء سيؤدي أيضاً إلى إزالة جميع تعيينات المستخدمين.',
    ],
    
    // Supplier Management
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
    
    // Product Management
    'product' => [
        'management' => 'إدارة المنتجات',
        'new_product' => 'منتج جديد',
        'edit_product' => 'تعديل المنتج',
        'product_list' => 'قائمة المنتجات',
        'product_name' => 'اسم المنتج',
        'category' => 'الفئة',
        'price' => 'السعر',
        'stock' => 'المخزون',
        'barcode' => 'الرمز الشريطي',
    ],
    
    // Order Management
    'order' => [
        'management' => 'إدارة الطلبات',
        'new_order' => 'طلب جديد',
        'edit_order' => 'تعديل الطلب',
        'order_list' => 'قائمة الطلبات',
        'order_number' => 'رقم الطلب',
        'customer' => 'العميل',
        'total' => 'المجموع',
        'order_date' => 'تاريخ الطلب',
        'delivery_date' => 'تاريخ التسليم',
    ],
    
    // Buttons & Actions
    'btn' => [
        'refresh' => 'تحديث',
        'add_new' => 'إضافة جديد',
        'view' => 'عرض',
        'modify' => 'تعديل',
        'remove' => 'إزالة',
        'confirm' => 'تأكيد',
        'close' => 'إغلاق',
    ],
    
    // Messages
    'msg' => [
        'success' => 'تمت العملية بنجاح',
        'error' => 'حدث خطأ',
        'warning' => 'تحذير',
        'info' => 'معلومات',
        'no_data' => 'لا توجد بيانات متاحة',
        'loading' => 'جاري التحميل...',
        'saved' => 'تم الحفظ بنجاح',
        'deleted' => 'تم الحذف بنجاح',
        'updated' => 'تم التحديث بنجاح',
    ],
    
    // Forms
    'form' => [
        'required' => 'مطلوب',
        'optional' => 'اختياري',
        'select_option' => 'اختر خياراً',
        'enter_value' => 'أدخل القيمة',
        'choose_file' => 'اختر ملف',
    ],
    
    // Additional missing translations
    'password' => 'كلمة المرور',
    'min_characters' => 'الحد الأدنى للأحرف',
    'instructions' => 'التعليمات',
    'important_info' => 'معلومات مهمة',
    'security' => 'الأمان',
    'ensure_secure_passwords' => 'تأكد من استخدام كلمات مرور آمنة وتعيين الأذونات الضرورية فقط.',
    'fields_marked_required' => 'الحقول المميزة بـ * مطلوبة',
    'username_must_be_unique' => 'يجب أن يكون اسم المستخدم فريداً في النظام',
    'password_min_length' => 'يجب أن تتكون كلمة المرور من 6 أحرف على الأقل',
    'assign_groups_after_creation' => 'بعد الإنشاء، يمكنك تعيين المستخدم للمجموعات',
    'validation_error' => 'خطأ في التحقق',
    'fill_required_fields' => 'يرجى ملء جميع الحقول المطلوبة',
];
?>