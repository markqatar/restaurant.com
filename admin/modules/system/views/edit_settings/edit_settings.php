<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2">
        <i class="fas fa-cogs me-2"></i><?php echo TranslationManager::t('configuration'); ?>
    </h1>
</div>

<div class="card shadow">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="global-tab" data-bs-toggle="tab" href="#global" role="tab">
                    <?php echo TranslationManager::t('global_settings_tab'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="languages-tab" data-bs-toggle="tab" href="#languages" role="tab">
                    <?php echo TranslationManager::t('languages_tab'); ?>
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
                    <label for="site_name" class="form-label"><?php echo TranslationManager::t('site_name'); ?> *</label>
                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="site_url" class="form-label"><?php echo TranslationManager::t('site_url'); ?> *</label>
                    <input type="url" class="form-control" id="site_url" name="site_url" value="<?php echo htmlspecialchars($settings['site_url'] ?? ''); ?>" required>
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" id="website_enabled" name="website_enabled" value="1"
                        <?php echo !empty($settings['website_enabled']) && $settings['website_enabled'] == 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="website_enabled">
                        <?php echo TranslationManager::t('website_enabled'); ?>
                    </label>
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label"><?php echo TranslationManager::t('logo'); ?></label>
                    <?php if (!empty($settings['logo_path'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo get_setting('site_url') . '/public/assets/images/logo/' . $settings['logo_path']; ?>" alt="Logo" class="img-thumbnail" style="max-width:150px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                </div>

                <?php $currenciesRaw = $settings['currencies'] ?? ($settings['currency'] ?? 'QAR');
                      $currencyList = array_values(array_filter(array_map('trim', preg_split('/[,\n]+/',$currenciesRaw))));
                      if(empty($currencyList)) $currencyList=['QAR'];
                      // Ensure default currency first (QAR if not set)
                      $defaultCurrency = $settings['currency'] ?? 'QAR';
                      if(!in_array($defaultCurrency,$currencyList)) array_unshift($currencyList,$defaultCurrency);
                ?>
                <div class="mb-3">
                    <label for="currencies" class="form-label"><?php echo TranslationManager::t('currencies'); ?> *</label>
                    <textarea class="form-control" id="currencies" name="currencies" rows="2" placeholder="QAR,EUR,USD"><?php echo htmlspecialchars(implode(',', $currencyList)); ?></textarea>
                    <small class="text-muted"><?php echo TranslationManager::t('currencies_help'); ?></small>
                </div>
                <div class="mb-3">
                    <label for="currency" class="form-label"><?php echo TranslationManager::t('currency'); ?> (<?php echo TranslationManager::t('optional'); ?>)</label>
                    <select class="form-select" id="currency" name="currency">
                        <?php foreach($currencyList as $c): ?>
                        <option value="<?php echo $c; ?>" <?php echo $c===$defaultCurrency? 'selected':''; ?>><?php echo $c; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="timezone" class="form-label"><?php echo TranslationManager::t('timezone'); ?> *</label>
                    <select class="form-select" id="timezone" name="timezone" required>
                        <?php foreach (DateTimeZone::listIdentifiers() as $tz): ?>
                            <option value="<?php echo $tz; ?>" <?php echo ($settings['timezone'] ?? '') === $tz ? 'selected' : ''; ?>>
                                <?php echo $tz; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="date_format_admin" class="form-label"><?php echo TranslationManager::t('date_format_admin'); ?></label>
                    <input type="text" class="form-control" id="date_format_admin" name="date_format_admin" value="<?php echo htmlspecialchars($settings['date_format_admin'] ?? 'd/m/Y H:i'); ?>">
                </div>

                <div class="mb-3">
                    <label for="date_format_public" class="form-label"><?php echo TranslationManager::t('date_format_public'); ?></label>
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
                    <h5><?php echo TranslationManager::t('languages_tab'); ?></h5>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                        <i class="fas fa-plus"></i> <?php echo TranslationManager::t('add_language'); ?>
                    </button>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo TranslationManager::t('code'); ?></th>
                            <th><?php echo TranslationManager::t('name'); ?></th>
                            <th><?php echo TranslationManager::t('direction'); ?></th>
                            <th><?php echo TranslationManager::t('admin'); ?></th>
                            <th><?php echo TranslationManager::t('public'); ?></th>
                            <th><?php echo TranslationManager::t('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($systemLanguages as $lang): ?>
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
          <h5 class="modal-title"><?php echo TranslationManager::t('add_language'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label><?php echo TranslationManager::t('code'); ?></label>
            <input type="text" name="code" class="form-control" required>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('name'); ?></label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label><?php echo TranslationManager::t('direction'); ?></label>
            <select name="direction" class="form-select">
              <option value="LTR">LTR</option>
              <option value="RTL">RTL</option>
            </select>
          </div>
          <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" id="is_active_admin" name="is_active_admin" value="1">
            <label class="form-check-label" for="is_active_admin">
              <?php echo TranslationManager::t('active_for_admin'); ?>
            </label>
          </div>
          <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" id="is_active_public" name="is_active_public" value="1">
            <label class="form-check-label" for="is_active_public">
              <?php echo TranslationManager::t('active_for_public'); ?>
            </label>
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
    const LANGUAGE_CONFIG_VARS = {
        deleteUrlBase: '<?php echo get_setting("site_url"); ?>/admin/system/SystemConfig/deleteLanguage/',
        translations: {
            deleteLanguageTitle: '<?php echo TranslationManager::t("delete_language"); ?>',
            deleteButton: '<?php echo TranslationManager::t("delete"); ?>'
        }
    };
</script>
<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/system/views/edit_settings/js/edit_settings.js',
];
?>

<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>