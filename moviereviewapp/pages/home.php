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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            color: #000000;
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
            transition: opacity 0.3s;
        }

        nav a:hover {
            opacity: 0.8;
        }

        .auth-links {
            display: flex;
            gap: 15px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero {
            padding: 80px 20px;
            text-align: center;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            color: #bbb;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: blue;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.2s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        section {
            padding: 60px 20px;
        }

        h2 {
            font-size: 32px;
            margin-bottom: 40px;
            text-align: center;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .movie-card {
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .movie-card:hover {
            transform: translateY(-5px);
        }

        .movie-poster {
            width: 100%;
            height: 250px;
            background-color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 12px;
        }

        .movie-info {
            padding: 15px;
        }

        .movie-title {
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .movie-rating {
            color: #ffc107;
            font-size: 14px;
        }

        .movie-year {
            color: #999;
            font-size: 12px;
            margin-top: 5px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .news-card {
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
        }

        .news-image {
            width: 100%;
            height: 180px;
            background-color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }

        .news-content {
            padding: 20px;
        }

        .news-title {
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .news-excerpt {
            color: #bbb;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .news-meta {
            color: #999;
            font-size: 12px;
        }

        footer {
            background: #0a0a0a;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #333;
            margin-top: 60px;
        }

        .empty-message {
            text-align: center;
            color: #999;
            padding: 40px 20px;
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
