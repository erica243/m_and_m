<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th:nth-child(1) { width: 15%; }
        .table th:nth-child(2) { width: 10%; }
        .table th:nth-child(3) { width: 15%; }
        .table th:nth-child(4) { width: 20%; }
        .table th:nth-child(5) { width: 10%; }
        .table th:nth-child(6) { width: 10%; }
        .table th:nth-child(7) { width: 5%; }
        .table th:nth-child(8) { width: 10%; }
        .table th:nth-child(9) { width: 5%; }
        .modal-dialog {
            max-width: 90%; /* Adjust this value as needed */
            margin: 30px auto; /* Centers the modal horizontally */
        }
        .modal-content {
            width: 100%; /* Ensures the content takes full width of the dialog */
        }
     </style>
</head>
<body>
<div class="container-fluid">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Transaction ID</th>
                <th>Order Date</th>
                <th>Address</th>
                <th>Delivery Method</th>
                <th>Payment Method</th>
                <th>Qty</th>
                <th>Product Name</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $total = 0;
        include 'db_connect.php';
        $qry = $conn->query("SELECT o.transaction_id, o.name, o.order_date, o.address, o.delivery_method, o.payment, 
                                ol.qty, p.name as product_name, p.price 
                             FROM orders o 
                             INNER JOIN order_list ol ON o.id = ol.order_id 
                             INNER JOIN product_list p ON ol.product_id = p.id 
                             WHERE o.id = " . intval($_GET['id']));
        while ($row = $qry->fetch_assoc()):
            $total += $row['qty'] * $row['price'];
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
            <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
            <td><?php echo htmlspecialchars($row['address']); ?></td>
            <td><?php echo htmlspecialchars($row['delivery_method']); ?></td>
            <td><?php echo htmlspecialchars($row['payment']); ?></td>
            <td><?php echo htmlspecialchars($row['qty']); ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo number_format($row['qty'] * $row['price'], 2); ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="text-right">TOTAL</th>
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
                    },
                    error: function() {
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
        // Select the content of the container
        var printContents = document.querySelector('.container-fluid').innerHTML;

        // Open a new window for printing
        var receiptWindow = window.open('', '', 'height=600,width=800,location=no');

        // Write the HTML structure for the print window
        receiptWindow.document.write('<html><head><title>Receipt</title>');
        receiptWindow.document.write('<style>');
        receiptWindow.document.write('table { border-collapse: collapse; width: 100%; }');
        receiptWindow.document.write('th, td { border: 1px solid black; padding: 8px; text-align: left; font-size: 14px; }');
        receiptWindow.document.write('th { background-color: #f2f2f2; }');
        receiptWindow.document.write('body { font-size: 12px; margin: 20px; }');
        receiptWindow.document.write('@media print { body { font-size: 10px; } }');
        receiptWindow.document.write('</style>');
        receiptWindow.document.write('</head><body>');

        // Header with Logo and Title
        receiptWindow.document.write('<div style="text-align: center;">');
        receiptWindow.document.write('<img src="img/logo.jpg" alt="Logo" style="width: 100px; height: auto;">');
        receiptWindow.document.write('<h1>M&M Cake Ordering</h1>');
        receiptWindow.document.write('</div>');

        receiptWindow.document.write('<h2 style="font-size: 20px; text-align: center;">Receipt</h2>');

        // Write the table structure including headers and rows
        receiptWindow.document.write('<div class="container-fluid">');
        receiptWindow.document.write('<table class="table table-bordered">');
        receiptWindow.document.write('<thead>');
        receiptWindow.document.write('<tr>');
        receiptWindow.document.write('<th>Customer Name</th>');
        receiptWindow.document.write('<th>Transaction ID</th>');
        receiptWindow.document.write('<th>Order Date</th>');
        receiptWindow.document.write('<th>Address</th>');
        receiptWindow.document.write('<th>Delivery Method</th>');
        receiptWindow.document.write('<th>Payment Method</th>');
        receiptWindow.document.write('<th>Qty</th>');
        receiptWindow.document.write('<th>Product Name</th>');
        receiptWindow.document.write('<th>Amount</th>');
        receiptWindow.document.write('</tr>');
        receiptWindow.document.write('</thead>');

        // Add the table body content
        receiptWindow.document.write('<tbody>');

        // PHP code to generate the table rows
        <?php
        $total = 0;
        include 'db_connect.php';
        $qry = $conn->query("SELECT o.transaction_id, o.name, o.order_date, o.address, o.delivery_method, o.payment, 
                                ol.qty, p.name as product_name, p.price 
                             FROM orders o 
                             INNER JOIN order_list ol ON o.id = ol.order_id 
                             INNER JOIN product_list p ON ol.product_id = p.id 
                             WHERE o.id = " . intval($_GET['id']));
        while ($row = $qry->fetch_assoc()):
            $total += $row['qty'] * $row['price'];
        ?>
        receiptWindow.document.write('<tr>');
        receiptWindow.document.write('<td><?php echo htmlspecialchars($row['name']); ?></td>');
        receiptWindow.document.write('<td><?php echo htmlspecialchars($row['transaction_id']); ?></td>');
        receiptWindow.document.write('<td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>');
        receiptWindow.document.write('<td><?php echo htmlspecialchars($row['address']); ?></td>');
        receiptWindow.document.write('<td><?php echo htmlspecialchars($row['delivery_method']); ?></td>');
        receiptWindow.document.write('<td><?php echo htmlspecialchars($row['payment']); ?></td>');
        receiptWindow.document.write('<td><?php echo htmlspecialchars($row['qty']); ?></td>');
        receiptWindow.document.write('<td><?php echo htmlspecialchars($row['product_name']); ?></td>');
        receiptWindow.document.write('<td><?php echo number_format($row['qty'] * $row['price'], 2); ?></td>');
        receiptWindow.document.write('</tr>');
        <?php endwhile; ?>

        receiptWindow.document.write('</tbody>');
        receiptWindow.document.write('<tfoot>');
        receiptWindow.document.write('<tr>');
        receiptWindow.document.write('<th colspan="8" class="text-right">TOTAL</th>');
        receiptWindow.document.write('<th><?php echo number_format($total, 2); ?></th>');
        receiptWindow.document.write('</tr>');
        receiptWindow.document.write('</tfoot>');
        receiptWindow.document.write('</table>');
        receiptWindow.document.write('</div>');

        receiptWindow.document.write('</body></html>');
        receiptWindow.document.close();
        receiptWindow.focus(); // Ensures the window is focused before printing
        receiptWindow.print();
    }
</script>
</body>
</html>
