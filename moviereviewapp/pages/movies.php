<?php

$movie = new Movie();
$search = htmlspecialchars(trim($_GET['search'] ?? ''));
$page = intval($_GET['p'] ?? 1);
$limit = 12;
$offset = ($page - 1) * $limit;

if ($search) {
    $movies = $movie->searchMovies($search, 100);
    $total = count($movies);
    $movies = array_slice($movies, $offset, $limit);
} else {
    $total = $movie->getTotalMoviesCount();
    $movies = $movie->getAllMovies($limit, $offset);
}

$total_pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - Movie Review App</title>
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

        h1 {
            margin-bottom: 30px;
        }

        .search-box {
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #1a1a1a;
            color: white;
        }

        .search-box button {
            padding: 12px 30px;
            background: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
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
        }

        .movie-info {
            padding: 15px;
        }

        .movie-title {
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .movie-title a {
            color: white;
            text-decoration: none;
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

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }

        .pagination a, .pagination span {
            padding: 10px 15px;
            background: #1a1a1a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination .active {
            background: blue;
        }

        .empty-message {
            text-align: center;
            color: #999;
            padding: 40px 20px;
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
                <li><a href="?page=about">About</a></li>
                <li><a href="?page=contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Browse Movies</h1>

        <div class="search-box">
            <form method="GET" action="" style="display: flex; width: 100%; gap: 10px;">
                <input type="hidden" name="page" value="movies">
                <input type="text" name="search" placeholder="Search movies..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
                <?php if ($search): ?>
                    <a href="?page=movies" style="padding: 12px 20px; background: #666; color: white; text-decoration: none; border-radius: 5px;">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!empty($movies)): ?>
            <div class="movies-grid">
                <?php foreach ($movies as $movie): ?>
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
                                <a href="?page=movie-detail&id=<?php echo $movie['id']; ?>">
                                    <?php echo htmlspecialchars($movie['title']); ?>
                                </a>
                            </div>
                            <div class="movie-rating">★ <?php echo htmlspecialchars($movie['rating']); ?>/10</div>
                            <div class="movie-year"><?php echo htmlspecialchars($movie['release_year']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=movies&p=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=movies&p=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=movies&p=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-message">
                <?php echo $search ? 'No movies found for your search' : 'No movies yet'; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; </p>
    </footer>
</body>
</html>
