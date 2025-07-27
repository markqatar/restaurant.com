<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/MenuItemController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'menu_items', 'view')) {
    header('Location: login.php');
    exit;
}

$controller = new MenuItemController($pdo);

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

$pageTitle = translate('menu_items');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><?php echo translate('public_menu'); ?></h2>
                        <p class="text-muted">Gestisci il menu di navigazione del sito pubblico</p>
                    </div>
                    <?php if (has_permission($_SESSION['user_id'], 'menu_items', 'create')): ?>
                        <a href="menu-items-create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <?php echo translate('add_menu_item'); ?>
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
                            <table class="table table-striped" id="menuItemsTable">
                                <thead>
                                    <tr>
                                        <th><?php echo translate('id'); ?></th>
                                        <th><?php echo translate('menu_title'); ?></th>
                                        <th>Link</th>
                                        <th><?php echo translate('parent_menu'); ?></th>
                                        <th>Ordine</th>
                                        <th><?php echo translate('status'); ?></th>
                                        <th><?php echo translate('created_at'); ?></th>
                                        <th><?php echo translate('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['menuItems'] as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($item['icon']): ?>
                                                        <i class="<?php echo htmlspecialchars($item['icon']); ?> me-2"></i>
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                                        <?php if ($item['css_class']): ?>
                                                            <br><small class="text-muted">CSS: <?php echo htmlspecialchars($item['css_class']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($item['page_title']): ?>
                                                    <span class="badge bg-info">Pagina: <?php echo htmlspecialchars($item['page_title']); ?></span>
                                                <?php elseif ($item['article_title']): ?>
                                                    <span class="badge bg-success">Articolo: <?php echo htmlspecialchars($item['article_title']); ?></span>
                                                <?php elseif ($item['url']): ?>
                                                    <span class="badge bg-warning">URL: <?php echo htmlspecialchars(substr($item['url'], 0, 30)) . '...'; ?></span>
                                                    <?php if ($item['target'] === '_blank'): ?>
                                                        <i class="fas fa-external-link-alt ms-1" title="Apre in nuova finestra"></i>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Nessun link</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($item['parent_title']): ?>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($item['parent_title']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Menu principale</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark"><?php echo $item['sort_order']; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $item['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo translate($item['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                            <td>
                                                <?php if (has_permission($_SESSION['user_id'], 'menu_items', 'edit')): ?>
                                                    <a href="menu-items-edit.php?id=<?php echo $item['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> <?php echo translate('edit'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (has_permission($_SESSION['user_id'], 'menu_items', 'delete')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" 
                                                            onclick="confirmDelete(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['title']); ?>')">
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
    $('#menuItemsTable').DataTable({
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
        text: 'Sei sicuro di voler eliminare la voce di menu "' + title + '"? Verranno eliminate anche tutte le voci figlie.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?php echo translate('yes_delete'); ?>',
        cancelButtonText: '<?php echo translate('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'menu-items.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>