<?php
require_once 'includes/header.php';

if (!$logged_in || !$is_admin) {
    header("location: index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: admin.php");
    exit();
}

$category_id = $_GET['id'];
$error = '';
$success = '';

// Get category details
$category_sql = "SELECT * FROM categories WHERE category_id = ?";
$stmt = mysqli_prepare($conn, $category_sql);
mysqli_stmt_bind_param($stmt, "i", $category_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("location: admin.php");
    exit();
}

$category = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        // Update category
        $update_sql = "UPDATE categories SET name = ?, description = ? WHERE category_id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $category_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Category updated successfully.";
            // Refresh category data
            $stmt = mysqli_prepare($conn, $category_sql);
            mysqli_stmt_bind_param($stmt, "i", $category_id);
            mysqli_stmt_execute($stmt);
            $category = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        } else {
            $error = "Something went wrong. Please try again later.";
        }
    }
}

// Get topic count
$topics_sql = "SELECT COUNT(*) as count FROM topics WHERE category_id = ?";
$stmt = mysqli_prepare($conn, $topics_sql);
mysqli_stmt_bind_param($stmt, "i", $category_id);
mysqli_stmt_execute($stmt);
$topic_count = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['count'];
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Edit Category</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">
                    This category contains <?php echo $topic_count; ?> topics
                </h6>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $category_id); ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($category['name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Category</button>
                    <a href="admin.php" class="btn btn-secondary">Cancel</a>
                    <a href="delete_category.php?id=<?php echo $category_id; ?>" 
                       class="btn btn-danger float-end"
                       onclick="return confirm('Are you sure? This will delete all topics in this category!')">
                        Delete Category
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 