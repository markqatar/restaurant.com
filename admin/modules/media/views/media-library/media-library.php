<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/MediaController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'media', 'view')) {
    redirect('/admin/login');
    exit;
}

$controller = new MediaController($pdo);

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    switch ($_GET['ajax']) {
        case 'upload':
            $controller->upload();
            break;
        case 'update':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->update($id);
            }
            break;
        case 'delete':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->delete($id);
            }
            break;
        case 'library':
            $controller->getMediaLibrary();
            break;
    }
    exit;
}

$data = $controller->index();

$pageTitle = translate('media_library');
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2><?php echo translate('media_library'); ?></h2>
                        <p class="text-muted">Gestisci immagini e file del sito</p>
                    </div>
                    <?php if (has_permission($_SESSION['user_id'], 'media', 'upload')): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="fas fa-upload"></i> <?php echo translate('upload_media'); ?>
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Filter Tabs -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?php echo !$data['currentType'] ? 'active' : ''; ?>" href="media-library.php">
                            <i class="fas fa-th"></i> Tutti i file
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $data['currentType'] === 'image' ? 'active' : ''; ?>" href="media-library.php?type=image">
                            <i class="fas fa-images"></i> Immagini
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $data['currentType'] === 'application' ? 'active' : ''; ?>" href="media-library.php?type=application">
                            <i class="fas fa-file"></i> Documenti
                        </a>
                    </li>
                </ul>

                <div id="alerts"></div>

                <!-- Media Grid -->
                <div class="row" id="mediaGrid">
                    <?php foreach ($data['mediaFiles'] as $file): ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4" data-media-id="<?php echo $file['id']; ?>">
                            <div class="card media-item h-100">
                                <div class="card-img-top media-preview" style="height: 150px; overflow: hidden; position: relative;">
                                    <?php if ($this->media->isImage($file['mime_type'])): ?>
                                        <img src="<?php echo htmlspecialchars($file['file_path']); ?>"
                                            alt="<?php echo htmlspecialchars($file['alt_text'] ?? $file['original_name']); ?>"
                                            class="img-fluid w-100 h-100" style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="fas fa-file fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Actions Overlay -->
                                    <div class="media-actions position-absolute top-0 end-0 p-2" style="background: rgba(0,0,0,0.5);">
                                        <?php if (has_permission($_SESSION['user_id'], 'media', 'edit')): ?>
                                            <button type="button" class="btn btn-sm btn-light me-1" onclick="editMedia(<?php echo $file['id']; ?>)" title="Modifica">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (has_permission($_SESSION['user_id'], 'media', 'delete')): ?>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteMedia(<?php echo $file['id']; ?>)" title="Elimina">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-body p-2">
                                    <h6 class="card-title small mb-1" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                        <?php echo htmlspecialchars(strlen($file['original_name']) > 20 ? substr($file['original_name'], 0, 17) . '...' : $file['original_name']); ?>
                                    </h6>
                                    <small class="text-muted d-block">
                                        <?php echo strtoupper(pathinfo($file['original_name'], PATHINFO_EXTENSION)); ?> -
                                        <?php echo number_format($file['file_size'] / 1024, 1); ?>KB
                                    </small>
                                    <small class="text-muted d-block">
                                        <?php echo date('d/m/Y', strtotime($file['created_at'])); ?>
                                    </small>

                                    <!-- Copy URL Button -->
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-2" onclick="copyUrl('<?php echo htmlspecialchars($file['file_path']); ?>')">
                                        <i class="fas fa-copy"></i> Copia URL
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($data['mediaFiles'])): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Nessun file trovato</h4>
                        <p class="text-muted">Carica il tuo primo file per iniziare</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo translate('upload_media'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="file" class="form-label">Seleziona File *</label>
                        <input type="file" class="form-control" id="file" name="file" required
                            accept="image/*,.pdf,.doc,.docx,.txt" multiple>
                        <div class="form-text">
                            Formati supportati: JPG, PNG, GIF, WebP, PDF, DOC, DOCX, TXT<br>
                            Dimensione massima: 10MB per file
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label"><?php echo translate('media_title'); ?></label>
                        <input type="text" class="form-control" id="title" name="title" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="alt_text" class="form-label"><?php echo translate('media_alt_text'); ?></label>
                        <input type="text" class="form-control" id="alt_text" name="alt_text" maxlength="255">
                        <div class="form-text">Importante per SEO e accessibilità</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label"><?php echo translate('media_description'); ?></label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"></textarea>
                    </div>
                </form>

                <!-- Upload Progress -->
                <div id="uploadProgress" style="display: none;">
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div id="uploadStatus"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('cancel'); ?></button>
                <button type="button" class="btn btn-primary" onclick="uploadFiles()">
                    <i class="fas fa-upload"></i> Carica
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifica Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_media_id">

                    <div class="mb-3">
                        <label for="edit_title" class="form-label"><?php echo translate('media_title'); ?></label>
                        <input type="text" class="form-control" id="edit_title" name="title" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="edit_alt_text" class="form-label"><?php echo translate('media_alt_text'); ?></label>
                        <input type="text" class="form-control" id="edit_alt_text" name="alt_text" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label"><?php echo translate('media_description'); ?></label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" maxlength="500"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo translate('cancel'); ?></button>
                <button type="button" class="btn btn-primary" onclick="updateMedia()">
                    <i class="fas fa-save"></i> <?php echo translate('save'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Upload functionality
    function uploadFiles() {
        const form = document.getElementById('uploadForm');
        const formData = new FormData(form);
        const files = document.getElementById('file').files;

        if (files.length === 0) {
            showAlert('Seleziona almeno un file', 'danger');
            return;
        }

        document.getElementById('uploadProgress').style.display = 'block';
        const progressBar = document.querySelector('.progress-bar');
        const statusDiv = document.getElementById('uploadStatus');

        // Upload each file
        let completed = 0;
        const total = files.length;

        Array.from(files).forEach((file, index) => {
            const fileFormData = new FormData();
            fileFormData.append('file', file);
            fileFormData.append('title', formData.get('title'));
            fileFormData.append('alt_text', formData.get('alt_text'));
            fileFormData.append('description', formData.get('description'));

            fetch('media-library.php?ajax=upload', {
                    method: 'POST',
                    body: fileFormData
                })
                .then(response => response.json())
                .then(data => {
                    completed++;
                    const progress = (completed / total) * 100;
                    progressBar.style.width = progress + '%';

                    if (data.success) {
                        statusDiv.innerHTML += `<div class="text-success">✓ ${file.name} caricato</div>`;
                    } else {
                        statusDiv.innerHTML += `<div class="text-danger">✗ ${file.name}: ${data.message}</div>`;
                    }

                    if (completed === total) {
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                })
                .catch(error => {
                    completed++;
                    statusDiv.innerHTML += `<div class="text-danger">✗ ${file.name}: Errore di rete</div>`;
                });
        });
    }

    // Edit functionality
    function editMedia(id) {
        // Get media details (you would fetch this via AJAX)
        // For now, just show the modal
        document.getElementById('edit_media_id').value = id;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    function updateMedia() {
        const mediaId = document.getElementById('edit_media_id').value;
        const formData = new FormData(document.getElementById('editForm'));

        fetch(`media-library.php?ajax=update&id=${mediaId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    // Optionally reload or update the specific media item
                } else {
                    showAlert(data.message, 'danger');
                }
            });
    }

    // Delete functionality
    function deleteMedia(id) {
        Swal.fire({
            title: 'Conferma eliminazione',
            text: 'Sei sicuro di voler eliminare questo file? L\'azione non può essere annullata.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sì, elimina',
            cancelButtonText: 'Annulla'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`media-library.php?ajax=delete&id=${id}`, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message, 'success');
                            document.querySelector(`[data-media-id="${id}"]`).remove();
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    });
            }
        });
    }

    // Copy URL functionality
    function copyUrl(url) {
        const fullUrl = window.location.origin + '/' + url;
        navigator.clipboard.writeText(fullUrl).then(() => {
            showAlert('URL copiato negli appunti!', 'success');
        });
    }

    // Alert helper
    function showAlert(message, type) {
        const alertDiv = document.getElementById('alerts');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
        alertDiv.appendChild(alert);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
</script>

<?php include 'includes/footer.php'; ?>