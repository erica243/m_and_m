<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * FROM system_settings LIMIT 1");
if ($qry->num_rows > 0) {
    foreach ($qry->fetch_array() as $k => $val) {
        $meta[$k] = $val;
    }
}
?>
<div class="container-fluid">
    <div class="card col-lg-12">
        <div class="card-body">
            <form action="" id="manage-settings" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name" class="control-label">System Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($meta['name']) ? $meta['name'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($meta['email']) ? $meta['email'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="contact" class="control-label">Contact</label>
                    <input type="text" class="form-control" id="contact" name="contact" value="<?php echo isset($meta['contact']) ? $meta['contact'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="about" class="control-label">About Content</label>
                    <textarea name="about" class="form-control"><?php echo isset($meta['about_content']) ? $meta['about_content'] : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Image</label>
                    <input type="file" class="form-control" name="img" onchange="displayImg(this)">
                </div>
                <div class="form-group">
                    <img src="<?php echo isset($meta['cover_img']) ? '../assets/img/' . $meta['cover_img'] : '' ?>" alt="" id="cimg" style="max-height: 10vh; max-width: 6vw;">
                </div>
                <center>
                    <button class="btn btn-info btn-primary btn-block col-md-2">Save</button>
                </center>
            </form>
        </div>
    </div>

    <div id="loading" style="display:none;">
        <img src="path/to/loading.gif" alt="Loading...">
    </div>

    <script src="path/to/jquery.min.js"></script>
    <script src="path/to/sweetalert2.all.min.js"></script>

    <script>
        function displayImg(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#cimg').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            $('#manage-settings').submit(function(e) {
                e.preventDefault();
                $('#loading').show();
                $.ajax({
                    url: 'ajax.php?action=save_settings',
                    data: new FormData($(this)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    success: function(resp) {
                        $('#loading').hide();
                        if (resp == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Data successfully saved.',
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',
                            });
                        }
                    },
                    error: function() {
                        $('#loading').hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong with the AJAX request!',
                        });
                    }
                });
            });
        });
    </script>
</div>
