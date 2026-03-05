<?php
require_once 'db.php';
 
// Fetch featured news (latest 5)
$featured_query = "SELECT n.*, c.category_name 
                   FROM news n 
                   JOIN categories c ON n.category_id = c.id 
                   ORDER BY n.created_at DESC 
                   LIMIT 5";
$featured_result = $conn->query($featured_query);
 
// Fetch latest news (all, excluding featured)
$latest_query = "SELECT n.*, c.category_name 
                 FROM news n 
                 JOIN categories c ON n.category_id = c.id 
                 ORDER BY n.created_at DESC 
                 LIMIT 20";
$latest_result = $conn->query($latest_query);
 
// Fetch breaking news (latest 3)
$breaking_query = "SELECT n.*, c.category_name 
                   FROM news n 
                   JOIN categories c ON n.category_id = c.id 
                   ORDER BY n.created_at DESC 
                   LIMIT 3";
$breaking_result = $conn->query($breaking_query);
 
// Get all categories
$categories_query = "SELECT * FROM categories";
$categories_result = $conn->query($categories_query);
 
$featured_news = [];
$latest_news = [];
$breaking_news = [];
$categories = [];
 
if ($featured_result && $featured_result->num_rows > 0) {
    while ($row = $featured_result->fetch_assoc()) {
        $featured_news[] = $row;
    }
}
 
if ($latest_result && $latest_result->num_rows > 0) {
    while ($row = $latest_result->fetch_assoc()) {
        $latest_news[] = $row;
    }
}
 
if ($breaking_result && $breaking_result->num_rows > 0) {
    while ($row = $breaking_result->fetch_assoc()) {
        $breaking_news[] = $row;
    }
}
 
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
    <title>CNN Clone - Breaking News</title>
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
 
    <?php if (!empty($breaking_news)): ?>
    <div class="breaking-news">
        <div class="breaking-news-container">
            <span class="breaking-label">BREAKING</span>
            <?php 
            $breaking_titles = [];
            foreach ($breaking_news as $news) {
                $breaking_titles[] = htmlspecialchars($news['title']);
            }
            echo implode(' • ', $breaking_titles);
            ?>
        </div>
    </div>
    <?php endif; ?>
 
    <?php if (!empty($featured_news)): ?>
    <div class="hero-slider">
        <div class="slider-container">
            <?php foreach ($featured_news as $news): ?>
            <div class="slide" style="background-image: url('<?php echo htmlspecialchars($news['image']); ?>');">
                <div class="slide-overlay">
                    <h2 class="slide-title"><?php echo htmlspecialchars($news['title']); ?></h2>
                    <p class="slide-content"><?php echo htmlspecialchars(substr($news['content'], 0, 150)) . '...'; ?></p>
                    <a href="article.php?id=<?php echo $news['id']; ?>" class="slide-link">Read More</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="slider-nav">
            <?php for ($i = 0; $i < count($featured_news); $i++): ?>
                <div class="slider-dot" data-slide="<?php echo $i; ?>"></div>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
 
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Latest News</h2>
        </div>
 
        <div class="news-grid">
            <?php if (!empty($latest_news)): ?>
                <?php foreach ($latest_news as $news): ?>
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
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; padding: 40px;">No news available at the moment.</p>
            <?php endif; ?>
        </div>
 
        <?php if (!empty($categories)): ?>
        <div class="section-header">
            <h2 class="section-title">Browse by Category</h2>
        </div>
        <div class="news-grid">
            <?php foreach ($categories as $cat): ?>
                <article class="news-card" style="min-height: 200px; justify-content: center; align-items: center; text-align: center;">
                    <div class="news-card-content">
                        <h3 class="news-card-title">
                            <a href="category.php?id=<?php echo $cat['id']; ?>" style="color: var(--cnn-red); font-size: 1.5rem;">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </a>
                        </h3>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
 
    <footer>
        <p>&copy; 2024 CNN Clone. All rights reserved.</p>
    </footer>
 
    <script src="script.js"></script>
</body>
</html>
 
 
