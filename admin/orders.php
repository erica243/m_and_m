<?php
include 'db_connect.php';

$statuses = [
    'all_orders' => 0,
    'approved_orders' => 1,
    'cancelled_orders' => 2,
    'delivery_orders' => 3,
    'deli_orders' => 4,
    'delivered_orders' => 5
];

$orderCounts = [];
foreach ($statuses as $status => $value) {
    $result = $conn->query("SELECT * FROM orders WHERE status = $value");
    $orderCounts[$status] = $result->num_rows;
}

// Fetch the order amounts for the last 7 days
$orders_arr = [0, 0, 0, 0, 0, 0, 0];
$dt = new DateTime();
$dates = [];
for ($d = 1; $d <= 7; $d++) {
    $dt->setISODate($dt->format('o'), $dt->format('W'), $d);
    $dates[] = $dt->format('Y-m-d');
}

foreach ($dates as $index => $date) {
    $order_sql = "SELECT * FROM orders WHERE status = 5 AND DATE(order_date) = '$date'";
    if ($order_result = $conn->query($order_sql)) {
        $total_orders = 0;
        while ($order_row = $order_result->fetch_array()) {
            $orderList_sql = "SELECT * FROM order_list 
                              INNER JOIN product_list 
                              ON product_list.id = order_list.product_id 
                              WHERE order_list.order_id = " . $order_row['id'];
            if ($orderList_result = $conn->query($orderList_sql)) {
                while ($orderList_row = $orderList_result->fetch_array()) {
                    $total_orders += $orderList_row['qty'] * $orderList_row['price'];
                }
            }
        }
        $orders_arr[$index] = $total_orders;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .overview-boxes {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 0 20px;
            margin-bottom: 18px;
        }
        .box {
            display: flex;
            align-items: top;
            justify-content: center;
            width: calc(100% / 4 - 5px);
            height: 120px;
            padding: 15px 14px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        .box-topic {
            font-size: 18px;
            letter-spacing: 1px;
            font-weight: 500;
            color: black;
        }
        .indicator {
            display: flex;
            align-items: center;
        }
        .indicator i {
            height: 20px;
            width: 20px;
            background: #8FDACB;
            line-height: 20px;
            text-align: center;
            border-radius: 50%;
            color: #fff;
            font-size: 20px;
            margin-right: 5px;
        }
        .badge {
            font-size: 40px;
            font-family: cursive;
            height: 50px;
            width: 50px;
            background: transparent;
            line-height: 32px;
            text-align: center;
            color: black;
            border-radius: 15px;
            margin-top: 38px;
            position: absolute;
        }
    </style>
</head>
<body>
<div class="container mt-3 bg-white rounded">
    <canvas id="myChart"></canvas>
</div>

<div class="home-content">
    <div class="overview-boxes">
        <?php foreach ($orderCounts as $status => $count): ?>
            <div class="box" style="background: <?= getColor($status) ?>;">
                <div class="right-side">
                    <div class="box-topic"><?= getStatusName($status) ?></div>
                </div>
                <span class="badge badge-secondary"><?= $count ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <?php foreach ($statuses as $status => $value): ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= $value === 0 ? 'active' : '' ?>" id="<?= $status ?>-tab" data-toggle="tab" href="#<?= $status ?>" role="tab"
                               aria-controls="<?= $status ?>" aria-selected="<?= $value === 0 ? 'true' : 'false' ?>" style="font-size: 16px;">
                                <?= getStatusName($status) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <?php foreach ($statuses as $status => $value): ?>
                        <div class="tab-pane fade <?= $value === 0 ? 'show active' : '' ?>" id="<?= $status ?>" role="tabpanel" aria-labelledby="<?= $status ?>-tab">
                            <table class="table table-bordered mt-3">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $result = $conn->query("SELECT * FROM orders WHERE status = $value");
                                $i = 1;
                                while ($row = $result->fetch_assoc()):
                                    $posted_date = date_format(date_create($row['order_date']), "F j, Y, g:i a");
                                    ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $posted_date ?></td>
                                        <td><?= $row['name'] ?></td>
                                        <td><?= $row['address'] ?></td>
                                        <td><?= $row['email'] ?></td>
                                        <td><?= $row['mobile'] ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= getBadgeClass($row['status']) ?>">
                                                <?= getStatusText($row['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary view_order" data-id="<?= $row['id'] ?>"
                                                    data-status="<?= $row['status'] ?>">View Order
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function getColor(status) {
        switch (status) {
            case 'all_orders':
                return 'plum';
            case 'approved_orders':
                return 'lightgreen';
            case 'delivery_orders':
                return 'mediumaquamarine';
            case 'deli_orders':
                return 'skyblue';
            case 'delivered_orders':
                return 'lightgray';
            case 'cancelled_orders':
                return 'yellowgreen';
            default:
                return 'lightblue';
        }
    }

    function getStatusName(status) {
        switch (status) {
            case 'all_orders':
                return 'All Orders';
            case 'approved_orders':
                return 'Prepare Orders';
            case 'delivery_orders':
                return 'Ready for Delivery';
            case 'deli_orders':
                return 'In Transit';
            case 'delivered_orders':
                return 'Delivered Orders';
            case 'cancelled_orders':
                return 'Cancelled Orders';
            default:
                return 'Orders';
        }
    }

    function getBadgeClass(status) {
        switch (status) {
            case 0:
                return 'secondary';
            case 1:
                return 'primary';
            case 2:
                return 'danger';
            case 3:
                return 'info';
            case 4:
                return 'success';
            case 5:
                return 'dark';
            default:
                return 'light';
        }
    }

    function getStatusText(status) {
        switch (status) {
            case 0:
                return 'Pending';
            case 1:
                return 'Confirmed';
            case 2:
                return 'Cancelled';
            case 3:
                return 'Out for Delivery';
            case 4:
                return 'Dispatched';
            case 5:
                return 'Delivered';
            default:
                return 'Unknown';
        }
    }

    $(document).ready(function () {
        $('.view_order').click(function () {
            var id = $(this).data('id');
            var status = $(this).data('status');
            uni_modal("Order Details", "view_order.php?id=" + id + "&status=" + status);
        });
    });

    var ctx = document.getElementById('myChart').getContext('2d');
    var ordersData = <?= json_encode($orders_arr) ?>;
    var dates = <?= json_encode($dates) ?>;
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Total Orders in the Last 7 Days',
                data: ordersData,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
</body>
</html>
