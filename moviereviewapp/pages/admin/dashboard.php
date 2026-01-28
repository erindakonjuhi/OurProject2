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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }

        h1 {
            margin-bottom: 30px;
            font-size: 32px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-label {
            color: #ddd;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background: #222;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #333;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-success {
            background: #28a745;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            background: #667eea;
        }

        .badge.admin {
            background: #dc3545;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            background: #1a1a1a;
            border: 1px solid #333;
            color: white;
            border-radius: 5px;
            font-family: inherit;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-container {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
        }

        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>🎬 Admin</h2>
            <ul>
                <li><a href="?page=dashboard&action=overview" class="<?php echo $action === 'overview' ? 'active' : ''; ?>">Overview</a></li>
                <li><a href="?page=dashboard&action=movies" class="<?php echo $action === 'movies' ? 'active' : ''; ?>">Movies</a></li>
                <li><a href="?page=dashboard&action=add-movie" class="<?php echo $action === 'add-movie' ? 'active' : ''; ?>">Add Movie</a></li>
                <li><a href="?page=dashboard&action=news" class="<?php echo $action === 'news' ? 'active' : ''; ?>">News</a></li>
                <li><a href="?page=dashboard&action=add-news" class="<?php echo $action === 'add-news' ? 'active' : ''; ?>">Add News</a></li>
                <li><a href="?page=dashboard&action=contacts" class="<?php echo $action === 'contacts' ? 'active' : ''; ?>">Messages</a></li>
                <li><a href="?page=dashboard&action=users" class="<?php echo $action === 'users' ? 'active' : ''; ?>">Users</a></li>
                <li><a href="?page=logout" style="margin-top: 20px; background: rgba(255, 255, 255, 0.2);">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-header">
                <h1><?php echo ucfirst(str_replace('-', ' ', $action)); ?></h1>
            </div>

            <?php if ($action === 'overview'): ?>
                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-label">Total Movies</div>
                        <div class="stat-number"><?php echo $total_movies; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total News</div>
                        <div class="stat-number"><?php echo $total_news; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Users</div>
                        <div class="stat-number"><?php echo $total_users; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Unread Messages</div>
                        <div class="stat-number"><?php echo $unread_messages; ?></div>
                    </div>
                </div>

            <?php elseif ($action === 'movies'): ?>
                <a href="?page=dashboard&action=add-movie" class="btn">Add New Movie</a>
                <br><br>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Director</th>
                            <th>Year</th>
                            <th>Rating</th>
                            <th>Added By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $movies_list = $movie->getAllMovies(100);
                        if (!empty($movies_list)):
                            foreach ($movies_list as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['title']); ?></td>
                                <td><?php echo htmlspecialchars($m['director'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($m['release_year'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($m['rating']); ?>/10</td>
                                <td><?php echo htmlspecialchars($m['created_by_username']); ?></td>
                                <td>
                                    <a href="?page=dashboard&action=edit-movie&id=<?php echo $m['id']; ?>" class="btn">Edit</a>
                                    <a href="?page=dashboard&action=delete-movie&id=<?php echo $m['id']; ?>" class="btn btn-danger" onclick="return confirm('Sure?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach;
                        else: ?>
                            <tr><td colspan="6">No movies yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'add-movie'): ?>
                <div class="form-container">
                    <form method="POST" action="">
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
                            <input type="number" name="release_year" min="1800" max="2100">
                        </div>
                        <div class="form-group">
                            <label>Genre</label>
                            <input type="text" name="genre">
                        </div>
                        <div class="form-group">
                            <label>Rating (0-10)</label>
                            <input type="number" name="rating" min="0" max="10" step="0.1">
                        </div>
                        <div class="form-group">
                            <label>Poster Image URL</label>
                            <input type="text" name="poster_image">
                        </div>
                        <button type="submit" class="btn btn-success">Add Movie</button>
                    </form>
                </div>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = $movie->addMovie(
                        $_POST['title'],
                        $_POST['description'],
                        $_POST['director'] ?? '',
                        $_POST['release_year'] ?? date('Y'),
                        $_POST['genre'] ?? '',
                        $_POST['poster_image'] ?? '',
                        $_POST['rating'] ?? 5,
                        $_SESSION['user_id']
                    );
                    if ($result['success']) {
                        echo '<script>alert("Movie added successfully!"); window.location="?page=dashboard&action=movies";</script>';
                    }
                }
                ?>

            <?php elseif ($action === 'news'): ?>
                <a href="?page=dashboard&action=add-news" class="btn">Add New News</a>
                <br><br>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $news_list = $news->getAllNews(100);
                        if (!empty($news_list)):
                            foreach ($news_list as $n): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($n['title']); ?></td>
                                <td><?php echo htmlspecialchars($n['created_by_username']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($n['created_at'])); ?></td>
                                <td>
                                    <a href="?page=dashboard&action=edit-news&id=<?php echo $n['id']; ?>" class="btn">Edit</a>
                                    <a href="?page=dashboard&action=delete-news&id=<?php echo $n['id']; ?>" class="btn btn-danger" onclick="return confirm('Sure?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach;
                        else: ?>
                            <tr><td colspan="4">No news yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'add-news'): ?>
                <div class="form-container">
                    <form method="POST" action="">
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
                            <input type="text" name="image">
                        </div>
                        <button type="submit" class="btn btn-success">Add News</button>
                    </form>
                </div>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = $news->addNews(
                        $_POST['title'],
                        $_POST['content'],
                        $_POST['image'] ?? '',
                        $_SESSION['user_id']
                    );
                    if ($result['success']) {
                        echo '<script>alert("News added successfully!"); window.location="?page=dashboard&action=news";</script>';
                    }
                }
                ?>

            <?php elseif ($action === 'contacts'): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $messages = $contact->getAllMessages(100);
                        if (!empty($messages)):
                            foreach ($messages as $msg): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars($msg['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                                <td>
                                    <a href="?page=dashboard&action=view-message&id=<?php echo $msg['id']; ?>" class="btn">View</a>
                                </td>
                            </tr>
                            <?php endforeach;
                        else: ?>
                            <tr><td colspan="6">No messages yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php elseif ($action === 'users'): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $users_list = $user_obj->getAllUsers();
                        foreach ($users_list as $u): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                            <td><span class="badge <?php echo $u['role'] === 'admin' ? 'admin' : ''; ?>"><?php echo htmlspecialchars($u['role']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                    <a href="?page=dashboard&action=edit-user&id=<?php echo $u['id']; ?>" class="btn">Edit</a>
                                    <a href="?page=dashboard&action=delete-user&id=<?php echo $u['id']; ?>" class="btn btn-danger" onclick="return confirm('Sure?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>
        </div>
    </div>
</body>
</html>
