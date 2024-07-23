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
    <div class="receipt">
        <div class="logo">
            <img src="path/to/your/logo.png" alt="Logo" width="100" height="100">
        </div>
        <h2>Cake Order Receipt</h2>
        <?php
            // Example order details
            $customerName = "John Doe";
            $cakeType = "Chocolate Cake";
            $size = "Large";
            $quantity = 1;
            $pricePerCake = 25.00;
            $totalPrice = $quantity * $pricePerCake;
            $orderDate = date("Y-m-d H:i:s");
        ?>
        <table>
            <tr>
                <th>Customer Name</th>
                <td><?php echo $customerName; ?></td>
            </tr>
            <tr>
                <th>Cake Type</th>
                <td><?php echo $cakeType; ?></td>
            </tr>
            <tr>
                <th>Size</th>
                <td><?php echo $size; ?></td>
            </tr>
            <tr>
                <th>Quantity</th>
                <td><?php echo $quantity; ?></td>
            </tr>
            <tr>
                <th>Price per Cake</th>
                <td><?php echo "$" . number_format($pricePerCake, 2); ?></td>
            </tr>
            <tr>
                <th>Order Date</th>
                <td><?php echo $orderDate; ?></td>
            </tr>
        </table>
        <div class="total">
            <strong>Total Price: <?php echo "$" . number_format($totalPrice, 2); ?></strong>
        </div>
    </div>
    
    <!-- Button to trigger print dialog -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Receipt</button>
    </div>
</body>
</html>
