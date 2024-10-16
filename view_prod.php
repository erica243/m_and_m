<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #uni_modal .modal-dialog {
            max-width: 90%;
            width: auto;
        }
        #uni_modal .modal-body {
            overflow-x: auto;
        }
        #uni_modal .modal-footer {
            display: none;
        }
    </style>
</head>
<body>
<?php
include 'db_connect.php';
$orderId = $_GET['id'];

// Use prepared statements for security
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$orderStatus = $order['status']; // 1 for confirmed, 0 for not confirmed

// Convert the order date to 'm-d-Y' format
$formatted_order_date = date("m-d-Y", strtotime($order['order_date']));

// Fetch order items
$stmt = $conn->prepare("SELECT o.qty, p.name, p.description, p.price, 
                                (o.qty * p.price) AS amount
                        FROM order_list o 
                        INNER JOIN product_list p ON o.product_id = p.id 
                        WHERE o.order_id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderItems = $stmt->get_result();

// Fetch shipping information based on the order's address
$address = $order['address']; // Get the address from the order
$shippingStmt = $conn->prepare("SELECT shipping_amount FROM shipping_info WHERE address = ?");
$shippingStmt->bind_param("s", $address);
$shippingStmt->execute();
$shippingResult = $shippingStmt->get_result();
$shippingAmount = $shippingResult->fetch_assoc()['shipping_amount'] ?? 0;

// Fetch unique user addresses for shipping
$addressesStmt = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
$addressesStmt->bind_param("i", $order['user_id']);
$addressesStmt->execute();
$addresses = $addressesStmt->get_result();
?>

<div class="container-fluid mt-4">
    <h4>Order Details</h4>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Order Date</th>
                <th>Order Number</th>
                <th>Customer Name</th>
                <th>Address</th>
                <th>Delivery Method</th>
                <th>Mode of Payment</th>
                <th>Qty</th>
                <th>Product</th>
                <th>Description</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            while ($row = $orderItems->fetch_assoc()):
                $total += $row['amount'];
            ?>
            <tr>
                <td><?php echo $formatted_order_date; ?></td>
                <td><?php echo $order['order_number']; ?></td>
                <td><?php echo $order['name']; ?></td>
                <td><?php echo $order['address']; ?></td>
                <td><?php echo $order['delivery_method']; ?></td>
                <td><?php echo $order['payment_method']; ?></td>
                <td><?php echo $row['qty']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo number_format($row['amount'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="10" class="text-right">Subtotal</th>
                <th><?php echo number_format($total, 2); ?></th>
            </tr>
            <tr>
                <th colspan="10" class="text-right">Shipping Amount</th>
                <th><?php echo number_format($shippingAmount, 2); ?></th>
            </tr>
            <tr>
                <th colspan="10" class="text-right">TOTAL</th>
                <th><?php echo number_format($total + $shippingAmount, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    
    <div class="text-center mt-4">
<<<<<<< HEAD
    <button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()" <?php echo $orderStatus == 1 ? 'disabled' : '' ?>>Confirm</button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button class="btn btn-success" type="button" onclick="print_receipt()">Print Receipt</button>
    <button class="btn btn-danger" type="button" id="delete_order" onclick="delete_order()">Delete Order</button>

 
    <!-- Delivery Status Dropdown -->
    <label for="delivery_status" class="mt-3">Update Delivery Status:</label>
    <select id="delivery_status" class="form-control w-50 mx-auto mt-2" onchange="update_delivery_status()">
        <option value="pending" <?php echo $deliveryStatus == 'pending' ? 'selected' : ''; ?>>Pending</option>
        <option value="confirmed" <?php echo $deliveryStatus == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
        <option value="arrived" <?php echo $deliveryStatus == 'arrived' ? 'selected' : ''; ?>>Arrived</option>
        <option value="delivered" <?php echo $deliveryStatus == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
        <option value="completed" <?php echo $deliveryStatus == 'completed' ? 'selected' : ''; ?>>Completed</option>
    </select>
</div>
=======
        <button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()" <?php echo $orderStatus == 1 ? 'disabled' : '' ?>>Confirm</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button class="btn btn-success" type="button" onclick="print_receipt()">Print Receipt</button>
        <button class="btn btn-danger" type="button" id="delete_order" onclick="delete_order()">Delete Order</button>
    </div>
>>>>>>> c58447c6c21b25d029365c30c287ce2cb1a6eae5
</div>

<script>
    function confirm_order() {
        // Existing code for confirming the order
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
                            Swal.fire('Confirmed!', 'Order has been successfully confirmed.', 'success').then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', 'Error confirming order: ' + resp, 'error');
                        }
                        end_load();
                    },
                    error: function() {
                        end_load();
                        Swal.fire('Error!', 'AJAX request failed.', 'error');
                    }
                });
            }
        });
    }
    function update_delivery_status() {
    var status = $('#delivery_status').val();
    var orderId = '<?php echo $_GET["id"]; ?>';

    Swal.fire({
        title: 'Update Delivery Status',
        text: 'Are you sure you want to update the delivery status?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
    }).then((result) => {
        if (result.isConfirmed) {
            start_load();
            $.ajax({
                url: 'ajax.php?action=update_delivery_status',
                method: 'POST',
                data: {
                    id: orderId,
                    status: status
                },
                success: function(resp) {
                    if (resp == 1) {
                        Swal.fire('Updated!', 'Delivery status has been updated successfully.', 'success').then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', 'Error updating delivery status: ' + resp, 'error');
                    }
                    end_load();
                },
                error: function() {
                    end_load();
                    Swal.fire('Error!', 'AJAX request failed.', 'error');
                }
            });
        }
    });
}
    function print_receipt() {
        // Existing code for printing the receipt
        var receiptWindow = window.open('', '', 'height=600,width=800,location=no');
        var logoUrl = 'assets/img/logo.jpg'; // Full URL

        var orderNumber = '<?php echo $order["order_number"]; ?>';
        var orderDate = '<?php echo $formatted_order_date; ?>';
        var customerName = '<?php echo $order["name"]; ?>';
        var address = '<?php echo $order["address"]; ?>';
        var deliveryMethod = '<?php echo $order["delivery_method"]; ?>';
        var paymentMethod = '<?php echo $order["payment_method"]; ?>';
        var totalAmount = '<?php echo number_format($total + $shippingAmount, 2); ?>'; // Total with shipping

        var headerContent = '<div style="text-align: center; margin-bottom: 20px;">' +
            '<img src="' + logoUrl + '" alt="Logo" style="max-width: 150px;"/>' +
            '<h3>M&M Cake Ordering</h3>' +
            '</div>';
        
        var orderDetailsContent = '<table style="width: 100%; border-collapse: collapse;">' +
            '<tr><th>Order Number:</th><td>' + orderNumber + '</td></tr>' +
            '<tr><th>Order Date:</th><td>' + orderDate + '</td></tr>' +
            '<tr><th>Customer Name:</th><td>' + customerName + '</td></tr>' +
            '<tr><th>Address:</th><td>' + address + '</td></tr>' +
            '<tr><th>Delivery Method:</th><td>' + deliveryMethod + '</td></tr>' +
            '<tr><th>Payment Method:</th><td>' + paymentMethod + '</td></tr>' +
            '<tr><th>Total Amount:</th><td>' + totalAmount + '</td></tr>' +
            '</table>';
        
        receiptWindow.document.write('<html><head><title>Receipt</title>');
        receiptWindow.document.write('</head><body>');
        receiptWindow.document.write(headerContent);
        receiptWindow.document.write(orderDetailsContent);
        receiptWindow.document.write('</body></html>');
        receiptWindow.document.close();
        receiptWindow.print();
    }

    function print_receipt() {
        // Existing code for printing the receipt
        var receiptWindow = window.open('', '', 'height=600,width=800,location=no');
        var logoUrl = 'assets/img/logo.jpg'; // Full URL

        var orderNumber = '<?php echo $order["order_number"]; ?>';
        var orderDate = '<?php echo $formatted_order_date; ?>';
        var customerName = '<?php echo $order["name"]; ?>';
        var address = '<?php echo $order["address"]; ?>';
        var deliveryMethod = '<?php echo $order["delivery_method"]; ?>';
        var paymentMethod = '<?php echo $order["payment_method"]; ?>';
        var totalAmount = '<?php echo number_format($total + $shippingAmount, 2); ?>'; // Total with shipping

        var headerContent = '<div style="text-align: center; margin-bottom: 20px;">' +
            '<img src="' + logoUrl + '" alt="Logo" style="max-width: 150px;"/>' +
            '<h3>M&M Cake Ordering</h3>' +
            '</div>';
        
        var orderDetailsContent = '<table style="width: 100%; border-collapse: collapse;">' +
            '<tr><th>Order Number:</th><td>' + orderNumber + '</td></tr>' +
            '<tr><th>Order Date:</th><td>' + orderDate + '</td></tr>' +
            '<tr><th>Customer Name:</th><td>' + customerName + '</td></tr>' +
            '<tr><th>Address:</th><td>' + address + '</td></tr>' +
            '<tr><th>Delivery Method:</th><td>' + deliveryMethod + '</td></tr>' +
            '<tr><th>Payment Method:</th><td>' + paymentMethod + '</td></tr>' +
            '<tr><th>Total Amount:</th><td>' + totalAmount + '</td></tr>' +
            '</table>';
        
        receiptWindow.document.write('<html><head><title>Receipt</title>');
        receiptWindow.document.write('</head><body>');
        receiptWindow.document.write(headerContent);
        receiptWindow.document.write(orderDetailsContent);
        receiptWindow.document.write('</body></html>');
        receiptWindow.document.close();
        receiptWindow.print();
    }

    function delete_order() {
        // Existing code for deleting the order
        Swal.fire({
            title: 'Delete Order',
            text: 'Are you sure you want to delete this order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                start_load();
                $.ajax({
                    url: 'ajax.php?action=delete_order',
                    method: 'POST',
                    data: { id: '<?php echo $_GET['id'] ?>' },
                    success: function(resp) {
                        if (resp == 1) {
                            Swal.fire('Deleted!', 'Order has been deleted.', 'success').then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', 'Error deleting order: ' + resp, 'error');
                        }
                        end_load();
                    },
                    error: function() {
                        end_load();
                        Swal.fire('Error!', 'AJAX request failed.', 'error');
                    }
                });
            }
        });
    }
</script>
</body>
</html>
