<?php
session_start();
?>
<div class="container-fluid">
    <form action="" id="forgot-password-frm">
        <div class="form-group">
            <label for="" class="control-label">Email</label>
            <input type="email" name="email" required="" class="form-control">
        </div>
        <button class="button btn btn-dark btn-sm">Reset Password</button>
        <div class="mt-3">
            <a href="login.php" class="text-dark">Back to Login</a>
        </div>
    </form>
</div>

<script>
$('#forgot-password-frm').submit(function(e){
    e.preventDefault();
    $('#forgot-password-frm button[type="submit"]').attr('disabled', true).html('Processing...');
    if($(this).find('.alert-danger').length > 0)
        $(this).find('.alert-danger').remove();
    $.ajax({
        url: 'admin/ajax.php?action=forgot_password',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        error: err => {
            console.log(err);
            $('#forgot-password-frm button[type="submit"]').removeAttr('disabled').html('Reset Password');
        },
        success: function(resp){
            if(resp.status == 'success'){
                $('#forgot-password-frm').prepend('<div class="alert alert-success">Password reset link has been sent to your email!</div>');
                $('#forgot-password-frm')[0].reset();
            }else{
                $('#forgot-password-frm').prepend('<div class="alert alert-danger">' + resp.message + '</div>');
            }
            $('#forgot-password-frm button[type="submit"]').removeAttr('disabled').html('Reset Password');
        }
    });
});
</script>