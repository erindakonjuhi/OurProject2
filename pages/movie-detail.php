<?php

if (!isset($id) || empty($id)) {
    header('Location: ?page=movies');
    exit;
}

$movie = new Movie();
$review = new Review();

$movie_data = $movie->getMovieById($id);

if (!$movie_data) {
    header('Location: ?page=movies');
    exit;
}

$reviews = $review->getReviewsByMovie($id, 10);
$rating_stats = $review->getAverageRating($id);

// Handle adding a review
$add_review_errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (!$isLoggedIn) {
        header('Location: ?page=login');
        exit;
    }

    $rating = intval($_POST['rating'] ?? 0);
    $review_text = htmlspecialchars(trim($_POST['review_text'] ?? ''));

    if ($rating < 1 || $rating > 10) {
        $add_review_errors[] = 'Rating must be between 1 and 10';
    }

    if (empty($add_review_errors)) {
        $result = $review->addReview($id, $_SESSION['user_id'], $rating, $review_text);
        if ($result['success']) {
            header('Location: ?page=movie-detail&id=' . $id);
            exit;
        } else {
            $add_review_errors = $result['errors'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie_data['title']); ?> - Movie Review App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">EM reviews</div>
            <ul>
                <li><a href="?page=home">Home</a></li>
                <li><a href="?page=movies">Movies</a></li>
                <li><a href="?page=news">News</a></li>
                <li><a href="?page=contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="movie-header">
            <div class="movie-poster">
                <?php if ($movie_data['poster_image']): ?>
                    <img src="<?php echo htmlspecialchars($movie_data['poster_image']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($movie_data['title']); ?>">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #666;">No Image</div>
                <?php endif; ?>
            </div>

            <div class="movie-info">
                <h1><?php echo htmlspecialchars($movie_data['title']); ?></h1>
                <div class="movie-meta">
                    Directed by <?php echo htmlspecialchars($movie_data['director'] ?? 'Unknown'); ?> • <?php echo htmlspecialchars($movie_data['release_year']); ?> • <?php echo htmlspecialchars($movie_data['genre']); ?>
                </div>
                <div class="movie-rating">
                    ★ <?php echo htmlspecialchars($movie_data['rating']); ?>/10
                    (<?php echo htmlspecialchars($rating_stats['total_reviews']); ?> reviews)
                </div>
                <div class="movie-description">
                    <?php echo nl2br(htmlspecialchars($movie_data['description'])); ?>
                </div>
            </div>
        </div>

        <div class="reviews-section">
            <h2>Reviews & Ratings</h2>

            <?php if ($isLoggedIn): ?>
                <div class="add-review-form">
                    <h3>Add Your Review</h3>
                    <?php if (!empty($add_review_errors)): ?>
                        <div class="error-message">
                            <?php foreach ($add_review_errors as $error): ?>
                                <div><?php echo htmlspecialchars($error); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="form-group">
                            <label>Rating</label>
                            <select name="rating" required>
                                <option value="">Select Rating</option>
                                <?php for ($i = 10; $i >= 1; $i--): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> / 10</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Your Review</label>
                            <textarea name="review_text" placeholder="Share your thoughts..."></textarea>
                        </div>
                        <button type="submit" name="add_review">Submit Review</button>
                    </form>
                </div>
            <?php else: ?>
                <p><a href="?page=login" style="color: #667eea;">Login</a> to add a review</p>
            <?php endif; ?>

            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $r): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div>
                                <div class="review-author"><?php echo htmlspecialchars($r['full_name']); ?></div>
                                <div class="review-date"><?php echo date('M d, Y', strtotime($r['created_at'])); ?></div>
                            </div>
                            <div class="review-rating">★ <?php echo htmlspecialchars($r['rating']); ?>/10</div>
                        </div>
                        <div class="review-text"><?php echo nl2br(htmlspecialchars($r['review_text'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-reviews">No reviews yet. Be the first to review!</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; </p>
    </footer>
</body>
</html>
