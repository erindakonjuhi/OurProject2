<?php

class Review {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addReview($movie_id, $user_id, $rating, $review_text) {
        $errors = [];

        if ($rating < 1 || $rating > 10) {
            $errors[] = 'Rating must be between 1 and 10';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('SELECT id FROM reviews WHERE movie_id = ? AND user_id = ?');
        $stmt->bind_param('ii', $movie_id, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'errors' => ['You already reviewed this movie']];
        }

        $stmt = $this->db->prepare('INSERT INTO reviews (movie_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iiss', $movie_id, $user_id, $rating, $review_text);

        if ($stmt->execute()) {

            $this->updateMovieRating($movie_id);
            return ['success' => true, 'review_id' => $this->db->insert_id, 'message' => 'Review added successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to add review']];
        }
    }

    public function getReviewsByMovie($movie_id, $limit = 10, $offset = 0) {
        $stmt = $this->db->prepare('SELECT r.*, u.username, u.full_name FROM reviews r 
                                    LEFT JOIN users u ON r.user_id = u.id 
                                    WHERE r.movie_id = ? 
                                    ORDER BY r.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iii', $movie_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserReviews($user_id, $limit = 10, $offset = 0) {
        $stmt = $this->db->prepare('SELECT r.*, m.title as movie_title, m.poster_image FROM reviews r 
                                    LEFT JOIN movies m ON r.movie_id = m.id 
                                    WHERE r.user_id = ? 
                                    ORDER BY r.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iii', $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getReviewById($id) {
        $stmt = $this->db->prepare('SELECT r.*, u.username, m.title as movie_title FROM reviews r 
                                    LEFT JOIN users u ON r.user_id = u.id 
                                    LEFT JOIN movies m ON r.movie_id = m.id 
                                    WHERE r.id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateReview($id, $rating, $review_text) {
        $errors = [];

        if ($rating < 1 || $rating > 10) {
            $errors[] = 'Rating must be between 1 and 10';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('UPDATE reviews SET rating = ?, review_text = ? WHERE id = ?');
        $stmt->bind_param('isi', $rating, $review_text, $id);

        if ($stmt->execute()) {
            $review = $this->getReviewById($id);
            $this->updateMovieRating($review['movie_id']);
            return ['success' => true, 'message' => 'Review updated successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to update review']];
        }
    }

    public function deleteReview($id) {
        $review = $this->getReviewById($id);
        
        $stmt = $this->db->prepare('DELETE FROM reviews WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            if ($review) {
                $this->updateMovieRating($review['movie_id']);
            }
            return ['success' => true, 'message' => 'Review deleted'];
        } else {
            return ['success' => false, 'errors' => ['Failed to delete review']];
        }
    }

    public function getAverageRating($movie_id) {
        $stmt = $this->db->prepare('SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews FROM reviews WHERE movie_id = ?');
        $stmt->bind_param('i', $movie_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function updateMovieRating($movie_id) {
        $rating_data = $this->getAverageRating($movie_id);
        $avg_rating = $rating_data['average_rating'] ?? 0;
        
        $stmt = $this->db->prepare('UPDATE movies SET rating = ? WHERE id = ?');
        $avg_rating = round($avg_rating, 1);
        $stmt->bind_param('di', $avg_rating, $movie_id);
        $stmt->execute();
    }

    public function getTotalReviewsCount($movie_id) {
        $stmt = $this->db->prepare('SELECT COUNT(*) as total FROM reviews WHERE movie_id = ?');
        $stmt->bind_param('i', $movie_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
}
?>
