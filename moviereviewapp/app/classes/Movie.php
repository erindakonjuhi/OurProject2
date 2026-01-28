<?php

class Movie {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addMovie($title, $description, $director, $release_year, $genre, $poster_image, $rating, $created_by) {
        $errors = [];

        if (empty($title) || strlen($title) < 2) {
            $errors[] = 'Title must be at least 2 characters';
        }

        if (empty($description)) {
            $errors[] = 'Description is required';
        }

        if ($release_year < 1800 || $release_year > date('Y') + 5) {
            $errors[] = 'Invalid release year';
        }

        if ($rating < 0 || $rating > 10) {
            $errors[] = 'Rating must be between 0 and 10';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('INSERT INTO movies (title, description, director, release_year, genre, poster_image, rating, created_by) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssiisd', $title, $description, $director, $release_year, $genre, $poster_image, $rating, $created_by);
        $rating = floatval($rating);

        if ($stmt->execute()) {
            return ['success' => true, 'movie_id' => $this->db->insert_id, 'message' => 'Movie added successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to add movie']];
        }
    }

    public function getMovieById($id) {
        $stmt = $this->db->prepare('SELECT m.*, u.username as created_by_username FROM movies m 
                                    LEFT JOIN users u ON m.created_by = u.id WHERE m.id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllMovies($limit = 10, $offset = 0) {
        $stmt = $this->db->prepare('SELECT m.*, u.username as created_by_username FROM movies m 
                                    LEFT JOIN users u ON m.created_by = u.id 
                                    ORDER BY m.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function searchMovies($query, $limit = 10) {
        $search = '%' . $query . '%';
        $stmt = $this->db->prepare('SELECT m.*, u.username as created_by_username FROM movies m 
                                    LEFT JOIN users u ON m.created_by = u.id 
                                    WHERE m.title LIKE ? OR m.description LIKE ? OR m.director LIKE ? OR m.genre LIKE ?
                                    ORDER BY m.created_at DESC LIMIT ?');
        $stmt->bind_param('ssssi', $search, $search, $search, $search, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get movies by genre
     */
    public function getMoviesByGenre($genre, $limit = 10, $offset = 0) {
        $stmt = $this->db->prepare('SELECT m.*, u.username as created_by_username FROM movies m 
                                    LEFT JOIN users u ON m.created_by = u.id 
                                    WHERE m.genre LIKE ? ORDER BY m.created_at DESC LIMIT ? OFFSET ?');
        $genre_search = '%' . $genre . '%';
        $stmt->bind_param('sii', $genre_search, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopRatedMovies($limit = 10) {
        $stmt = $this->db->prepare('SELECT m.*, u.username as created_by_username FROM movies m 
                                    LEFT JOIN users u ON m.created_by = u.id 
                                    ORDER BY m.rating DESC LIMIT ?');
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateMovie($id, $title, $description, $director, $release_year, $genre, $rating, $updated_by) {
        $errors = [];

        if (empty($title) || strlen($title) < 2) {
            $errors[] = 'Title must be at least 2 characters';
        }

        if (empty($description)) {
            $errors[] = 'Description is required';
        }

        if ($rating < 0 || $rating > 10) {
            $errors[] = 'Rating must be between 0 and 10';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('UPDATE movies SET title = ?, description = ?, director = ?, release_year = ?, genre = ?, rating = ?, updated_by = ? WHERE id = ?');
        $rating = floatval($rating);
        $stmt->bind_param('sssiiisi', $title, $description, $director, $release_year, $genre, $rating, $updated_by, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Movie updated successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to update movie']];
        }
    }

    public function deleteMovie($id) {
        $stmt = $this->db->prepare('DELETE FROM movies WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Movie deleted'];
        } else {
            return ['success' => false, 'errors' => ['Failed to delete movie']];
        }
    }

    public function getTotalMoviesCount() {
        $result = $this->db->query('SELECT COUNT(*) as total FROM movies');
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getGenres() {
        $result = $this->db->query('SELECT DISTINCT genre FROM movies WHERE genre IS NOT NULL AND genre != ""');
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
