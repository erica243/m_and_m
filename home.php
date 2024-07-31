
Keneth Ducay Batusbatusan
<?php
include 'admin/db_connect.php';

// Default limit and pagination
$limit = 10;
$page = (isset($_GET['_page']) && $_GET['_page'] > 0) ? $_GET['_page'] - 1 : 0;
$offset = $page > 0 ? $page * $limit : 0;

// Get search parameter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Modify query based on search parameter
$search_query = $search ? "WHERE name LIKE '%$search%' OR description LIKE '%$search%'" : '';
$qry = $conn->query("SELECT id, name, description, img_path, size, price, status FROM product_list $search_query ORDER BY name ASC LIMIT $limit OFFSET $offset");

// Get total count of items based on search
$total_count_query = $conn->query("SELECT id FROM product_list $search_query");
$all_menu = $total_count_query->num_rows;
$page_btn_count = ceil($all_menu / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Page</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/font-awesome.min.css">
    <link rel="stylesheet" href="path/to/custom-styles.css">
    <script src="path/to/jquery.min.js"></script>
    <script src="path/to/bootstrap.min.js"></script>
    <script src="path/to/sweetalert2.all.min.js"></script>
</head>
<body>
    <!-- Masthead -->
    <header class="masthead">
        <div class="container h-100">
            <div class="row h-100 align-items-center justify-content-center text-center">
                <div class="col-lg-10 align-self-center mb-4 page-title">
                    <h1 class="text-white">Welcome to <?php echo htmlspecialchars($_SESSION['setting_name']); ?></h1>
                    <hr class="divider my-4 bg-dark" />
                    <a class="btn btn-dark bg-black btn-xl js-scroll-trigger" href="#menu">Order Now</a>
                </div>
            </div>
        </div>
    </header>

    <section class="page-section" id="menu">
        <h1 class="text-center text-cursive" style="font-size:3em"><b>Menu</b></h1>
        <div class="d-flex justify-content-center">
            <hr class="border-dark" width="5%">
        </div>
        <!-- Search Bar -->
        <div class="container">
            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search for cakes..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <div class="input-group-append">
                        <button class="btn btn-dark" type="submit">Search for Cakes</button>
                    </div>
                </div>
            </form>
        </div>
        <div id="menu-field" class="card-deck mt-2">
            <?php if ($all_menu > 0): ?>
                <?php while ($row = $qry->fetch_assoc()): ?>
                <div class="col-lg-3 mb-3">
                    <div class="card menu-item rounded-0">
                        <div class="position-relative overflow-hidden" id="item-img-holder">
                            <img src="assets/img/<?php echo htmlspecialchars($row['img_path']); ?>" class="card-img-top" alt="...">
                        </div>
                        <div class="card-body rounded-0">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text truncate"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-text">Size: <?php echo htmlspecialchars($row['size']); ?></p>
                            <p class="card-text">Price: <?php echo htmlspecialchars($row['price']); ?></p>
                            <p class="card-text">
                                Availability: 
                                <?php echo htmlspecialchars($row['status']); ?>
                            </p>
                            <div class="text-center">
                                <button class="btn btn-sm btn-outline-dark view_prod btn-block" data-id="<?php echo htmlspecialchars($row['id']); ?>"><i class="fa fa-eye"></i> View</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <h3>No products found</h3>
                </div>
            <?php endif; ?>
        </div>
        <!-- Pagination Buttons Block -->
        <div class="w-100 mx-4 d-flex justify-content-center">
            <div class="btn-group paginate-btns">
                <!-- Previous Page Button -->
                <a class="btn btn-default border border-dark" <?php echo ($page == 0) ? 'disabled' : ''; ?> href="./?_page=<?php echo ($page); ?>&search=<?php echo urlencode($search); ?>">Prev.</a>
                <!-- End of Previous Page Button -->
                <!-- Pages Page Button -->
                <?php for ($i = 1; $i <= $page_btn_count; $i++): ?>
                    <?php if ($page_btn_count > 10): ?>
                        <?php if ($i == $page_btn_count && !in_array($i, range(($page - 3), ($page + 3)))): ?>
                            <a class="btn btn-default border border-dark ellipsis">...</a>
                        <?php endif; ?>
                        <?php if ($i == 1 || $i == $page_btn_count || in_array($i, range(($page - 3), ($page + 3)))): ?>
                            <a class="btn btn-default border border-dark <?php echo ($i == ($page + 1)) ? 'active' : ''; ?>" href="./?_page=<?php echo $i ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            <?php if ($i == 1 && !in_array($i, range(($page - 3), ($page + 3)))): ?>
                                <a class="btn btn-default border border-dark ellipsis">...</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <a class="btn btn-default border border-dark <?php echo ($i == ($page + 1)) ? 'active' : ''; ?>" href="./?_page=<?php echo $i ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <!-- Next Page Button -->
                <a class="btn btn-default border border-dark" <?php echo (($page + 1) == $page_btn_count) ? 'disabled' : ''; ?> href="./?_page=<?php echo ($page + 2); ?>&search=<?php echo urlencode($search); ?>">Next</a>
            </div>
        </div>
        <!-- End Pagination Buttons Block -->
    </section>

    <script>
        $('.view_prod').click(function(){
            uni_modal_right('Product Details', 'view_prod.php?id=' + $(this).attr('data-id'));
        });

        <?php if (isset($_GET['_page'])): ?>
            $(function(){
                document.querySelector('html').scrollTop = $('#menu').offset().top - 100;
            });
        <?php endif; ?>
    </script>
</body>
</html>
