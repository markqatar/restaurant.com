<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/PageController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'pages', 'edit')) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: pages.php?error=' . urlencode(translate('invalid_request')));
    exit;
}

$controller = new PageController($pdo);
$data = $controller->edit($id);

$pageTitle = translate('edit_page');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo translate('edit_page'); ?></h2>
                    <a href="pages.php" class="btn btn-secondary">
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
                                    <h5 class="card-title mb-0"><?php echo translate('page_content'); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label"><?php echo translate('page_title'); ?> *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($_POST['title'] ?? $data['page']['title']); ?>" 
                                               required maxlength="255">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label"><?php echo translate('page_content'); ?></label>
                                        <textarea class="form-control tinymce-editor" id="content" name="content" rows="15">
                                            <?php echo htmlspecialchars($_POST['content'] ?? $data['page']['content']); ?>
                                        </textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label"><?php echo translate('page_excerpt'); ?></label>
                                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" maxlength="500">
                                            <?php echo htmlspecialchars($_POST['excerpt'] ?? $data['page']['excerpt']); ?>
                                        </textarea>
                                        <div class="form-text"><?php echo translate('optional'); ?></div>
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
                                               value="<?php echo htmlspecialchars($_POST['meta_title'] ?? $data['page']['meta_title']); ?>" 
                                               maxlength="255">
                                        <div class="form-text"><?php echo translate('optional'); ?> - Se vuoto, sarà usato il titolo della pagina</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label"><?php echo translate('meta_description'); ?></label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160">
                                            <?php echo htmlspecialchars($_POST['meta_description'] ?? $data['page']['meta_description']); ?>
                                        </textarea>
                                        <div class="form-text">Massimo 160 caratteri per i motori di ricerca</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="meta_keywords" class="form-label"><?php echo translate('meta_keywords'); ?></label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                               value="<?php echo htmlspecialchars($_POST['meta_keywords'] ?? $data['page']['meta_keywords']); ?>" 
                                               maxlength="255">
                                        <div class="form-text">Parole chiave separate da virgole</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Page Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Impostazioni Pagina</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label"><?php echo translate('page_status'); ?></label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft" <?php echo (($_POST['status'] ?? $data['page']['status']) === 'draft') ? 'selected' : ''; ?>>
                                                <?php echo translate('draft'); ?>
                                            </option>
                                            <option value="published" <?php echo (($_POST['status'] ?? $data['page']['status']) === 'published') ? 'selected' : ''; ?>>
                                                <?php echo translate('published'); ?>
                                            </option>
                                            <option value="private" <?php echo (($_POST['status'] ?? $data['page']['status']) === 'private') ? 'selected' : ''; ?>>
                                                <?php echo translate('private'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="template" class="form-label"><?php echo translate('page_template'); ?></label>
                                        <select class="form-select" id="template" name="template">
                                            <option value="default" <?php echo (($_POST['template'] ?? $data['page']['template']) === 'default') ? 'selected' : ''; ?>>Default</option>
                                            <option value="full-width" <?php echo (($_POST['template'] ?? $data['page']['template']) === 'full-width') ? 'selected' : ''; ?>>Full Width</option>
                                            <option value="landing" <?php echo (($_POST['template'] ?? $data['page']['template']) === 'landing') ? 'selected' : ''; ?>>Landing Page</option>
                                            <option value="contact" <?php echo (($_POST['template'] ?? $data['page']['template']) === 'contact') ? 'selected' : ''; ?>>Contact Page</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="parent_id" class="form-label"><?php echo translate('parent_page'); ?></label>
                                        <select class="form-select" id="parent_id" name="parent_id">
                                            <option value=""><?php echo translate('no'); ?> - Pagina principale</option>
                                            <?php if (isset($data['parentPages'])): ?>
                                                <?php foreach ($data['parentPages'] as $parent): ?>
                                                    <?php if ($parent['id'] != $data['page']['id']): // Don't allow self as parent ?>
                                                        <option value="<?php echo $parent['id']; ?>" 
                                                                <?php echo (($_POST['parent_id'] ?? $data['page']['parent_id']) == $parent['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($parent['title']); ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Ordine di Visualizzazione</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                               value="<?php echo htmlspecialchars($_POST['sort_order'] ?? $data['page']['sort_order']); ?>" 
                                               min="0">
                                        <div class="form-text">0 = primo nella lista</div>
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
                                               value="<?php echo htmlspecialchars($_POST['featured_image'] ?? $data['page']['featured_image']); ?>" 
                                               readonly>
                                        <button type="button" class="btn btn-outline-primary mt-2" id="selectImageBtn">
                                            <i class="fas fa-images"></i> Scegli Immagine
                                        </button>
                                        <button type="button" class="btn btn-outline-danger mt-2" id="removeImageBtn">
                                            <i class="fas fa-times"></i> Rimuovi
                                        </button>
                                    </div>
                                    <div id="imagePreview" class="text-center">
                                        <img id="previewImg" src="<?php echo htmlspecialchars($data['page']['featured_image'] ?? ''); ?>" alt="Preview" class="img-fluid" style="max-height: 200px;">
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
                                        <a href="pages.php" class="btn btn-outline-secondary">
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
    height: 400,
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
    document.getElementById('previewImg').src = '';
    document.getElementById('imagePreview').style.display = 'none';
    this.style.display = 'none';
});

// Simple media library opener (to be enhanced)
function openMediaLibrary(callback) {
    // For now, just prompt for URL - will be replaced with proper media library
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
    } else {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('removeImageBtn').style.display = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>