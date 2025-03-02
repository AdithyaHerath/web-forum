<?php
require_once 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: index.php");
    exit();
}

$topic_id = $_GET['id'];
$error = '';
$success = '';

// Get topic details
$topic_sql = "SELECT t.*, c.name as category_name, c.category_id, u.username 
              FROM topics t 
              JOIN categories c ON t.category_id = c.category_id 
              JOIN users u ON t.user_id = u.user_id 
              WHERE t.topic_id = ?";

$stmt = mysqli_prepare($conn, $topic_sql);
mysqli_stmt_bind_param($stmt, "i", $topic_id);
mysqli_stmt_execute($stmt);
$topic_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($topic_result) == 0) {
    header("location: index.php");
    exit();
}

$topic = mysqli_fetch_assoc($topic_result);

// Handle new reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $logged_in) {
    $reply_content = trim($_POST['content']);
    
    if (empty($reply_content)) {
        $error = "Reply content cannot be empty.";
    } else {
        $sql = "INSERT INTO replies (topic_id, user_id, content) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $topic_id, $_SESSION['user_id'], $reply_content);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Reply posted successfully.";
            // Clear the form
            $_POST['content'] = '';
        } else {
            $error = "Something went wrong. Please try again later.";
        }
    }
}

// Get replies
$replies_sql = "SELECT r.*, u.username 
                FROM replies r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.topic_id = ? 
                ORDER BY r.created_at ASC";

$stmt = mysqli_prepare($conn, $replies_sql);
mysqli_stmt_bind_param($stmt, "i", $topic_id);
mysqli_stmt_execute($stmt);
$replies_result = mysqli_stmt_get_result($stmt);
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">
                <a href="category.php?id=<?php echo $topic['category_id']; ?>">
                    <?php echo htmlspecialchars($topic['category_name']); ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo htmlspecialchars($topic['title']); ?>
            </li>
        </ol>
    </nav>
</div>

<div class="topic-container">
    <div class="d-flex justify-content-between align-items-center">
        <h2><?php echo htmlspecialchars($topic['title']); ?></h2>
        <?php if ($logged_in && ($is_admin || $_SESSION['user_id'] == $topic['user_id'])): ?>
            <div class="action-buttons">
                <a href="edit_topic.php?id=<?php echo $topic_id; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="delete_topic.php?id=<?php echo $topic_id; ?>" 
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Are you sure you want to delete this topic?')">Delete</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="metadata mb-3">
        Posted by <?php echo htmlspecialchars($topic['username']); ?> | 
        <?php echo date('M j, Y', strtotime($topic['created_at'])); ?>
    </div>
    <div class="content mb-4">
        <?php echo nl2br(htmlspecialchars($topic['content'])); ?>
    </div>
</div>

<h3 class="mb-4">Replies</h3>

<?php while ($reply = mysqli_fetch_assoc($replies_result)): ?>
    <div class="reply-container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="metadata">
                <?php echo htmlspecialchars($reply['username']); ?> | 
                <?php echo date('M j, Y', strtotime($reply['created_at'])); ?>
            </div>
            <?php if ($logged_in && ($is_admin || $_SESSION['user_id'] == $reply['user_id'])): ?>
                <div class="action-buttons">
                    <a href="edit_reply.php?id=<?php echo $reply['reply_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <a href="delete_reply.php?id=<?php echo $reply['reply_id']; ?>" 
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Are you sure you want to delete this reply?')">Delete</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="content mt-2">
            <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
        </div>
    </div>
<?php endwhile; ?>

<?php if ($logged_in): ?>
    <div class="mt-4">
        <h4>Post a Reply</h4>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $topic_id); ?>" method="post">
            <div class="mb-3">
                <textarea class="form-control" name="content" rows="4" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Reply</button>
        </form>
    </div>
<?php else: ?>
    <div class="mt-4">
        <p>Please <a href="login.php">login</a> to post a reply.</p>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?> 