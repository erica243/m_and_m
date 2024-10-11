<?php
include 'db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shipping_amounts = $_POST['shipping_amount'];

    foreach ($shipping_amounts as $address => $amount) {
        // Sanitize input
        $address = $conn->real_escape_string($address);
        $amount = floatval($amount);

        // Here, you can insert or update the shipping amount in your database
        $query = "INSERT INTO shipping_info (address, shipping_amount) VALUES ('$address', $amount)
                  ON DUPLICATE KEY UPDATE shipping_amount = $amount"; // Adjust table and column names as needed

        if ($conn->query($query) === TRUE) {
            echo "Shipping amount updated for address: $address<br>";
        } else {
            echo "Error: " . $conn->error . "<br>";
        }
    }
}

$conn->close();
?>
