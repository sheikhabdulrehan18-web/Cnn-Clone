<?php
require_once 'db.php';
 
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 
if ($article_id == 0) {
    header('Location: index.php');
    exit();
}
 
// Get article
$stmt = $conn->prepare("SELECT n.*, c.category_name 
                        FROM news n 
                        JOIN categories c ON n.category_id = c.id 
                        WHERE n.id = ?");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();
 
if ($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}
 
$article = $result->fetch_assoc();
$stmt->close();
 
// Get all categories for navigation
$categories_query = "SELECT * FROM categories";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
 
// Get related articles (same category, excluding current)
$related_stmt = $conn->prepare("SELECT n.*, c.category_name 
                                FROM news n 
                                JOIN categories c ON n.category_id = c.id 
                                WHERE n.category_id = ? AND n.id != ? 
                                ORDER BY n.created_at DESC 
                                LIMIT 4");
$related_stmt->bind_param("ii", $article['category_id'], $article_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
 
$related_articles = [];
if ($related_result->num_rows > 0) {
    while ($row = $related_result->fetch_assoc()) {
        $related_articles[] = $row;
    }
}
$related_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - CNN Clone</title>
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
                        <li><a href="category.php?id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></a></li>
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
 
    <article class="article-container">
        <div class="article-header">
            <span class="article-category"><?php echo htmlspecialchars($article['category_name']); ?></span>
            <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="article-meta">
                <span>Published: <?php echo date('F j, Y', strtotime($article['created_at'])); ?></span>
                <span>•</span>
                <a href="category.php?id=<?php echo $article['category_id']; ?>" style="color: var(--cnn-red);">
                    <?php echo htmlspecialchars($article['category_name']); ?>
                </a>
            </div>
        </div>
 
        <?php if ($article['image']): ?>
            <img src="<?php echo htmlspecialchars($article['image']); ?>" 
                 alt="<?php echo htmlspecialchars($article['title']); ?>" 
                 class="article-image"
                 onerror="this.src='https://via.placeholder.com/900x400?text=News+Image';">
        <?php else: ?>
            <img src="https://via.placeholder.com/900x400?text=News+Image" 
                 alt="News Image" 
                 class="article-image">
        <?php endif; ?>
 
        <div class="article-content">
            <?php 
            // Split content into paragraphs and display
            $paragraphs = explode("\n", $article['content']);
            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if (!empty($paragraph)) {
                    echo '<p>' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                }
            }
            ?>
        </div>
    </article>
 
    <?php if (!empty($related_articles)): ?>
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Related Articles</h2>
        </div>
        <div class="news-grid">
            <?php foreach ($related_articles as $related): ?>
                <article class="news-card">
                    <?php if ($related['image']): ?>
                        <img src="<?php echo htmlspecialchars($related['image']); ?>" 
                             alt="<?php echo htmlspecialchars($related['title']); ?>" 
                             class="news-card-image"
                             onerror="this.src='https://via.placeholder.com/400x200?text=News+Image';">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x200?text=News+Image" 
                             alt="News Image" 
                             class="news-card-image">
                    <?php endif; ?>
                    <div class="news-card-content">
                        <span class="news-card-category"><?php echo htmlspecialchars($related['category_name']); ?></span>
                        <h3 class="news-card-title">
                            <a href="article.php?id=<?php echo $related['id']; ?>">
                                <?php echo htmlspecialchars($related['title']); ?>
                            </a>
                        </h3>
                        <p class="news-card-excerpt">
                            <?php echo htmlspecialchars(substr($related['content'], 0, 120)) . '...'; ?>
                        </p>
                        <div class="news-card-date">
                            <?php echo date('F j, Y', strtotime($related['created_at'])); ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
 
    <footer>
        <p>&copy; 2024 CNN Clone. All rights reserved.</p>
    </footer>
 
    <script src="script.js"></script>
</body>
</html>
 
