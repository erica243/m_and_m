<?php
session_start();
require_once('admin/db_connect.php');

$email = $_GET['email'] ?? '';

if(empty($email)) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <form id="reset-password-form">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            
            <div class="form-group mb-3">
                <label for="otp">Enter OTP</label>
                <input type="text" name="otp" required class="form-control" maxlength="6" pattern="\d{6}">
                <small class="form-text text-muted">
                    Enter the 6-digit code sent to your email
                </small>
            </div>

            <div class="form-group mb-3">
                <label for="password">New Password</label>
                <input type="password" name="password" required class="form-control">
                <small class="form-text text-muted">
                    Password must be at least 8 characters long and include uppercase, lowercase, numbers, and symbols.
                </small>
            </div>
            
            <div class="form-group mb-3">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#reset-password-form').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            
            // Validate password
            let password = form.find('input[name="password"]').val();
            let confirm_password = form.find('input[name="confirm_password"]').val();
            
            if(password !== confirm_password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Passwords do not match'
                });
                return;
            }
            
            if(password.length < 8 || 
               !/[A-Z]/.test(password) || 
               !/[a-z]/.test(password) || 
               !/[0-9]/.test(password) || 
               !/[^A-Za-z0-9]/.test(password)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Password',
                    text: 'Password must be at least 8 characters long and include uppercase, lowercase, numbers, and symbols'
                });
                return;
            }
            
            form.find('button').attr('disabled', true).html('Processing...');
            
            $.ajax({
                url: 'admin/ajax.php?action=reset_password',
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(resp) {
                    if(resp.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Your password has been updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = 'index.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: resp.message
                        });
                        form.find('button').removeAttr('disabled').html('Update Password');
                    }
                },
                error: function(err) {
                    console.log(err);
                    form.find('button').removeAttr('disabled').html('Update Password');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.'
                    });
                }
            });
        });
    </script>
</body>
</html>
