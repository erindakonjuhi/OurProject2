<?php

$movie = new Movie();
$page = isset($_GET['page_num']) ? intval($_GET['page_num']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$movies = $movie->getAllMovies($limit, $offset);
$total_movies = $movie->getTotalMoviesCount();
$total_pages = ceil($total_movies / $limit);

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - Movie Review App</title>
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

    <section>
        <div class="container">
            <h1>Movies</h1>
            
            <?php if (!empty($movies)): ?>
                <div class="movies-grid">
                    <?php foreach ($movies as $m): ?>
                        <div class="movie-card">
                            <div class="movie-poster">
                                <?php if ($m['poster_image']): ?>
                                    <img src="<?php echo htmlspecialchars($m['poster_image']); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </div>
                            <div class="movie-info">
                                <h3><?php echo htmlspecialchars($m['title']); ?></h3>
                                <p class="director">Director: <?php echo htmlspecialchars($m['director']); ?></p>
                                <p class="year">Year: <?php echo htmlspecialchars($m['release_year']); ?></p>
                                <p class="rating">Rating: <strong><?php echo htmlspecialchars($m['rating']); ?>/10</strong></p>
                                <a href="?page=movie-detail&id=<?php echo $m['id']; ?>" class="btn btn-sm">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=movies&page_num=1" class="btn btn-sm">First</a>
                        <a href="?page=movies&page_num=<?php echo $page - 1; ?>" class="btn btn-sm">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="page-current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=movies&page_num=<?php echo $i; ?>" class="btn btn-sm"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=movies&page_num=<?php echo $page + 1; ?>" class="btn btn-sm">Next</a>
                        <a href="?page=movies&page_num=<?php echo $total_pages; ?>" class="btn btn-sm">Last</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="empty-message">No movies found</div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 EM Reviews. All rights reserved.</p>
    </footer>

    <script src="javascripts/main.js"></script>
</body>
</html>
