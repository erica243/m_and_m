<?php
session_start();
include('admin/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['login_user_id'])) {
    header('Location: index.php?page=home');
    exit;
}

// Fetch messages from the database
$user_id = $_SESSION['login_user_id'];
$query = $conn->query("SELECT * FROM messages WHERE user_id = $user_id ORDER BY date_sent DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <?php include('header.php'); ?>
    <style>
        .message-item {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .message-item:hover {
            background-color: #f9f9f9;
        }
        .message-content {
            display: flex;
            justify-content: space-between;
        }
        .message-date {
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <h2>Messages</h2>
        <?php if ($query->num_rows > 0): ?>
            <?php while ($row = $query->fetch_assoc()): ?>
                <div class="message-item">
                    <div class="message-content">
                        <div>
                            <strong><?php echo htmlspecialchars($row['subject']); ?></strong>
                            <p><?php echo htmlspecialchars(substr($row['message_body'], 0, 100)); ?>...</p>
                        </div>
                        <div class="message-date"><?php echo date('F j, Y, g:i a', strtotime($row['date_sent'])); ?></div>
                    </div>
                    <a href="view_message.php?id=<?php echo $row['id']; ?>" class="btn btn-primary mt-2">View Details</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No messages found.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include('footer.php'); ?>
</body>
</html>

<?php $conn->close(); ?>
