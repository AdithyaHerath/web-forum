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

// Check if reply exists and get topic_id
$reply_sql = "SELECT r.*, t.topic_id FROM replies r 
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

// Check if user has permission to delete
if (!$is_admin && $_SESSION['user_id'] != $reply['user_id']) {
    header("location: topic.php?id=" . $reply['topic_id']);
    exit();
}

// Delete reply
$delete_sql = "DELETE FROM replies WHERE reply_id = ?";
$stmt = mysqli_prepare($conn, $delete_sql);
mysqli_stmt_bind_param($stmt, "i", $reply_id);

if (mysqli_stmt_execute($stmt)) {
    header("location: topic.php?id=" . $reply['topic_id']);
    exit();
} else {
    echo "Error deleting reply.";
}
?> 