<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/DeliveryAreaController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !hasPermission('create_delivery_areas')) {
    header('Location: login.php');
    exit;
}

$controller = new DeliveryAreaController($pdo);
$data = $controller->create();

$pageTitle = translate('add_delivery_area');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo translate('add_delivery_area'); ?></h2>
                    <a href="delivery-areas.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> <?php echo translate('back'); ?>
                    </a>
                </div>

                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($data['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="area_name" class="form-label"><?php echo translate('area_name'); ?> *</label>
                                        <input type="text" class="form-control" id="area_name" name="area_name" 
                                               value="<?php echo htmlspecialchars($_POST['area_name'] ?? ''); ?>" 
                                               required maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shop_id" class="form-label"><?php echo translate('branch'); ?> *</label>
                                        <select class="form-select" id="shop_id" name="shop_id" required>
                                            <option value=""><?php echo translate('select_branch'); ?></option>
                                            <?php foreach ($data['branches'] as $branch): ?>
                                                <option value="<?php echo $branch['id']; ?>" 
                                                        <?php echo (isset($_POST['shop_id']) && $_POST['shop_id'] == $branch['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($branch['branch_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" onclick="history.back()">
                                    <?php echo translate('cancel'); ?>
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo translate('save'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#shop_id').select2({
        placeholder: '<?php echo translate('select_branch'); ?>',
        allowClear: true
    });
});
</script>

<?php include 'includes/footer.php'; ?>