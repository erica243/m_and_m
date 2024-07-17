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
        <button onclick="printReport()" class="btn btn-primary mb-3">Print Reports</button>
        <table class="table table-bordered" id="order-report-table">
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
                        <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment']); ?></td>
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

<script>
function printReport() {
    var printContents = document.getElementById('order-report-table').outerHTML;
    var originalContents = document.body.innerHTML;
    var headerContent = `
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="img/logo.jpg" alt="Logo" style="max-width: 100px;">
            <h1>Order Reports</h2>
            <h2>M&M Cake Ordering System</h2>
        </div>
    `;
    var style = `
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
    `;
    document.body.innerHTML = "<html><head><title>Order Reports</title>" + style + "</head><body>" + headerContent + printContents + "</body></html>";
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload(); // Reload the page to restore the original content
}
</script>
