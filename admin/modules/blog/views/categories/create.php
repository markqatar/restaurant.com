<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/CategoryController.php';
require_once '../includes/functions.php';
if(!isset($_SESSION['user_id'])||!has_permission($_SESSION['user_id'],'categories','create')){ header('Location: /admin/login'); exit; }
$controller=new CategoryController($pdo); $data=$controller->create(); $languages=$data['languages']; $activeLanguages=$data['activeLanguages']; $pageTitle=TranslationManager::t('add_category'); include 'includes/header.php';
?>
<div class="container-fluid"><div class="row"><div class="col-md-12"><div class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-4"><h2><?php echo TranslationManager::t('add_category'); ?></h2><a href="categories.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> <?php echo TranslationManager::t('back'); ?></a></div>
  <?php if(isset($data['error'])): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo htmlspecialchars($data['error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
  <form method="POST" action="">
    <div class="row">
      <div class="col-md-8">
        <div class="card mb-4"><div class="card-header"><h5 class="card-title mb-0"><?php echo TranslationManager::t('category_translations'); ?></h5></div>
          <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
              <?php $ti=0; foreach($activeLanguages as $lang): $code=$lang['code']; ?>
                <li class="nav-item" role="presentation"><button class="nav-link <?php echo $ti===0?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#lang-<?php echo $code; ?>" type="button" role="tab"><?php echo strtoupper($code); ?></button></li>
              <?php $ti++; endforeach; ?>
            </ul>
            <div class="tab-content border border-top-0 p-3">
              <?php $ti=0; foreach($activeLanguages as $lang): $code=$lang['code']; ?>
                <div class="tab-pane fade <?php echo $ti===0?'show active':''; ?>" id="lang-<?php echo $code; ?>" role="tabpanel">
                  <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('name'); ?> (<?php echo strtoupper($code); ?>)<?php echo $code===get_default_public_language_from_db()?' *':''; ?></label><input name="translations[<?php echo $code; ?>][name]" type="text" class="form-control" value="<?php echo htmlspecialchars($_POST['translations'][$code]['name']??''); ?>" ></div>
                  <div class="mb-3"><label class="form-label">Slug</label><input name="translations[<?php echo $code; ?>][slug]" type="text" class="form-control" value="<?php echo htmlspecialchars($_POST['translations'][$code]['slug']??''); ?>" ></div>
                  <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('description'); ?></label><textarea name="translations[<?php echo $code; ?>][description]" class="form-control" rows="3"><?php echo htmlspecialchars($_POST['translations'][$code]['description']??''); ?></textarea></div>
                  <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('meta_title'); ?></label><input name="translations[<?php echo $code; ?>][meta_title]" type="text" class="form-control" value="<?php echo htmlspecialchars($_POST['translations'][$code]['meta_title']??''); ?>"></div>
                  <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('meta_description'); ?></label><textarea name="translations[<?php echo $code; ?>][meta_description]" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['translations'][$code]['meta_description']??''); ?></textarea></div>
                </div>
              <?php $ti++; endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-4"><div class="card-header"><h5 class="card-title mb-0"><?php echo TranslationManager::t('category_settings'); ?></h5></div>
          <div class="card-body">
            <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('parent_category'); ?></label><select name="parent_id" class="form-select"><option value="">--</option><?php foreach($data['parentCategories'] as $pc): ?><option value="<?php echo $pc['id']; ?>"><?php echo htmlspecialchars($pc['name']); ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label"><?php echo TranslationManager::t('sort_order'); ?></label><input type="number" class="form-control" name="sort_order" value="<?php echo htmlspecialchars($_POST['sort_order']??0); ?>"></div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked><label class="form-check-label" for="is_active"><?php echo TranslationManager::t('active'); ?></label></div>
          </div>
        </div>
        <div class="card"><div class="card-body"><div class="d-grid gap-2">
          <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> <?php echo TranslationManager::t('save'); ?></button>
          <a href="categories.php" class="btn btn-outline-secondary"><?php echo TranslationManager::t('cancel'); ?></a>
        </div></div></div>
      </div>
    </div>
  </form>
</div></div></div></div>
<?php include 'includes/footer.php'; ?>
