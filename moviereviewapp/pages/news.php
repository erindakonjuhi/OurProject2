<?php

$news = new News();
$page = intval($_GET['p'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

$total = $news->getTotalNewsCount();
$news_list = $news->getAllNews($limit, $offset);
$total_pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News - Movie Review App</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            margin-bottom: 30px;
        }

        .news-item {
            background: #1a1a1a;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .news-image {
            width: 100%;
            height: 300px;
            background-color: #333;
            border-radius: 5px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .news-title {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .news-meta {
            color: #999;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .news-content {
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 20px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                <li><a href="?page=contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Latest News</h1>

        <?php if (!empty($news_list)): ?>
            <?php foreach ($news_list as $item): ?>
                <div class="news-item">
                    <?php if ($item['image']): ?>
                        <div class="news-image">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                    <h2 class="news-title"><?php echo htmlspecialchars($item['title']); ?></h2>
                    <div class="news-meta">
                        By <?php echo htmlspecialchars($item['created_by_username']); ?> • <?php echo date('M d, Y H:i', strtotime($item['created_at'])); ?>
                    </div>
                    <div class="news-content">
                        <?php echo nl2br(htmlspecialchars(substr($item['content'], 0, 500))); ?>
                        <?php if (strlen($item['content']) > 500): ?>
                            ...
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=news&p=<?php echo $page - 1; ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=news&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=news&p=<?php echo $page + 1; ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-message">No news yet</div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; </p>
    </footer>
</body>
</html>
