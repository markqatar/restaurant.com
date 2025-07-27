<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/SlideshowController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'slideshows', 'view')) {
    header('Location: login.php');
    exit;
}

$controller = new SlideshowController($pdo);

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

$pageTitle = translate('slideshow');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo translate('slideshow'); ?></h2>
                    <?php if (has_permission($_SESSION['user_id'], 'slideshows', 'create')): ?>
                        <a href="slideshows-create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo translate('add_slide'); ?>
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
                            <table class="table table-striped" id="slideshowTable">
                                <thead>
                                    <tr>
                                        <th><?php echo translate('id'); ?></th>
                                        <th>Anteprima</th>
                                        <th><?php echo translate('slide_title'); ?></th>
                                        <th>Link</th>
                                        <th>Ordine</th>
                                        <th><?php echo translate('status'); ?></th>
                                        <th><?php echo translate('created_at'); ?></th>
                                        <th><?php echo translate('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['slides'] as $slide): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($slide['id']); ?></td>
                                            <td>
                                                <?php if ($slide['image']): ?>
                                                    <img src="<?php echo htmlspecialchars($slide['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($slide['title']); ?>" 
                                                         class="img-thumbnail" style="max-width: 80px; max-height: 60px;">
                                                <?php else: ?>
                                                    <span class="text-muted">Nessuna immagine</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($slide['title']); ?></strong>
                                                <?php if ($slide['caption']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($slide['caption'], 0, 50)) . (strlen($slide['caption']) > 50 ? '...' : ''); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($slide['page_title']): ?>
                                                    <small class="badge bg-info">Pagina: <?php echo htmlspecialchars($slide['page_title']); ?></small>
                                                <?php elseif ($slide['article_title']): ?>
                                                    <small class="badge bg-success">Articolo: <?php echo htmlspecialchars($slide['article_title']); ?></small>
                                                <?php elseif ($slide['link_url']): ?>
                                                    <small class="badge bg-warning">URL: <?php echo htmlspecialchars(substr($slide['link_url'], 0, 30)) . '...'; ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Nessun link</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo $slide['sort_order']; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $slide['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo translate($slide['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($slide['created_at'])); ?></td>
                                            <td>
                                                <?php if (has_permission($_SESSION['user_id'], 'slideshows', 'edit')): ?>
                                                    <a href="slideshows-edit.php?id=<?php echo $slide['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> <?php echo translate('edit'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (has_permission($_SESSION['user_id'], 'slideshows', 'delete')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" 
                                                            onclick="confirmDelete(<?php echo $slide['id']; ?>, '<?php echo htmlspecialchars($slide['title']); ?>')">
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
    $('#slideshowTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/<?php echo get_current_language(); ?>.json"
        },
        "order": [[ 4, "asc" ]], // Order by sort_order
        "pageLength": 25
    });
});

function confirmDelete(id, title) {
    Swal.fire({
        title: '<?php echo translate('confirm_delete'); ?>',
        text: '<?php echo translate('Are you sure you want to delete the slide'); ?> "' + title + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?php echo translate('yes_delete'); ?>',
        cancelButtonText: '<?php echo translate('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'slideshows.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>