<?php require_once get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php'; ?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2"><i class="fas fa-pen-to-square me-2"></i><?php echo TranslationManager::t('page.edit'); ?></h1>
    <div class="btn-toolbar ms-auto">
        <a href="<?php echo get_setting('site_url', 'http://localhost') . '/admin/pages/page'; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i><?php echo TranslationManager::t('back'); ?></a>
    </div>
</div>

<form method="POST" action="<?php echo get_setting('site_url', 'http://localhost') . '/admin/pages/page/update/' . (int)$page['id']; ?>">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 shadow">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('page_content'); ?></h6></div>
                <div class="card-body">
                    <?php $defaultLang = get_default_public_language_from_db(); $translations = $page['translations'] ?? []; ?>
                    <?php if (empty($activeLanguages)): ?>
                        <div class="alert alert-warning mb-0"><?php echo TranslationManager::t('no_active_languages'); ?></div>
                    <?php else: ?>
                        <ul class="nav nav-tabs" role="tablist">
                            <?php $i=0; foreach ($activeLanguages as $lang): $code=$lang['code']; ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo $i===0?'active':''; ?>" id="tab-<?php echo $code; ?>" data-bs-toggle="tab" data-bs-target="#pane-<?php echo $code; ?>" type="button" role="tab">
                                        <?php echo htmlspecialchars(strtoupper($code).' - '.$lang['name']); ?><?php echo $code === $defaultLang ? ' *' : ''; ?>
                                    </button>
                                </li>
                            <?php $i++; endforeach; ?>
                        </ul>
                        <div class="tab-content border border-top-0 p-3">
                            <?php $i=0; foreach ($activeLanguages as $lang): $code=$lang['code']; $t=$translations[$code] ?? []; ?>
                                <div class="tab-pane fade <?php echo $i===0?'show active':''; ?>" id="pane-<?php echo $code; ?>" role="tabpanel">
                                    <div class="mb-3">
                                        <label class="form-label" for="title-<?php echo $code; ?>"><?php echo TranslationManager::t('page_title'); ?> (<?php echo strtoupper($code); ?>)<?php echo $code === $defaultLang ? ' *' : ''; ?></label>
                                        <input type="text" class="form-control" id="title-<?php echo $code; ?>" name="translations[<?php echo $code; ?>][title]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['title'] ?? ($t['title'] ?? '')); ?>" maxlength="255" <?php echo $code === $defaultLang ? 'required' : ''; ?>>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="content-<?php echo $code; ?>"><?php echo TranslationManager::t('page_content'); ?> (<?php echo strtoupper($code); ?>)</label>
                                        <textarea class="form-control tinymce-editor" id="content-<?php echo $code; ?>" name="translations[<?php echo $code; ?>][content]" rows="10"><?php echo htmlspecialchars($_POST['translations'][$code]['content'] ?? ($t['content'] ?? '')); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="meta_title-<?php echo $code; ?>"><?php echo TranslationManager::t('meta_title'); ?> (<?php echo strtoupper($code); ?>)</label>
                                        <input type="text" class="form-control" id="meta_title-<?php echo $code; ?>" name="translations[<?php echo $code; ?>][meta_title]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['meta_title'] ?? ($t['meta_title'] ?? '')); ?>" maxlength="255">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="meta_description-<?php echo $code; ?>"><?php echo TranslationManager::t('meta_description'); ?> (<?php echo strtoupper($code); ?>)</label>
                                        <textarea class="form-control" id="meta_description-<?php echo $code; ?>" name="translations[<?php echo $code; ?>][meta_description]" rows="3" maxlength="160"><?php echo htmlspecialchars($_POST['translations'][$code]['meta_description'] ?? ($t['meta_description'] ?? '')); ?></textarea>
                                    </div>
                                    <?php if (!empty($t['slug'])): ?>
                                        <div class="mb-3">
                                            <label class="form-label"><?php echo TranslationManager::t('slug'); ?> (<?php echo strtoupper($code); ?>)</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($t['slug']); ?>" disabled>
                                            <input type="hidden" name="translations[<?php echo $code; ?>][slug]" value="<?php echo htmlspecialchars($t['slug']); ?>">
                                        </div>
                                    <?php endif; ?>
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
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" <?php echo ($page['is_published'] ?? 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_published"><?php echo TranslationManager::t('published'); ?></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="sort_order"><?php echo TranslationManager::t('display_order'); ?></label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($page['sort_order'] ?? 0); ?>" min="0">
                        <div class="form-text">0 = <?php echo TranslationManager::t('first_in_list'); ?></div>
                    </div>
                </div>
            </div>
            <div class="card mb-4 shadow">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary"><?php echo TranslationManager::t('featured_image'); ?></h6></div>
                <div class="card-body">
                    <?php $currentImage = $_POST['featured_image'] ?? ($page['featured_image'] ?? ''); $hasImage = trim($currentImage) !== ''; ?>
                    <div class="mb-3">
                        <label for="featured_image" class="form-label"><?php echo TranslationManager::t('select_image'); ?></label>
                        <input type="text" class="form-control" id="featured_image" name="featured_image" data-original="<?php echo htmlspecialchars($page['featured_image'] ?? ''); ?>" value="<?php echo htmlspecialchars($currentImage); ?>" readonly>
                        <input type="file" id="featured_file" accept="image/*" style="display:none;">
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary" id="selectImageBtn"><i class="fas fa-images"></i> <?php echo TranslationManager::t('choose_image'); ?></button>
                            <button type="button" class="btn btn-outline-secondary" id="restoreImageBtn" style="<?php echo ($hasImage && ($page['featured_image'] ?? '') && ($currentImage !== ($page['featured_image'] ?? '')))? '':'display:none;'; ?>"><i class="fas fa-undo"></i> <?php echo TranslationManager::t('restore'); ?></button>
                            <button type="button" class="btn btn-outline-danger" id="removeImageBtn" style="<?php echo $hasImage? '':'display:none;'; ?>"><i class="fas fa-times"></i> <?php echo TranslationManager::t('remove'); ?></button>
                        </div>
                        <div id="dropZone" class="mt-3 p-3 border border-2 border-dashed rounded text-center bg-light" style="cursor:pointer;">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-secondary"></i>
                            <div class="fw-semibold small"><?php echo TranslationManager::t('drag_drop_image'); ?></div>
                        </div>
                        <div id="uploadStatus" class="small mt-2 text-muted" style="display:none;"></div>
                    </div>
                    <div id="imagePreview" class="text-center" style="<?php echo $hasImage? '':'display:none;'; ?>">
                        <img id="previewImg" src="<?php echo $hasImage? htmlspecialchars($currentImage):''; ?>" alt="Preview" class="img-fluid" style="max-height:200px;">
                    </div>
                </div>
            </div>
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i><?php echo TranslationManager::t('update'); ?></button>
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
  get_setting('site_url', 'http://localhost') . '/admin/modules/pages/views/pages/js/edit.js',
];
?>
<script>
// Enhance existing edit.js behavior with restore button without editing bundled file
(function(){
    const input = document.getElementById('featured_image');
    if(!input) return;
    const original = input.getAttribute('data-original') || '';
    const restoreBtn = document.getElementById('restoreImageBtn');
    const removeBtn = document.getElementById('removeImageBtn');
    const preview = document.getElementById('imagePreview');
    const img = document.getElementById('previewImg');
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('featured_file');
    const uploadStatus = document.getElementById('uploadStatus');
    function syncRestoreVisibility(){
        if(!restoreBtn) return;
        const current = input.value.trim();
        if(original && current !== original){ restoreBtn.style.display='inline-block'; } else { restoreBtn.style.display='none'; }
    }
    if(restoreBtn){
        restoreBtn.addEventListener('click', function(){
            input.value = original;
            if(original){ if(img) img.src = original; if(preview) preview.style.display='block'; if(removeBtn) removeBtn.style.display='inline-block'; }
            syncRestoreVisibility();
        });
    }
    // Hook into existing remove button
    if(removeBtn){
        const origHandler = removeBtn.onclick;
        removeBtn.addEventListener('click', function(){ setTimeout(syncRestoreVisibility, 50); });
    }
    // Observe input changes triggered by media selector
    const obs = new MutationObserver(syncRestoreVisibility);
    obs.observe(input, { attributes:true, attributeFilter:['value'] });
    syncRestoreVisibility();

        function showStatus(msg, cls){ if(!uploadStatus) return; uploadStatus.textContent=msg; uploadStatus.className='small mt-2 '+cls; uploadStatus.style.display='block'; }
        function doUpload(file){
            if(!file) return; if(!file.type.match(/^image\//)){ showStatus('Only image files allowed','text-danger'); return; }
            const fd = new FormData(); fd.append('file', file); showStatus('Uploading...','text-info');
            fetch('/admin/media/media/upload',{method:'POST',body:fd}).then(r=>r.json()).then(j=>{
                if(j.success && j.url){ input.value=j.url; if(img) img.src=j.url; if(preview) preview.style.display='block'; if(removeBtn) removeBtn.style.display='inline-block'; showStatus('Uploaded','text-success'); syncRestoreVisibility(); }
                else showStatus(j.message||'Upload failed','text-danger');
            }).catch(()=>showStatus('Upload error','text-danger'));
        }
        if(dropZone){
            ['dragenter','dragover'].forEach(ev=>dropZone.addEventListener(ev,e=>{e.preventDefault();dropZone.classList.add('border-primary');}));
            ['dragleave','drop'].forEach(ev=>dropZone.addEventListener(ev,e=>{e.preventDefault();dropZone.classList.remove('border-primary');}));
            dropZone.addEventListener('drop', e=>{ const f=e.dataTransfer.files[0]; doUpload(f); });
            dropZone.addEventListener('click', ()=> fileInput && fileInput.click());
        }
        if(fileInput){ fileInput.addEventListener('change', e=>doUpload(e.target.files[0])); }
})();
</script>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>