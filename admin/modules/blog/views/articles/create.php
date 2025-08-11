<?php
// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'articles', 'create')) {
    header('Location: /admin/login');
    exit;
}

$controller = new ArticleController($pdo);
$data = $controller->create();
$languages = $data['languages'] ?? [];
$activeLanguages = $data['activeLanguages'] ?? [];

$pageTitle = TranslationManager::t('add_article');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo TranslationManager::t('add_article'); ?></h2>
                    <a href="articles.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('back'); ?>
                    </a>
                </div>

                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($data['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0"><?php echo TranslationManager::t('article_translations'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <?php $ti=0; foreach($activeLanguages as $lang): $code=$lang['code']; ?>
                                            <li class="nav-item" role="presentation"><button class="nav-link <?php echo $ti===0?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#lang-<?php echo $code; ?>" type="button" role="tab"><?php echo strtoupper($code); ?></button></li>
                                        <?php $ti++; endforeach; ?>
                                    </ul>
                                    <div class="tab-content border border-top-0 p-3">
                                        <?php $ti=0; $defaultLang = get_default_public_language_from_db(); foreach($activeLanguages as $lang): $code=$lang['code']; ?>
                                        <div class="tab-pane fade <?php echo $ti===0?'show active':''; ?>" id="lang-<?php echo $code; ?>" role="tabpanel">
                                            <div class="mb-3">
                                                <label class="form-label"><?php echo TranslationManager::t('article_title'); ?> (<?php echo strtoupper($code); ?>)<?php echo $code===$defaultLang?' *':''; ?></label>
                                                <input type="text" class="form-control" name="translations[<?php echo $code; ?>][title]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['title'] ?? ''); ?>" maxlength="255" <?php echo $code===$defaultLang?'required':''; ?>>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Slug</label>
                                                <input type="text" class="form-control slug-input" data-lang="<?php echo $code; ?>" name="translations[<?php echo $code; ?>][slug]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['slug'] ?? ''); ?>" maxlength="255">
                                                <div class="form-text d-flex justify-content-between"><span><?php echo TranslationManager::t('leave_empty_auto'); ?></span><span class="slug-status text-muted" data-status-for="<?php echo $code; ?>"></span></div>
                                            </div>
// Slug live validation
document.querySelectorAll('.slug-input').forEach(inp=>{ inp.addEventListener('blur',()=>{ const val=inp.value.trim(); if(!val) return; const lang=inp.dataset.lang; const statusEl=document.querySelector('[data-status-for="'+lang+'"]'); statusEl.textContent='...'; fetch('article-slug-check.php?slug='+encodeURIComponent(val)+'&lang='+encodeURIComponent(lang)).then(r=>r.json()).then(j=>{ if(j.available){ statusEl.textContent='OK'; statusEl.classList.remove('text-danger'); statusEl.classList.add('text-success'); } else { statusEl.textContent='IN USO'; statusEl.classList.remove('text-success'); statusEl.classList.add('text-danger'); } }).catch(()=>{ statusEl.textContent='err'; statusEl.classList.add('text-danger'); }); }); });
                                            <div class="mb-3">
                                                <label class="form-label"><?php echo TranslationManager::t('article_content'); ?> <?php echo $code===$defaultLang?'*':''; ?></label>
                                                <textarea class="form-control tinymce-editor" name="translations[<?php echo $code; ?>][content]" rows="12"><?php echo htmlspecialchars($_POST['translations'][$code]['content'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><?php echo TranslationManager::t('article_excerpt'); ?></label>
                                                <textarea class="form-control" name="translations[<?php echo $code; ?>][excerpt]" rows="3" maxlength="500"><?php echo htmlspecialchars($_POST['translations'][$code]['excerpt'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label"><?php echo TranslationManager::t('meta_title'); ?></label>
                                                    <input type="text" class="form-control" name="translations[<?php echo $code; ?>][meta_title]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['meta_title'] ?? ''); ?>" maxlength="255">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label"><?php echo TranslationManager::t('meta_keywords'); ?></label>
                                                    <input type="text" class="form-control" name="translations[<?php echo $code; ?>][meta_keywords]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['meta_keywords'] ?? ''); ?>" maxlength="255">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><?php echo TranslationManager::t('meta_description'); ?></label>
                                                <textarea class="form-control" name="translations[<?php echo $code; ?>][meta_description]" rows="2" maxlength="160"><?php echo htmlspecialchars($_POST['translations'][$code]['meta_description'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                        <?php $ti++; endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?php echo TranslationManager::t('seo_settings'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label"><?php echo TranslationManager::t('meta_title'); ?></label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="<?php echo htmlspecialchars($_POST['meta_title'] ?? ''); ?>" 
                                               maxlength="255">
                                        <div class="form-text"><?php echo TranslationManager::t('optional'); ?> - Se vuoto, sarà usato il titolo dell'articolo</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label"><?php echo TranslationManager::t('meta_description'); ?></label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160">
                                            <?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?>
                                        </textarea>
                                        <div class="form-text">Massimo 160 caratteri per i motori di ricerca</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="meta_keywords" class="form-label"><?php echo TranslationManager::t('meta_keywords'); ?></label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                               value="<?php echo htmlspecialchars($_POST['meta_keywords'] ?? ''); ?>" 
                                               maxlength="255">
                                        <div class="form-text">Parole chiave separate da virgole</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Article Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Impostazioni Articolo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label"><?php echo TranslationManager::t('article_status'); ?></label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft" <?php echo (($_POST['status'] ?? 'draft') === 'draft') ? 'selected' : ''; ?>>
                                                <?php echo TranslationManager::t('draft'); ?>
                                            </option>
                                            <option value="published" <?php echo (($_POST['status'] ?? '') === 'published') ? 'selected' : ''; ?>>
                                                <?php echo TranslationManager::t('published'); ?>
                                            </option>
                                            <option value="private" <?php echo (($_POST['status'] ?? '') === 'private') ? 'selected' : ''; ?>>
                                                <?php echo TranslationManager::t('private'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label"><?php echo TranslationManager::t('article_category'); ?></label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="">Nessuna categoria</option>
                                            <?php if (isset($data['categories'])): ?>
                                                <?php foreach ($data['categories'] as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" 
                                                            <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="published_at" class="form-label"><?php echo TranslationManager::t('published_at'); ?></label>
                                        <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="<?php echo htmlspecialchars($_POST['published_at'] ?? ''); ?>">
                                        <div class="form-text">Lascia vuoto per pubblicazione immediata</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tags</label>
                                        <input type="text" class="form-control" name="tags" value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>" placeholder="seo, cucina, ricetta">
                                        <div class="form-text">Separati da virgola</div>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                               <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_featured">
                                            <?php echo TranslationManager::t('featured_article'); ?>
                                        </label>
                                        <div class="form-text">Gli articoli in evidenza appaiono in homepage</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?php echo TranslationManager::t('featured_image'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Seleziona Immagine</label>
                                        <input type="text" class="form-control" id="featured_image" name="featured_image" 
                                               value="<?php echo htmlspecialchars($_POST['featured_image'] ?? ''); ?>" 
                                               readonly>
                                        <button type="button" class="btn btn-outline-primary mt-2" id="selectImageBtn">
                                            <i class="fas fa-images"></i> Scegli Immagine
                                        </button>
                                        <button type="button" class="btn btn-outline-danger mt-2" id="removeImageBtn" style="display:none;">
                                            <i class="fas fa-times"></i> Rimuovi
                                        </button>
                                    </div>
                                    <div id="imagePreview" class="text-center" style="display:none;">
                                        <img id="previewImg" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo TranslationManager::t('save'); ?>
                                        </button>
                                        <a href="articles.php" class="btn btn-outline-secondary">
                                            <?php echo TranslationManager::t('cancel'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
function initEditors(){
    tinymce.init({
        selector: '.tinymce-editor',
        height: 400,
        menubar: true,
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image media link | code | help',
        language: '<?php echo $language; ?>',
        relative_urls:false, remove_script_host:false, convert_urls:true, branding:false,
        file_picker_types:'image',
        file_picker_callback:(cb,value,meta)=>{ if(meta.filetype==='image'){ openMediaLibrary(url=>cb(url,{alt:''})); } }
    });
}
document.addEventListener('DOMContentLoaded',function(){ initEditors(); });
document.getElementById('selectImageBtn').addEventListener('click', function(){ openMediaLibrary(function(imageUrl){ document.getElementById('featured_image').value=imageUrl; document.getElementById('previewImg').src=imageUrl; document.getElementById('imagePreview').style.display='block'; document.getElementById('removeImageBtn').style.display='inline-block'; });});
document.getElementById('removeImageBtn').addEventListener('click', function(){ document.getElementById('featured_image').value=''; document.getElementById('imagePreview').style.display='none'; this.style.display='none'; });
function openMediaLibrary(cb){ const imageUrl=prompt('Inserisci URL dell\'immagine:'); if(imageUrl){ cb(imageUrl);} }
document.addEventListener('DOMContentLoaded',function(){ const fi=document.getElementById('featured_image').value; if(fi){ document.getElementById('previewImg').src=fi; document.getElementById('imagePreview').style.display='block'; document.getElementById('removeImageBtn').style.display='inline-block'; }});
</script>

<?php include 'includes/footer.php'; ?>