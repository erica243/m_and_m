<!DOCTYPE html>
<html>
<head>
    <title>Cake Order Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .total { text-align: right; font-size: 1.2em; margin-top: 20px; }
        .logo { text-align: center; margin-bottom: 20px; }
        
        /* Print-specific CSS */
        @media print {
            body { margin: 0; }
            .receipt { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Qty</th>
                    <th>Order</th>
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
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['mobile']; ?></td>
                    <td><?php echo $row['qty']; ?></td>
                    <td><?php echo $row['order']; ?></td>
                    <td><?php echo number_format($row['qty'] * $row['price'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-right">TOTAL</th>
                    <th><?php echo number_format($total, 2); ?></th>
                </tr>
            </tfoot>
        </table>
        <div class="text-center">
            <button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()">Confirm</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button class="btn btn-success" type="button" onclick="print_receipt()">Print Receipt</button>
        </div>
    </div>

    <style>
        #uni_modal .modal-footer {
            display: none;
        }
    </style>

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
            // Clone the contents of the container and remove buttons
            var container = document.querySelector('.container-fluid').cloneNode(true);
            container.querySelectorAll('button').forEach(function(button) {
                button.remove();
            });

            // Get the updated HTML content of the container
            var printContents = container.innerHTML;

            // Data to include in the receipt
            var customerName = 'John Doe'; // Replace with actual customer name
            var orderDate = '15/05/2024 22:07:10'; // Replace with actual order date
            var orderNumber = '5788385448286862261'; // Replace with actual order number
            var subtotal = 100.00; // Replace with actual subtotal
            var shipping = 10.00; // Replace with actual shipping cost
            var total = subtotal + shipping; // Total amount

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
            receiptWindow.document.write('</table>');
            receiptWindow.document.write('<h2>Cake Order Receipt</h2>');
            receiptWindow.document.write('<p><strong>Customer Name:</strong> ' + customerName + '</p>');
            receiptWindow.document.write('<p><strong>Order Date:</strong> ' + orderDate + '</p>');
            receiptWindow.document.write('<p><strong>Order Number:</strong> ' + orderNumber + '</p>');
            receiptWindow.document.write('</table>');
            
            // Order Details Table
            receiptWindow.document.write('<h3>Order Details</h3>');
            receiptWindow.document.write('<div>' + printContents + '</div>');
            
            // Footer with Subtotal, Shipping, and Total
            receiptWindow.document.write('<div class="total">');
           
            receiptWindow.document.write('<tr><th>Subtotal:</th><td>$' + subtotal.toFixed(2) + '</td></tr>');
            receiptWindow.document.write('<tr><th>Shipping:</th><td>$' + shipping.toFixed(2) + '</td></tr>');
            receiptWindow.document.write('<tr><th>Total:</th><td>$' + total.toFixed(2) + '</td></tr>');
         
            receiptWindow.document.write('<p>This receipt serves as proof of purchase and does not qualify as a tax invoice.</p>');
            receiptWindow.document.write('<p>Thank you for your purchase.</p>');
            receiptWindow.document.write('</div>');
            
            receiptWindow.document.write('</body></html>');
            
            receiptWindow.document.close();
            receiptWindow.print();
        }
    </script>
</body>
</html>
