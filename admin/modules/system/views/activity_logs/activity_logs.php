<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <h1 class="h2"><i class="fas fa-history me-2"></i><?php echo TranslationManager::t('system.activity_logs'); ?></h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-bordered" id="logsTable" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo TranslationManager::t('user'); ?></th>
                    <th><?php echo TranslationManager::t('module'); ?></th>
                    <th><?php echo TranslationManager::t('table'); ?></th>
                    <th><?php echo TranslationManager::t('action'); ?></th>
                    <th><?php echo TranslationManager::t('record_id'); ?></th>
                    <th><?php echo TranslationManager::t('date'); ?></th>
                    <th><?php echo TranslationManager::t('actions'); ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo TranslationManager::t('log.details'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="logOldData" class="bg-light p-3 border rounded mb-3"></pre>
                <pre id="logNewData" class="bg-light p-3 border rounded"></pre>
            </div>
        </div>
    </div>
</div>
<script>
    const ACTIVITY_LOGS_VARS = {
        datatableUrl: '<?php echo module_admin_url("system/systemlogs/datatable"); ?>',
        restoreUrl: '<?php echo module_admin_url("system/systemlogs/restore"); ?>',
        logDetailsUrl: '<?php echo module_admin_url("system/systemlogs/getLogDetails"); ?>',
        csrfToken: '<?php echo csrf_token_field(); ?>',
        translations: {
            confirmTitle: '<?php echo TranslationManager::t("restore.confirm"); ?>',
            confirmText: '<?php echo TranslationManager::t("restore.description"); ?>',
            yesRestore: '<?php echo TranslationManager::t("yes_restore"); ?>',
            cancel: '<?php echo TranslationManager::t("cancel"); ?>',
            success: '<?php echo TranslationManager::t("success"); ?>',
            error: '<?php echo TranslationManager::t("error"); ?>'
        }
    };
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/system/views/activity_logs/js/activity_logs.js',
];
?>
<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>