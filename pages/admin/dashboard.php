<?php

if (!User::isAdmin()) {
    header('Location: ?page=login');
    exit;
}

$action = htmlspecialchars($_GET['action'] ?? 'overview');
$movie = new Movie();
$user_obj = new User();
$news = new News();
$contact = new Contact();

$total_movies = $movie->getTotalMoviesCount();
$total_news = $news->getTotalNewsCount();
$total_users = count($user_obj->getAllUsers());
$unread_messages = $contact->getUnreadCount();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_movie'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $director = htmlspecialchars($_POST['director']);
    $release_year = intval($_POST['release_year']);
    $genre = htmlspecialchars($_POST['genre']);
    $rating = floatval($_POST['rating']);
    $poster_image = htmlspecialchars($_POST['poster_image']);
    
    $result = $movie->addMovie($title, $description, $director, $release_year, $genre, $poster_image, $rating, $_SESSION['user_id']);
    if ($result['success']) {
        echo '<script>alert("Movie added successfully"); window.location.href="?page=dashboard&action=movies";</script>';
    } else {
        echo '<script>alert("Error adding movie: ' . implode(', ', $result['errors']) . '");</script>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])) {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $image_url = htmlspecialchars($_POST['image_url']);
    
    $result = $news->addNews($title, $content, $image_url, $_SESSION['user_id']);
    if ($result['success']) {
        echo '<script>alert("News added successfully"); window.location.href="?page=dashboard&action=news";</script>';
    } else {
        echo '<script>alert("Error adding news");</script>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_news'])) {
    $news_id = intval($_POST['news_id']);
    $result = $news->deleteNews($news_id);
    if ($result['success']) {
        echo '<script>alert("News deleted successfully"); window.location.href="?page=dashboard&action=news";</script>';
    } else {
        echo '<script>alert("Error deleting news");</script>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_movie'])) {
    $movie_id = intval($_POST['movie_id']);
    $result = $movie->deleteMovie($movie_id);
    if ($result['success']) {
        echo '<script>alert("Movie deleted successfully"); window.location.href="?page=dashboard&action=movies";</script>';
    } else {
        echo '<script>alert("Error deleting movie");</script>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_movie'])) {
    $movie_id = intval($_POST['movie_id']);
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $director = htmlspecialchars($_POST['director']);
    $release_year = intval($_POST['release_year']);
    $genre = htmlspecialchars($_POST['genre']);
    $rating = floatval($_POST['rating']);
    $poster_image = htmlspecialchars($_POST['poster_image']);
    
    $result = $movie->updateMovie($movie_id, $title, $description, $director, $release_year, $genre, $poster_image, $rating, $_SESSION['user_id']);
    if ($result['success']) {
        echo '<script>alert("Movie updated successfully"); window.location.href="?page=dashboard&action=movies";</script>';
    } else {
        echo '<script>alert("Error updating movie: ' . $result['error'] . '");</script>';
    }
}

$edit_movie = null;
if ($action === 'edit-movie' && isset($_GET['id'])) {
    $edit_movie = $movie->getMovieById(intval($_GET['id']));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin</h2>
            <ul>
                <li><a href="?page=dashboard&action=overview" class="<?php echo $action === 'overview' ? 'active' : ''; ?>">Overview</a></li>
                <li><a href="?page=dashboard&action=movies" class="<?php echo $action === 'movies' ? 'active' : ''; ?>">Movies</a></li>
                <li><a href="?page=dashboard&action=add-movie" class="<?php echo $action === 'add-movie' ? 'active' : ''; ?>">Add Movie</a></li>
                <li><a href="?page=dashboard&action=news" class="<?php echo $action === 'news' ? 'active' : ''; ?>">News</a></li>
                <li><a href="?page=dashboard&action=add-news" class="<?php echo $action === 'add-news' ? 'active' : ''; ?>">Add News</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
            </div>

            <?php if ($action === 'overview'): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Movies</h3>
                        <p><?php echo $total_movies; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total News</h3>
                        <p><?php echo $total_news; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Users</h3>
                        <p><?php echo $total_users; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Unread Messages</h3>
                        <p><?php echo $unread_messages; ?></p>
                    </div>
                </div>

            <?php elseif ($action === 'movies'): ?>
                <h2>Manage Movies</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Director</th>
                            <th>Year</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $all_movies = $movie->getAllMovies(100);
                        foreach ($all_movies as $m): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['title']); ?></td>
                                <td><?php echo htmlspecialchars($m['director']); ?></td>
                                <td><?php echo htmlspecialchars($m['release_year']); ?></td>
                                <td><?php echo htmlspecialchars($m['rating']); ?>/10</td>
                                <td>
                                    <a href="?page=dashboard&action=edit-movie&id=<?php echo $m['id']; ?>" class="btn btn-sm">Edit</a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this movie?');">
                                        <input type="hidden" name="movie_id" value="<?php echo $m['id']; ?>">
                                        <button type="submit" name="delete_movie" class="btn btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'add-movie'): ?>
                <h2>Add New Movie</h2>
                <form method="POST" class="admin-form">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Director</label>
                        <input type="text" name="director">
                    </div>
                    <div class="form-group">
                        <label>Release Year</label>
                        <input type="number" name="release_year" required>
                    </div>
                    <div class="form-group">
                        <label>Genre</label>
                        <input type="text" name="genre">
                    </div>
                    <div class="form-group">
                        <label>Rating (0-10)</label>
                        <input type="number" name="rating" step="0.1" min="0" max="10" required>
                    </div>
                    <div class="form-group">
                        <label>Poster Image URL</label>
                        <input type="url" name="poster_image" placeholder="https://example.com/image.jpg">
                    </div>
                    <button type="submit" name="add_movie" class="btn">Add Movie</button>
                </form>

            <?php elseif ($action === 'edit-movie' && $edit_movie): ?>
                <h2>Edit Movie</h2>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="movie_id" value="<?php echo $edit_movie['id']; ?>">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($edit_movie['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required><?php echo htmlspecialchars($edit_movie['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Director</label>
                        <input type="text" name="director" value="<?php echo htmlspecialchars($edit_movie['director']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Release Year</label>
                        <input type="number" name="release_year" value="<?php echo $edit_movie['release_year']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Genre</label>
                        <input type="text" name="genre" value="<?php echo htmlspecialchars($edit_movie['genre']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Rating (0-10)</label>
                        <input type="number" name="rating" step="0.1" min="0" max="10" value="<?php echo $edit_movie['rating']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Poster Image URL</label>
                        <input type="url" name="poster_image" value="<?php echo htmlspecialchars($edit_movie['poster_image'] ?? ''); ?>">
                    </div>
                    <button type="submit" name="update_movie" class="btn">Update Movie</button>
                    <a href="?page=dashboard&action=movies" class="btn btn-secondary">Cancel</a>
                </form>

            <?php elseif ($action === 'news'): ?>
                <h2>Manage News</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $all_news = $news->getAllNews(100);
                        foreach ($all_news as $n): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($n['title']); ?></td>
                                <td><?php echo htmlspecialchars($n['created_at']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this news?');">
                                        <input type="hidden" name="news_id" value="<?php echo $n['id']; ?>">
                                        <button type="submit" name="delete_news" class="btn btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'add-news'): ?>
                <h2>Add New News</h2>
                <form method="POST" class="admin-form">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="content" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="url" name="image_url" placeholder="https://example.com/image.jpg">
                    </div>
                    <button type="submit" name="add_news" class="btn">Add News</button>
                </form>

            <?php endif; ?>
        </div>
    </div>

    <script src="javascripts/main.js"></script>
</body>
</html>

<?php
echo "Merisa ka punuar këtu!";
?>
