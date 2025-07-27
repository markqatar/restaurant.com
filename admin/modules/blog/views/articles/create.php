<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/ArticleController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'articles', 'create')) {
    header('Location: login.php');
    exit;
}

$controller = new ArticleController($pdo);
$data = $controller->create();

$pageTitle = translate('add_article');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo translate('add_article'); ?></h2>
                    <a href="articles.php" class="btn btn-secondary">
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
                                    <h5 class="card-title mb-0"><?php echo translate('article_content'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label"><?php echo translate('article_title'); ?> *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                                               required maxlength="255">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label"><?php echo translate('article_content'); ?> *</label>
                                        <textarea class="form-control tinymce-editor" id="content" name="content" rows="15">
                                            <?php echo htmlspecialchars($_POST['content'] ?? ''); ?>
                                        </textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label"><?php echo translate('article_excerpt'); ?></label>
                                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" maxlength="500">
                                            <?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?>
                                        </textarea>
                                        <div class="form-text">Riassunto dell'articolo che appare nelle liste e condivisioni social</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?php echo translate('seo_settings'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label"><?php echo translate('meta_title'); ?></label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="<?php echo htmlspecialchars($_POST['meta_title'] ?? ''); ?>" 
                                               maxlength="255">
                                        <div class="form-text"><?php echo translate('optional'); ?> - Se vuoto, sarà usato il titolo dell'articolo</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label"><?php echo translate('meta_description'); ?></label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160">
                                            <?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?>
                                        </textarea>
                                        <div class="form-text">Massimo 160 caratteri per i motori di ricerca</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="meta_keywords" class="form-label"><?php echo translate('meta_keywords'); ?></label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                               value="<?php echo htmlspecialchars($_POST['meta_keywords'] ?? ''); ?>" 
                                               maxlength="255">
                                        <div class="form-text">Parole chiave separate da virgole</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Article Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Impostazioni Articolo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label"><?php echo translate('article_status'); ?></label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft" <?php echo (($_POST['status'] ?? 'draft') === 'draft') ? 'selected' : ''; ?>>
                                                <?php echo translate('draft'); ?>
                                            </option>
                                            <option value="published" <?php echo (($_POST['status'] ?? '') === 'published') ? 'selected' : ''; ?>>
                                                <?php echo translate('published'); ?>
                                            </option>
                                            <option value="private" <?php echo (($_POST['status'] ?? '') === 'private') ? 'selected' : ''; ?>>
                                                <?php echo translate('private'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label"><?php echo translate('article_category'); ?></label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="">Nessuna categoria</option>
                                            <?php if (isset($data['categories'])): ?>
                                                <?php foreach ($data['categories'] as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" 
                                                            <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="published_at" class="form-label"><?php echo translate('published_at'); ?></label>
                                        <input type="datetime-local" class="form-control" id="published_at" name="published_at" 
                                               value="<?php echo htmlspecialchars($_POST['published_at'] ?? ''); ?>">
                                        <div class="form-text">Lascia vuoto per pubblicazione immediata</div>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                               <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_featured">
                                            <?php echo translate('featured_article'); ?>
                                        </label>
                                        <div class="form-text">Gli articoli in evidenza appaiono in homepage</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?php echo translate('featured_image'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">Seleziona Immagine</label>
                                        <input type="text" class="form-control" id="featured_image" name="featured_image" 
                                               value="<?php echo htmlspecialchars($_POST['featured_image'] ?? ''); ?>" 
                                               readonly>
                                        <button type="button" class="btn btn-outline-primary mt-2" id="selectImageBtn">
                                            <i class="fas fa-images"></i> Scegli Immagine
                                        </button>
                                        <button type="button" class="btn btn-outline-danger mt-2" id="removeImageBtn" style="display:none;">
                                            <i class="fas fa-times"></i> Rimuovi
                                        </button>
                                    </div>
                                    <div id="imagePreview" class="text-center" style="display:none;">
                                        <img id="previewImg" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> <?php echo translate('save'); ?>
                                        </button>
                                        <a href="articles.php" class="btn btn-outline-secondary">
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

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '.tinymce-editor',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic forecolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | image media link | code | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    language: '<?php echo get_current_language(); ?>',
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    branding: false,
    image_uploadtab: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
        if (meta.filetype === 'image') {
            openMediaLibrary(function(imageUrl) {
                cb(imageUrl, { alt: '' });
            });
        }
    }
});

// Image selection functionality
document.getElementById('selectImageBtn').addEventListener('click', function() {
    openMediaLibrary(function(imageUrl) {
        document.getElementById('featured_image').value = imageUrl;
        document.getElementById('previewImg').src = imageUrl;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('removeImageBtn').style.display = 'inline-block';
    });
});

document.getElementById('removeImageBtn').addEventListener('click', function() {
    document.getElementById('featured_image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    this.style.display = 'none';
});

// Simple media library opener (to be enhanced)
function openMediaLibrary(callback) {
    const imageUrl = prompt('Inserisci URL dell\'immagine:');
    if (imageUrl) {
        callback(imageUrl);
    }
}

// Initialize image preview if image already selected
document.addEventListener('DOMContentLoaded', function() {
    const featuredImage = document.getElementById('featured_image').value;
    if (featuredImage) {
        document.getElementById('previewImg').src = featuredImage;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('removeImageBtn').style.display = 'inline-block';
    }
});
</script>

<?php include 'includes/footer.php'; ?>