<?php


if (!$isLoggedIn) {
    header('Location: ?page=login');
    exit;
}

$user = new User();
$user_data = $user->getUserById($_SESSION['user_id']);
$review = new Review();
$user_reviews = $review->getUserReviews($_SESSION['user_id'], 10);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = htmlspecialchars(trim($_POST['full_name'] ?? ''));
        $email = htmlspecialchars(trim($_POST['email'] ?? ''));

        $result = $user->updateProfile($_SESSION['user_id'], $full_name, $email);
        if ($result['success']) {
            $success = true;
            $user_data = $user->getUserById($_SESSION['user_id']);
        } else {
            $errors = $result['errors'];
        }
    } elseif (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $result = $user->changePassword($_SESSION['user_id'], $old_password, $new_password, $confirm_password);
        if ($result['success']) {
            $success = true;
        } else {
            $errors = $result['errors'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Movie Review App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
        <nav>
            <div class="logo">EM reviews</div>
            <ul>
                <li><a href="?page=home">Home</a></li>
                <li><a href="?page=movies">Movies</a></li>
                <li><a href="?page=profile">Profile</a></li>
                <li><a href="?page=logout">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="profile-content">
            <div class="sidebar">
                <div class="profile-info">
                    <label>Username</label>
                    <div class="value"><?php echo htmlspecialchars($user_data['username']); ?></div>
                </div>
                <div class="profile-info">
                    <label>Email</label>
                    <div class="value"><?php echo htmlspecialchars($user_data['email']); ?></div>
                </div>
                <div class="profile-info">
                    <label>Full Name</label>
                    <div class="value"><?php echo htmlspecialchars($user_data['full_name']); ?></div>
                </div>
                <div class="profile-info">
                    <label>Role</label>
                    <div class="value"><?php echo htmlspecialchars($user_data['role']); ?></div>
                </div>
                <div class="profile-info">
                    <label>Member Since</label>
                    <div class="value"><?php echo date('M d, Y', strtotime($user_data['created_at'])); ?></div>
                </div>
            </div>

            <div class="main">
                <?php if ($success): ?>
                    <div class="success-message">Changes saved successfully!</div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="error-message">
                        <ul class="error-list">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="form-section">
                    <h2>Edit Profile</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>

                        <button type="submit" name="update_profile">Update Profile</button>
                    </form>
                </div>

                <div class="form-section">
                    <h2>Change Password</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="old_password">Current Password</label>
                            <input type="password" id="old_password" name="old_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" name="change_password">Change Password</button>
                    </form>
                </div>

                <div class="form-section">
                    <h2>Your Reviews</h2>
                    <?php if (!empty($user_reviews)): ?>
                        <div class="reviews-list">
                            <?php foreach ($user_reviews as $r): ?>
                                <div class="review-item">
                                    <div class="review-movie"><?php echo htmlspecialchars($r['movie_title']); ?></div>
                                    <div class="review-rating">★ <?php echo htmlspecialchars($r['rating']); ?>/10</div>
                                    <div style="color: #ccc; margin-top: 10px;"><?php echo htmlspecialchars(substr($r['review_text'], 0, 100)); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #999;">You haven't written any reviews yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; </p>
    </footer>
</body>
</html>
