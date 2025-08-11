<?php
return [
    // (Navigation keys removed - now global)

    // Error messages
    'error' => [
        '404_title' => 'الصفحة غير موجودة',
        '404_message' => 'الصفحة التي تبحث عنها غير موجودة. يرجى التحقق من العنوان أو العودة إلى لوحة التحكم.',
    ],

    'page_not_found' => 'الصفحة غير موجودة',
    'back_to_dashboard' => 'العودة إلى لوحة التحكم',

    // (User management keys removed - handled by access-management module / global)

    // (Branch management keys removed - now global)

    // (Supplier management keys removed - in suppliers module)

    // (Product management keys removed - not restaurant-specific)

    // (Order management keys removed - not restaurant-specific)

    // (Generic button keys removed - now global)

    // (Generic message keys removed - now global)

    // (Generic form keys removed - now global)

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

    // Delivery Areas Management
    'add_delivery_area' => 'إضافة منطقة توصيل',
    'edit_delivery_area' => 'تعديل منطقة التوصيل',
    'area_name' => 'اسم المنطقة',
    'area_name_required' => 'اسم المنطقة مطلوب',
    'branch_required' => 'تحديد الفرع مطلوب',
    'no_branch' => 'لا يوجد فرع',
    'delivery_area_created' => 'تم إنشاء منطقة التوصيل بنجاح',
    'delivery_area_updated' => 'تم تحديث منطقة التوصيل بنجاح',
    'delivery_area_deleted' => 'تم حذف منطقة التوصيل بنجاح',
    'delivery_area_not_found' => 'لم يتم العثور على منطقة التوصيل',
    'error_creating_delivery_area' => 'خطأ في إنشاء منطقة التوصيل',
    'error_updating_delivery_area' => 'خطأ في تحديث منطقة التوصيل',
    'error_deleting_delivery_area' => 'خطأ في حذف منطقة التوصيل',
    'delete_delivery_area_confirm' => 'هل أنت متأكد من رغبتك في حذف منطقة التوصيل "%s"؟',
    'branch' => 'الفرع',
    'created_at' => 'تاريخ الإنشاء',
    'id' => 'ID',
    'back' => 'عودة',
    'invalid_request' => 'طلب غير صالح',
    'no_permission' => 'ليس لديك إذن للوصول إلى هذه الصفحة',
    'confirm_delete' => 'تأكيد الحذف',
    'yes_delete' => 'نعم، احذف',

    // وحدة الوصفات (مصفوفات متداخلة)
    'restaurant' => [
        'menu' => 'المطعم',
        'recipes' => 'الوصفات',
        'production' => 'الإنتاج',
    ],
    'recipes' => [
        'title' => 'الوصفات',
        'action' => [
            'new' => 'جديدة',
            'edit' => 'تعديل',
            'delete' => 'حذف',
            'production' => 'الإنتاج',
            'add_component' => 'إضافة مكون',
        ],
        'field' => [
            'name' => 'الاسم',
            'yield' => 'العائد',
            'components' => 'المكونات',
            'actions' => 'إجراءات',
            'output_quantity' => 'كمية الخرج',
            'reference_code' => 'رمز مرجعي',
            'base_qty' => 'الكمية الأساسية',
            'scaled_qty' => 'الكمية المحسوبة',
            'unit' => 'الوحدة',
            'yield_label' => 'العائد',
        ],
        'production' => [
            'batch_title' => 'دفعة إنتاج',
        ],
        'msg' => [
            'batch_success' => 'تم إنتاج الدفعة بنجاح',
            'batch_error' => 'فشل إنتاج الدفعة',
        ],
        'components' => [
            'preview' => 'معاينة المكونات',
        ],
        'confirm' => [
            'delete' => 'حذف الوصفة؟',
        ],
    ],

    // المخزون (متداخل)
    'inventory' => [
        'title' => 'المخزون',
        'field' => [
            'type' => 'النوع',
            'updated_at' => 'محدث',
            'quantity' => 'الكمية',
            'unit' => 'الوحدة',
        ],
        'reason' => [
            'batch_consume' => 'استهلاك الدفعة',
            'batch_produce' => 'إنتاج الدفعة',
            'po_receive' => 'استلام أمر شراء',
        ],
    ],
    'cancel' => 'إلغاء',

];
