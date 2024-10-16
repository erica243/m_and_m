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

        input[type="text"], input[type="number"] {
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

            input[type="text"], input[type="number"] {
                padding: 6px;
            }

            input[type="submit"] {
                padding: 10px;
            }
        }

        /* Success message styling */
        .success-message {
            text-align: center;
            color: green;
            margin: 20px 0;
            font-weight: bold;
        }

        /* Error message styling */
        .error-message {
            text-align: center;
            color: red;
            margin: 20px 0;
            font-weight: bold;
        }

        /* Results styling */
        .results {
            margin: 20px 0;
            text-align: center;
        }

    </style>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<h1>Set Shipping Amounts</h1>

<?php
include 'db_connect.php'; // Database connection

$success_message = ""; // Initialize success message variable
$error_message = ""; // Initialize error message variable

// Initialize an array to hold shipping amounts for display
$submitted_shipping_amounts = [];

// Check if form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = $conn->real_escape_string($_POST['address']); // Get and sanitize address input
    $amount = floatval($_POST['shipping_amount']); // Get and sanitize shipping amount input

    // Insert or update the shipping amount in your database
    $query = "INSERT INTO shipping_info (address, shipping_amount) VALUES ('$address', $amount)
              ON DUPLICATE KEY UPDATE shipping_amount = $amount"; // Adjust table and column names as needed

    if (!$conn->query($query)) {
        $error_message = "Error updating shipping amount for address: $address - " . $conn->error;
    } else {
        // Store the address and amount for displaying results
        $submitted_shipping_amounts[$address] = $amount;
        $success_message = "Shipping amount saved successfully!";
    }
}

// Fetch all existing shipping records
$query = "SELECT address, shipping_amount FROM shipping_info";
$result = $conn->query($query);

// Store all shipping amounts in an array
$all_shipping_amounts = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_shipping_amounts[$row['address']] = $row['shipping_amount'];
    }
}

?>

<form id='shippingForm' action='' method='POST'> <!-- Action set to same page -->
    <table>
        <tr>
            <th>Address</th>
            <th>Shipping Amount</th>
        </tr>
        <tr>
            <td><input type="text" name="address" placeholder="Enter address" required></td>
            <td><input type="number" name="shipping_amount" placeholder="Enter amount" required></td>
        </tr>
    </table>
    <input type='submit' value='Save Shipping Amount'>
</form>

<?php
// Display success or error message
if ($error_message): ?>
    <div class="error-message"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if ($success_message): ?>
    <div class="success-message"><?php echo $success_message; ?></div>
<?php endif; ?>

<!-- Display all shipping amounts -->
<?php if (!empty($all_shipping_amounts)): ?>
    <div class="results">
        
        <table>
            <tr><th>Address</th><th>Shipping Amount</th></tr>
            <?php foreach ($all_shipping_amounts as $address => $amount): ?>
                <tr>
                    <td><?php echo htmlspecialchars($address); ?></td>
                    <td><?php echo htmlspecialchars($amount); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<script>
    // Add SweetAlert confirmation before submitting the form
    document.getElementById('shippingForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form from submitting immediately

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to save this shipping amount?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if confirmed
                this.submit();
            }
        });
    });
</script>

</body>
</html>

