<?php

$movie = new Movie();
$news = new News();
$topMovies = $movie->getTopRatedMovies(6);
$recentNews = $news->getRecentNews(3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Movie Review App</title>
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
                <li><a href="?page=about">About</a></li>
                <li><a href="?page=contact">Contact</a></li>
            </ul>
            <div class="auth-links">
                <?php if ($isLoggedIn): ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <?php if ($isAdmin): ?>
                        <a href="?page=dashboard" class="btn">Dashboard</a>
                    <?php endif; ?>
                    <a href="?page=logout" class="btn">Logout</a>
                <?php else: ?>
                    <a href="?page=login" class="btn">Login</a>
                    <a href="?page=register" class="btn">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="hero">
        <div class="container">
            <h1>Welcome to EM reviews</h1>
            <p>Discover, rate, and review your favorite movies</p>
            <a href="?page=movies" class="btn">Browse Movies</a>
        </div>
    </div>

    <section>
        <div class="container">
            <h2>Top Rated Movies</h2>
            <?php if (!empty($topMovies)): ?>
                <div class="movies-grid">
                    <?php foreach ($topMovies as $movie): ?>
                        <div class="movie-card">
                            <div class="movie-poster">
                                <?php if ($movie['poster_image']): ?>
                                    <img src="<?php echo htmlspecialchars($movie['poster_image']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </div>
                            <div class="movie-info">
                                <div class="movie-title">
                                    <a href="?page=movie-detail&id=<?php echo $movie['id']; ?>" style="color: white; text-decoration: none;">
                                        <?php echo htmlspecialchars($movie['title']); ?>
                                    </a>
                                </div>
                                <div class="movie-rating">★ <?php echo htmlspecialchars($movie['rating']); ?>/10</div>
                                <div class="movie-year"><?php echo htmlspecialchars($movie['release_year']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-message">No movies yet</div>
            <?php endif; ?>
        </div>
    </section>

    <section>
        <div class="container">
            <h2>Latest News</h2>
            <?php if (!empty($recentNews)): ?>
                <div class="news-grid">
                    <?php foreach ($recentNews as $article): ?>
                        <div class="news-card">
                            <div class="news-image">
                                <?php if ($article['image']): ?>
                                    <img src="<?php echo htmlspecialchars($article['image']); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </div>
                            <div class="news-content">
                                <div class="news-title"><?php echo htmlspecialchars($article['title']); ?></div>
                                <div class="news-excerpt"><?php echo substr(htmlspecialchars($article['content']), 0, 100) . '...'; ?></div>
                                <div class="news-meta">By <?php echo htmlspecialchars($article['created_by_username']); ?> • <?php echo date('M d, Y', strtotime($article['created_at'])); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-message">No news yet</div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>&copy; </p>
    </footer>
</body>
</html>
