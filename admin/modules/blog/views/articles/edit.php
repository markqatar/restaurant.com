<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/ArticleController.php';
require_once '../includes/functions.php';
if(!isset($_SESSION['user_id'])||!has_permission($_SESSION['user_id'],'articles','edit')){ header('Location: /admin/login'); exit; }
$id = $_GET['id'] ?? null; if(!$id){ header('Location: articles.php'); exit; }
$controller=new ArticleController($pdo); $data=$controller->edit($id); $article=$data['article']; $languages=$data['languages']; $activeLanguages=$data['activeLanguages']; $translations=$article['translations']??[]; $pageTitle=TranslationManager::t('edit_article'); include 'includes/header.php';
?>
<div class="container-fluid"><div class="row"><div class="col-md-12"><div class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-4"><h2><?php echo TranslationManager::t('edit_article'); ?></h2><a href="articles.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('back'); ?></a></div>
  <?php if(isset($data['error'])): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($data['error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
  <form method="POST" action="" enctype="multipart/form-data">
    <div class="row">
      <div class="col-md-8">
        <div class="card mb-4"><div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0"><?php echo TranslationManager::t('article_translations'); ?></h5></div>
          <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
              <?php $ti=0; foreach($activeLanguages as $lang): $code=$lang['code']; ?>
                <li class="nav-item" role="presentation"><button class="nav-link <?php echo $ti===0?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#lang-<?php echo $code; ?>" type="button" role="tab"><?php echo strtoupper($code); ?></button></li>
              <?php $ti++; endforeach; ?>
            </ul>
            <div class="tab-content border border-top-0 p-3">
              <?php $ti=0; $defaultLang=get_default_public_language_from_db(); foreach($activeLanguages as $lang): $code=$lang['code']; $t=$translations[$code]??[]; ?>
                <div class="tab-pane fade <?php echo $ti===0?'show active':''; ?>" id="lang-<?php echo $code; ?>" role="tabpanel">
                  <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('article_title'); ?> (<?php echo strtoupper($code); ?>)<?php echo $code===$defaultLang?' *':''; ?></label><input type="text" class="form-control" name="translations[<?php echo $code; ?>][title]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['title']??($t['title']??'')); ?>" maxlength="255" <?php echo $code===$defaultLang?'required':''; ?>></div>
                  <div class="mb-3"><label class="form-label">Slug</label><input type="text" class="form-control slug-input" data-lang="<?php echo $code; ?>" name="translations[<?php echo $code; ?>][slug]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['slug']??($t['slug']??'')); ?>" maxlength="255"><div class="form-text d-flex justify-content-between"><span><?php echo TranslationManager::t('leave_empty_auto'); ?></span><span class="slug-status text-muted" data-status-for="<?php echo $code; ?>"></span></div></div>
                  <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('article_content'); ?> <?php echo $code===$defaultLang?'*':''; ?></label><textarea class="form-control tinymce-editor" name="translations[<?php echo $code; ?>][content]" rows="12"><?php echo htmlspecialchars($_POST['translations'][$code]['content']??($t['content']??'')); ?></textarea></div>
                  <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('article_excerpt'); ?></label><textarea class="form-control" name="translations[<?php echo $code; ?>][excerpt]" rows="3" maxlength="500"><?php echo htmlspecialchars($_POST['translations'][$code]['excerpt']??($t['excerpt']??'')); ?></textarea></div>
                  <div class="row"><div class="col-md-6 mb-3"><label class="form-label"><?php echo TranslationManager::t('meta_title'); ?></label><input type="text" class="form-control" name="translations[<?php echo $code; ?>][meta_title]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['meta_title']??($t['meta_title']??'')); ?>" maxlength="255"></div><div class="col-md-6 mb-3"><label class="form-label"><?php echo TranslationManager::t('meta_keywords'); ?></label><input type="text" class="form-control" name="translations[<?php echo $code; ?>][meta_keywords]" value="<?php echo htmlspecialchars($_POST['translations'][$code]['meta_keywords']??($t['meta_keywords']??'')); ?>" maxlength="255"></div></div>
                  <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('meta_description'); ?></label><textarea class="form-control" name="translations[<?php echo $code; ?>][meta_description]" rows="2" maxlength="160"><?php echo htmlspecialchars($_POST['translations'][$code]['meta_description']??($t['meta_description']??'')); ?></textarea></div>
                </div>
              <?php $ti++; endforeach; ?>
            </div>
          </div>
        </div>
  <div class="card mb-4"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Revisioni</h5><button type="button" id="reloadRevisions" class="btn btn-sm btn-outline-secondary"><i class="fas fa-rotate"></i></button></div><div class="card-body"><div id="revisionsList" class="list-group small"></div><div class="mt-2 text-muted" id="revEmpty" style="display:none;">Nessuna revisione ancora.</div></div></div>
      </div>
      <div class="col-md-4">
        <div class="card mb-4"><div class="card-header"><h5 class="card-title mb-0">Impostazioni Articolo</h5></div>
          <div class="card-body">
            <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('article_status'); ?></label><select class="form-select" name="status"><option value="draft" <?php echo $article['status']==='draft'?'selected':''; ?>><?php echo TranslationManager::t('draft'); ?></option><option value="published" <?php echo $article['status']==='published'?'selected':''; ?>><?php echo TranslationManager::t('published'); ?></option><option value="private" <?php echo $article['status']==='private'?'selected':''; ?>><?php echo TranslationManager::t('private'); ?></option></select></div>
            <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('article_category'); ?></label><select class="form-select" name="category_id"><option value="">Nessuna categoria</option><?php foreach($data['categories'] as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo ($article['category_id']==$c['id'])?'selected':''; ?>><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('published_at'); ?></label><input type="datetime-local" class="form-control" name="published_at" value="<?php echo htmlspecialchars($article['published_at']? date('Y-m-d\TH:i', strtotime($article['published_at'])):''); ?>"><div class="form-text">Lascia vuoto per pubblicazione immediata</div></div>
            <div class="form-check mb-3"><input type="checkbox" class="form-check-input" name="is_featured" id="is_featured" <?php echo $article['is_featured']?'checked':''; ?>><label class="form-check-label" for="is_featured"><?php echo TranslationManager::t('featured_article'); ?></label></div>
            <div class="mb-3"><label class="form-label">Tags</label><input type="text" class="form-control" name="tags" value="<?php echo htmlspecialchars(implode(', ',$article['tags']??[])); ?>" placeholder="seo, cucina, ricetta"><div class="form-text">Separati da virgola</div></div>
          </div>
        </div>
        <div class="card mb-4"><div class="card-header"><h5 class="card-title mb-0"><?php echo TranslationManager::t('featured_image'); ?></h5></div><div class="card-body"><div class="mb-3"><label class="form-label">Seleziona Immagine</label><input type="text" class="form-control" id="featured_image" name="featured_image" value="<?php echo htmlspecialchars($article['featured_image']??''); ?>" readonly><button type="button" class="btn btn-outline-primary mt-2" id="selectImageBtn"><i class="fas fa-images"></i> Scegli Immagine</button><button type="button" class="btn btn-outline-danger mt-2" id="removeImageBtn" style="display:<?php echo ($article['featured_image']??'')?'inline-block':'none'; ?>;"><i class="fas fa-times"></i> Rimuovi</button></div><div id="imagePreview" class="text-center" style="display:<?php echo ($article['featured_image']??'')?'block':'none'; ?>;"><img id="previewImg" src="<?php echo htmlspecialchars($article['featured_image']??''); ?>" alt="Preview" class="img-fluid" style="max-height:200px;"></div></div></div>
        <div class="card"><div class="card-body"><div class="d-grid gap-2"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo TranslationManager::t('save'); ?></button><a href="articles.php" class="btn btn-outline-secondary"><?php echo TranslationManager::t('cancel'); ?></a></div></div></div>
      </div>
    </div>
  </form>
</div></div></div></div>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
function initEditors(){ tinymce.init({ selector: '.tinymce-editor', height: 400, menubar: true, plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount', toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image media link | code | help', language: '<?php echo $language; ?>', relative_urls:false, remove_script_host:false, convert_urls:true, branding:false, file_picker_types:'image', file_picker_callback:(cb,value,meta)=>{ if(meta.filetype==='image'){ openMediaLibrary(url=>cb(url,{alt:''})); } } }); }
initEditors();
// Revisions handling
function loadRevisions(){
 fetch('article-revisions.php?article_id=<?php echo (int)$article['id']; ?>').then(r=>r.json()).then(list=>{
  const wrap=document.getElementById('revisionsList'); wrap.innerHTML=''; if(!list.length){ document.getElementById('revEmpty').style.display='block'; return;} document.getElementById('revEmpty').style.display='none'; list.forEach(r=>{ const a=document.createElement('div'); a.className='list-group-item d-flex justify-content-between align-items-center'; a.innerHTML='<span><i class="fas fa-history me-1"></i>'+ (r.title? r.title.substring(0,60):'(senza titolo)') + ' <small class="text-muted">'+r.created_at+'</small></span><button class="btn btn-sm btn-outline-primary" data-rev="'+r.id+'">Ripristina</button>'; wrap.appendChild(a); }); });
}
document.getElementById('reloadRevisions').addEventListener('click',loadRevisions);
document.getElementById('revisionsList').addEventListener('click',e=>{ const btn=e.target.closest('button[data-rev]'); if(!btn) return; if(!confirm('Ripristinare questa revisione?')) return; fetch('article-restore-revision.php?revision_id='+btn.dataset.rev,{method:'POST'}).then(r=>r.json()).then(j=>{ if(j.success){ alert('Revisione ripristinata'); location.reload(); } else { alert('Errore ripristino'); } }); });
document.addEventListener('DOMContentLoaded',loadRevisions);
function openMediaLibrary(cb){ const url=prompt('Inserisci URL dell\'immagine:'); if(url) cb(url); }
const selectBtn=document.getElementById('selectImageBtn'); const removeBtn=document.getElementById('removeImageBtn'); selectBtn.addEventListener('click',()=>{ openMediaLibrary(url=>{ document.getElementById('featured_image').value=url; document.getElementById('previewImg').src=url; document.getElementById('imagePreview').style.display='block'; removeBtn.style.display='inline-block'; });}); removeBtn.addEventListener('click',()=>{ document.getElementById('featured_image').value=''; document.getElementById('imagePreview').style.display='none'; removeBtn.style.display='none'; });
// Slug live validation
document.querySelectorAll('.slug-input').forEach(inp=>{ inp.addEventListener('blur',()=>{ const val=inp.value.trim(); if(!val) return; const lang=inp.dataset.lang; const statusEl=document.querySelector('[data-status-for="'+lang+'"]'); statusEl.textContent='...'; fetch('article-slug-check.php?slug='+encodeURIComponent(val)+'&lang='+encodeURIComponent(lang)+'&exclude_id=<?php echo (int)$article['id']; ?>').then(r=>r.json()).then(j=>{ if(j.available){ statusEl.textContent='OK'; statusEl.classList.remove('text-danger'); statusEl.classList.add('text-success'); } else { statusEl.textContent='IN USO'; statusEl.classList.remove('text-success'); statusEl.classList.add('text-danger'); } }).catch(()=>{ statusEl.textContent='err'; statusEl.classList.add('text-danger'); }); }); });
</script>
<?php include 'includes/footer.php'; ?>
