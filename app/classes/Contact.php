<?php

class Contact {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    public function submitMessage($name, $email, $subject, $message) {
        $errors = [];

        if (strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (strlen($subject) < 3) {
            $errors[] = 'Subject must be at least 3 characters';
        }

        if (strlen($message) < 10) {
            $errors[] = 'Message must be at least 10 characters';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $status = 'unread';
        $stmt = $this->db->prepare('INSERT INTO contactmessages (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $name, $email, $subject, $message, $status);

        if ($stmt->execute()) {
            return ['success' => true, 'message_id' => $this->db->insert_id, 'message' => 'Message sent successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to send message']];
        }
    }

    public function getAllMessages($limit = 10, $offset = 0) {
        $stmt = $this->db->prepare('SELECT * FROM contactmessages ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMessageById($id) {
        $stmt = $this->db->prepare('SELECT * FROM contactmessages WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

 
    public function markAsRead($id) {
        $status = 'read';
        $stmt = $this->db->prepare('UPDATE contactmessages SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Message marked as read'];
        } else {
            return ['success' => false, 'errors' => ['Failed to update status']];
        }
    }


    public function replyToMessage($id, $reply_message, $replied_by) {
        $errors = [];

        if (strlen($reply_message) < 5) {
            $errors[] = 'Reply must be at least 5 characters';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $status = 'replied';
        $stmt = $this->db->prepare('UPDATE contactmessages SET status = ?, reply_message = ?, replied_by = ?, replied_at = NOW() WHERE id = ?');
        $stmt->bind_param('ssii', $status, $reply_message, $replied_by, $id);
        $replied_by = (int)$replied_by;

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Reply sent successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to send reply']];
        }
    }

    public function deleteMessage($id) {
        $stmt = $this->db->prepare('DELETE FROM contactmessages WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Message deleted'];
        } else {
            return ['success' => false, 'errors' => ['Failed to delete message']];
        }
    }

  
    public function getUnreadCount() {
        $result = $this->db->query('SELECT COUNT(*) as total FROM contactmessages WHERE status = "unread"');
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getMessagesByStatus($status, $limit = 10, $offset = 0) {
        $stmt = $this->db->prepare('SELECT * FROM contactmessages WHERE status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('sii', $status, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalCount() {
        $result = $this->db->query('SELECT COUNT(*) as total FROM contactmessages');
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
