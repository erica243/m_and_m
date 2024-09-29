<?php
session_start();
include('admin/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['login_user_id'])) {
    die("User not logged in.");
}

// Fetch orders for the user
$user_id = $_SESSION['login_user_id'];
$query = "SELECT o.id, o.order_date, o.delivery_method, o.payment_method, 
                 p.name AS product_name, ol.qty AS quantity, p.price 
          FROM orders o
          JOIN order_list ol ON o.id = ol.order_id
          JOIN product_list p ON ol.product_id = p.id
          WHERE o.id = ?";
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
    <style>
        /* General styling */
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

        /* Main section styling */
        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Table styling */
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

        table thead {
            background-color: #f4f4f4;
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

        /* Responsive design */
        @media (max-width: 768px) {
            table {
                border: 0;
            }

            table thead {
                display: none;
            }

            table tbody tr {
                display: block;
                margin-bottom: 10px;
                border: 1px solid #ddd;
            }

            table tbody td {
                display: block;
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            table tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 10px;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <header>
        <!-- Include header content here -->
    </header>

    <main>
        <h1>My Orders</h1>
        
        <!-- Display order details -->
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Delivery Method</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['delivery_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <!-- Include footer content here -->
    </footer>

    <?php $conn->close(); ?>
</body>
</html>
