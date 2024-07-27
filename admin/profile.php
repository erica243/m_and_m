<?php
include 'db_connect.php';
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch the current admin details
$query = "SELECT * FROM users WHERE id = $admin_id AND type = 1";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$admin = $result->fetch_assoc();

if (!$admin) {
    echo "Admin not found.";
    exit();
}

$message = '';

if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $picture = $_FILES['picture'];

    // Validate inputs
    if (empty($name) || empty($username) || empty($password)) {
        $message = 'All fields are required.';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $target_file = $admin['picture']; // Keep the existing picture if no new one is uploaded

        // Handle profile picture upload
        if ($picture['size'] > 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($picture["name"]);
            if (!move_uploaded_file($picture["tmp_name"], $target_file)) {
                $message = 'Error uploading profile picture.';
            }
        }

        // Update the admin details in the database
        $query = "UPDATE users SET name = ?, username = ?, password = ?, picture = ? WHERE id = ? AND type = 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $name, $username, $password_hash, $target_file, $admin_id);

        if ($stmt->execute()) {
            $message = "Profile updated successfully.";
            // Update session variables if needed
            $_SESSION['login_name'] = $name;
        } else {
            $message = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Admin Profile</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form action="admin_profile.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="picture">Profile Picture:</label>
            <input type="file" class="form-control" id="picture" name="picture">
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
    </form>
</div>
</body>
</html>
