<?php
session_start();
include('admin/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['login_user_id'])) {
    die("User not logged in.");
}

$message = ''; // Variable to store success/error messages

// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);

    // Delete the order and its related data
    $delete_query = "DELETE FROM orders WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);

    if (!$delete_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $delete_stmt->bind_param("i", $order_id);

    // Execute and check for errors
    if (!$delete_stmt->execute()) {
        $message = "Error deleting order: " . $delete_stmt->error;
    } else {
        $message = "Order deleted successfully.";
    }
}

// Handle form submission for rating and feedback
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rate_product'])) {
    $rating = intval($_POST['rating']);
    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['login_user_id'];

    // Check if the user has already rated this product
    $check_query = "SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Update the rating and feedback
        $update_query = "UPDATE product_ratings SET rating = ?, feedback = ? WHERE user_id = ? AND product_id = ?";
        $update_stmt = $conn->prepare($update_query);
        
        if (!$update_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $update_stmt->bind_param("isii", $rating, $feedback, $user_id, $product_id);
        
        // Execute and check for errors
        if (!$update_stmt->execute()) {
            $message = "Error updating rating and feedback: " . $update_stmt->error;
        } else {
            $message = "Thank you for updating your rating and feedback!";
        }
    } else {
        // Insert rating and feedback into the database
        $rating_query = "INSERT INTO product_ratings (user_id, product_id, rating, feedback) VALUES (?, ?, ?, ?)";
        $rating_stmt = $conn->prepare($rating_query);
        
        if (!$rating_stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $rating_stmt->bind_param("iiis", $user_id, $product_id, $rating, $feedback);
        
        // Execute and check for errors
        if (!$rating_stmt->execute()) {
            $message = "Error inserting rating and feedback: " . $rating_stmt->error;
        } else {
            $message = "Thank you for rating the product and leaving feedback!";
        }
    }
}

// Fetch orders for the user
$user_id = $_SESSION['login_user_id'];
$query = "SELECT o.id, o.order_number, o.order_date, o.delivery_method, o.payment_method, 
                 p.id AS product_id, p.name AS product_name, ol.qty AS quantity, p.price, 
                 pr.rating, pr.feedback, o.delivery_status 
          FROM orders o
          JOIN order_list ol ON o.id = ol.order_id
          JOIN product_list p ON ol.product_id = p.id
          JOIN user_info u ON u.email = o.email
          LEFT JOIN product_ratings pr ON pr.user_id = u.user_id AND pr.product_id = p.id
          WHERE u.user_id = ?
          ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header, footer {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }

        h1 {
            text-align: center;
            color: #333;
            margin: 20px 0;
        }

        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .back-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table thead th {
            background-color: #333;
            color: #fff;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        table tbody td select, table tbody td textarea {
            width: 100%;
            padding: 5px;
        }

        button {
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
        }

        button:focus {
            outline: none;
        }

        button[type="submit"] {
            background-color: #28a745; /* Green */
            color: white;
            margin-right: 10px; /* Space between buttons */
        }

        button[type="submit"]:hover {
            background-color: #218838; /* Darker green */
        }

        button[type="submit"]:active {
            background-color: #1e7e34; /* Even darker green */
        }

        button[name="delete_order"] {
            background-color: #dc3545; /* Red */
            color: white;
        }

        button[name="delete_order"]:hover {
            background-color: #c82333; /* Darker red */
        }

        button[name="delete_order"]:active {
            background-color: #bd2130; /* Even darker red */
        }

        .print-receipt {
            background-color: #007bff; /* Blue */
            color: white;
            margin-top: 10px;
        }

        .print-receipt:hover {
            background-color: #0056b3; /* Darker blue */
        }
    </style>
</head>
<body>
    <header>
        <h1>My Orders</h1>
    </header>

    <main>
        <!-- Back button -->
        <button class="back-btn" onclick="window.history.back();">Back</button>
        
        <!-- Display order details and rating/feedback option -->
        <table>
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Delivery Method</th>
                    <th>Payment Method</th>
                    <th>Delivery Status</th>
                    <th>Rate and Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['delivery_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['delivery_status']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <label for="rating">Rate:</label>
                            <select name="rating" id="rating" required>
                                <option value="">Select</option>
                                <option value="1" <?php if ($row['rating'] == 1) echo 'selected'; ?>>1</option>
                                <option value="2" <?php if ($row['rating'] == 2) echo 'selected'; ?>>2</option>
                                <option value="3" <?php if ($row['rating'] == 3) echo 'selected'; ?>>3</option>
                                <option value="4" <?php if ($row['rating'] == 4) echo 'selected'; ?>>4</option>
                                <option value="5" <?php if ($row['rating'] == 5) echo 'selected'; ?>>5</option>
                            </select>
                            <label for="feedback">Feedback:</label>
                            <textarea name="feedback" id="feedback" rows="3" placeholder="Leave your feedback here"><?php echo htmlspecialchars($row['feedback']); ?></textarea>
                            <button type="submit" name="rate_product">Submit</button>
                            <button type="submit" name="delete_order" onclick="return confirm('Are you sure you want to delete this order?');">Delete</button>
                        </form>

                        <?php if (strcasecmp($row['delivery_status'], 'completed') == 0): ?>
    <button class="print-receipt" onclick="printReceipt(<?php echo $row['id']; ?>)">Receipt</button>
<?php endif; ?>

                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if ($message): ?>
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Notification',
                    text: "<?php echo addslashes($message); ?>",
                });
            </script>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> M&M Cake Ordering System. All rights reserved.</p>
    </footer>

    <script>
    function printReceipt(orderId) {
        // Replace with the actual URL for printing the receipt
        window.open('print_receipt.php?order_id=' + orderId, '_blank');
    }
    </script>
</body>
</html>
