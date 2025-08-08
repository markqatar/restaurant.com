<?php
class Media {
    private $db;
    private $uploadPath;
    
    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->uploadPath = __DIR__ . '/../uploads/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

    }
    
    public function getAllMedia($type = null) {
        try {
            $sql = "SELECT m.*, u.username as uploaded_by_name 
                    FROM media m 
                    LEFT JOIN users u ON m.uploaded_by = u.id";
            
            if ($type) {
                $sql .= " WHERE m.mime_type LIKE ?";
            }
            
            $sql .= " ORDER BY m.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            
            if ($type) {
                $stmt->execute([$type . '%']);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching media: " . $e->getMessage());
            return [];
        }
    }
    
    public function getMediaById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, u.username as uploaded_by_name 
                FROM media m 
                LEFT JOIN users u ON m.uploaded_by = u.id 
                WHERE m.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching media: " . $e->getMessage());
            return false;
        }
    }
    
    public function uploadFile($file, $uploadedBy, $altText = null, $title = null, $description = null) {
        try {
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'message' => 'Upload error: ' . $file['error']];
            }
            
            // Check file size (10MB limit)
            if ($file['size'] > 10 * 1024 * 1024) {
                return ['success' => false, 'message' => 'File too large. Maximum size is 10MB.'];
            }
            
            // Get file info
            $originalName = $file['name'];
            $fileSize = $file['size'];
            $mimeType = $file['type'];
            $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt'];
            if (!in_array($fileExt, $allowedTypes)) {
                return ['success' => false, 'message' => 'File type not allowed.'];
            }
            
            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.' . $fileExt;
            $filePath = 'uploads/' . $filename;
            $fullPath = $this->uploadPath . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                return ['success' => false, 'message' => 'Failed to move uploaded file.'];
            }
            
            // Save to database
            $stmt = $this->db->prepare("
                INSERT INTO media (filename, original_name, file_path, file_size, mime_type, alt_text, title, description, uploaded_by, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $result = $stmt->execute([
                $filename,
                $originalName,
                $filePath,
                $fileSize,
                $mimeType,
                $altText,
                $title,
                $description,
                $uploadedBy
            ]);
            
            if ($result) {
                $mediaId = $this->db->lastInsertId();
                return [
                    'success' => true, 
                    'message' => 'File uploaded successfully.',
                    'media_id' => $mediaId,
                    'filename' => $filename,
                    'file_path' => $filePath
                ];
            } else {
                // Remove uploaded file if database insert failed
                unlink($fullPath);
                return ['success' => false, 'message' => 'Failed to save file information.'];
            }
            
        } catch (PDOException $e) {
            error_log("Error uploading file: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred.'];
        }
    }
    
    public function updateMedia($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE media 
                SET alt_text = ?, title = ?, description = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['alt_text'] ?? null,
                $data['title'] ?? null,
                $data['description'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating media: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteMedia($id) {
        try {
            // Get file info first
            $media = $this->getMediaById($id);
            if (!$media) {
                return false;
            }
            
            // Delete from database
            $stmt = $this->db->prepare("DELETE FROM media WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                // Delete physical file
                $fullPath = $this->uploadPath . $media['filename'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error deleting media: " . $e->getMessage());
            return false;
        }
    }
    
    public function getImageMedia() {
        return $this->getAllMedia('image');
    }
    
    public function isImage($mimeType) {
        return strpos($mimeType, 'image/') === 0;
    }
}
?>