<?php
// forgot_password.php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <form id="forgot-password-form">
            <div class="form-group mb-3">
                <label for="email">Email Address</label>
                <input type="email" name="email" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
            <div class="mt-3">
                <a href="javascript:void(0)" onclick="location.reload()" class="text-dark">Back to Login</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#forgot-password-form').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            form.find('button').attr('disabled', true).html('Processing...');

            $.ajax({
                url: 'admin/ajax.php?action=forgot_password',
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(resp) {
                    if(resp.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Password reset OTP has been sent to your email',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = 'reset_password.php?email=' + encodeURIComponent(form.find('input[name="email"]').val());
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: resp.message
                        });
                        form.find('button').removeAttr('disabled').html('Reset Password');
                    }
                },
                error: function(err) {
                    console.log(err);
                    form.find('button').removeAttr('disabled').html('Reset Password');
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
