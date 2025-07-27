<?php
// Media selector popup for TinyMCE and other editors
session_start();
require_once '../config/database.php';
require_once '../controllers/MediaController.php';
require_once '../includes/functions.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !has_permission($_SESSION['user_id'], 'media', 'view')) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

$controller = new MediaController($pdo);

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    $controller->getMediaLibrary();
    exit;
}

$data = $controller->index();
?>
<!DOCTYPE html>
<html lang="<?php echo get_current_language(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Seleziona Media</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-size: 14px; }
        .media-item { cursor: pointer; transition: all 0.3s; }
        .media-item:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .media-item.selected { border: 3px solid #0d6efd; }
        .media-preview { position: relative; }
        .media-actions { opacity: 0; transition: opacity 0.3s; }
        .media-item:hover .media-actions { opacity: 1; }
    </style>
</head>
<body>
    <div class="container-fluid p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Seleziona Media</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="refreshMedia()">
                    <i class="fas fa-sync"></i> Aggiorna
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="selectMedia()" id="selectBtn" disabled>
                    <i class="fas fa-check"></i> Seleziona
                </button>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <ul class="nav nav-pills nav-sm mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="#" onclick="filterMedia('all')" data-filter="all">
                    <i class="fas fa-th"></i> Tutti
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="filterMedia('image')" data-filter="image">
                    <i class="fas fa-images"></i> Immagini
                </a>
            </li>
        </ul>

        <!-- Search -->
        <div class="mb-3">
            <input type="text" class="form-control form-control-sm" placeholder="Cerca file..." id="searchInput" onkeyup="searchMedia()">
        </div>

        <!-- Media Grid -->
        <div class="row" id="mediaGrid">
            <?php foreach ($data['mediaFiles'] as $file): ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3 media-grid-item" 
                     data-media-id="<?php echo $file['id']; ?>"
                     data-media-url="<?php echo htmlspecialchars($file['file_path']); ?>"
                     data-media-type="<?php echo $file['mime_type']; ?>"
                     data-media-name="<?php echo htmlspecialchars($file['original_name']); ?>"
                     onclick="toggleSelection(this)">
                    <div class="card media-item h-100">
                        <div class="card-img-top media-preview" style="height: 120px; overflow: hidden; position: relative;">
                            <?php if (strpos($file['mime_type'], 'image/') === 0): ?>
                                <img src="<?php echo htmlspecialchars($file['file_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($file['alt_text'] ?? $file['original_name']); ?>" 
                                     class="img-fluid w-100 h-100" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <i class="fas fa-file fa-2x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body p-2">
                            <h6 class="card-title small mb-1" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                <?php echo htmlspecialchars(strlen($file['original_name']) > 15 ? substr($file['original_name'], 0, 12) . '...' : $file['original_name']); ?>
                            </h6>
                            <small class="text-muted">
                                <?php echo strtoupper(pathinfo($file['original_name'], PATHINFO_EXTENSION)); ?> - 
                                <?php echo number_format($file['file_size'] / 1024, 1); ?>KB
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($data['mediaFiles'])): ?>
            <div class="text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">Nessun file trovato</h6>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedMedia = null;

        function toggleSelection(element) {
            // Remove previous selection
            document.querySelectorAll('.media-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            // Add selection to clicked item
            element.querySelector('.media-item').classList.add('selected');
            
            // Store selected media info
            selectedMedia = {
                id: element.dataset.mediaId,
                url: element.dataset.mediaUrl,
                type: element.dataset.mediaType,
                name: element.dataset.mediaName
            };
            
            // Enable select button
            document.getElementById('selectBtn').disabled = false;
        }

        function selectMedia() {
            if (selectedMedia) {
                // For TinyMCE integration
                if (window.parent && window.parent.mediaSelectionCallback) {
                    window.parent.mediaSelectionCallback(selectedMedia.url, selectedMedia);
                }
                
                // For general callback
                if (window.parent && window.parent.onMediaSelected) {
                    window.parent.onMediaSelected(selectedMedia);
                }
                
                // Close popup
                if (window.parent) {
                    window.parent.postMessage({
                        type: 'mediaSelected',
                        media: selectedMedia
                    }, '*');
                }
            }
        }

        function filterMedia(type) {
            // Update active tab
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`[data-filter="${type}"]`).classList.add('active');
            
            // Filter media items
            document.querySelectorAll('.media-grid-item').forEach(item => {
                const mediaType = item.dataset.mediaType;
                if (type === 'all' || mediaType.startsWith(type)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function searchMedia() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.media-grid-item').forEach(item => {
                const name = item.dataset.mediaName.toLowerCase();
                if (name.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function refreshMedia() {
            location.reload();
        }

        // Handle escape key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (window.parent) {
                    window.parent.postMessage({type: 'closeModal'}, '*');
                }
            }
        });
    </script>
</body>
</html>