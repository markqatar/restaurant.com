<?php
require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';
?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2"><i class="fas fa-file-circle-plus me-2"></i><?php echo TranslationManager::t('add_page'); ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/pages/page'; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back'); ?></a>
    </div>
</div>

<form method="POST" action="<?php echo get_setting('site_url', 'http://localhost') . '/admin/pages/page/store'; ?>">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 shadow">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('page_content'); ?></h6>
                </div>
                <div class="card-body">
                    <?php $defaultLang = get_default_public_language_from_db(); ?>
                    <?php if (empty($activeLanguages)): ?>
                        <div class="alert alert-warning mb-0"><?php echo TranslationManager::t('no_active_languages'); ?></div>
                    <?php else: ?>
                        <ul class="nav nav-tabs" role="tablist">
                            <?php $i=0; foreach ($activeLanguages as $lang): $code=$lang['code']; ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo $i===0? 'active':''; ?>" id="tab-<?php echo $code; ?>" data-bs-toggle="tab" data-bs-target="#pane-<?php echo $code; ?>" type="button" role="tab">
                                        <?php echo htmlspecialchars(strtoupper($code).' - '.$lang['name']); ?><?php echo $code === $defaultLang ? ' *' : ''; ?>
                                    </button>
                                </li>
                            <?php $i++; endforeach; ?>
                        </ul>
                        <div class="tab-content border border-top-0 p-3">
                            <?php $i=0; foreach ($activeLanguages as $lang): $code=$lang['code']; ?>
                                <div class="tab-pane fade <?php echo $i===0? 'show active':''; ?>" id="pane-<?php echo $code; ?>" role="tabpanel">
                                    <div class="mb-3">
                                        <label class="form-label" for="title-<?php echo $code; ?>"><?php echo TranslationManager::t('page_title'); ?> (<?php echo strtoupper($code); ?>)<?php echo $code === $defaultLang ? ' *' : ''; ?></label>
                                        <input type="text" class="form-control" id="title-<?php echo $code; ?>" name="translations[<?php echo $code; ?>][title]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['title'] ?? ''); ?>" maxlength="255" <?php echo $code === $defaultLang ? 'required' : ''; ?>>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="content-<?php echo $code; ?>"><?php echo TranslationManager::t('page_content'); ?> (<?php echo strtoupper($code); ?>)</label>
                                        <textarea class="form-control tinymce-editor" id="content-<?php echo $code; ?>" name="translations[<?php echo $code; ?>][content]" rows="10"><?php echo htmlspecialchars($_POST['translations'][$code]['content'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="meta_title-<?php echo $code; ?>"><?php echo TranslationManager::t('meta_title'); ?> (<?php echo strtoupper($code); ?>)</label>
                                        <input type="text" class="form-control" id="meta_title-<?php echo $code; ?>" name="translations[<?php echo $code; ?>][meta_title]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['meta_title'] ?? ''); ?>" maxlength="255">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="meta_description-<?php echo $code; ?>"><?php echo TranslationManager::t('meta_description'); ?> (<?php echo strtoupper($code); ?>)</label>
                                        <textarea class="form-control" id="meta_description-<?php echo $code; ?>" name="translations[<?php echo $code; ?>][meta_description]" rows="3" maxlength="160"><?php echo htmlspecialchars($_POST['translations'][$code]['meta_description'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            <?php $i++; endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('page_settings'); ?></h6></div>
                <div class="card-body">
                    <?php echo csrf_token_field(); ?>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" <?php echo !empty($_POST['is_published']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_published"><?php echo TranslationManager::t('published'); ?></label>
                    </div>
                    <div class="mb-3">
                        <label for="sort_order" class="form-label"><?php echo TranslationManager::t('display_order'); ?></label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? '0'); ?>" min="0">
                        <div class="form-text">0 = <?php echo TranslationManager::t('first_in_list'); ?></div>
                    </div>
                </div>
            </div>
            <div class="card mb-4 shadow">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('featured_image'); ?></h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="featured_image" class="form-label"><?php echo TranslationManager::t('select_image'); ?></label>
                        <input type="text" class="form-control" id="featured_image" name="featured_image" value="<?php echo htmlspecialchars($_POST['featured_image'] ?? ''); ?>" readonly>
                        <input type="file" id="featured_file" accept="image/*" style="display:none;">
                        <button type="button" class="btn btn-outline-primary mt-2" id="selectImageBtn"><i class="fas fa-images"></i> <?php echo TranslationManager::t('choose_image'); ?></button>
                        <button type="button" class="btn btn-outline-danger mt-2" id="removeImageBtn" style="display:none;"><i class="fas fa-times"></i> <?php echo TranslationManager::t('remove'); ?></button>
                        <div id="dropZone" class="mt-3 p-3 border border-2 border-dashed rounded text-center bg-light" style="cursor:pointer;">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-secondary"></i>
                            <div class="fw-semibold small"><?php echo TranslationManager::t('drag_drop_image'); ?></div>
                        </div>
                        <div id="uploadStatus" class="small mt-2 text-muted" style="display:none;"></div>
                    </div>
                    <div id="imagePreview" class="text-center" style="display:none;">
                        <img id="previewImg" src="" alt="Preview" class="img-fluid" style="max-height:200px;">
                    </div>
                </div>
            </div>
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i><?php echo TranslationManager::t('save'); ?></button>
                        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/pages/page'; ?>" class="btn btn-outline-secondary"><?php echo TranslationManager::t('cancel'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
  const TINYMCE_VARS = {
    language: '<?php echo $language; ?>',
    urls: {
      mediaSelector: '<?php echo get_setting('site_url', 'http://localhost') . '/admin/media/media/mediaSelector'; ?>'
    },
    translations: {
      selectMedia: '<?php echo TranslationManager::t("select_media"); ?>'
    },
    // Selettori/ID usati nella pagina
    selectors: {
      editor: '.tinymce-editor',
      selectImageBtn: '#selectImageBtn',
      removeImageBtn: '#removeImageBtn',
      featuredImageInput: '#featured_image',
      imagePreviewWrapper: '#imagePreview',
      imagePreviewImg: '#previewImg'
    }
  };
</script>
<?php
$pageScripts = [
  get_setting('site_url', 'http://localhost') . '/admin/modules/pages/views/pages/js/create.js',
];
?>
<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>