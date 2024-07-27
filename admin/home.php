<?php
require_once("./db_connect.php");

// Fetch data for total sales and pending/confirmed orders
$total_sales_result = $conn->query("SELECT SUM(p.price * ol.qty) AS total_sales 
                                    FROM orders o 
                                    JOIN order_list ol ON o.id = ol.order_id
                                    JOIN product_list p ON ol.product_id = p.id 
                                    WHERE o.status = 1");
$total_sales = $total_sales_result->fetch_assoc()['total_sales'];

$cancelled_orders = $conn->query("SELECT * FROM orders WHERE status = 0")->num_rows;
$confirmed_orders = $conn->query("SELECT * FROM orders WHERE status = 1")->num_rows;

// Fetch data for pie chart (sales by address)
$sales_by_address_data = $conn->query("
    SELECT o.address AS address, SUM(ol.qty * p.price) AS total_sales 
    FROM order_list ol
    JOIN product_list p ON ol.product_id = p.id
    JOIN orders o ON ol.order_id = o.id
    WHERE o.status = 1
    GROUP BY o.address
    ORDER BY total_sales DESC
");

$data = [];
while ($row = $sales_by_address_data->fetch_assoc()) {
    $data[] = $row;
}

// Fetch monthly sales data for the last 12 months
$monthly_sales_data = [];
for ($i = 0; $i < 12; $i++) {
    $date = date('Y-m', strtotime("-$i months"));
    $monthly_sales_result = $conn->query("SELECT SUM(p.price * ol.qty) AS monthly_sales 
                                          FROM orders o 
                                          JOIN order_list ol ON o.id = ol.order_id
                                          JOIN product_list p ON ol.product_id = p.id 
                                          WHERE o.status = 1 AND DATE_FORMAT(o.created_at, '%Y-%m') = '$date'");
    $monthly_sales = $monthly_sales_result->fetch_assoc()['monthly_sales'];
    $month_name = date('F', strtotime($date));

    $monthly_sales_data[$month_name] = $monthly_sales ?: 0;
}

// Query to fetch total number of categories
$sql = "SELECT COUNT(*) AS total_categories FROM category_list";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_categories = $row['total_categories'];
} else {
    $total_categories = 0; // Default value if no categories found
}

// Query to fetch total number of products
$sql = "SELECT COUNT(*) AS total_products FROM product_list";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_products = $row['total_products'];
} else {
    $total_products = 0; // Default value if no products found
}

// Fetch total number of users
$sql = "SELECT COUNT(*) as total_users FROM user_info";
$result = $conn->query($sql);

$total_users = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_users = $row['total_users'];
}
// Fetch total number of orders
$sql = "SELECT COUNT(*) as total_orders FROM orders";
$result = $conn->query($sql);

$total_orders = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_orders = $row['total_orders'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel = "icon" href="assets/img/1.jpg"  type = ".///image/x-icon">
    <style>
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .bounce {
            animation: bounce 2s infinite;
        }

        .custom-menu {
            z-index: 1000;
            position: absolute;
            background-color: #ffffff;
            border: 1px solid #0000001c;
            border-radius: 5px;
            padding: 8px;
            min-width: 13vw;
        }

        a.custom-menu-list {
            width: 100%;
            display: flex;
            color: #4c4b4b;
            font-weight: 600;
            font-size: 1em;
            padding: 1px 11px;
        }

        span.card-icon {
            position: absolute;
            font-size: 3em;
            bottom: .2em;
            color: #ffffff80;
        }

        .file-item {
            cursor: pointer;
        }

        a.custom-menu-list:hover, .file-item:hover, .file-item.active {
            background: #80808024;
        }

        table th, td {
            /*border-left:1px solid gray;*/
        }

        a.custom-menu-list span.icon {
            width: 1em;
            margin-right: 5px;
        }

        .candidate {
            margin: auto;
            width: 23vw;
            padding: 0 10px;
            border-radius: 20px;
            margin-bottom: 1em;
            display: flex;
            border: 3px solid #00000008;
            background: #8080801a;
        }

        .candidate_name {
            margin: 8px;
            margin-left: 3.4em;
            margin-right: 3em;
            width: 100%;
        }

        .img-field {
            display: flex;
            height: 8vh;
            width: 4.3vw;
            padding: .3em;
            background: #80808047;
            border-radius: 50%;
            position: absolute;
            left: -.7em;
            top: -.7em;
        }

        .candidate img {
            height: 100%;
            width: 100%;
            margin: auto;
            border-radius: 50%;
        }

        .vote-field {
            position: absolute;
            right: 0;
            bottom: -.4em;
        }

        .card-custom {
            border-left: 4px solid #007bff;
        }

        .card-custom-primary {
            border-left-color: #007bff;
        }

        .card-custom-danger {
            border-left-color: #dc3545;
        }

        .card-custom-success {
            border-left-color: #28a745;
        }

        .card-custom-warning {
            border-left-color: #ffc107;
        }

        .bg-light-blue {
            background-color: #cce5ff;
        }

        .bg-light-red {
            background-color: #f8d7da;
        }

        .bg-light-green {
            background-color: #d4edda;
        }

        .bg-light-yellow {
            background-color: #fff3cd;
        }

        .fa-bounce {
            animation: bounce 2s infinite;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10">
                
                    <div class="card-body">
                        <h1>Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-3">
    <!-- Total Sales Card -->
    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
        <div class="card rounded-0 shadow card-custom bg-blue">
            <div class="card-body" style="background: #4d94ff; color: green;">
                <div class="media">
                    <div class="media-left meida media-middle"> 
                        <span><i class="fa fa-money-bill-wave bounce" style="height: 50px; width: 50px;" aria-hidden="true"></i></span>
                    </div>
                    <div class="media-body media-text-center">
                        <h5 class="text-right" style="color: black; font-size: 30px; font-family: courier-new;">Total Sales</h5>
                        <h2 class="text-right" style="color: black;"><b><?= number_format($total_sales, 2) ?></b></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Orders Card -->
    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
        <div class="card rounded-0 shadow card-custom bg-light-red">
            <div class="card-body" style="background: #ff99ff; color: orange;">
                <div class="media">
                    <div class="media-left meida media-middle"> 
                        <span><i class="fa fa-clock bounce" style="height: 50px; width: 50px;" aria-hidden="true"></i></span>
                    </div>
                    <div class="media-body media-text-center">
                        <h5 class="text-right" style="color: black; font-size: 30px; font-family: courier-new;">Pending Orders</h5>
                        <h2 class="text-right" style="color: black;"><b><?= number_format($cancelled_orders) ?></b></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmed Orders Card -->
    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
        <div class="card rounded-0 shadow card-custom bg-light-green">
            <div class="card-body" style="background: #80ff80; color: green;">
                <div class="media">
                    <div class="media-left meida media-middle"> 
                        <span><i class="fa fa-check-circle bounce" style="height: 50px; width: 50px;" aria-hidden="true"></i></span>
                    </div>
                    <div class="media-body media-text-center">
                        <h5 class="text-right" style="color: black; font-size: 30px; font-family: courier-new;">Confirmed Orders</h5>
                        <h2 class="text-right" style="color: black;"><b><?= number_format($confirmed_orders) ?></b></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales This Month Card -->
    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
        <div class="card rounded-0 shadow card-custom bg-light-yellow">
            <div class="card-body" style="background: #ffff99; color: orange;">
                <div class="media">
                    <div class="media-left meida media-middle"> 
                        <span><i class="fa fa-chart-bar bounce" style="height: 50px; width: 50px;" aria-hidden="true"></i></span>
                    </div>
                    <div class="media-body media-text-center">
                        <h5 class="text-right" style="color: black; font-size: 30px; font-family: courier-new;">Sales This Month</h5>
                        <h2 class="text-right" style="color: black;"><b><?= number_format(array_sum($monthly_sales_data)) ?></b></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row m-3">
    <!-- Total Categories Card -->
    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
        <div class="card rounded-0 shadow card-custom bg-blue">
            <div class="card-body" style="background:#d1c7b5; color: #26bf33;">
                <div class="media">
                    <div class="media-left meida media-middle"> 
                        <span><i class="fa fa-folder-open bounce" style="height: 50px; width: 50px;" aria-hidden="true"></i></span>
                    </div>
                    <div class="media-body media-text-center">
                        <h5 class="text-right" style="color: black; font-size: 30px; font-family: courier-new;">Total Categories</h5>
                        <h2 class="text-right" style="color: black;"><b><?= $total_categories ?></b></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Cakes Card -->
    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
        <div class="card rounded-0 shadow card-custom bg-blue">
            <div class="card-body" style="background: #adebd6; color: #d982ff;">
                <div class="media">
                    <div class="media-left meida media-middle"> 
                        <span><i class="fa fa-birthday-cake bounce" style="height: 50px; width: 50px;" aria-hidden="true"></i></span>
                    </div>
                    <div class="media-body media-text-center">
                        <h5 class="text-right" style="color: black; font-size: 30px; font-family: courier-new;">Total Cakes</h5>
                        <h2 class="text-right" style="color: black;"><b><?= number_format($total_products) ?></b></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
    <div class="card rounded-0 shadow card-custom bg-blue">
        <div class="card-body" style="background:  #b8abff; color: #1c332e;">
            <div class="media">
                <div class="media-left meida media-middle"> 
                    <span><i class="fa fa-users bounce" style="height: 50px; width: 50px;" aria-hidden="true"></i></span>
                </div>
                <div class="media-body media-text-center">
                    <h5 class="text-right" style="color: black; font-size: 30px; font-family: courier-new;">Total Users</h5>
                    <h2 class="text-right" style="color: black;"><b><?= number_format($total_users) ?></b></h2>
                </div>
            </div>
        </div>
    </div>
</div>
 <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
    <div class="card rounded-0 shadow card-custom bg-blue">
            <div class="card-body" style="background: #8f61e8; color: #ff7733;">
                <div class="media">
                    <div class="media-left meida media-middle"> 
                        <span><i class=" fa fa-shopping-cart bounce" style="height: 50px; width: 50px;" aria-hidden="true"></i></span>
                    </div>
                    <div class="media-body media-text-center">
                        <h5 class="text-right" style="color: black; font-size: 30px; font-family: courier-new;">Total Orders</h5>
                        <h2 class="text-right" style="color: black;"><b><?= number_format($total_orders) ?></b></h2>
                    </div>
            </div>
        </div>
    </div>
</div>

            
        


    <!-- Pie Chart -->
<div class="col-md-6">
    <h3>Sales by Address</h3>
    <canvas id="salesByAddressChart" style="width: 100%; height: 400px;"></canvas>
</div>
<!-- Bar Chart -->
<div class="col-md-6">
    <h3>Monthly Sales</h3>
    <canvas id="monthlySalesChart" style="width: 100%; height: 400px;"></canvas>
</div>

<script>
    // Pie Chart
    const salesByAddressCtx = document.getElementById('salesByAddressChart').getContext('2d');
    const salesByAddressData = <?= json_encode($data) ?>;
    new Chart(salesByAddressCtx, {
        type: 'pie',
        data: {
            labels: salesByAddressData.map(d => d.address),
            datasets: [{
                data: salesByAddressData.map(d => d.total_sales),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        }
    });

    // Bar Chart
    const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
    const monthlySalesLabels = Object.keys(<?= json_encode($monthly_sales_data) ?>);
    const monthlySalesData = Object.values(<?= json_encode($monthly_sales_data) ?>);
    new Chart(monthlySalesCtx, {
        type: 'bar',
        data: {
            labels: monthlySalesLabels,
            datasets: [{
                label: 'Sales',
                data: monthlySalesData,
                backgroundColor: '#007bff'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
