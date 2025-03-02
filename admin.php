<?php
require_once 'includes/header.php';

// Check if user is admin
if (!$logged_in || !$is_admin) {
    header("location: index.php");
    exit();
}

$error = '';
$success = '';

// Handle new category creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'create_category') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        if (empty($name)) {
            $error = "Category name is required.";
        } else {
            $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $name, $description);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Category created successfully.";
                $_POST = array();
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        }
    }
}

// Get statistics
$stats = array();

// Total users
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$stats['users'] = mysqli_fetch_assoc($result)['count'];

// Total topics
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM topics");
$stats['topics'] = mysqli_fetch_assoc($result)['count'];

// Total replies
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM replies");
$stats['replies'] = mysqli_fetch_assoc($result)['count'];

// Get categories
$categories_sql = "SELECT c.*, 
                   (SELECT COUNT(*) FROM topics WHERE category_id = c.category_id) as topic_count 
                   FROM categories c 
                   ORDER BY c.name";
$categories_result = mysqli_query($conn, $categories_sql);

// Get recent topics
$recent_topics_sql = "SELECT t.*, c.name as category_name, u.username 
                      FROM topics t 
                      JOIN categories c ON t.category_id = c.category_id 
                      JOIN users u ON t.user_id = u.user_id 
                      ORDER BY t.created_at DESC 
                      LIMIT 5";
$recent_topics_result = mysqli_query($conn, $recent_topics_sql);

// Get recent users
$recent_users_sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users_result = mysqli_query($conn, $recent_users_sql);
?>

<h2 class="mb-4">Admin Panel</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <p class="card-text display-4"><?php echo $stats['users']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Topics</h5>
                <p class="card-text display-4"><?php echo $stats['topics']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Replies</h5>
                <p class="card-text display-4"><?php echo $stats['replies']; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title h5 mb-0">Create New Category</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="action" value="create_category">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </form>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title h5 mb-0">Recent Users</h3>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php while ($user = mysqli_fetch_assoc($recent_users_result)): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($user['username']); ?></h6>
                                    <small class="text-muted">
                                        Joined <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                    </small>
                                </div>
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge bg-primary">Admin</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title h5 mb-0">Categories</h3>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($category['name']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo $category['topic_count']; ?> topics
                                    </small>
                                </div>
                                <div>
                                    <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="delete_category.php?id=<?php echo $category['category_id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure? This will delete all topics in this category!')">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title h5 mb-0">Recent Topics</h3>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php while ($topic = mysqli_fetch_assoc($recent_topics_result)): ?>
                        <div class="list-group-item">
                            <h6 class="mb-1">
                                <a href="topic.php?id=<?php echo $topic['topic_id']; ?>">
                                    <?php echo htmlspecialchars($topic['title']); ?>
                                </a>
                            </h6>
                            <small class="text-muted">
                                in <?php echo htmlspecialchars($topic['category_name']); ?> | 
                                by <?php echo htmlspecialchars($topic['username']); ?> | 
                                <?php echo date('M j, Y', strtotime($topic['created_at'])); ?>
                            </small>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 