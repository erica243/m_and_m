<?php
include 'admin/db_connect.php';

if (!isset($_SESSION['login_user_id'])) {
    echo "<script>alert('Please login first.'); location.replace('login.php')</script>";
}

$chk = $conn->query("SELECT * FROM cart WHERE user_id = {$_SESSION['login_user_id']}")->num_rows;
if ($chk <= 0) {
    echo "<script>alert('You don\'t have an Item in your cart yet.'); location.replace('./')</script>";
}
?>
<header class="masthead">
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-10 align-self-center mb-4 page-title">
                <h1 class="text-white">Checkout</h1>
                <hr class="divider my-4 bg-dark" />
            </div>
        </div>
    </div>
</header>
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="" id="checkout-frm">
                <h4>Select Payment Method</h4>
                <div class="form-group">
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash on Delivery</option>
                        <option value="gcash">G-Cash</option>
                    </select>
                </div>

                <div class="form-group order-type-selection" style="display:none;">
                    <label class="control-label">Order Type</label>
                    <div>
                        <label><input type="radio" name="order_type" value="delivery" id="delivery"> Delivery</label>
                        <label><input type="radio" name="order_type" value="pickup" id="pickup"> Pick-up</label>
                    </div>
                </div>

                <div class="form-group delivery-info">
                    <label class="control-label">Firstname</label>
                    <input type="text" name="first_name" required class="form-control" value="<?php echo htmlspecialchars($_SESSION['login_first_name']); ?>" readonly>
                </div>
                <div class="form-group delivery-info">
                    <label class="control-label">Lastname</label>
                    <input type="text" name="last_name" required class="form-control" value="<?php echo htmlspecialchars($_SESSION['login_last_name']); ?>" readonly>
                </div>
                <div class="form-group delivery-info">
                    <label class="control-label">Contact</label>
                    <input type="text" name="mobile" required class="form-control" value="<?php echo htmlspecialchars($_SESSION['login_mobile']); ?>" readonly>
                </div>
                <div class="form-group delivery-info">
                    <label class="control-label">Address</label>
                    <textarea cols="30" rows="3" name="address" required class="form-control" readonly><?php echo htmlspecialchars($_SESSION['login_address']); ?></textarea>
                </div>
                <div class="form-group delivery-info">
                    <label class="control-label">Email</label>
                    <input type="email" name="email" required class="form-control" value="<?php echo htmlspecialchars($_SESSION['login_email']); ?>" readonly>
                </div>

                <div class="form-group pickup-info" style="display:none;">
                    <label class="control-label">Pick-up Date</label>
                    <input type="date" name="pickup_date" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group pickup-info" style="display:none;">
                    <label class="control-label">Pick-up Time</label>
                    <input type="time" name="pickup_time" class="form-control">
                </div>

                <div class="form-group payment-proof" style="display:none;">
                    <label class="control-label">Upload Payment Proof</label>
                    <input type="file" name="payment_proof" accept="image/*" class="form-control" required>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree_terms" required>
                        <label class="form-check-label" for="agree_terms">
                            I agree that orders cannot be canceled after placing.
                        </label>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-block btn-outline-dark">Place Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 and jQuery CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
    $('#payment_method').change(function(){
        if ($(this).val() == 'gcash') {
            Swal.fire({
                title: 'Scan QR Code with G-Cash',
                imageUrl: 'assets/img/gcash.jpg',
                imageWidth: 500,
                imageHeight: 600,
                imageAlt: 'G-Cash QR Code',
                showCloseButton: true,
                confirmButtonText: 'Proceed',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.order-type-selection').show();
                    $('.payment-proof').show();
                }
            });
        } else {
            $('.order-type-selection').show();
            $('.payment-proof').hide();
        }
    });

    $('input[name="order_type"]').change(function(){
        if ($('#delivery').is(':checked')) {
            $('.delivery-info').show();
            $('.pickup-info').hide();
        } else if ($('#pickup').is(':checked')) {
            $('.pickup-info').show();
            $('.delivery-info').hide();
        } else {
            $('.delivery-info').hide();
            $('.pickup-info').hide();
        }
    });

    $('#checkout-frm').submit(function(e){
        e.preventDefault();

        if (!$("#agree_terms").is(":checked")) {
            Swal.fire({
                icon: 'warning',
                title: 'Agreement Required',
                text: 'Please agree that orders cannot be canceled after placing.',
                showConfirmButton: true,
            });
            return;
        }

        start_load();

        var formData = new FormData(this);

        $.ajax({
            url: "admin/ajax.php?action=save_order",
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(resp){
                if (resp == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed Successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        location.replace('index.php?page=home');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Place Order',
                        text: resp.msg || 'Please try again later.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                end_load();
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to process the order: ' + error,
                    showConfirmButton: true,
                });
                end_load();
            }
        });
    });
});

function start_load(){
    $('body').prepend('<div id="preloader"></div>');
}

function end_load(){
    $('#preloader').fadeOut('fast', function() {
        $(this).remove();
    });
}
</script>
