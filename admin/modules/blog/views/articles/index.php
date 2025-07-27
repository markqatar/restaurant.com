<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/ArticleController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'articles', 'view')) {
    header('Location: login.php');
    exit;
}

$controller = new ArticleController($pdo);

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

$pageTitle = translate('articles');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><?php echo translate('articles'); ?></h2>
                        <p class="text-muted">Gestisci gli articoli del blog</p>
                    </div>
                    <div>
                        <?php if (has_permission($_SESSION['user_id'], 'categories', 'view')): ?>
                            <a href="categories.php" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-tags"></i> <?php echo translate('categories'); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (has_permission($_SESSION['user_id'], 'articles', 'create')): ?>
                            <a href="articles-create.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <?php echo translate('add_article'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
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
                            <table class="table table-striped" id="articlesTable">
                                <thead>
                                    <tr>
                                        <th><?php echo translate('id'); ?></th>
                                        <th>Anteprima</th>
                                        <th><?php echo translate('article_title'); ?></th>
                                        <th><?php echo translate('category'); ?></th>
                                        <th><?php echo translate('author'); ?></th>
                                        <th><?php echo translate('article_status'); ?></th>
                                        <th>Visualizzazioni</th>
                                        <th><?php echo translate('published_at'); ?></th>
                                        <th><?php echo translate('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['articles'] as $article): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($article['id']); ?></td>
                                            <td>
                                                <?php if ($article['featured_image']): ?>
                                                    <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($article['title']); ?>" 
                                                         class="img-thumbnail" style="max-width: 60px; max-height: 40px;">
                                                <?php else: ?>
                                                    <span class="text-muted"><i class="fas fa-image"></i></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                                                    <?php if ($article['is_featured']): ?>
                                                        <span class="badge bg-warning text-dark ms-1">Featured</span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted"><?php echo htmlspecialchars($article['slug']); ?></small>
                                                <?php if ($article['excerpt']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($article['excerpt'], 0, 80)) . '...'; ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($article['category_name']): ?>
                                                    <span class="badge bg-info"><?php echo htmlspecialchars($article['category_name']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Nessuna categoria</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($article['author_name'] ?? 'Sconosciuto'); ?></td>
                                            <td>
                                                <span class="badge <?php echo $article['status'] === 'published' ? 'bg-success' : ($article['status'] === 'draft' ? 'bg-warning' : 'bg-secondary'); ?>">
                                                    <?php echo translate($article['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark"><?php echo number_format($article['views']); ?></span>
                                            </td>
                                            <td>
                                                <?php if ($article['published_at']): ?>
                                                    <?php echo date('d/m/Y', strtotime($article['published_at'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Non pubblicato</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (has_permission($_SESSION['user_id'], 'articles', 'edit')): ?>
                                                    <a href="articles-edit.php?id=<?php echo $article['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> <?php echo translate('edit'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (has_permission($_SESSION['user_id'], 'articles', 'delete')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" 
                                                            onclick="confirmDelete(<?php echo $article['id']; ?>, '<?php echo htmlspecialchars($article['title']); ?>')">
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
    $('#articlesTable').DataTable({
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
        text: 'Sei sicuro di voler eliminare l\'articolo "' + title + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?php echo translate('yes_delete'); ?>',
        cancelButtonText: '<?php echo translate('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'articles.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>