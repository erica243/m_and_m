<!-- reset_password.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Reset Password | M&M Cake Ordering System</title>
    <?php include('./header.php'); ?>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php
        include('./db_connect.php');
        
        if (!isset($_GET['token'])) {
            echo "<div class='alert alert-danger'>Invalid reset request.</div>";
            exit();
        }
        
        $token = $conn->real_escape_string($_GET['token']);
        $query = $conn->query("SELECT * FROM users 
                             WHERE temp_reset_token = '$token' 
                             AND temp_reset_token IS NOT NULL");
        
        if ($query->num_rows === 0) {
            echo "<div class='alert alert-danger'>Invalid or expired reset link.</div>";
            exit();
        }
        ?>
        
        <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" 
                       class="form-control" required minlength="8">
                <small class="form-text text-muted">
                    Password must be at least 8 characters long
                </small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       class="form-control" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($new_password !== $confirm_password) {
                echo "<div class='alert alert-danger'>Passwords do not match.</div>";
                exit();
            }
            
            if (strlen($new_password) < 8) {
                echo "<div class='alert alert-danger'>
                    Password must be at least 8 characters long.
                    </div>";
                exit();
            }
            
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update the password and clear reset tokens
            $conn->query("UPDATE users 
                         SET password = '$hashed_password', 
                             reset_code = NULL, 
                             reset_code_expiry = NULL, 
                             temp_reset_token = NULL 
                         WHERE temp_reset_token = '$token'");
            
            if ($conn->affected_rows > 0) {
                echo "<div class='alert alert-success'>
                    Password has been reset successfully. 
                    <a href='login.php'>Click here to login</a>
                    </div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to reset password.</div>";
            }
        }
        ?>
    </div>
</body>
</html>