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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f0f0f;
            color: #fff;
        }

        header {
            background: blue;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 30px;
        }

        nav a {
            color: white;
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .movie-header {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .movie-poster {
            width: 100%;
            height: 400px;
            background-color: #333;
            border-radius: 8px;
            overflow: hidden;
        }

        .movie-info h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .movie-meta {
            color: #bbb;
            margin-bottom: 20px;
        }

        .movie-rating {
            font-size: 24px;
            color: #ffc107;
            margin-bottom: 20px;
        }

        .movie-description {
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .reviews-section {
            margin-top: 40px;
        }

        .reviews-section h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .add-review-form {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            background: #0a0a0a;
            border: 1px solid #333;
            color: white;
            border-radius: 5px;
            font-family: inherit;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            padding: 10px 30px;
            background: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            opacity: 0.9;
        }

        .review-item {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .review-author {
            font-weight: 600;
        }

        .review-rating {
            color: #ffc107;
        }

        .review-date {
            color: #999;
            font-size: 12px;
        }

        .review-text {
            color: #ccc;
            line-height: 1.6;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
        }

        .empty-reviews {
            text-align: center;
            color: #999;
            padding: 20px;
        }

        footer {
            background: #0a0a0a;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #333;
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">🎬 EM reviews</div>
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
