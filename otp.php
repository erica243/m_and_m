<?php
session_start();
require 'vendor/autoload.php'; // Adjust the path if necessary
use Twilio\Rest\Client;

// Twilio credentials
$sid = 'AC36f2fe29a8b794deeff40e5ccf8c5b1a'; // Replace with your Account SID
$token = '39f6faa4fc2f3a1f625f9521af45a28e'; // Replace with your Auth Token
$twilio_number = '09158259643'; // Replace with your Twilio number

// Function to send SMS
function sendSms($to, $body) {
    global $sid, $token, $twilio_number;

    $client = new Client($sid, $token);
    try {
        $client->messages->create($to, [
            'from' => $twilio_number,
            'body' => $body
        ]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Handle sending OTP
if (isset($_POST['send_otp'])) {
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $otp = rand(100000, 999999); // Generate a 6-digit OTP

    if (sendSms($mobile, "Your OTP code is: $otp")) {
        // Store OTP in session for verification
        $_SESSION['otp'] = $otp;
        $_SESSION['mobile'] = $mobile; // Store mobile number for further use
        echo "OTP sent successfully!";
    } else {
        echo "Failed to send OTP.";
    }
}

// Handle verifying OTP
if (isset($_POST['verify_otp'])) {
    $input_otp = htmlspecialchars(trim($_POST['otp']));

    if ($input_otp == $_SESSION['otp']) {
        echo "OTP verified successfully!";
        // Here you can proceed with further actions (e.g., user registration, login, etc.)
        unset($_SESSION['otp']); // Clear OTP from session after verification
    } else {
        echo "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
</head>
<body>
    <h1>Send OTP</h1>
    <form method="POST">
        <input type="tel" name="mobile" placeholder="Enter your mobile number" required>
        <button type="submit" name="send_otp">Send OTP</button>
    </form>

    <h1>Verify OTP</h1>
    <form method="POST">
        <input type="text" name="otp" placeholder="Enter your OTP" required>
        <button type="submit" name="verify_otp">Verify OTP</button>
    </form>
</body>
</html>
