<?php
include 'admin/db_connect.php';
// var_dump($_SESSION);
$chk = $conn->query("SELECT * FROM cart where user_id = {$_SESSION['login_user_id']} ")->num_rows;
if($chk <= 0){
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
                    <select name="payment_method" id="payment_method" class="form-control" required="">
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash on Delivery</option>
                        <option value="gcash">G-Cash</option>
                        <!-- Add more payment methods as needed -->
                    </select>
                </div>

                <!-- Delivery or Pick-up Selection -->
                <div class="form-group gcash-info" style="display:none;">
                    <label for="" class="control-label">Order Type</label>
                    <div>
                        <label><input type="checkbox" name="order_type" value="delivery" id="delivery"> Delivery</label>
                        <label><input type="checkbox" name="order_type" value="pickup" id="pickup"> Pick-up</label>
                    </div>
                </div>
                <!-- End Delivery or Pick-up Selection -->

                <div class="form-group delivery-info">
                    <label for="" class="control-label">Firstname</label>
                    <input type="text" name="first_name" required="" class="form-control" value="<?php echo $_SESSION['login_first_name'] ?>">
                </div>
                <div class="form-group delivery-info">
                    <label for="" class="control-label">Lastname</label>
                    <input type="text" name="last_name" required="" class="form-control" value="<?php echo $_SESSION['login_last_name'] ?>">
                </div>
                <div class="form-group delivery-info">
                    <label for="" class="control-label">Contact</label>
                    <input type="text" name="mobile" required="" class="form-control" value="<?php echo $_SESSION['login_mobile'] ?>">
                </div>
                <div class="form-group delivery-info">
                    <label for="" class="control-label">Address</label>
                    <textarea cols="30" rows="3" name="address" required="" class="form-control"><?php echo $_SESSION['login_address'] ?></textarea>
                </div>
                <div class="form-group delivery-info">
                    <label for="" class="control-label">Email</label>
                    <input type="email" name="email" required="" class="form-control" value="<?php echo $_SESSION['login_email'] ?>">
                </div>

                <!-- Pick-up Date and Time -->
                <div class="form-group pickup-info" style="display:none;">
                    <label for="" class="control-label">Pick-up Date</label>
                    <input type="date" name="pickup_date" class="form-control">
                </div>
                <div class="form-group pickup-info" style="display:none;">
                    <label for="" class="control-label">Pick-up Time</label>
                    <input type="time" name="pickup_time" class="form-control">
                </div>
                <!-- End Pick-up Date and Time -->

                <!-- Agreement Checkbox -->
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree_terms" required="">
                        <label class="form-check-label" for="agree_terms">
                            I agree that orders cannot be canceled after placing.
                        </label>
                    </div>
                </div>
                <!-- End Agreement Checkbox -->

                <div class="text-center">
                    <button type="submit" class="btn btn-block btn-outline-dark">Place Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    $(document).ready(function(){
        $('#payment_method').change(function(){
            if($(this).val() == 'gcash') {
                $('.gcash-info').show();
            } else {
                $('.gcash-info').hide();
                $('.delivery-info').show();
                $('.pickup-info').hide();
                $('#delivery').prop('checked', false);
                $('#pickup').prop('checked', false);
            }
        });

        $('#delivery').change(function(){
            if($(this).is(':checked')) {
                $('.delivery-info').show();
                $('.pickup-info').hide();
                $('#pickup').prop('checked', false);
            } else {
                $('.delivery-info').hide();
            }
        });

        $('#pickup').change(function(){
            if($(this).is(':checked')) {
                $('.pickup-info').show();
                $('.delivery-info').hide();
                $('#delivery').prop('checked', false);
            } else {
                $('.pickup-info').hide();
            }
        });

        $('#checkout-frm').submit(function(e){
            e.preventDefault();

            // Check if the agreement checkbox is checked
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
            $.ajax({
                url:"admin/ajax.php?action=save_order",
                method:'POST',
                data:$(this).serialize(),
                success:function(resp){
                    if(resp==1){
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
                            text: 'Please try again later.',
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
</script>
