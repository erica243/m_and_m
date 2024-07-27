

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">List of Menu Items</h3>
                    <div class="card-tools">
                        <a href="javascript:void(0)" id="add_menu_button" class="btn btn-flat btn-primary">
                            <span class="fas fa-plus"></span> Create New
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <table class="table table-hover table-striped">
                            <colgroup>
                                <col width="10%">
                                <col width="30%">
                                <col width="40%">
                                <col width="20%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Menu Details</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $menus = $conn->query("SELECT p.*, c.name as category_name FROM product_list p INNER JOIN category_list c ON c.id = p.category_id ORDER BY p.id ASC");
                                while($row = $menus->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td class="text-center">
                                        <img src="<?php echo isset($row['img_path']) ? '../assets/img/'.$row['img_path'] : 'https://via.placeholder.com/150' ?>" alt="" class="img-fluid img-thumbnail" style="max-width: 200px; height: 200px">
                                    </td>
                                    <td>
                                        <p><b>Name:</b> <?php echo $row['name'] ?></p>
                                        <p><b>Category:</b> <?php echo $row['category_name'] ?></p>
                                        <p><b>Description:</b> <?php echo $row['description'] ?></p>
                                        <p><b>Price:</b> <?php echo number_format($row['price'], 2) ?></p>
                                        <p><b>Size:</b> <?php echo $row['size'] ?></p>
                                        <p><b>Availability:</b> <?php echo $row['status'] == 'Available' ? 'Available' : 'Unavailable' ?></p>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_menu" type="button" 
                                                data-id="<?php echo $row['id'] ?>"
                                                data-name="<?php echo $row['name'] ?>"
                                                data-status="<?php echo $row['status'] ?>"
                                                data-description="<?php echo $row['description'] ?>"
                                                data-price="<?php echo $row['price'] ?>"
                                                data-category_id="<?php echo $row['category_id'] ?>"
                                                data-size="<?php echo $row['size'] ?>"
                                                data-img_path="<?php echo $row['img_path'] ?>">Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete_menu" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-lg-12" id="manage-menu-form" style="display: none;">
            <div class="card card-outline card-primary">
                <form action="" id="manage-menu">
                    <div class="card-header">
                        <h3 class="card-title">Menu Form</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Menu Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Menu Description</label>
                            <textarea cols="30" rows="3" class="form-control" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="availability">Availability</label>
                            <select name="status" class="form-control" id="availability">
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Category</label>
                            <select name="category_id" class="custom-select browser-default" required>
                                <option value="">Select Category</option>
                                <?php
                                $categories = $conn->query("SELECT * FROM category_list ORDER BY name ASC");
                                while($row = $categories->fetch_assoc()):
                                ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Price</label>
                            <input type="number" class="form-control text-left" name="price" step="any" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Size</label> 
                            <input type="text" class="form-control" name="size" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Image</label>
                            <input type="file" class="form-control" name="img" onchange="displayImg(this)">
                        </div>
                        <div class="form-group">
                            <img src="" alt="" id="preview_image" style="max-width: 100px;">
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-sm btn-primary col-sm-3 offset-md-3">Save</button>
                                <button type="button" class="btn btn-sm btn-default col-sm-3" onclick="$('#manage-menu').get(0).reset(); $('#preview_image').attr('src', ''); $('#manage-menu-form').hide();">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    function displayImg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview_image').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#add_menu_button').click(function() {
        $('#manage-menu-form').show();
        $('html, body').animate({
            scrollTop: $("#manage-menu-form").offset().top
        }, 500);
    });

    $('#manage-menu').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'ajax.php?action=save_menu',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response == 1) {
                    Swal.fire('Success', 'Menu item saved successfully!', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', 'Failed to save menu item.', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'An error occurred while saving the menu item.', 'error');
            }
        });
    });

    $('.edit_menu').click(function() {
        $('#manage-menu-form').show();
        var id = $(this).data('id');
        var name = $(this).data('name');
        var description = $(this).data('description');
        var status = $(this).data('status');
        var price = $(this).data('price');
        var category_id = $(this).data('category_id');
        var size = $(this).data('size');
        var img_path = $(this).data('img_path');

        $('#manage-menu [name="id"]').val(id);
        $('#manage-menu [name="name"]').val(name);
        $('#manage-menu [name="description"]').val(description);
        $('#manage-menu [name="status"]').val(status);
        $('#manage-menu [name="price"]').val(price);
        $('#manage-menu [name="category_id"]').val(category_id);
        $('#manage-menu [name="size"]').val(size);
        $('#preview_image').attr('src', '../assets/img/' + img_path);
        $('html, body').animate({
            scrollTop: $("#manage-menu-form").offset().top
        }, 500);
    });

    $('.delete_menu').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the menu item.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax.php?action=delete_menu',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response == 1) {
                            Swal.fire('Deleted!', 'Menu item has been deleted.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', 'Failed to delete menu item.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'An error occurred while deleting the menu item.', 'error');
                    }
                });
            }
        });
    });
</script>

</body>
</html>
