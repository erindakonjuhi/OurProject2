<?php

class User {
    private $db;
    private $id;
    private $username;
    private $email;
    private $password;
    private $role;
    private $full_name;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register($username, $email, $password, $full_name) {
        // Validation
        $errors = [];

        if (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }

        if (strlen($full_name) < 2) {
            $errors[] = 'Full name must be at least 2 characters';
        }

        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = 'Username or email already exists';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)');
        $role = 'user';
        $stmt->bind_param('sssss', $username, $email, $hashed_password, $full_name, $role);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User registered successfully'];
        } else {
            return ['success' => false, 'errors' => ['Registration failed']];
        }
    }

    public function login($email, $password) {
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('SELECT id, username, password, role, full_name FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'errors' => ['User not found']];
        }

        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'errors' => ['Invalid password']];
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        return ['success' => true, 'message' => 'Login successful'];
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare('SELECT id, username, email, role, full_name, created_at FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    public function updateProfile($id, $full_name, $email) {
        $errors = [];

        if (strlen($full_name) < 2) {
            $errors[] = 'Full name must be at least 2 characters';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->bind_param('si', $email, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'Email already in use';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('UPDATE users SET full_name = ?, email = ? WHERE id = ?');
        $stmt->bind_param('ssi', $full_name, $email, $id);

        if ($stmt->execute()) {
            $_SESSION['full_name'] = $full_name;
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'errors' => ['Update failed']];
        }
    }

    /**
     * Change password
     */
    public function changePassword($id, $oldPassword, $newPassword, $confirmPassword) {
        $errors = [];

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match';
        }

        if (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!password_verify($oldPassword, $result['password'])) {
            return ['success' => false, 'errors' => ['Old password is incorrect']];
        }

        $hashed_password = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->bind_param('si', $hashed_password, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        } else {
            return ['success' => false, 'errors' => ['Password change failed']];
        }
    }

    public function getAllUsers() {
        $stmt = $this->db->prepare('SELECT id, username, email, role, full_name, created_at FROM users ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateUserRole($id, $role) {
        $allowed_roles = ['admin', 'user'];
        if (!in_array($role, $allowed_roles)) {
            return ['success' => false, 'errors' => ['Invalid role']];
        }

        $stmt = $this->db->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->bind_param('si', $role, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User role updated'];
        } else {
            return ['success' => false, 'errors' => ['Update failed']];
        }
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User deleted'];
        } else {
            return ['success' => false, 'errors' => ['Deletion failed']];
        }
    }

    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
?>
