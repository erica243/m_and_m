<?php session_start(); ?>
<?php
include('admin/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = htmlspecialchars(trim($_POST['otp']));
    $email = $_SESSION['email'];

    // Retrieve stored OTP
    $stmt = $conn->prepare("SELECT otp FROM user_info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($storedOtp);
    $stmt->fetch();

    // Check OTP
    if ($otp == $storedOtp) {
        echo "OTP verified successfully!";
        
        // Clear OTP after successful verification
        $stmt = $conn->prepare("UPDATE user_info SET otp = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        header("Location: success.php"); // Redirect to a success page
        exit();
    } else {
        echo "Invalid OTP. Please try again.";
    }
}
?>

<!-- HTML Form -->
<form action="" method="POST">
    <input type="text" name="otp" required placeholder="Enter OTP">
    <button type="submit">Verify OTP</button>
</form>
