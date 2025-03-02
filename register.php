<?php
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if username exists
        $sql = "SELECT user_id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "This username is already taken.";
        } else {
            mysqli_stmt_close($stmt);
            
            // Check if email exists
            $sql = "SELECT user_id FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "This email is already registered.";
            } else {
                mysqli_stmt_close($stmt);
                
                // Insert new user
                $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $error = "Something went wrong. Please try again later.";
                }
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4">Register</h2>
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <?php if (!empty($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 