<?php
require_once 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: index.php");
    exit();
}

$category_id = $_GET['id'];

// Get category details
$category_sql = "SELECT * FROM categories WHERE category_id = ?";
$stmt = mysqli_prepare($conn, $category_sql);
mysqli_stmt_bind_param($stmt, "i", $category_id);
mysqli_stmt_execute($stmt);
$category_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($category_result) == 0) {
    header("location: index.php");
    exit();
}

$category = mysqli_fetch_assoc($category_result);

// Get all topics in this category
$topics_sql = "SELECT t.*, u.username, 
              (SELECT COUNT(*) FROM replies WHERE topic_id = t.topic_id) as reply_count 
              FROM topics t 
              JOIN users u ON t.user_id = u.user_id 
              WHERE t.category_id = ? 
              ORDER BY t.created_at DESC";

$stmt = mysqli_prepare($conn, $topics_sql);
mysqli_stmt_bind_param($stmt, "i", $category_id);
mysqli_stmt_execute($stmt);
$topics_result = mysqli_stmt_get_result($stmt);
?>

<div class="mb-4">
    <h2><?php echo htmlspecialchars($category['name']); ?></h2>
    <p class="text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
</div>

<?php if ($logged_in): ?>
    <div class="mb-4">
        <a href="create_topic.php?category_id=<?php echo $category_id; ?>" class="btn btn-primary">
            Create New Topic
        </a>
    </div>
<?php endif; ?>

<?php if (mysqli_num_rows($topics_result) > 0): ?>
    <?php while ($topic = mysqli_fetch_assoc($topics_result)): ?>
        <div class="topic-container">
            <div class="d-flex justify-content-between align-items-center">
                <h3>
                    <a href="topic.php?id=<?php echo $topic['topic_id']; ?>">
                        <?php echo htmlspecialchars($topic['title']); ?>
                    </a>
                </h3>
                <?php if ($logged_in && ($is_admin || $_SESSION['user_id'] == $topic['user_id'])): ?>
                    <div class="action-buttons">
                        <a href="edit_topic.php?id=<?php echo $topic['topic_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="delete_topic.php?id=<?php echo $topic['topic_id']; ?>" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Are you sure you want to delete this topic?')">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="metadata">
                Posted by <?php echo htmlspecialchars($topic['username']); ?> | 
                <?php echo date('M j, Y', strtotime($topic['created_at'])); ?> | 
                <?php echo $topic['reply_count']; ?> replies
            </div>
            <div class="mt-2">
                <?php echo nl2br(htmlspecialchars(substr($topic['content'], 0, 200))); ?>
                <?php if (strlen($topic['content']) > 200): ?>
                    ... <a href="topic.php?id=<?php echo $topic['topic_id']; ?>">Read more</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No topics in this category yet.</p>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?> 