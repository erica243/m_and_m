<?php
// admin/ajax.php

ob_start(); // Start output buffering
$action = $_GET['action'];
include 'admin_class.php'; // Include your class file
$crud = new Action(); // Instantiate your class

if ($action == 'login') {
    $login = $crud->login();
    if ($login) echo $login;
}
if ($action == 'login2') {
    $login = $crud->login2();
    if ($login) echo $login;
}
if ($action == 'logout') {
    $logout = $crud->logout();
    if ($logout) echo $logout;
}
if ($action == 'logout2') {
    $logout = $crud->logout2();
    if ($logout) echo $logout;
}
if ($action == 'save_user') {
    $save = $crud->save_user();
    if ($save) echo $save;
}
if ($action == 'signup') {
    $save = $crud->signup();
    if ($save) echo $save;
}
if ($action == "save_settings") {
    $save = $crud->save_settings();
    if ($save) echo $save;
}
if ($action == "save_category") {
    $save = $crud->save_category();
    if ($save) echo $save;
}
if ($action == "delete_category") {
    $save = $crud->delete_category();
    if ($save) echo $save;
}
if ($action == "save_menu") {
    $save = $crud->save_menu();
    if ($save) echo $save;
}
if ($action == "delete_menu") {
    $save = $crud->delete_menu();
    if ($save) echo $save;
}
if ($action == "add_to_cart") {
    $save = $crud->add_to_cart();
    if ($save) echo $save;
}
if ($action == "get_cart_count") {
    $save = $crud->get_cart_count();
    if ($save) echo $save;
}
if ($action == "delete_cart") {
    $delete = $crud->delete_cart();
    if ($delete) echo $delete;
}
if ($action == "update_cart_qty") {
    $save = $crud->update_cart_qty();
    if ($save) echo $save;
}
if ($action == "save_order") {
    $save = $crud->save_order();
    if ($save) {
        // Notify admin about the new order
        notifyAdmin($save); // Pass the order ID to notify
        echo $save; // Return the order ID or success message
    }
}
if ($action == "confirm_order") {
    $save = $crud->confirm_order();
    if ($save) echo $save;
}
if ($action == "cancel_order") {
    $cancel = $crud->cancel_order();
    if ($cancel) echo $cancel;
}
if ($action == "submit_rating") {
    $submit = $crud->submit_rating();
    if ($submit) echo $submit;
}
if ($action == "delete_user") {
    $delete = $crud->delete_user();
    if ($delete) echo $delete;
}

// Fetch notifications
if ($action === 'get_notifications') {
    include 'db_connect.php'; // Include your database connection

    $query = "SELECT id FROM orders WHERE status = 0"; // Modify according to your order status
    $result = $conn->query($query);
    
    $newOrders = [];
    
    while ($row = $result->fetch_assoc()) {
        $newOrders[] = $row;
    }

    echo json_encode($newOrders);
    $conn->close();
    exit;
}

// Function to notify admin
function notifyAdmin($order_id) {
    include 'db_connect.php'; // Include your database connection
    $message = "New order placed with ID: " . $order_id;

    // Insert notification into notifications table
    $stmt = $conn->prepare("INSERT INTO notifications (order_id, message, is_read) VALUES (?, ?, 0)");
    $stmt->bind_param("is", $order_id, $message);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

ob_end_flush(); // Flush the output buffer and turn off output buffering
?>
