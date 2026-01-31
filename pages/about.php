<?php

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Movie Review App</title>
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

    <section class="about-section">
        <div class="container">
            <h1>About EM Reviews</h1>
            <p>Welcome to EM Reviews - Your ultimate destination for movie reviews and ratings!</p>
            
            <div class="about-content">
                <h2>Our Mission</h2>
                <p>We are dedicated to providing a platform where movie enthusiasts can discover, rate, and review their favorite films. Our mission is to create a community where passionate movie lovers can share their opinions and help others find their next favorite movie.</p>

                <h2>What We Offer</h2>
                <ul>
                    <li>Comprehensive movie database with ratings and reviews</li>
                    <li>User-friendly interface for rating and reviewing movies</li>
                    <li>Latest news and updates from the entertainment industry</li>
                    <li>Community-driven content and discussions</li>
                    <li>Expert recommendations and curated lists</li>
                </ul>

                <h2>Our Community</h2>
                <p>EM Reviews is powered by movie enthusiasts like you! Join thousands of users who share their passion for cinema. Whether you're a casual moviegoer or a film critic, our platform welcomes everyone.</p>

                <h2>Get Involved</h2>
                <p>Share your thoughts on your favorite movies, discover new films recommended by our community, and connect with fellow movie lovers. <a href="?page=register">Sign up today</a> to start your journey with us!</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 EM Reviews. All rights reserved.</p>
    </footer>

    <script src="javascripts/main.js"></script>
</body>
</html>