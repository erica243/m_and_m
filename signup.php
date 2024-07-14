<?php 
session_start();

$first_nameErr = $last_nameErr = $mobileErr = "";
$first_name = $last_name = $mobile = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($_POST["first_name"])) {
        $first_nameErr = "First name is required";
    } else {
        $first_name = test_input($_POST["first_name"]);
        // check if the name only contains letters and white space
        if (!preg_match("/^[a-zA-Z-' ]*$/", $first_name)) {
            $first_nameErr = "Only letters and white space allowed";
        }
    }
    
    if (empty($_POST["last_name"])) {
        $last_nameErr = "Last name is required";
    } else {
        $last_name = test_input($_POST["last_name"]);
        // check if the name only contains letters and white space
        if (!preg_match("/^[a-zA-Z-' ]*$/", $last_name)) {
            $last_nameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["mobile"])) {
        $mobileErr = "Mobile number is required";
    } else {
        $mobile = test_input($_POST["mobile"]);
        // check if phone only contains numbers
        if (!preg_match("/^[0-9]*$/", $mobile)) {
            $mobileErr = "Only numbers allowed";
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<div class="container-fluid">
    <form action="" id="signup-frm">
        <div class="form-group">
            <label for="" class="control-label">Firstname</label>
            <input type="text" name="first_name" required="" class="form-control">
        </div>
        <div class="form-group">
            <label for="" class="control-label">Lastname</label>
            <input type="text" name="last_name" required="" class="form-control">
        </div>
        <div class="form-group">
            <label for="" class="control-label">Contact</label>
            <input type="tel" id="mobile" name="mobile" maxlength="14" required="" class="form-control" oninput="formatPhoneNumber()">
        </div>
        <div class="form-group">
            <label for="" class="control-label">Address</label>
            <textarea cols="30" rows="3" name="address" required="" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Email</label>
            <input type="email" name="email" required="" class="form-control">
        </div>
        <div class="form-group">
            <label for="" class="control-label">Password</label>
            <input type="password" name="password" required="" class="form-control">
        </div>
        <button type="submit" class="button btn btn-info btn-sm">Create</button>
    </form>
</div>

<script>
function formatPhoneNumber() {
    var phoneNumber = document.getElementById("mobile").value;
    // Remove non-numeric characters
    var cleanedNumber = phoneNumber.replace(/\D/g, '');
    // Add formatting
    var formattedNumber = cleanedNumber.replace(/(\d{3})(\d{4})(\d{4})/, "($1) $2-$3");
    // Update input value with formatted number
    document.getElementById("mobile").value = formattedNumber;
}

$('#signup-frm').submit(function(e){
    e.preventDefault();
    $('#signup-frm button[type="submit"]').attr('disabled', true).html('Saving...');
    if ($(this).find('.alert-danger').length > 0) {
        $(this).find('.alert-danger').remove();
    }
    $.ajax({
        url: 'admin/ajax.php?action=signup',
        method: 'POST',
        data: $(this).serialize(),
        error: err => {
            console.log(err);
            $('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');
        },
        success: function(resp) {
            console.log(resp); // Log the server response
            if (resp == 1) {
                location.href = '<?php echo isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?page=home' ?>';
            } else if (resp == 0) {
                $('#signup-frm').prepend('<div class="alert alert-danger">Email already exists.</div>');
                $('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');
            } else {
                $('#signup-frm').prepend('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                $('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');
            }
        }
    });
});
</script>

<style>
#uni_modal .modal-footer {
    display: none;
}
</style>
