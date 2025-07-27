<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/SlideshowController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'slideshows', 'edit')) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: slideshows.php?error=' . urlencode(translate('invalid_request')));
    exit;
}

$controller = new SlideshowController($pdo);
$data = $controller->edit($id);

$pageTitle = translate('edit_slide');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo translate('edit_slide'); ?></h2>
                    <a href="slideshows.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> <?php echo translate('back'); ?>
                    </a>
                </div>

                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($data['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Main Content -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informazioni Slide</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label"><?php echo translate('slide_title'); ?> *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($_POST['title'] ?? $data['slide']['title']); ?>" 
                                               required maxlength="255">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="image" class="form-label"><?php echo translate('slide_image'); ?> *</label>
                                        <input type="text" class="form-control" id="image" name="image" 
                                               value="<?php echo htmlspecialchars($_POST['image'] ?? $data['slide']['image']); ?>" 
                                               required readonly>
                                        <button type="button" class="btn btn-outline-primary mt-2" id="selectImageBtn">
                                            <i class="fas fa-images"></i> Scegli Immagine
                                        </button>
                                        <button type="button" class="btn btn-outline-danger mt-2" id="removeImageBtn">
                                            <i class="fas fa-times"></i> Rimuovi
                                        </button>
                                        <div class="form-text">Dimensioni consigliate: 1920x800px per il miglior risultato</div>
                                    </div>
                                    
                                    <div id="imagePreview" class="mb-3 text-center">
                                        <img id="previewImg" src="<?php echo htmlspecialchars($data['slide']['image'] ?? ''); ?>" alt="Preview" class="img-fluid border" style="max-height: 200px;">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="caption" class="form-label"><?php echo translate('slide_caption'); ?></label>
                                        <textarea class="form-control" id="caption" name="caption" rows="3" maxlength="500">
                                            <?php echo htmlspecialchars($_POST['caption'] ?? $data['slide']['caption']); ?>
                                        </textarea>
                                        <div class="form-text">Testo che appare sopra l'immagine della slide</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Link Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Impostazioni Link</h5>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    $currentLinkType = 'none';
                                    if ($data['slide']['page_id']) $currentLinkType = 'page';
                                    elseif ($data['slide']['article_id']) $currentLinkType = 'article';
                                    elseif ($data['slide']['link_url']) $currentLinkType = 'url';
                                    ?>
                                    <div class="mb-3">
                                        <label class="form-label">Tipo di Link</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="link_type" id="link_none" value="none" <?php echo $currentLinkType === 'none' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="link_none">
                                                Nessun link
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="link_type" id="link_page" value="page" <?php echo $currentLinkType === 'page' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="link_page">
                                                Link a pagina esistente
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="link_type" id="link_article" value="article" <?php echo $currentLinkType === 'article' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="link_article">
                                                Link ad articolo esistente
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="link_type" id="link_url" value="url" <?php echo $currentLinkType === 'url' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="link_url">
                                                Link esterno (URL)
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3" id="page_select" style="display:<?php echo $currentLinkType === 'page' ? 'block' : 'none'; ?>;">
                                        <label for="page_id" class="form-label">Seleziona Pagina</label>
                                        <select class="form-select" id="page_id" name="page_id">
                                            <option value="">Seleziona una pagina...</option>
                                            <?php if (isset($data['pages'])): ?>
                                                <?php foreach ($data['pages'] as $page): ?>
                                                    <option value="<?php echo $page['id']; ?>" <?php echo ($data['slide']['page_id'] == $page['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($page['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3" id="article_select" style="display:<?php echo $currentLinkType === 'article' ? 'block' : 'none'; ?>;">
                                        <label for="article_id" class="form-label">Seleziona Articolo</label>
                                        <select class="form-select" id="article_id" name="article_id">
                                            <option value="">Seleziona un articolo...</option>
                                            <?php if (isset($data['articles'])): ?>
                                                <?php foreach ($data['articles'] as $article): ?>
                                                    <option value="<?php echo $article['id']; ?>" <?php echo ($data['slide']['article_id'] == $article['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($article['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3" id="url_input" style="display:<?php echo $currentLinkType === 'url' ? 'block' : 'none'; ?>;">
                                        <label for="link_url" class="form-label"><?php echo translate('slide_link_url'); ?></label>
                                        <input type="url" class="form-control" id="link_url" name="link_url" 
                                               value="<?php echo htmlspecialchars($_POST['link_url'] ?? $data['slide']['link_url']); ?>" 
                                               placeholder="https://esempio.com">
                                    </div>
                                    
                                    <div class="mb-3" id="link_text_input" style="display:<?php echo $currentLinkType !== 'none' ? 'block' : 'none'; ?>;">
                                        <label for="link_text" class="form-label"><?php echo translate('slide_link_text'); ?></label>
                                        <input type="text" class="form-control" id="link_text" name="link_text" 
                                               value="<?php echo htmlspecialchars($_POST['link_text'] ?? $data['slide']['link_text']); ?>" 
                                               maxlength="100" placeholder="Scopri di più">
                                        <div class="form-text">Testo del pulsante (opzionale)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Slide Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Impostazioni Slide</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label"><?php echo translate('status'); ?></label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="active" <?php echo (($_POST['status'] ?? $data['slide']['status']) === 'active') ? 'selected' : ''; ?>>
                                                <?php echo translate('active'); ?>
                                            </option>
                                            <option value="inactive" <?php echo (($_POST['status'] ?? $data['slide']['status']) === 'inactive') ? 'selected' : ''; ?>>
                                                <?php echo translate('inactive'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label"><?php echo translate('slide_sort_order'); ?></label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                               value="<?php echo htmlspecialchars($_POST['sort_order'] ?? $data['slide']['sort_order']); ?>" 
                                               min="0">
                                        <div class="form-text">0 = prima slide nello slideshow</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo translate('update'); ?>
                                        </button>
                                        <a href="slideshows.php" class="btn btn-outline-secondary">
                                            <?php echo translate('cancel'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Link type toggle functionality
document.querySelectorAll('input[name="link_type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        // Hide all link inputs
        document.getElementById('page_select').style.display = 'none';
        document.getElementById('article_select').style.display = 'none';
        document.getElementById('url_input').style.display = 'none';
        document.getElementById('link_text_input').style.display = 'none';
        
        // Show relevant inputs
        switch(this.value) {
            case 'page':
                document.getElementById('page_select').style.display = 'block';
                document.getElementById('link_text_input').style.display = 'block';
                break;
            case 'article':
                document.getElementById('article_select').style.display = 'block';
                document.getElementById('link_text_input').style.display = 'block';
                break;
            case 'url':
                document.getElementById('url_input').style.display = 'block';
                document.getElementById('link_text_input').style.display = 'block';
                break;
        }
    });
});

// Image selection functionality
document.getElementById('selectImageBtn').addEventListener('click', function() {
    openMediaLibrary(function(imageUrl) {
        document.getElementById('image').value = imageUrl;
        document.getElementById('previewImg').src = imageUrl;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('removeImageBtn').style.display = 'inline-block';
    });
});

document.getElementById('removeImageBtn').addEventListener('click', function() {
    document.getElementById('image').value = '';
    document.getElementById('previewImg').src = '';
    document.getElementById('imagePreview').style.display = 'none';
    this.style.display = 'none';
});

// Simple media library opener (to be enhanced with proper media library)
function openMediaLibrary(callback) {
    const imageUrl = prompt('Inserisci URL dell\'immagine:');
    if (imageUrl) {
        callback(imageUrl);
    }
}

// Initialize image preview
document.addEventListener('DOMContentLoaded', function() {
    const image = document.getElementById('image').value;
    if (image) {
        document.getElementById('previewImg').src = image;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('removeImageBtn').style.display = 'inline-block';
    } else {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('removeImageBtn').style.display = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>