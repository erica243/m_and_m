<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Forgot Password | M&M Cake Ordering System</title>
    <?php include('./header.php'); ?>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form method="POST" action="forgot_password.php">
            <div class="form-group">
                <label for="username">Enter your email address:</label>
                <input type="username" id="username" name="username" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include('./db_connect.php');

            $username = $conn->real_escape_string($_POST['username']);
            $query = $conn->query("SELECT * FROM users WHERE username = '$username'");

            if ($query->num_rows > 0) {
                // Generate a reset link
                $token = bin2hex(random_bytes(50));
                $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";

                // Save the token in the database (ensure you have a column for this)
                $conn->query("UPDATE users SET reset_token = '$token' WHERE username = '$username'");

                // Send the reset link to the user's email
                $subject = "Password Reset";
                $message = "Click the link to reset your password: $reset_link";
                // Use your mailing function (e.g., PHPMailer) here to send the email
                // mail($email, $subject, $message);

                echo "<div class='alert alert-success'>A reset link has been sent to your email.</div>";
            } else {
                echo "<div class='alert alert-danger'>This email address is not registered.</div>";
            }
        }
        ?>
    </div>
</body>
</html>
