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

$topic_id = $_GET['id'];

// Check if topic exists and get category_id
$topic_sql = "SELECT t.*, c.category_id FROM topics t 
              JOIN categories c ON t.category_id = c.category_id 
              WHERE t.topic_id = ?";
$stmt = mysqli_prepare($conn, $topic_sql);
mysqli_stmt_bind_param($stmt, "i", $topic_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("location: index.php");
    exit();
}

$topic = mysqli_fetch_assoc($result);

// Check if user has permission to delete
if (!$is_admin && $_SESSION['user_id'] != $topic['user_id']) {
    header("location: topic.php?id=" . $topic_id);
    exit();
}

// Delete topic (replies will be deleted automatically due to CASCADE)
$delete_sql = "DELETE FROM topics WHERE topic_id = ?";
$stmt = mysqli_prepare($conn, $delete_sql);
mysqli_stmt_bind_param($stmt, "i", $topic_id);

if (mysqli_stmt_execute($stmt)) {
    header("location: category.php?id=" . $topic['category_id']);
    exit();
} else {
    echo "Error deleting topic.";
}
?> 