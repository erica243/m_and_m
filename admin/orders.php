<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .search-bar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="search-bar">
                <input type="text" id="searchInput" class="form-control" placeholder="Search orders...">
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Customer Name</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Order Date</th>
                        <th>Delivery Method</th>
                        <th>Pick-up Date</th>
                        <th>Pick-up Time</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <?php 
                    include 'db_connect.php';
                    $qry = $conn->query("SELECT * FROM orders");
                    $i = 1;
                    while($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $i++ ?></td>
                        <td><?php echo htmlspecialchars($row['order_number']) ?></td>
                        <td><?php echo htmlspecialchars($row['name']) ?></td>
                        <td><?php echo htmlspecialchars($row['address']) ?></td>
                        <td><?php echo htmlspecialchars($row['email']) ?></td>
                        <td><?php echo htmlspecialchars($row['mobile']) ?></td>
                        <td><?php 
                            if (!empty($row['order_date'])) {
                                echo date('m-d-Y', strtotime($row['order_date']));
                            } else {
                                echo 'N/A'; // Handle empty dates
                            }
                        ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_method']) ?></td>
                        <td><?php echo !empty($row['pickup_date']) ? date('m-d-Y', strtotime($row['pickup_date'])) : 'N/A'; ?></td>
                        <td><?php echo !empty($row['pickup_time']) ? htmlspecialchars($row['pickup_time']) : 'N/A'; ?></td>
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
    $(document).ready(function(){
        $('.view_order').click(function(){
            uni_modal('Order','view_order.php?id='+$(this).attr('data-id'))
        });

        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#orderTableBody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

</body>
</html>
