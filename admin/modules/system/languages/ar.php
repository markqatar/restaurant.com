<?php
return [
    'configuration' => 'إعدادات النظام',
    'global_settings_tab' => 'الإعدادات العامة',
    'languages_tab' => 'اللغات',
    'site_name' => 'اسم الموقع',
    'site_url' => 'رابط الموقع',
    'logo' => 'الشعار',
    'currency' => 'العملة',
    'currencies' => 'العملات',
    'currencies_help' => 'قائمة رموز العملات المفعلة (ISO 4217)، الأولى هي الافتراضية',
    'timezone' => 'المنطقة الزمنية',
    'date_format_admin' => 'تنسيق التاريخ (الإدارة)',
    'date_format_public' => 'تنسيق التاريخ (الموقع)',
    'website_enabled' => 'الموقع مفعل',
    'add_language' => 'إضافة لغة',
    'code' => 'الكود',
    'name' => 'الاسم',
    'direction' => 'الاتجاه',
    'admin' => 'الإدارة',
    'public' => 'الموقع',
    // (Generic actions key removed - now global)
    'delete_language' => 'حذف اللغة',

    'modal' => [
        'direction_ltr' => 'من اليسار لليمين',
        'direction_rtl' => 'من اليمين لليسار'
    ],

    'success' => [
        'settings_updated' => 'تم تحديث الإعدادات بنجاح',
        'language_added' => 'تمت إضافة اللغة بنجاح',
        'language_deleted' => 'تم حذف اللغة بنجاح',
        'logo_deleted' => 'تم حذف الشعار بنجاح'
    ],

    'error' => [
        'invalid_csrf' => 'رمز الأمان غير صالح',
        'settings_update_failed' => 'فشل في تحديث الإعدادات',
        'language_add_failed' => 'فشل في إضافة اللغة',
        'language_delete_failed' => 'فشل في حذف اللغة',
        'unauthorized' => 'دخول غير مصرح به',
        'no_logo' => 'لا يوجد شعار للحذف',
        'delete_failed' => 'فشل في حذف ملف الشعار',
        'update_failed' => 'فشل في تحديث قاعدة البيانات'
    ],

    // Activity Logs / System Logs
    'system' => [
        'activity_logs' => 'سجل النشاطات'
    ],
    'log' => [
        'details' => 'تفاصيل السجل',
        'not_found' => 'السجل غير موجود'
    ],
    'restore' => [
        'confirm' => 'استعادة هذا التغيير؟',
        'description' => 'سيتم محاولة استعادة البيانات السابقة.'
    ],
    'yes_restore' => 'نعم، استعادة',
    'details' => 'تفاصيل',
    'restore' => 'استعادة',
    // Admin Menu management keys (متداخلة)
    'menu' => [
        'management' => 'إدارة القائمة',
        'total_items' => 'إجمالي العناصر',
        'active_items' => 'العناصر النشطة',
        'items' => 'عناصر القائمة',
    ],
    'add' => [
        'new' => 'إضافة جديد',
        'menu' => [
            'item' => 'إضافة عنصر قائمة'
        ],
    ],
    'parent' => [
        'menu' => 'القائمة الأب'
    ],
    'select' => [
        'parent' => 'اختر الأب'
    ],
    'sort' => [
        'order' => 'ترتيب'
    ],
    'permission' => [
        'module' => 'وحدة الصلاحية',
        'action' => [
            '_value' => 'إجراء الصلاحية',
            'view' => 'عرض',
            'create' => 'إنشاء',
            'update' => 'تحديث',
            'delete' => 'حذف',
        ],
    ],
    'target' => 'الهدف',
    'css' => [
        'class' => 'فئة CSS'
    ],
    'is' => [
        'active' => 'نشط',
        'separator' => 'فاصل'
    ],
    'example' => [
        'url' => 'مثال: users.php',
        'module' => 'مثال: users'
    ],
    'leave' => [
        'empty' => [
            'parent' => 'اتركه فارغاً لعناصر القائمة الرئيسية',
            'permission' => 'اتركه فارغاً بدون فحص صلاحيات'
        ]
    ],
    'preview' => 'معاينة',
    'browse' => [
        'icons' => 'تصفح أيقونات FontAwesome',
        'icons_info' => 'افتح موقع FontAwesome لاستعراض الأيقونات'
    ],
    'same' => [
        'window' => 'نفس النافذة'
    ],
    'new' => [
        'window' => 'نافذة جديدة'
    ],
    'icon' => [
        'placeholder' => 'fas fa-circle'
    ],
    'custom' => [
        'class' => [
            'placeholder' => 'custom-class'
        ]
    ],
    'root' => 'الجذر',
    'order' => 'الترتيب',
    // (Status & confirmation keys removed - now global)
];