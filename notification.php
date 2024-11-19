<?php
session_start();
include 'db_connect.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['login_user_id'])) {
    header("Location: index.php?page=home"); // Redirect to home if not logged in
    exit();
}

// Get the logged-in user's ID
$userId = $_SESSION['login_user_id'];

// Fetch notifications for the logged-in user
function getNotifications($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result();
}

$notifications = getNotifications($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="path/to/font-awesome.css"> <!-- Include Font Awesome CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
    <script src="path/to/bootstrap.js"></script> <!-- Include Bootstrap JS -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .notification-item:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Your Notifications</h2>
        <?php if ($notifications->num_rows > 0): ?>
            <?php while ($row = $notifications->fetch_assoc()): ?>
                <div class="notification-item" onclick="markAsRead(<?php echo $row['id']; ?>)">
                    <p><?php echo $row['message']; ?></p>
                    <small><?php echo $row['created_at']; ?></small>
                    <?php if ($row['is_read'] == 0): ?>
                        <span class="badge badge-danger">New</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No notifications found.</p>
        <?php endif; ?>
    </div>

    <script>
    function markAsRead(notificationId) {
        $.ajax({
            url: 'mark_notification.php', // PHP file to mark notification as read
            method: 'POST',
            data: { id: notificationId },
            success: function(response) {
                // Reload the page to show updated notifications
                location.reload();
            },
            error: function() {
                alert('An error occurred while marking the notification as read.');
            }
        });
    }
    </script>
</body>
</html>
