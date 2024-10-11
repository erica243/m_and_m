<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Amounts</title>
    <style>
        /* General form styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f7f7f7;
            color: #333;
            font-weight: bold;
        }

        td {
            background-color: #fff;
        }

        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            form {
                width: 100%;
                padding: 10px;
            }

            th, td {
                padding: 10px;
                font-size: 14px;
            }

            input[type="number"] {
                padding: 6px;
            }

            input[type="submit"] {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

    <h1>Set Shipping Amounts</h1>

    <?php
    include 'db_connect.php'; // Database connection

    // Fetch unique addresses
    $query = "SELECT DISTINCT address FROM user_info"; // Adjust the column name as needed
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<form action='process_shipping.php' method='POST'>"; // Action page to process shipping amounts
        echo "<table>";
        echo "<tr><th>Address</th><th>Shipping Amount</th></tr>";

        // Output unique addresses
        while ($row = $result->fetch_assoc()) {
            $address = htmlspecialchars($row['address']); // Sanitize output
            echo "<tr>";
            echo "<td>$address</td>";
            echo "<td><input type='number' name='shipping_amount[$address]' placeholder='Enter amount' required></td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<input type='submit' value='Save Shipping Amounts'>";
        echo "</form>";
    } else {
        echo "<p>No unique addresses found.</p>";
    }

    $conn->close();
    ?>

</body>

</html>
