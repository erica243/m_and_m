<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Ordering Receipt</title>
    <style>
        /* Print-specific CSS */
        @media print {
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .print-container {
                width: 100%;
                padding: 20px;
                border: 1px solid #ddd;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .order-details {
                margin-bottom: 20px;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
            }
            /* Hide non-print elements */
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print Receipt</button>
    </div>
    
    <div class="print-container">
        <div class="logo">
            <img src="logo.png" alt="Company Logo" width="200">
        </div>
        
        <div class="order-details">
            <h1>Order Receipt</h1>
            <p><strong>Order Number:</strong> #12345</p>
            <p><strong>Date:</strong> July 23, 2024</p>
            <p><strong>Customer Name:</strong> John Doe</p>
            <p><strong>Product:</strong> Chocolate Cake</p>
            <p><strong>Quantity:</strong> 1</p>
            <p><strong>Total Price:</strong> $25.00</p>
        </div>
        
        <div class="footer">
            <p>Thank you for your order!</p>
        </div>
    </div>
</body>
</html>
