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
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            // Verify the token
            include('./db_connect.php');
            $query = $conn->query("SELECT * FROM users WHERE reset_token = '$token'");

            if ($query->num_rows > 0) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $conn->query("UPDATE users SET password = '$new_password', reset_token = NULL WHERE reset_token = '$token'");
                    echo "<div class='alert alert-success'>Your password has been reset successfully.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Invalid token.</div>";
            }
        }
        ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>
</body>
</html>
