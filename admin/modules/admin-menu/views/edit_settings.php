<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-cogs me-2"></i><?php echo TranslationManager::t('system.configuration'); ?>
    </h1>
</div>

<div class="card shadow">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="global-tab" data-bs-toggle="tab" href="#global" role="tab">
                    <?php echo TranslationManager::t('system.global_settings_tab'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="languages-tab" data-bs-toggle="tab" href="#languages" role="tab">
                    <?php echo TranslationManager::t('system.languages_tab'); ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body tab-content" id="settingsTabsContent">
        <!-- GLOBAL SETTINGS -->
        <div class="tab-pane fade show active" id="global" role="tabpanel">
            <form method="POST" enctype="multipart/form-data" action="<?php echo get_setting('site_url'); ?>/admin/system/SystemConfig/update">
                <?php echo csrf_token_field(); ?>
                
                <div class="mb-3">
                    <label for="site_name" class="form-label"><?php echo TranslationManager::t('system.site_name'); ?> *</label>
                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="site_url" class="form-label"><?php echo TranslationManager::t('system.site_url'); ?> *</label>
                    <input type="url" class="form-control" id="site_url" name="site_url" value="<?php echo htmlspecialchars($settings['site_url'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label"><?php echo TranslationManager::t('system.logo'); ?></label>
                    <?php if (!empty($settings['logo_path'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo get_setting('site_url') . '/public/assets/images/logo/' . $settings['logo_path']; ?>" alt="Logo" class="img-thumbnail" style="max-width:150px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="currency" class="form-label"><?php echo TranslationManager::t('system.currency'); ?> *</label>
                    <input type="text" class="form-control" id="currency" name="currency" value="<?php echo htmlspecialchars($settings['currency'] ?? 'USD'); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="timezone" class="form-label"><?php echo TranslationManager::t('system.timezone'); ?> *</label>
                    <select class="form-select" id="timezone" name="timezone" required>
                        <?php foreach (DateTimeZone::listIdentifiers() as $tz): ?>
                            <option value="<?php echo $tz; ?>" <?php echo ($settings['timezone'] ?? '') === $tz ? 'selected' : ''; ?>>
                                <?php echo $tz; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="date_format_admin" class="form-label"><?php echo TranslationManager::t('system.date_format_admin'); ?></label>
                    <input type="text" class="form-control" id="date_format_admin" name="date_format_admin" value="<?php echo htmlspecialchars($settings['date_format_admin'] ?? 'd/m/Y H:i'); ?>">
                </div>

                <div class="mb-3">
                    <label for="date_format_public" class="form-label"><?php echo TranslationManager::t('system.date_format_public'); ?></label>
                    <input type="text" class="form-control" id="date_format_public" name="date_format_public" value="<?php echo htmlspecialchars($settings['date_format_public'] ?? 'm/d/Y h:i A'); ?>">
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i><?php echo TranslationManager::t('btn.save'); ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- LANGUAGES -->
        <div class="tab-pane fade" id="languages" role="tabpanel">
            <form method="POST" action="<?php echo get_setting('site_url'); ?>/admin/system/SystemConfig/updateLanguages">
                <?php echo csrf_token_field(); ?>

                <div class="d-flex justify-content-between mb-3">
                    <h5><?php echo TranslationManager::t('system.languages_tab'); ?></h5>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                        <i class="fas fa-plus"></i> <?php echo TranslationManager::t('system.add_language'); ?>
                    </button>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo TranslationManager::t('system.code'); ?></th>
                            <th><?php echo TranslationManager::t('system.name'); ?></th>
                            <th><?php echo TranslationManager::t('system.direction'); ?></th>
                            <th><?php echo TranslationManager::t('system.admin'); ?></th>
                            <th><?php echo TranslationManager::t('system.public'); ?></th>
                            <th><?php echo TranslationManager::t('system.actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($languages as $lang): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($lang['code']); ?></td>
                                <td><?php echo htmlspecialchars($lang['name']); ?></td>
                                <td><?php echo htmlspecialchars($lang['direction']); ?></td>
                                <td><input type="checkbox" name="active_admin[]" value="<?php echo $lang['code']; ?>" <?php echo $lang['is_active_admin'] ? 'checked' : ''; ?>></td>
                                <td><input type="checkbox" name="active_public[]" value="<?php echo $lang['code']; ?>" <?php echo $lang['is_active_public'] ? 'checked' : ''; ?>></td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-language" data-code="<?php echo $lang['code']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo TranslationManager::t('btn.save'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Language -->
<div class="modal fade" id="addLanguageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="<?php echo get_setting('site_url'); ?>/admin/system/SystemConfig/addLanguage">
        <?php echo csrf_token_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title"><?php echo TranslationManager::t('system.add_language'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label><?php echo TranslationManager::t('system.code'); ?></label>
            <input type="text" name="code" class="form-control" required>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('system.name'); ?></label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('system.direction'); ?></label>
            <select name="direction" class="form-select">
              <option value="LTR">LTR</option>
              <option value="RTL">RTL</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><?php echo TranslationManager::t('btn.save'); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-language');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            Swal.fire({
                title: '<?php echo TranslationManager::t('system.delete_language'); ?>',
                text: code,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<?php echo TranslationManager::t('delete'); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo get_setting("site_url"); ?>/admin/system/SystemConfig/deleteLanguage/' + code;
                }
            });
        });
    });
});
</script>
<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>