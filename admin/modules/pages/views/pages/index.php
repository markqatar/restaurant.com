<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/PageController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'pages', 'view')) {
    header('Location: login.php');
    exit;
}

$controller = new PageController($pdo);

// Handle different actions
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'delete':
        if ($id) {
            $controller->delete($id);
        }
        break;
    case 'index':
    default:
        $data = $controller->index();
        break;
}

$pageTitle = translate('pages');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo translate('pages'); ?></h2>
                    <?php if (has_permission($_SESSION['user_id'], 'pages', 'create')): ?>
                        <a href="pages-create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo translate('add_page'); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="pagesTable">
                                <thead>
                                    <tr>
                                        <th><?php echo translate('id'); ?></th>
                                        <th><?php echo translate('page_title'); ?></th>
                                        <th><?php echo translate('page_status'); ?></th>
                                        <th><?php echo translate('author'); ?></th>
                                        <th><?php echo translate('created_at'); ?></th>
                                        <th><?php echo translate('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['pages'] as $page): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($page['id']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($page['title']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($page['slug']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $page['status'] === 'published' ? 'bg-success' : ($page['status'] === 'draft' ? 'bg-warning' : 'bg-secondary'); ?>">
                                                    <?php echo translate($page['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($page['author_name'] ?? 'Unknown'); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($page['created_at'])); ?></td>
                                            <td>
                                                <?php if (has_permission($_SESSION['user_id'], 'pages', 'edit')): ?>
                                                    <a href="pages-edit.php?id=<?php echo $page['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> <?php echo translate('edit'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (has_permission($_SESSION['user_id'], 'pages', 'delete')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" 
                                                            onclick="confirmDelete(<?php echo $page['id']; ?>, '<?php echo htmlspecialchars($page['title']); ?>')">
                                                        <i class="fas fa-trash"></i> <?php echo translate('delete'); ?>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#pagesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/<?php echo get_current_language(); ?>.json"
        },
        "order": [[ 0, "desc" ]],
        "pageLength": 25
    });
});

function confirmDelete(id, title) {
    Swal.fire({
        title: '<?php echo translate('confirm_delete'); ?>',
        text: '<?php echo translate('Are you sure you want to delete the page'); ?> "' + title + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?php echo translate('yes_delete'); ?>',
        cancelButtonText: '<?php echo translate('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'pages.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>