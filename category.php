<?php
require_once 'db.php';
 
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 
if ($category_id == 0) {
    header('Location: index.php');
    exit();
}
 
// Get category information
$cat_stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$cat_stmt->bind_param("i", $category_id);
$cat_stmt->execute();
$category_result = $cat_stmt->get_result();
 
if ($category_result->num_rows == 0) {
    header('Location: index.php');
    exit();
}
 
$category = $category_result->fetch_assoc();
$cat_stmt->close();
 
// Get news for this category
$news_stmt = $conn->prepare("SELECT n.*, c.category_name 
                             FROM news n 
                             JOIN categories c ON n.category_id = c.id 
                             WHERE n.category_id = ? 
                             ORDER BY n.created_at DESC");
$news_stmt->bind_param("i", $category_id);
$news_stmt->execute();
$news_result = $news_stmt->get_result();
 
$news_items = [];
if ($news_result->num_rows > 0) {
    while ($row = $news_result->fetch_assoc()) {
        $news_items[] = $row;
    }
}
$news_stmt->close();
 
// Get all categories for navigation
$categories_query = "SELECT * FROM categories";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['category_name']); ?> - CNN Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">CNN CLONE</a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php foreach ($categories as $cat): ?>
                        <li><a href="category.php?id=<?php echo $cat['id']; ?>" 
                               <?php echo ($cat['id'] == $category_id) ? 'style="border-color: var(--cnn-red); color: var(--cnn-red);"' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <div class="user-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span style="color: white; padding: 10px 20px;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
 
    <div class="category-header">
        <h1><?php echo htmlspecialchars($category['category_name']); ?></h1>
    </div>
 
    <div class="container">
        <?php if (!empty($news_items)): ?>
            <div class="news-grid">
                <?php foreach ($news_items as $news): ?>
                    <article class="news-card">
                        <?php if ($news['image']): ?>
                            <img src="<?php echo htmlspecialchars($news['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($news['title']); ?>" 
                                 class="news-card-image"
                                 onerror="this.src='https://via.placeholder.com/400x200?text=News+Image';">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x200?text=News+Image" 
                                 alt="News Image" 
                                 class="news-card-image">
                        <?php endif; ?>
                        <div class="news-card-content">
                            <span class="news-card-category"><?php echo htmlspecialchars($news['category_name']); ?></span>
                            <h3 class="news-card-title">
                                <a href="article.php?id=<?php echo $news['id']; ?>">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </a>
                            </h3>
                            <p class="news-card-excerpt">
                                <?php echo htmlspecialchars(substr($news['content'], 0, 120)) . '...'; ?>
                            </p>
                            <div class="news-card-date">
                                <?php echo date('F j, Y', strtotime($news['created_at'])); ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px;">
                <p style="font-size: 1.2rem; color: #666;">No news found in this category yet.</p>
                <a href="index.php" style="display: inline-block; margin-top: 20px; color: var(--cnn-red);">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
 
    <footer>
        <p>&copy; 2024 CNN Clone. All rights reserved.</p>
    </footer>
 
    <script src="script.js"></script>
</body>
</html>
 
 
