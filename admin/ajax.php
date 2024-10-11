<?php
ob_start();
include 'admin_class.php';
$crud = new Action();

// Check if 'action' exists in either GET or POST
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
} else {
    // Handle missing action case
    echo "Error: Action not specified.";
    exit();
}
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
    if ($save) echo $save;
}

if ($action == "confirm_order") {
    $save = $crud->confirm_order();
    if ($save) echo $save;
}

if ($action == "cancel_order") {
    $cancel = $crud->cancel_order();
    if ($cancel) echo $cancel;
}

if ($action == 'delete_order') {
    include 'db_connect.php';
    $orderId = $_POST['id']; // Get the order ID from POST

    // Use prepared statements for security
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        echo 1; // Success
    } else {
        echo $conn->error; // Return the error message for debugging
    }

    $stmt->close();
    $conn->close();
    exit();
}


// Handle delete_user action
if ($action == 'delete_user') {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $result = $conn->query("DELETE FROM users WHERE id = $id");
        if ($result) {
            echo 1; // Success
        } else {
            error_log("SQL Error: " . $conn->error);
            echo 0; // Failure
        }
    } else {
        echo 0; // Missing ID
    }
}

if($action == 'submit_rating'){
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];
    $user_id = 1; // Replace with actual user ID logic

    // Validate inputs
    if(empty($product_id) || empty($rating)){
        echo 0; // Invalid inputs
        exit;
    }

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO product_ratings (product_id, user_id, rating, feedback) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $product_id, $user_id, $rating, $feedback);
    
    if($stmt->execute()){
        echo 1; // Success
    } else {
        echo 0; // Failure
    }

    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] == 'update_delivery_status') {
    $orderId = $_POST['id'];
    $status = $_POST['status'];

    // Validate input
    $allowed_statuses = ['pending', 'confirmed', 'delivered', 'arrived', 'completed'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid delivery status.']);
        exit;
    }

    // Prepare the statement
    $stmt = $conn->prepare("UPDATE orders SET delivery_status = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
        exit;
    }

    // Bind parameters
    $stmt->bind_param("si", $status, $orderId);

    // Execute the statement
    if ($stmt->execute()) {
        // Log successful update
        error_log("Delivery status updated for order ID $orderId to '$status'.");
        echo json_encode(['success' => true, 'message' => 'Delivery status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating delivery status: ' . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
}


?>
