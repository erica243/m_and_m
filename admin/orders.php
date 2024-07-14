<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                       
                        <th>Customer Name</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Mobile</th>
                      
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    include 'db_connect.php';
                    $qry = $conn->query("SELECT * FROM orders");
                    while($row=$qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $i++ ?></td>
                        <td><?php echo $row['name'] ?></td>
                        <td><?php echo $row['address'] ?></td>
                        <td><?php echo $row['email'] ?></td>
                        <td><?php echo $row['mobile'] ?></td>
                
                        <?php if($row['status'] == 1): ?>
                            <td class="text-center"><span class="badge badge-success">Confirmed</span></td>
                        <?php else: ?>
                            <td class="text-center"><span class="badge badge-secondary">For Verification</span></td>
                        <?php endif; ?>
                        <td>
                            <button class="btn btn-sm btn-primary view_order" data-id="<?php echo $row['id'] ?>">View Order</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal HTML -->
<div class="modal fade" id="uniModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Order Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- Order details will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script>
function uni_modal(title, url) {
    $('#exampleModalLabel').text(title);
    $.ajax({
        url: url,
        success: function(response) {
            $('#modalContent').html(response);
            $('#uniModal').modal('show');
        }
    });
}

$(document).ready(function() {
    $('.view_order').click(function() {
        uni_modal('Order', 'view_order.php?id=' + $(this).attr('data-id'));
    });
});
</script>
</body>
</html>
