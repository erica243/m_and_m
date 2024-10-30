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
    $stmt = $conn->prepare("INSERT INTO product_ratings (product_id, user_id, rating, feedback) VALUES (?, ?, ?, ?)");
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

// Fetch all ratings and feedback for the product, along with the user's email from user_info table
$feedback_qry = $conn->query("
    SELECT pr.rating, pr.feedback, ui.email 
    FROM product_ratings pr
    JOIN user_info ui ON pr.user_id = ui.user_id
    WHERE pr.product_id = $product_id
");

$feedbacks = $feedback_qry->fetch_all(MYSQLI_ASSOC);

// Check product availability
$availability = $qry['status'];
$stock_quantity = $qry['stock']; // Stock quantity

function display_star_rating($rating) {
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars >= 0.5) ? 1 : 0;
    $empty_stars = 5 - ($full_stars + $half_star);

    for ($i = 0; $i < $full_stars; $i++) {
        echo '<i class="fas fa-star" style="color: #ffd700;"></i>';
    }
    if ($half_star) {
        echo '<i class="fas fa-star-half-alt" style="color: #ffd700;"></i>';
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        echo '<i class="far fa-star" style="color: #ffd700;"></i>';
    }
}
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
        .star { cursor: pointer; font-size: 2rem; color: #ddd; }
        .star.selected { color: #ffd700; }
        .btn.disabled { opacity: 0.65; cursor: not-allowed; }
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
            <p class="card-text">Stock: <span id="stock_display"><?php echo $stock_quantity; ?></span> available</p>
            
            <p class="card-text">Average Rating: 
                <?php if ($avg_rating !== 'No ratings yet'): ?>
                    <?php display_star_rating($avg_rating); ?> (<?php echo $avg_rating; ?> / 5)
                <?php else: ?>
                    No ratings yet
                <?php endif; ?>
            </p>
            
            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="control-label">Qty</label>
                </div>
                <div class="input-group col-md-7">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button" id="qty-minus" <?php echo $stock_quantity <= 0 ? 'disabled' : ''; ?>>
                            <span class="fa fa-minus"></span>
                        </button>
                    </div>
                    <input type="number" id="qty-input" readonly value="1" min="1" max="<?php echo $stock_quantity; ?>" class="form-control text-center" name="qty" <?php echo $stock_quantity <= 0 ? 'disabled' : ''; ?>>
                    <div class="input-group-append">
                        <button class="btn btn-outline-dark" type="button" id="qty-plus" <?php echo $stock_quantity <= 0 ? 'disabled' : ''; ?>>
                            <span class="fa fa-plus"></span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="text-center mb-4">
                <button 
                    class="btn btn-outline-dark btn-sm btn-block <?php echo !$availability || $stock_quantity <= 0 ? 'disabled' : ''; ?>" 
                    id="add_to_cart_modal" 
                    data-availability="<?php echo $availability; ?>" 
                    data-stock="<?php echo $stock_quantity; ?>"
                    <?php echo !$availability || $stock_quantity <= 0 ? 'disabled' : ''; ?>
                >
                    <i class="fa fa-cart-plus"></i> <?php echo ($availability && $stock_quantity > 0) ? 'Add to Cart' : 'Unavailable'; ?>
                </button>
            </div>
        </div>
    </div>

    <h5 class="mt-4">User Ratings and Feedback</h5>
    <?php if ($feedbacks): ?>
        <div class="list-group">
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="list-group-item">
                    <h6 class="mb-1">Rating: 
                        <?php display_star_rating($feedback['rating']); ?> 
                        (<?php echo htmlspecialchars($feedback['rating']); ?> / 5)
                    </h6>
                    <p><?php echo htmlspecialchars($feedback['feedback']); ?></p>
                    <small>Submitted by: <?php echo htmlspecialchars($feedback['email']); ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No feedback available yet.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('#qty-minus').click(function(){
        var qty = $('input[name="qty"]').val();
        if (qty > 1) {
            $('input[name="qty"]').val(parseInt(qty) - 1);
        }
    });

    $('#qty-plus').click(function(){
        var qty = $('input[name="qty"]').val();
        var maxQty = <?php echo $stock_quantity; ?>;
        if (qty < maxQty) {
            $('input[name="qty"]').val(parseInt(qty) + 1);
        } else {
            Swal.fire('Limit Reached', 'You have reached the maximum quantity available.', 'warning');
        }
    });

    $('#add_to_cart_modal').click(function(){
        var availability = $(this).data('availability');
        var stock = $(this).data('stock');
        
        if (!availability || stock <= 0) {
            Swal.fire('Unavailable', 'This product is currently unavailable or out of stock.', 'warning');
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
                    data: { pid: '<?php echo $product_id ?>', qty: $('input[name="qty"]').val() },
                    success: function(resp) {
                        if (resp == 1) {
                            let currentStock = parseInt($('#stock_display').text());
                            let qty = parseInt($('input[name="qty"]').val());
                            $('#stock_display').text(currentStock - qty); // Update stock display on the page
                            Swal.fire('Added!', 'The product has been added to your cart.', 'success');
                        } else {
                            Swal.fire('Error!', 'There was an error adding the product to your cart.', 'error');
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>
