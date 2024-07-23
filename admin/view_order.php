
<?php
include 'db_connect.php'; // Ensure this file connects to your database

$order_id = $_GET['id']; // Get the order ID from the request

// Fetch order details
$order_query = $conn->query("SELECT o.order_number, o.name AS customer_name, o.address, o.payment, p.name AS product_name, o.qty, p.price
                             FROM orders o 
                             INNER JOIN order_list ol ON o.id = ol.order_id 
                             INNER JOIN product_list p ON ol.product_id = p.id 
                             WHERE o.id = $order_id");

// Initialize total amount
$total_amount = 0;
$order_details = [];

// Process results
while ($row = $order_query->fetch_assoc()) {
    $total_amount += $row['qty'] * $row['price'];
    $order_details[] = [
        'product_name' => $row['product_name'],
        'qty' => $row['qty'],
        'price' => $row['price'],
        'amount' => $row['qty'] * $row['price']
    ];
}

// Output JSON data for use in JavaScript
echo json_encode([
    'order_number' => $row['order_number'],
    'customer_name' => $row['customer_name'],
    'address' => $row['address'],
    'payment_method' => $row['payment'],
    'order_details' => $order_details,
    'total_amount' => $total_amount
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        #uni_modal .modal-footer {
            display: none;
        }
        .no-print {
            display: none;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Qty</th>
                <th>Product</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            include 'db_connect.php';
            $qry = $conn->query("SELECT * FROM order_list o INNER JOIN product_list p ON o.product_id = p.id WHERE order_id = ".$_GET['id']);
            while($row = $qry->fetch_assoc()):
                $total += $row['qty'] * $row['price'];
            ?>
            <tr>
                <td><?php echo $row['qty'] ?></td>
                <td><?php echo $row['name'] ?></td>
                <td><?php echo number_format($row['qty'] * $row['price'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th><?php echo number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>
    <div class="text-center">
        <button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()">Confirm</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button class="btn btn-success" type="button" onclick="print_receipt()">Print Receipt</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function confirm_order() {
        Swal.fire({
            title: 'Confirm Order',
            text: 'Are you sure you want to confirm this order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm!'
        }).then((result) => {
            if (result.isConfirmed) {
                start_load();
                $.ajax({
                    url: 'ajax.php?action=confirm_order',
                    method: 'POST',
                    data: { id: '<?php echo $_GET['id'] ?>' },
                    success: function(resp) {
                        if (resp == 1) {
                            Swal.fire(
                                'Confirmed!',
                                'Order has been successfully confirmed.',
                                'success'
                            ).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Error confirming order: ' + resp,
                                'error'
                            );
                        }
                        end_load();
                    },
                    error: function() {
                        end_load();
                        Swal.fire(
                            'Error!',
                            'AJAX request failed.',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function print_receipt() {
        // Fetch order details from the server
        fetch('fetch_order_details.php?id=' + <?php echo $_GET['id']; ?>)
            .then(response => response.json())
            .then(data => {
                // Clone the contents of the container and remove buttons
                var container = document.querySelector('.container-fluid').cloneNode(true);
                container.querySelectorAll('button').forEach(function(button) {
                    button.remove();
                });

                // Get the updated HTML content of the container
                var printContents = container.innerHTML;

                // Open a new window for printing
                var receiptWindow = window.open('', '', 'height=600,width=800,location=no');

                // URL of your logo image (ensure this path is correct)
                var logoUrl = 'img/logo.jpg'; // Update this path to your actual logo

                // Write the HTML content to the new window
                receiptWindow.document.write('<html><head><title>Receipt</title>');
                receiptWindow.document.write('<style>');
                receiptWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
                receiptWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
                receiptWindow.document.write('th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }');
                receiptWindow.document.write('th { background-color: #f4f4f4; }');
                receiptWindow.document.write('.logo { text-align: center; margin-bottom: 20px; }');
                receiptWindow.document.write('@media print { .no-print { display: none; } body { font-size: 10px; } }');
                receiptWindow.document.write('</style></head><body>');
                
                // Header Section with Logo
                receiptWindow.document.write('<div class="logo">');
                receiptWindow.document.write('<img src="' + logoUrl + '" alt="Logo" width="100" height="100">');
                receiptWindow.document.write('</div>');
                
                // Header Details
                receiptWindow.document.write('<h2>Cake Order Receipt</h2>');
                receiptWindow.document.write('<p><strong>Order Number:</strong> ' + data.order_number + '</p>');
                receiptWindow.document.write('<p><strong>Customer Name:</strong> ' + data.customer_name + '</p>');
                receiptWindow.document.write('<p><strong>Address:</strong> ' + data.address + '</p>');
                receiptWindow.document.write('<p><strong>Payment Method:</strong> ' + data.payment_method + '</p>');
                
                // Order Details Table
                receiptWindow.document.write('<h3>Order Details</h3>');
                receiptWindow.document.write('<table>');
                receiptWindow.document.write('<thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Amount</th></tr></thead>');
                receiptWindow.document.write('<tbody>');
                data.order_details.forEach(detail => {
                    receiptWindow.document.write('<tr>');
                    receiptWindow.document.write('<td>' + detail.product_name + '</td>');
                    receiptWindow.document.write('<td>' + detail.qty + '</td>');
                    receiptWindow.document.write('<td>$' + detail.price.toFixed(2) + '</td>');
                    receiptWindow.document.write('<td>$' + detail.amount.toFixed(2) + '</td>');
                    receiptWindow.document.write('</tr>');
                });
                receiptWindow.document.write('</tbody>');
                receiptWindow.document.write('</table>');
                
                // Footer with Total Amount
                receiptWindow.document.write('<div class="total">');
                receiptWindow.document.write('<table>');
                receiptWindow.document.write('<tr><th>Total Amount:</th><td>$' + data.total_amount.toFixed(2) + '</td></tr>');
                receiptWindow.document.write('</table>');
                receiptWindow.document.write('<p>This receipt serves as proof of purchase and does not qualify as a tax invoice.</p>');
                receiptWindow.document.write('<p>Thank you for your purchase.</p>');
                receiptWindow.document.write('</div>');
                
                receiptWindow.document.write('</body></html>');
                
                receiptWindow.document.close();
                receiptWindow.print();
            })
            .catch(error => console.error('Error fetching order details:', error));
    }
</script>

</body>
</html>
