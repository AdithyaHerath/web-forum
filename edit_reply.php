<?php
require_once 'includes/header.php';

if (!$logged_in) {
    header("location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: index.php");
    exit();
}

$reply_id = $_GET['id'];
$error = '';
$success = '';

// Get reply details
$reply_sql = "SELECT r.*, t.topic_id, t.title as topic_title 
              FROM replies r 
              JOIN topics t ON r.topic_id = t.topic_id 
              WHERE r.reply_id = ?";
$stmt = mysqli_prepare($conn, $reply_sql);
mysqli_stmt_bind_param($stmt, "i", $reply_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("location: index.php");
    exit();
}

$reply = mysqli_fetch_assoc($result);

// Check if user has permission to edit
if (!$is_admin && $_SESSION['user_id'] != $reply['user_id']) {
    header("location: topic.php?id=" . $reply['topic_id']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = trim($_POST['content']);
    
    if (empty($content)) {
        $error = "Reply content cannot be empty.";
    } else {
        // Update reply
        $update_sql = "UPDATE replies SET content = ? WHERE reply_id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "si", $content, $reply_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Reply updated successfully.";
            // Refresh reply data
            $stmt = mysqli_prepare($conn, $reply_sql);
            mysqli_stmt_bind_param($stmt, "i", $reply_id);
            mysqli_stmt_execute($stmt);
            $reply = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        } else {
            $error = "Something went wrong. Please try again later.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Edit Reply</h2>
        <p class="text-muted">
            Replying to: <a href="topic.php?id=<?php echo $reply['topic_id']; ?>">
                <?php echo htmlspecialchars($reply['topic_title']); ?>
            </a>
        </p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $reply_id); ?>" method="post">
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="6" required><?php echo htmlspecialchars($reply['content']); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Reply</button>
            <a href="topic.php?id=<?php echo $reply['topic_id']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 