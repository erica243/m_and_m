<?php session_start(); ?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include('admin/db_connect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to send OTP email
function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'mandmcakeorderingsystem.com'; 
        $mail->Password = 'dgld kvqo yecu wdka'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('mandmcakeorderingsystem.com', 'Your Name');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP code is: <strong>$otp</strong>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $address = htmlspecialchars(trim($_POST['address']));

    // Validate inputs
    if (!preg_match("/^[A-Za-z\s'-]+$/", $first_name) || !preg_match("/^[A-Za-z\s'-]+$/", $last_name)) {
        echo '<div class="alert alert-danger">Invalid input for names.</div>';
        exit();
    }

    // Generate OTP
    $otp = rand(100000, 999999); 

    // Send OTP email
    if (sendOtpEmail($email, $otp)) {
        // Store OTP and user information in the database
        $stmt = $conn->prepare("UPDATE user_info SET otp = ? WHERE email = ?");
        $stmt->bind_param("is", $otp, $email);
        
        if ($stmt->execute()) {
            $_SESSION['email'] = $email; // Store email in session
            echo '<div class="alert alert-success">OTP has been sent to your email. Please verify it.</div>';
            header("Location: verify.php");
            exit();
        } else {
            echo '<div class="alert alert-danger">Failed to store OTP in the database. Please try again.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Failed to send OTP. Please try again.</div>';
    }
}

// Fetch all addresses from the shipping_info table
$addresses = [];
$stmt = $conn->prepare("SELECT address FROM shipping_info");
$stmt->execute();
$result = $stmt->get_result();

// Store all addresses in the addresses array
while ($row = $result->fetch_assoc()) {
    $addresses[] = $row['address'];
}

$conn->close();
?>

<div class="container-fluid">
    <form action="" method="POST" id="signup-frm">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="fname" name="first_name" placeholder="Enter Firstname" required>
        </div>
        <div class="form-group">
            <label for="last_name" class="control-label">Lastname</label>
            <input type="text" class="form-control" id="lname" name="last_name" placeholder="Enter Lastname" required>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Contact</label>
            <input type="tel" name="mobile" required="" class="form-control" maxlength="11">
        </div>
        <div class="form-group">
            <label for="" class="control-label">Address</label>
            <select name="address" class="form-control" required>
                <option value="">Select Address</option>
                <?php foreach ($addresses as $address): ?>
                    <option value="<?php echo htmlspecialchars($address); ?>"><?php echo htmlspecialchars($address); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Email</label>
            <input type="email" name="email" required="" class="form-control">
        </div>
        <div class="form-group">
            <label for="" class="control-label">Password</label>
            <input type="password" name="password" id="password" required="" class="form-control">
            <input type="checkbox" id="show-password"> Show Password
            <small class="form-text text-muted">Password must be at least 8 characters long and include a combination of uppercase letters, lowercase letters, numbers, and symbols.</small>
            <div id="password-strength-meter" class="progress mt-2" style="height: 20px;">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small id="password-strength-text" class="form-text mt-2"></small>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="terms" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="terms_and_conditions.php" target="_blank">Terms and Conditions</a>
            </label>
        </div>
        <button type="submit" class="btn btn-info btn-sm">Create</button>
    </form>
</div>

<style>
    #uni_modal .modal-footer {
        display: none;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#signup-frm').submit(function (e) {
        e.preventDefault();
        $('#signup-frm button[type="submit"]').attr('disabled', true).html('Saving...');

        $.ajax({
            url: 'admin/ajax.php?action=signup',
            method: 'POST',
            data: $(this).serialize(),
            error: err => {
                console.log(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred while saving. Please try again.',
                });
                $('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');
            },
            success: function (resp) {
                if (resp == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Account created successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.href = '<?php echo isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?page=home' ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Email already exists.',
                    });
                    $('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');
                }
            }
        });
    });

    $(document).ready(function() {
        // Password visibility toggle
        $('#show-password').on('click', function() {
            var passwordField = $('#password');
            if ($(this).is(':checked')) {
                passwordField.attr('type', 'text');
            } else {
                passwordField.attr('type', 'password');
            }
        });

        // Password strength checker
        function checkPasswordStrength(password) {
            var strength = 0;
            var feedback = [];

            // Check length
            if (password.length >= 8) strength += 1;
            else feedback.push("be at least 8 characters long");

            // Check for uppercase letters
            if (password.match(/[A-Z]/)) strength += 1;
            else feedback.push("include uppercase letters");

            // Check for lowercase letters
            if (password.match(/[a-z]/)) strength += 1;
            else feedback.push("include lowercase letters");

            // Check for numbers
            if (password.match(/\d/)) strength += 1;
            else feedback.push("include numbers");

            // Check for symbols
            if (password.match(/[^a-zA-Z\d]/)) strength += 1;
            else feedback.push("include symbols");

            return { strength: strength, feedback: feedback };
        }

        $('#password').on('input', function() {
            var password = $(this).val();
            var result = checkPasswordStrength(password);

            // Update progress bar
            var percent = (result.strength / 5) * 100;
            $('#password-strength-meter .progress-bar').css('width', percent + '%').attr('aria-valuenow', percent);

            // Update feedback text
            var strengthText;
            switch (result.strength) {
                case 0:
                case 1:
                    strengthText = 'Very Weak';
                    break;
                case 2:
                    strengthText = 'Weak';
                    break;
                case 3:
                    strengthText = 'Moderate';
                    break;
                case 4:
                    strengthText = 'Strong';
                    break;
                case 5:
                    strengthText = 'Very Strong';
                    break;
            }
            $('#password-strength-text').text(strengthText + ' (' + result.feedback.join(', ') + ')');
        });
    });
</script>
