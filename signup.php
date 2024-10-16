<?php session_start() ?>
<?php
        // Check if the form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $first_name = $_POST['first_name'];

            // Sanitize input to remove any HTML or script tags
            $first_name_sanitized = htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8');

            // Validate the input: allow letters, hyphens, apostrophes, and spaces, but block < or >
            if (!preg_match("/^[A-Za-z\s'-]+$/", $first_name)) {
                echo '<div class="alert alert-danger">Invalid input: Please enter a valid name (letters, hyphens, apostrophes, and spaces only).</div>';
            } else if ($first_name !== $first_name_sanitized) {
                echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed.</div>';
            } else {
                // If valid, display success message
                echo '<div class="alert alert-success">Input is valid. Form submitted successfully!</div>';
                // Here, you can proceed with storing or processing the sanitized input.
            }

            $last_name = $_POST['last_name'];

            // Sanitize input to remove any HTML or script tags
            $last_name_sanitized = htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8');

            // Validate the input: allow letters, hyphens, apostrophes, and spaces, but block < or >
            if (!preg_match("/^[A-Za-z\s'-]+$/", $last_name)) {
                echo '<div class="alert alert-danger">Invalid input: Please enter a valid name (letters, hyphens, apostrophes, and spaces only).</div>';
            } else if ($last_name !== $last_name_sanitized) {
                echo '<div class="alert alert-danger">Invalid input: HTML or script tags are not allowed.</div>';
            } else {
                // If valid, display success message
                echo '<div class="alert alert-success">Input is valid. Form submitted successfully!</div>';
                // Here, you can proceed with storing or processing the sanitized input.
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

        ?>
<div class="container-fluid">
	<form action="" id="signup-frm">
		<div class="form-group">
         <label for="first_name">First Name</label>
         <input type="text" class="form-control" id="fname" name="first_name" placeholder="" required oninput="validateInput()" pattern="[A-Za-z\s'-]+">
                     
		</div>
		<div class="form-group">
			<label for="last_name" class="control-label">Lastname</label>
            <input type="text" class="form-control" id="lname" name="last_name" placeholder="" required oninput="validateInputs()" pattern="[A-Za-z\s'-]+">
            </div>
		<div class="form-group">
			<label for="" class="control-label">Contact</label>
			<input type="text" name="mobile" required="" class="form-control" maxlength="11">
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
		<button type="submit" class="btn btn-info btn-sm">Create</button>
	</form>
</div>

<style>
	#uni_modal .modal-footer{
		display:none;
	}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
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
        switch(result.strength) {
            case 0:
            case 1:
                strengthText = 'Very weak';
                $('#password-strength-meter .progress-bar').removeClass().addClass('progress-bar bg-danger');
                break;
            case 2:
                strengthText = 'Weak';
                $('#password-strength-meter .progress-bar').removeClass().addClass('progress-bar bg-warning');
                break;
            case 3:
                strengthText = 'Fair';
                $('#password-strength-meter .progress-bar').removeClass().addClass('progress-bar bg-info');
                break;
            case 4:
                strengthText = 'Good';
                $('#password-strength-meter .progress-bar').removeClass().addClass('progress-bar bg-primary');
                break;
            case 5:
                strengthText = 'Strong';
                $('#password-strength-meter .progress-bar').removeClass().addClass('progress-bar bg-success');
                break;
        }

        var feedbackText = result.feedback.length > 0 ? 'Password should ' + result.feedback.join(", ") + '.' : '';
        $('#password-strength-text').html('Password strength: ' + strengthText + '<br>' + feedbackText);
    });

    // Form submit with SweetAlert
    $('#signup-frm').submit(function(e){
        e.preventDefault();
        var password = $('#password').val();
        var result = checkPasswordStrength(password);

        if (result.strength < 5) {
            Swal.fire({
                icon: 'error',
                title: 'Weak Password',
                text: 'Please create a stronger password. ' + result.feedback.join(", "),
            });
            return false;
        }

        // Disable the button and show loading text
        $('#signup-frm button[type="submit"]').attr('disabled',true).html('Saving...');

        // AJAX request
        $.ajax({
            url: 'admin/ajax.php?action=signup',
            method: 'POST',
            data: $(this).serialize(),
            error: function(err){
                console.log(err);
                $('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');
            },
            success: function(resp){
                if(resp == 1){
                    Swal.fire({
                        icon: 'success',
                        title: 'Signup Successful',
                        text: 'You have successfully created an account!',
                    }).then(function() {
                        location.href = '<?php echo isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?page=home' ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Exists',
                        text: 'The email you provided is already registered.',
                    });
                    $('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');
                }
            }
        });
    });
});
</script>