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
