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

// Fetch data for pie chart (sales by product)
$sales_by_product_data = $conn->query("
    SELECT p.name as product_name, SUM(ol.qty) as total_qty 
    FROM order_list ol
    JOIN product_list p ON ol.product_id = p.id
    JOIN orders o ON ol.order_id = o.id
    WHERE o.status = 1
    GROUP BY p.name
    ORDER BY total_qty DESC
");

$data = [];
while ($row = $sales_by_product_data->fetch_assoc()) {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mt-3">
                    <div class="card-body">
                       <h1> Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-3">
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
            </div>
            <div class="card p-30" style="background:#9999ff; color: #39ac73 ;">
                                                <div class="media">
                                                    <div class="media-left meida media-middle"> 
                                                        <span><i class="fa fa-bounce fa-money-bill-wave bgreen f-s-40" aria-hidden="true"></i></span>
                                                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <h5 class="text-muted">Total Sales</h5>
                            <h2 class="text-right"><b><?= number_format($total_sales, 2) ?></b></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
                <div class="card rounded-0 shadow card-custom card-custom-danger bg-light-red">
                    <div class="card-body">
                        <div class="container-fluid">
                            <h5 class="text-muted">Pending Orders</h5>
                            <h2 class="text-right"><b><?= number_format($cancelled_orders) ?></b></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
                <div class="card rounded-0 shadow card-custom card-custom-success bg-light-green">
                    <div class="card-body">
                        <div class="container-fluid">
                            <h5 class="text-muted">Confirmed Orders</h5>
                            <h2 class="text-right"><b><?= number_format($confirmed_orders) ?></b></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-3">
                <div class="card rounded-0 shadow card-custom card-custom-warning bg-light-yellow">
                    <div class="card-body">
                        <div class="container-fluid">
                            <h5 class="text-muted">Monthly Sales</h5>
                            <?php 
                            $current_month = date('Y-m');
                            $monthly_sales_result = $conn->query("SELECT SUM(p.price * ol.qty) AS monthly_sales 
                                                                 FROM orders o 
                                                                 JOIN order_list ol ON o.id = ol.order_id
                                                                 JOIN product_list p ON ol.product_id = p.id 
                                                                 WHERE o.status = 1 AND DATE_FORMAT(o.created_at, '%Y-%m') = '$current_month'");
                            $monthly_sales = $monthly_sales_result->fetch_assoc()['monthly_sales'];
                            ?>
                            <h2 class="text-right"><b><?= number_format($monthly_sales, 2) ?></b></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="salesChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="pieChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <canvas id="lineChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    var salesData = <?php echo json_encode($data); ?>;
    var productNames = salesData.map(item => item.product_name);
    var totalQuantities = salesData.map(item => item.total_qty);

    var ctxBar = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: productNames,
            datasets: [{
                label: 'Sales by Product',
                data: totalQuantities,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1 // Adjust step size as needed
                    }
                }
            }
        }
    });

    var pieChartLabels = productNames.slice(0, 6); // Limit to 6 items for better visualization
    var pieChartData = totalQuantities.slice(0, 6); // Limit to 6 items for better visualization

    var ctxPie = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: pieChartLabels,
            datasets: [{
                data: pieChartData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        }
    });

    var monthlySalesData = <?php echo json_encode(array_values($monthly_sales_data)); ?>;
    var months = <?php echo json_encode(array_keys($monthly_sales_data)); ?>;

    var ctxLine = document.getElementById('lineChart').getContext('2d');
    var lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Monthly Sales',
                data: monthlySalesData,
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
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

    <?php $conn->close(); ?>
</body>
</html>
