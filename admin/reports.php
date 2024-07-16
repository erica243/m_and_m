<?php
include 'db_connect.php'; // Include your database connection

$query = "
    SELECT o.order_date, ol.qty, ol.order_id, p.name AS product_name,   o.transaction_id, o.payment, p.price
    FROM orders o
    JOIN order_list ol ON o.id = ol.order_id
    JOIN product_list p ON ol.product_id = p.id
    ORDER BY o.order_date DESC
";

$result = $conn->query($query);

if ($result->num_rows > 0): ?>
    <div class="container-fluid">
        <h2>Order Reports</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Transaction ID</th>
                    <th>Mode of Payment</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('Y-m-d', $row['order_date']); ?></td> <!-- Assuming order_date is a UNIX timestamp -->
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment']); ?></td> <!-- Ensure `payment` contains the mode of payment -->
                        <td><?php echo htmlspecialchars($row['qty']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['qty'] * $row['price'], 2)); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="container-fluid">
        <h2>No orders found</h2>
    </div>
<?php endif;

$conn->close();
?>
