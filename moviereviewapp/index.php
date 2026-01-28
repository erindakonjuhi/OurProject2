<?php
session_start();
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'moviereviewapp');
define('BASE_URL', 'http://localhost/moviereviewapp/');

spl_autoload_register(function($class) {
    $path = __DIR__ . '/app/classes/' . $class . '.php';
    if (file_exists($path)) {
        require $path;
    }
});

Database::getInstance();

$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'home';
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$isLoggedIn = User::isLoggedIn();
$isAdmin = User::isAdmin();

switch ($page) {
    case 'home':
        require 'pages/home.php';
        break;
    case 'about':
        require 'pages/about.php';
        break;
    case 'movies':
        require 'pages/movies.php';
        break;
    case 'movie-detail':
        require 'pages/movie-detail.php';
        break;
    case 'news':
        require 'pages/news.php';
        break;
    case 'contact':
        require 'pages/contact.php';
        break;
    case 'login':
        if ($isLoggedIn) {
            header('Location: ?page=home');
            exit;
        }
        require 'pages/auth/login.php';
        break;
    case 'register':
        if ($isLoggedIn) {
            header('Location: ?page=home');
            exit;
        }
        require 'pages/auth/register.php';
        break;
    case 'profile':
        if (!$isLoggedIn) {
            header('Location: ?page=login');
            exit;
        }
        require 'pages/profile.php';
        break;
    case 'dashboard':
        if (!$isAdmin) {
            header('Location: ?page=login');
            exit;
        }
        require 'pages/admin/dashboard.php';
        break;
    case 'logout':
        session_destroy();
        header('Location: ?page=home');
        exit;
    default:
        require 'pages/home.php';
}
?>