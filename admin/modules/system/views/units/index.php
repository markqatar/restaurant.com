<?php require_once get_setting('base_path') . 'admin/layouts/header.php'; ?>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3><?php echo TranslationManager::t('units.manage'); ?></h3>
        <button class="btn btn-success" id="addUnit"><i class="fa fa-plus"></i> <?php echo TranslationManager::t('add'); ?></button>
    </div>
    <div class="card-body">
        <table id="unitsTable" class="table table-bordered table-striped" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo TranslationManager::t('name'); ?></th>
                    <th><?php echo TranslationManager::t('short_name'); ?></th>
                    <th><?php echo TranslationManager::t('factor'); ?></th>
                    <th><?php echo TranslationManager::t('type'); ?></th>
                    <th><?php echo TranslationManager::t('status'); ?></th>
                    <th><?php echo TranslationManager::t('actions'); ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="unitModal">
    <div class="modal-dialog modal-lg">
        <form id="unitForm">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"><?php echo TranslationManager::t('unit_details'); ?></h5></div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="unitId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><?php echo TranslationManager::t('name'); ?></label>
                            <input type="text" name="name" id="unitName" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?php echo TranslationManager::t('short_name'); ?></label>
                            <input type="text" name="short_name" id="unitShortName" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?php echo TranslationManager::t('factor'); ?></label>
                            <input type="number" name="factor" id="unitFactor" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?php echo TranslationManager::t('type'); ?></label>
                            <select name="type" id="unitType" class="form-control">
                                <option value="volume">Volume</option>
                                <option value="weight">Weight</option>
                                <option value="piece">Piece</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label><input type="checkbox" name="is_active" id="unitActive"> <?php echo TranslationManager::t('active'); ?></label>
                        </div>
                    </div>
                    <h5><?php echo TranslationManager::t('translations'); ?></h5>
                    <?php foreach(['it'=>'Italiano','en'=>'English','ar'=>'العربية'] as $lang=>$label): ?>
                    <div class="mb-3">
                        <label><?php echo $label; ?></label>
                        <input type="text" name="translations[<?php echo $lang; ?>]" class="form-control translation-field" data-lang="<?php echo $lang; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo TranslationManager::t('save'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const SUPPLIER_UNITS_VARS = {
    urls: {
        datatable: "<?php echo get_setting('site_url'); ?>/admin/system/units/datatable",
        list: "<?php echo get_setting('site_url'); ?>/admin/system/units/list",
        store: "<?php echo get_setting('site_url'); ?>/admin/system/units/store",
        update: "<?php echo get_setting('site_url'); ?>/admin/system/units/update/:id",
        get: "<?php echo get_setting('site_url'); ?>/admin/system/units/get/:id",
        delete: "<?php echo get_setting('site_url'); ?>/admin/system/units/delete/:id"
    },
    csrfToken: "<?php echo generate_csrf_token(); ?>",
    translations: {
        addTitle: "<?php echo TranslationManager::t('add_unit'); ?>",
        editTitle: "<?php echo TranslationManager::t('edit_unit'); ?>",
        confirmDelete: "<?php echo TranslationManager::t('confirm_delete'); ?>",
        none: "<?php echo TranslationManager::t('none'); ?>"
    }
};
</script>

<?php
$pageScripts = [
    get_setting('site_url', 'http://localhost') . '/admin/modules/system/views/units/js/index.js',
];
?>

<?php require_once get_setting('base_path') . 'admin/layouts/footer.php'; ?>