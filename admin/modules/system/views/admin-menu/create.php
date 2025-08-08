<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"><?php echo TranslationManager::t('settings'); ?></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a><?php echo TranslationManager::t('menu.management'); ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo TranslationManager::t('add.new'); ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card border-top border-0 border-4 border-primary">
                <div class="card-body p-5">
                    <div class="card-title d-flex align-items-center">
                        <div><i class="bx bx-plus me-1 font-22 text-primary"></i></div>
                        <h5 class="mb-0 text-primary"><?php echo TranslationManager::t('add.menu.item'); ?></h5>
                    </div>
                    <hr/>

                    <?php if (isset($notification)): ?>
                    <div class="alert alert-<?php echo $notification['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $notification['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo get_setting('site_url', 'http://localhost'); ?>/admin/menu-management/store">
                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                        
                        <div class="row mb-3">
                            <label for="parent_id" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('parent.menu'); ?></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="parent_id" id="parent_id">
                                    <option value=""><?php echo TranslationManager::t('select.parent'); ?> (<?php echo TranslationManager::t('optional'); ?>)</option>
                                    <?php foreach ($parent_items as $parent): ?>
                                    <option value="<?php echo $parent['id']; ?>">
                                        <?php echo htmlspecialchars($parent['title']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="title" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('title'); ?> (EN) *</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="title" id="title" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="title_ar" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('title'); ?> (AR)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="title_ar" id="title_ar" dir="rtl">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="title_it" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('title'); ?> (IT)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="title_it" id="title_it">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="url" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('url'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="url" id="url" placeholder="<?php echo TranslationManager::t('example.url'); ?>">
                                <div class="form-text"><?php echo TranslationManager::t('leave.empty.parent'); ?></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="icon" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('icon'); ?></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="icon" id="icon" value="<?php echo TranslationManager::t('icon.placeholder'); ?>" placeholder="<?php echo TranslationManager::t('icon.placeholder'); ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="showIconPicker()">
                                        <i class="fas fa-icons"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <?php echo TranslationManager::t('preview'); ?>: <i id="icon-preview" class="fas fa-circle"></i>
                                    <a href="https://fontawesome.com/icons" target="_blank"><?php echo TranslationManager::t('browse.icons'); ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="sort_order" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('sort.order'); ?></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="sort_order" id="sort_order" value="0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="permission_module" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('permission.module'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="permission_module" id="permission_module" placeholder="<?php echo TranslationManager::t('example.module'); ?>">
                                <div class="form-text"><?php echo TranslationManager::t('leave.empty.permission'); ?></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="permission_action" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('permission.action'); ?></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="permission_action" id="permission_action">
                                    <option value="view"><?php echo TranslationManager::t('permission.action.view'); ?></option>
                                    <option value="create"><?php echo TranslationManager::t('permission.action.create'); ?></option>
                                    <option value="update"><?php echo TranslationManager::t('permission.action.update'); ?></option>
                                    <option value="delete"><?php echo TranslationManager::t('permission.action.delete'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="target" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('target'); ?></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="target" id="target">
                                    <option value="_self"><?php echo TranslationManager::t('same.window'); ?></option>
                                    <option value="_blank"><?php echo TranslationManager::t('new.window'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="css_class" class="col-sm-3 col-form-label"><?php echo TranslationManager::t('css.class'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="css_class" id="css_class" placeholder="<?php echo TranslationManager::t('custom.class.placeholder'); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        <?php echo TranslationManager::t('is.active'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_separator" id="is_separator">
                                    <label class="form-check-label" for="is_separator">
                                        <?php echo TranslationManager::t('is.separator'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="bx bx-save"></i> <?php echo TranslationManager::t('save'); ?>
                                </button>
                                <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/system/adminmenu'; ?>" class="btn btn-secondary px-5 ms-2">
                                    <i class="bx bx-x"></i> <?php echo TranslationManager::t('cancel'); ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Icon preview
document.getElementById('icon').addEventListener('input', function() {
    const iconClass = this.value || 'fas fa-circle';
    document.getElementById('icon-preview').className = iconClass;
});

function showIconPicker() {
    alert('<?php echo TranslationManager::t('browse.icons.info'); ?>');
}
</script>

<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>