<?php
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero-section my-5">
    <!-- Welcome Message -->
    <div class="welcome-message bg-light p-4 mb-4 rounded-3 shadow-sm text-center">
        <h1 class="display-5 fw-bold">Welcome to Our Forum</h1>
        <p class="fs-5 text-muted">Join discussions, share knowledge, and explore topics.</p>
    </div>

    <!-- Image Carousel -->
    <div id="heroCarousel" class="carousel slide shadow-sm" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner rounded-3">
            <div class="carousel-item active">
                <img src="images/ti.jpg" class="d-block w-100" alt="Tiger Image">
                <!-- Optional Caption -->
                <!-- <div class="carousel-caption d-none d-md-block">
                    <h5>First slide label</h5>
                    <p>Some representative placeholder content for the first slide.</p>
                </div> -->
            </div>
            <div class="carousel-item">
                <img src="images/tur.jpg" class="d-block w-100" alt="Turtle Image">
                 <!-- Optional Caption -->
            </div>
            <div class="carousel-item">
                <img src="images/wo.jpg" class="d-block w-100" alt="Wolf Image">
                 <!-- Optional Caption -->
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
<!-- End Hero Section -->


<?php
// Get all categories
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_sql);
?>

<h2 class="mb-4">Forum Categories</h2>

<?php if ($logged_in): ?>
    <div class="mb-4">
        <a href="create_topic.php" class="btn btn-primary">Create New Topic</a>
    </div>
<?php endif; ?>

<?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
    <div class="category-box">
        <h3>
            <a href="category.php?id=<?php echo $category['category_id']; ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </a>
        </h3>
        <p><?php echo htmlspecialchars($category['description']); ?></p>
        
        <?php
        // Get latest topics for this category
        $topics_sql = "SELECT t.*, u.username, 
                      (SELECT COUNT(*) FROM replies WHERE topic_id = t.topic_id) as reply_count 
                      FROM topics t 
                      JOIN users u ON t.user_id = u.user_id 
                      WHERE t.category_id = ? 
                      ORDER BY t.created_at DESC 
                      LIMIT 3";
        
        $stmt = mysqli_prepare($conn, $topics_sql);
        mysqli_stmt_bind_param($stmt, "i", $category['category_id']);
        mysqli_stmt_execute($stmt);
        $topics_result = mysqli_stmt_get_result($stmt);
        ?>
        
        <?php if (mysqli_num_rows($topics_result) > 0): ?>
            <div class="latest-topics">
                <h5>Latest Topics:</h5>
                <ul class="list-unstyled">
                    <?php while ($topic = mysqli_fetch_assoc($topics_result)): ?>
                        <li>
                            <a href="topic.php?id=<?php echo $topic['topic_id']; ?>">
                                <?php echo htmlspecialchars($topic['title']); ?>
                            </a>
                            <span class="metadata">
                                by <?php echo htmlspecialchars($topic['username']); ?> | 
                                <?php echo date('M j, Y', strtotime($topic['created_at'])); ?> | 
                                <?php echo $topic['reply_count']; ?> replies
                            </span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php else: ?>
            <p class="text-muted">No topics yet</p>
        <?php endif; ?>
        
        <a href="category.php?id=<?php echo $category['category_id']; ?>" class="btn btn-sm btn-outline-primary">
            View All Topics
        </a>
    </div>
<?php endwhile; ?>

<?php require_once 'includes/footer.php'; ?>
