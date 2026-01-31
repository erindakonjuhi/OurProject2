<?php

class News {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addNews($title, $content, $image, $created_by) {
        $errors = [];

        if (empty($title) || strlen($title) < 3) {
            $errors[] = 'Title must be at least 3 characters';
        }

        if (empty($content)) {
            $errors[] = 'Content is required';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('INSERT INTO news (title, content, image, created_by) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sssi', $title, $content, $image, $created_by);

        if ($stmt->execute()) {
            return ['success' => true, 'news_id' => $this->db->insert_id, 'message' => 'News added successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to add news']];
        }
    }

    public function getNewsById($id) {
        $stmt = $this->db->prepare('SELECT n.*, u.username as created_by_username, u2.username as updated_by_username 
                                    FROM news n 
                                    LEFT JOIN users u ON n.created_by = u.id 
                                    LEFT JOIN users u2 ON n.updated_by = u2.id 
                                    WHERE n.id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllNews($limit = 10, $offset = 0) {
        $stmt = $this->db->prepare('SELECT n.*, u.username as created_by_username FROM news n 
                                    LEFT JOIN users u ON n.created_by = u.id 
                                    ORDER BY n.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function searchNews($query, $limit = 10) {
        $search = '%' . $query . '%';
        $stmt = $this->db->prepare('SELECT n.*, u.username as created_by_username FROM news n 
                                    LEFT JOIN users u ON n.created_by = u.id 
                                    WHERE n.title LIKE ? OR n.content LIKE ? 
                                    ORDER BY n.created_at DESC LIMIT ?');
        $stmt->bind_param('ssi', $search, $search, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateNews($id, $title, $content, $image, $updated_by) {
        $errors = [];

        if (empty($title) || strlen($title) < 3) {
            $errors[] = 'Title must be at least 3 characters';
        }

        if (empty($content)) {
            $errors[] = 'Content is required';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('UPDATE news SET title = ?, content = ?, image = ?, updated_by = ? WHERE id = ?');
        $stmt->bind_param('sssii', $title, $content, $image, $updated_by, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'News updated successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to update news']];
        }
    }

    public function deleteNews($id) {
        $stmt = $this->db->prepare('DELETE FROM news WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'News deleted'];
        } else {
            return ['success' => false, 'errors' => ['Failed to delete news']];
        }
    }

    public function getTotalNewsCount() {
        $result = $this->db->query('SELECT COUNT(*) as total FROM news');
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getRecentNews($limit = 5) {
        $stmt = $this->db->prepare('SELECT n.*, u.username as created_by_username FROM news n 
                                    LEFT JOIN users u ON n.created_by = u.id 
                                    ORDER BY n.created_at DESC LIMIT ?');
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
