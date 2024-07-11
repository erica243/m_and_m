<!-- Masthead -->
<header class="masthead">
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-10 align-self-center mb-4 page-title">
                <h1 class="text-white">Welcome to <?php echo $_SESSION['setting_name']; ?></h1>
                <hr class="divider my-4 bg-dark" />
                <a class="btn btn-dark bg-black btn-xl js-scroll-trigger" href="#menu">Order Now</a>
            </div>
        </div>
    </div>
</header>

<!-- Menu Section -->
<section class="page-section" id="menu">
    <h1 class="text-center text-cursive" style="font-size:3em"><b>Menu</b></h1>
    <div class="d-flex justify-content-center">
        <hr class="border-dark" width="5%">
    </div>
    <div id="menu-field" class="card-deck mt-2">
        <?php 
        include 'admin/db_connect.php';
        $limit = 10;
        $page = (isset($_GET['_page']) && $_GET['_page'] > 0) ? $_GET['_page'] - 1 : 0;
        $offset = $page > 0 ? $page * $limit : 0;
        $all_menu = $conn->query("SELECT id FROM product_list")->num_rows;
        $page_btn_count = ceil($all_menu / $limit);
        $qry = $conn->query("SELECT * FROM product_list ORDER BY `name` ASC LIMIT $limit OFFSET $offset");
        while ($row = $qry->fetch_assoc()):
            // Fetch average rating
            $rating_query = $conn->prepare("SELECT AVG(rating) as avg_rating FROM product_ratings WHERE product_id = ?");
            $rating_query->bind_param("i", $row['id']);
            $rating_query->execute();
            $rating_result = $rating_query->get_result();
            $avg_rating = $rating_result->fetch_assoc()['avg_rating'];
            $avg_rating_display = $avg_rating ? number_format($avg_rating, 1) : 'No ratings yet';
            ?>
            <div class="col-lg-3 mb-3">
                <div class="card menu-item rounded-0">
                    <div class="position-relative overflow-hidden" id="item-img-holder">
                        <img src="assets/img/<?php echo $row['img_path'] ?>" class="card-img-top" alt="...">
                    </div>
                    <div class="card-body rounded-0">
                        <h5 class="card-title"><?php echo $row['name'] ?></h5>
                        <p class="card-text truncate"><?php echo $row['description'] ?></p>
                        <p class="card-text">Price: <?php echo number_format($row['price'], 2) ?></p>
                        <p class="card-text">Average Rating: <?php echo $avg_rating_display ?> / 5</p>
                        <div class="text-center">
                            <button class="btn btn-sm btn-outline-dark view_prod btn-block" data-id="<?php echo $row['id'] ?>"><i class="fa fa-eye"></i> View</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination Buttons Block -->
    <div class="w-100 mx-4 d-flex justify-content-center">
        <div class="btn-group paginate-btns">
            <!-- Previous Page Button -->
            <a class="btn btn-default border border-dark" <?php echo ($page == 0) ? 'disabled' : '' ?> href="./?_page=<?php echo ($page + 1) ?>">Prev.</a>
            <!-- End of Previous Page Button -->

            <!-- Looping Page Buttons -->
            <?php for ($i = 1; $i <= $page_btn_count; $i++): ?>
                <a class="btn btn-default border border-dark <?php echo ($i == ($page + 1)) ? 'active' : ''; ?>" href="./?_page=<?php echo $i ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <!-- Next Page Button -->
            <a class="btn btn-default border border-dark" <?php echo (($page + 1) == $page_btn_count) ? 'disabled' : '' ?> href="./?_page=<?php echo ($page + 2) ?>">Next</a>
            <!-- End of Next Page Button -->
        </div>
    </div>
    <!-- End Pagination Buttons Block -->
</section>

<script>
    $('.view_prod').click(function () {
        uni_modal_right('Product Details', 'view_prod.php?id=' + $(this).attr('data-id'));
    });
</script>

<?php if (isset($_GET['_page'])): ?>
    <script>
        $(function () {
            document.querySelector('html').scrollTop = $('#menu').offset().top - 100;
        });
    </script>
<?php endif; ?>
