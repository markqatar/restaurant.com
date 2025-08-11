<?php

return [

    // ===========================
    // الأزرار والإجراءات العامة
    // ===========================
    // (Generic button / CRUD keys removed - now global)

    // ===========================
    // الرسائل
    // ===========================
    // (Generic message keys removed - now global)

    // ===========================
    // النماذج
    // ===========================
    // (Generic form keys removed - now global)

    // ===========================
    // إدارة المستخدمين
    // ===========================
    'user' => [
        'management' => 'إدارة المستخدمين',
        'user_list' => 'قائمة المستخدمين',
        'new_user' => 'مستخدم جديد',
        'edit_user' => 'تعديل المستخدم',
        'save_changes' => 'حفظ التغييرات',
        'account_info' => 'معلومات الحساب',
        'quick_actions' => 'إجراءات سريعة',
        'reset_password' => 'إعادة تعيين كلمة المرور',
        'delete_user' => 'حذف المستخدم',
        'confirm_reset_password' => 'هل أنت متأكد أنك تريد إعادة تعيين كلمة مرور هذا المستخدم؟',
        'confirm_delete' => 'هل أنت متأكد أنك تريد حذف هذا المستخدم؟',
        'confirm_delete_text' => 'حذف المستخدم إجراء دائم ولا يمكن التراجع عنه.',
        'password_note' => 'اترك حقول كلمة المرور فارغة للاحتفاظ بكلمة المرور الحالية.',
        'username' => 'اسم المستخدم',
        'full_name' => 'الاسم الكامل',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'user_id' => 'معرّف المستخدم',
        'created_on' => 'تم الإنشاء في',
        'last_modified' => 'آخر تعديل',
        'user_active' => 'المستخدم نشط',
        'status' => 'الحالة',
        'no_groups' => 'لا توجد مجموعات',
        'groups' => 'مجموعات المستخدمين',
        'permissions' => 'الأذونات',
        'permissions_assigned' => 'الأذونات المعينة',
        'branch_assignments' => 'تعيينات الفروع',
        'select_branches' => 'حدد الفروع لتعيين هذا المستخدم.',
        'total_users' => 'إجمالي المستخدمين',
        'active_users' => 'المستخدمون النشطون',
    // تمت إضافة حقل الصورة الرمزية داخل المصفوفة
    'avatar' => 'الصورة الرمزية',
    ],

    // ===========================
    // مجموعات المستخدمين
    // ===========================
    'user_groups' => [
        'total_groups' => 'إجمالي المجموعات',
        'confirm_delete' => 'هل أنت متأكد أنك تريد حذف هذه المجموعة؟',
        'confirm_delete_text' => 'سيؤدي حذف المجموعة إلى إزالة جميع الأذونات الخاصة بها.',
    ],

    // ===========================
    // الأذونات
    // ===========================
    'permissions' => 'الأذونات',

    // ===========================
    // الموارد
    // ===========================
    'resource' => 'المورد',
    'permissions_label' => 'الأذونات',
    'module' => 'الوحدة',
    'action' => 'الإجراء',

    // ===========================
    // الحقول الشائعة
    // ===========================
    // (Common field labels removed - now global)

    // ===========================
    // الحالة
    // ===========================
    // (Status keys removed - now global)

    // ===========================
    // الفروع
    // ===========================
    'branch' => [
        'name' => 'اسم الفرع',
        'location' => 'الموقع',
        'no_branches' => 'لا توجد فروع متاحة.',
    ],

    // ===========================
    // التعيينات
    // ===========================
    'assign' => 'تعيين',
    'primary' => 'أساسي',

    // ===========================
    // Profile Management
    // ===========================
    'profile' => [
        'page_title' => 'الملف الشخصي',

        'current_password_required' => 'كلمة المرور الحالية مطلوبة.',
        'new_password_required' => 'كلمة المرور الجديدة مطلوبة.',
        'password_min_length' => 'يجب أن تكون كلمة المرور 6 أحرف على الأقل.',
        'passwords_do_not_match' => 'كلمتا المرور غير متطابقتين.',
        'current_password_incorrect' => 'كلمة المرور الحالية غير صحيحة.',
        'password_updated_successfully' => 'تم تحديث كلمة المرور بنجاح.',
        'failed_to_update_password' => 'فشل تحديث كلمة المرور.',

        'username_required' => 'اسم المستخدم مطلوب.',
        'username_min_length' => 'يجب أن يحتوي اسم المستخدم على 3 أحرف على الأقل.',
        'username_already_exists' => 'اسم المستخدم موجود بالفعل.',
        'username_updated_successfully' => 'تم تحديث اسم المستخدم بنجاح.',
        'failed_to_update_username' => 'فشل تحديث اسم المستخدم.',

        'invalid_image_format' => 'تنسيق الصورة غير صالح. المسموح: JPG, JPEG, PNG, GIF.',
        'avatar_updated_successfully' => 'تم تحديث صورة الملف الشخصي بنجاح.',
        'failed_to_update_avatar' => 'فشل تحديث صورة الملف الشخصي.',
        'failed_to_upload_avatar' => 'فشل تحميل صورة الملف الشخصي.',
        'error_uploading_file' => 'خطأ في تحميل الملف',
        'avatar_removed_successfully' => 'تمت إزالة صورة الملف الشخصي بنجاح.',
        'failed_to_remove_avatar' => 'فشل إزالة صورة الملف الشخصي.',
    ],
    'profile' => 'Profile',
    // نقل تسمية الصورة الرمزية داخل مصفوفة user
    'update_profile_picture' => 'Update Profile Picture',
    'upload_avatar' => 'Upload Avatar',
    'remove_avatar' => 'Remove Avatar',
    'username' => 'Username',
    'password' => 'Password',
    'update_username' => 'Update Username',
    'current_password' => 'Current Password',
    'new_password' => 'New Password',
    'confirm_new_password' => 'Confirm New Password',
    'update_password' => 'Update Password',


];
