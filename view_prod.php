<?php 
include 'admin/db_connect.php';
session_start(); // Ensure session handling

// Handle form submission for ratings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];
    $user = $_SESSION['user_id']; // Ensure the user is logged in and their ID is stored in session

    // Insert rating and feedback into the database
    $stmt = $conn->prepare("INSERT INTO product_ratings (product_id, user, rating, feedback) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $product_id, $user, $rating, $feedback);

    if ($stmt->execute()) {
        $success_message = 'Your rating has been submitted.';
    } else {
        $error_message = 'There was an error submitting your rating.';
    }
}

// Fetch product details
$product_id = intval($_GET['id']); // Ensure id is an integer
$qry = $conn->query("SELECT * FROM product_list WHERE id = $product_id")->fetch_array();

// Fetch average rating
$rating_qry = $conn->query("SELECT AVG(rating) as avg_rating FROM product_ratings WHERE product_id = $product_id");
$avg_rating = $rating_qry->fetch_assoc()['avg_rating'];
$avg_rating = $avg_rating ? number_format($avg_rating, 1) : 'No ratings yet';

// Check product availability
$availability = $qry['status']; // Assuming 'available' is a boolean or 1/0
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .star {
            cursor: pointer;
            font-size: 2rem;
            color: #ddd;
        }
        .star.selected {
            color: #ffd700;
        }
        .btn-disabled {
            pointer-events: none;
            opacity: 0.5;
        }
    </style>
</head>
<body>
<div class="container-fluid mt-4">
    <div class="card">
        <img src="assets/img/<?php echo htmlspecialchars($qry['img_path']) ?>" class="card-img-top" alt="Product Image">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($qry['name']) ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($qry['description']) ?></p>
            <p class="card-text">Price: <?php echo number_format($qry['price'], 2) ?></p>
            <p class="card-text">Average Rating: <?php echo $avg_rating ?> / 5</p>
            <p class="card-text <?php echo $availability ? '' : 'text-danger' ?>"><?php echo $availability ? 'In Stock' : 'Unavailable' ?></p>
            <div class="row mb-3">
                <div class="col-md-2"><label class="control-label">Qty</label></div>
                <div class="input-group col-md-7">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button" id="qty-minus"><span class="fa fa-minus"></span></button>
                    </div>
                    <input type="number" readonly value="1" min="1" class="form-control text-center" name="qty">
                    <div class="input-group-append">
                        <button class="btn btn-outline-dark" type="button" id="qty-plus"><span class="fa fa-plus"></span></button>
                    </div>
                </div>
            </div>
            <div class="text-center mb-4">
                <button class="btn btn-outline-dark btn-sm btn-block" id="add_to_cart_modal" data-availability="<?php echo $availability ?>" <?php echo !$availability ? 'class="btn-disabled"' : '' ?>>
                    <i class="fa fa-cart-plus"></i> Add to Cart
                </button>
            </div>
            <!-- Rating Form -->
            <div class="rating-form">
                <h6>Rate this product:</h6>
                <div class="rating">
                    <span class="star" data-rating="5">&#9733;</span>
                    <span class="star" data-rating="4">&#9733;</span>
                    <span class="star" data-rating="3">&#9733;</span>
                    <span class="star" data-rating="2">&#9733;</span>
                    <span class="star" data-rating="1">&#9733;</span>
                </div>
                <input type="hidden" name="rating" id="rating-value">
                <div class="form-group mt-3">
                    <label for="feedback">Feedback:</label>
                    <textarea class="form-control" id="feedback" rows="3" placeholder="Enter your feedback here..."></textarea>
                </div>
                <button class="btn btn-outline-dark btn-sm" id="submit-rating">Submit Rating</button>
            </div>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success mt-3" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Adjust quantity
    $('#qty-minus').click(function(){
        var qty = $('input[name="qty"]').val();
        if (qty > 1) {
            $('input[name="qty"]').val(parseInt(qty) - 1);
        }
    });

    $('#qty-plus').click(function(){
        var qty = $('input[name="qty"]').val();
        $('input[name="qty"]').val(parseInt(qty) + 1);
    });

    // Handle "Add to Cart" button click
    $('#add_to_cart_modal').click(function(){
        var availability = $(this).data('availability');
        
        if (!availability) {
            Swal.fire('Unavailable', 'This product is currently unavailable.', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Add to Cart',
            text: 'Are you sure you want to add this item to your cart?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, add it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'admin/ajax.php?action=add_to_cart',
                    method: 'POST',
                    data: { pid: '<?php echo $product_id ?>', qty: $('[name="qty"]').val() },
                    success: function(resp) {
                        if (resp == 1) {
                            Swal.fire('Added!', 'Item successfully added to cart.', 'success');
                        } else {
                            Swal.fire('Error!', 'Error adding item to cart.', 'error');
                        }
                    }
                });
            }
        });
    });

    // Handle rating submission
    $('.star').click(function(){
        var rating = $(this).data('rating');
        $('#rating-value').val(rating);
        $('.star').each(function(){
            $(this).toggleClass('selected', $(this).data('rating') <= rating);
        });
    });

    $('#submit-rating').click(function(){
        var rating = $('#rating-value').val();
        var feedback = $('#feedback').val();
        if (!rating) {
            Swal.fire('Error', 'Please select a rating.', 'error');
            return;
        }
        $.ajax({
            url: 'admin/ajax.php?action=submit_rating',
            method: 'POST',
            data: { product_id: '<?php echo $product_id ?>', rating: rating, feedback: feedback },
            success: function(resp) {
                if (resp == 1) {
                    Swal.fire('Thank you!', 'Your rating has been submitted.', 'success');
                } else {
                    Swal.fire('Error!', 'There was an error submitting your rating.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
            }
        });
    });
</script>
</body>
</html>
