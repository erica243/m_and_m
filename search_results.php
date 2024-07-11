<?php
include('admin/db_connect.php');

$query = isset($_GET['query']) ? $_GET['query'] : '';
$search_results = $conn->query("SELECT p.*, AVG(pr.rating) as avg_rating
                                FROM product_list p
                                LEFT JOIN product_ratings pr ON p.id = pr.product_id
                                WHERE p.name LIKE '%$query%' OR p.description LIKE '%$query%'
                                GROUP BY p.id");

?>

<div class="container mt-5">
    <h3>Search Results for "<?php echo htmlspecialchars($query); ?>"</h3>
    <?php if($search_results->num_rows > 0): ?>
        <div class="row">
            <?php while($row = $search_results->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="assets/img/<?php echo $row['img_path'] ?>" class="card-img-top" alt="<?php echo $row['name'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['name'] ?></h5>
                            <p class="card-text"><?php echo $row['description'] ?></p>
                            <p class="card-text">Price: <?php echo number_format($row['price'], 2) ?></p>
                            <?php if ($row['avg_rating']): ?>
                                <p class="card-text">Average Rating: <?php echo number_format($row['avg_rating'], 1) ?> / 5</p>
                            <?php else: ?>
                                <p class="card-text">No ratings yet</p>
                            <?php endif; ?>
                            <a href="view_prod.php?page=view_product&id=<?php echo $row['id'] ?>" class="btn btn-primary">View Product</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No products found matching your query.</p>
    <?php endif; ?>
</div>
