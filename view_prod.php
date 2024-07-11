<?php 
include 'admin/db_connect.php';
$qry = $conn->query("SELECT * FROM product_list WHERE id = " . $_GET['id'])->fetch_array();

// Fetch average rating
$rating_qry = $conn->query("SELECT AVG(rating) as avg_rating FROM product_ratings WHERE product_id = " . $_GET['id']);
$avg_rating = $rating_qry->fetch_assoc()['avg_rating'];
$avg_rating = $avg_rating ? number_format($avg_rating, 1) : 'No ratings yet';
?>
<div class="container-fluid">
  <div class="card">
    <img src="assets/img/<?php echo $qry['img_path'] ?>" class="card-img-top" alt="...">
    <div class="card-body">
      <h5 class="card-title"><?php echo $qry['name'] ?></h5>
      <p class="card-text truncate"><?php echo $qry['description'] ?></p>
      <p class="card-text">Price: <?php echo number_format($qry['price'], 2) ?></p>
      <p class="card-text">Average Rating: <?php echo $avg_rating ?> / 5</p>
      <div class="form-group"></div>
      <div class="row">
        <div class="col-md-2"><label class="control-label">Qty</label></div>
        <div class="input-group col-md-7 mb-3">
          <div class="input-group-prepend">
            <button class="btn btn-outline-secondary" type="button" id="qty-minus"><span class="fa fa-minus"></span></button>
          </div>
          <input type="number" readonly value="1" min="1" class="form-control text-center" name="qty">
          <div class="input-group-prepend">
            <button class="btn btn-outline-dark" type="button" id="qty-plus"><span class="fa fa-plus"></span></button>
          </div>
        </div>
      </div>
      <div class="text-center">
        <button class="btn btn-outline-dark btn-sm btn-block" id="add_to_cart_modal"><i class="fa fa-cart-plus"></i> Add to Cart</button>
      </div>
      <!-- Rating Form -->
      <div class="rating-form mt-4">
        <h6>Rate this product:</h6>
        <div class="rating">
          <span class="star" data-rating="5">&#9733;</span>
          <span class="star" data-rating="4">&#9733;</span>
          <span class="star" data-rating="3">&#9733;</span>
          <span class="star" data-rating="2">&#9733;</span>
          <span class="star" data-rating="1">&#9733;</span>
        </div>
        <input type="hidden" name="rating" id="rating-value">
        <button class="btn btn-outline-dark btn-sm" id="submit-rating">Submit Rating</button>
      </div>
    </div>
  </div>
</div>

<script>
  $('#qty-minus').click(function(){
    var qty = $('input[name="qty"]').val();
    if(qty == 1){
      return false;
    } else {
      $('input[name="qty"]').val(parseInt(qty) - 1);
    }
  });

  $('#qty-plus').click(function(){
    var qty = $('input[name="qty"]').val();
    $('input[name="qty"]').val(parseInt(qty) + 1);
  });

  $('#add_to_cart_modal').click(function(){
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
        start_load();
        $.ajax({
          url: 'admin/ajax.php?action=add_to_cart',
          method: 'POST',
          data: { pid: '<?php echo $_GET['id'] ?>', qty: $('[name="qty"]').val() },
          success: function(resp) {
            if (resp == 1) {
              Swal.fire(
                'Added!',
                'Item successfully added to cart.',
                'success'
              );
              $('.item_count').html(parseInt($('.item_count').html()) + parseInt($('[name="qty"]').val()));
              $('.modal').modal('hide');
            } else {
              Swal.fire(
                'Error!',
                'Error adding item to cart.',
                'error'
              );
            }
            end_load();
          }
        });
      }
    });
  });

  $('.star').click(function(){
    var rating = $(this).data('rating');
    $('#rating-value').val(rating);
    $('.star').each(function(){
      if($(this).data('rating') <= rating){
        $(this).addClass('selected');
      } else {
        $(this).removeClass('selected');
      }
    });
  });

  $('#submit-rating').click(function(){
    var rating = $('#rating-value').val();
    if(!rating){
      Swal.fire('Error', 'Please select a rating.', 'error');
      return;
    }
    $.ajax({
      url: 'admin/ajax.php?action=submit_rating',
      method: 'POST',
      data: { pid: '<?php echo $_GET['id'] ?>', rating: rating },
      success: function(resp) {
        if(resp == 1){
          Swal.fire('Thank you!', 'Your rating has been submitted.', 'success');
        } else {
          Swal.fire('Error!', 'There was an error submitting your rating.', 'error');
        }
      }
    });
  });
</script>
