<?php
require_once 'includes/header.php';

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