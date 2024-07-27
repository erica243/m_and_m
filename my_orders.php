<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Container Styling */
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        /* Heading */
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Table Headers */
        thead th {
            background-color: #f4f4f4;
            border-bottom: 2px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        /* Table Rows */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        /* Submit Button */
        .btn-primary {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Error Message */
        .text-danger {
            color: #dc3545;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Orders</h2>
        <?php
        session_start();
        include 'admin/db_connect.php';

        // Ensure user_id is available
        if (!isset($_SESSION['login_user_id'])) {
            die("User not logged in.");
        }

        $user_id = $_SESSION['login_user_id'];

        // Fetch all orders for the logged-in user
        $query = "SELECT * FROM orders WHERE id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Failed to prepare the SQL statement: " . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there are any orders
        if ($result->num_rows > 0):
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Payment Method</th>
                    <th>Order Type</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($order['delivery_method']); ?></td>
                    <td><?php echo htmlspecialchars($order['address']); ?></td>
                    <td>
                        <?php
                        // Check the status and display accordingly
                        if ($order['status'] == '1' 
                        ) {
                            echo "Confirmed";
                        } else {
                            echo "Pending"; // Or use htmlspecialchars($order['status']) if it can be a different value
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php
        else:
            echo "<p>You have no orders.</p>";
        endif;

        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
