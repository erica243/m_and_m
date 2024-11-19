<div class="notification-dropdown">
    <a href="#" id="notification-icon">
        <i class="fas fa-bell"></i>
        <span id="notification-badge" class="badge badge-danger">0</span>
    </a>
    <ul id="notification-list" class="dropdown-menu"></ul>
</div>

<?php
include 'admin/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['notification_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$notification_id = $_POST['notification_id'];

$query = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
$query->bind_param("ii", $notification_id, $user_id);
$query->execute();

echo json_encode(['status' => 'success']);

?>
