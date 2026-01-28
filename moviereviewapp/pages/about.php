<?php

$db = Database::getInstance();

$result = $db->query('SELECT * FROM about_us ORDER BY created_at DESC LIMIT 1');
$about = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Movie Review App</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f0f0f;
            color: #000000;
        }

        header {
            background: blue;
            padding: 20px 0;
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
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .about-content {
            background: #1a1a1a;
            padding: 30px;
            border-radius: 8px;
        }

        .about-image {
            width: 100%;
            max-height: 400px;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .about-text {
            line-height: 1.8;
            color: #000000;
            font-size: 16px;
        }

        .empty-about {
            text-align: center;
            color: #000000;
            padding: 40px 20px;
        }

        footer {
            background: #ffffff;
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
        <h1>About EM reviews</h1>

        <?php if ($about): ?>
            <div class="about-content">
                <?php if ($about['image']): ?>
                    <div class="about-image">
                        <img src="<?php echo htmlspecialchars($about['image']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                <?php endif; ?>
                <div class="about-text">
                    <?php echo nl2br(htmlspecialchars($about['content'])); ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-about">
                No about information available yet.
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; </p>
    </footer>
</body>
</html>
