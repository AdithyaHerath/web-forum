<?php
require_once 'config.php';

// Admin credentials
$username = 'admin';
$password = 'admin';
$email = 'admin@example.com';

// Generate password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// First, delete existing admin user if exists
$delete_sql = "DELETE FROM users WHERE username = 'admin'";
mysqli_query($conn, $delete_sql);

// Insert new admin user
$sql = "INSERT INTO users (username, password, email, is_admin) VALUES (?, ?, ?, TRUE)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $email);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin user created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin<br>";
} else {
    echo "Error creating admin user: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 