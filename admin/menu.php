<?php include('db_connect.php'); ?>

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
                        <a href="javascript:void(0)" id="add_menu_button" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Create New</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <table class="table table-hover table-stripped">
                            <colgroup>
                                <col width="10%">
                                <col width="30%">
                                <col width="40%">
                                <col width="20%">
                                <col width="15%">
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
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_menu" type="button" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>" data-status="<?php echo $row['status'] ?>" data-description="<?php echo $row['description'] ?>" data-price="<?php echo $row['price'] ?>" data-category_id="<?php echo $row['category_id'] ?>" data-img_path="<?php echo $row['img_path'] ?>">Edit</button>
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
                            <div class="custom-control custom-switch">
                              <input type="checkbox" name="status" class="custom-control-input" id="availability" checked>
                              <label class="custom-control-label" for="availability">Available</label>
                            </div>
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
                            <input type="number" class="form-control text-right" name="price" step="any" required>
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

    // Show Manage Menu Form when Create New button is clicked
    $('#add_menu_button').click(function() {
        $('#manage-menu-form').show();
    });

    // Handle form submission
    $('#manage-menu').submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: 'ajax.php?action=save_menu',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data successfully updated',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else if (resp == 2) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data successfully added',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save data',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save data',
                });
            }
        });
    });

    // Edit menu item
    $('.edit_menu').click(function() {
        var menuForm = $('#manage-menu');
        menuForm.get(0).reset();
        menuForm.find("[name='id']").val($(this).attr('data-id'));
        menuForm.find("[name='name']").val($(this).attr('data-name'));
        menuForm.find("[name='description']").val($(this).attr('data-description'));
        menuForm.find("[name='price']").val($(this).attr('data-price'));
        menuForm.find("[name='category_id']").val($(this).attr('data-category_id'));
        if ($(this).attr('data-status') == 1) {
            $('#availability').prop('checked', true);
        } else {
            $('#availability').prop('checked', false);
        }
        $('#preview_image').attr('src', '../assets/img/' + $(this).attr('data-img_path'));
        $('#manage-menu-form').show();
    });

    // Delete menu item
    $('.delete_menu').click(function() {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                delete_menu(id);
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                );
            }
        });
    });

    // Function to delete menu item
    function delete_menu(id) {
        $.ajax({
            url: 'ajax.php?action=delete_menu',
            method: 'POST',
            data: { id: id },
            success: function(resp) {
                if (resp == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data successfully deleted',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete data',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete data',
                });
            }
        });
    }
</script>

</body>
</html>
