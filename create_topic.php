<?php
require_once 'includes/header.php';

if (!$logged_in) {
    header("location: login.php");
    exit();
}

$error = '';
$success = '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

// Get categories for dropdown
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = $_POST['category_id'];
    
    if (empty($title) || empty($content) || empty($category_id)) {
        $error = "Please fill in all fields.";
    } else {
        // Verify category exists
        $category_check = mysqli_prepare($conn, "SELECT category_id FROM categories WHERE category_id = ?");
        mysqli_stmt_bind_param($category_check, "i", $category_id);
        mysqli_stmt_execute($category_check);
        mysqli_stmt_store_result($category_check);
        
        if (mysqli_stmt_num_rows($category_check) == 0) {
            $error = "Invalid category selected.";
        } else {
            // Create new topic
            $sql = "INSERT INTO topics (category_id, user_id, title, content) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiss", $category_id, $_SESSION['user_id'], $title, $content);
            
            if (mysqli_stmt_execute($stmt)) {
                $topic_id = mysqli_insert_id($conn);
                header("location: topic.php?id=" . $topic_id);
                exit();
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Create New Topic</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?php echo $category['category_id']; ?>" 
                                <?php echo ($category_id == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Topic</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 