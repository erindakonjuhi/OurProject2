<?php

class Media {
    private $db;
    private $upload_dir = __DIR__ . '/../../uploads/';
    private $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    private $max_file_size = 5242880; 

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }

    public function uploadFile($file, $movie_id = null, $news_id = null, $uploaded_by) {
        $errors = [];

        if (!isset($file['error']) || is_array($file['error'])) {
            $errors[] = 'Invalid file upload';
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error: ' . $this->getUploadErrorMessage($file['error']);
        }

        if ($file['size'] > $this->max_file_size) {
            $errors[] = 'File is too large. Maximum size is 5MB';
        }

        if (!in_array($file['type'], $this->allowed_types)) {
            $errors[] = 'File type not allowed. Only JPG, PNG, GIF, and PDF are allowed';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('media_') . '.' . $ext;
        $filepath = $this->upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'errors' => ['Failed to move uploaded file']];
        }

        $media_type = (strpos($file['type'], 'image') !== false) ? 'image' : 'pdf';

        $file_path = 'uploads/' . $filename;
        $stmt = $this->db->prepare('INSERT INTO media (movie_id, news_id, media_type, file_path, file_name, uploaded_by) 
                                    VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iissi', $movie_id, $news_id, $media_type, $file_path, $file, $uploaded_by);
        $file = $file['name'];

        if ($stmt->execute()) {
            return ['success' => true, 'media_id' => $this->db->insert_id, 'file_path' => $file_path, 'message' => 'File uploaded successfully'];
        } else {
            unlink($filepath);
            return ['success' => false, 'errors' => ['Failed to save file information']];
        }
    }

    public function getMediaByMovie($movie_id) {
        $stmt = $this->db->prepare('SELECT * FROM media WHERE movie_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $movie_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    
    public function getMediaByNews($news_id) {
        $stmt = $this->db->prepare('SELECT * FROM media WHERE news_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $news_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    public function getMediaById($id) {
        $stmt = $this->db->prepare('SELECT * FROM media WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    public function deleteMedia($id) {
        $media = $this->getMediaById($id);

        if (!$media) {
            return ['success' => false, 'errors' => ['Media not found']];
        }

        $filepath = __DIR__ . '/../../' . $media['file_path'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        $stmt = $this->db->prepare('DELETE FROM media WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Media deleted'];
        } else {
            return ['success' => false, 'errors' => ['Failed to delete media']];
        }
    }
    private function getUploadErrorMessage($error_code) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];

        return $messages[$error_code] ?? 'Unknown error';
    }
}
?>
