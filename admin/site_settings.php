<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * from system_settings limit 1");
if($qry->num_rows > 0){
	foreach($qry->fetch_array() as $k => $val){
		$meta[$k] = $val;
	}
}
?>
<div class="container-fluid">
    <div class="card col-lg-12">
        <div class="card-body">
            <form action="" id="manage-settings">
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
                    <textarea name="about" class="text-jqte"><?php echo isset($meta['about_content']) ? $meta['about_content'] : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Image</label>
                    <input type="file" class="form-control" name="img" onchange="displayImg(this,$(this))">
                </div>
                <div class="form-group">
                    <img src="<?php echo isset($meta['cover_img']) ? '../assets/img/'.$meta['cover_img'] :'' ?>" alt="" id="cimg">
                </div>
                <center>
                    <button class="btn btn-info btn-primary btn-block col-md-2">Save</button>
                </center>
            </form>
        </div>
    </div>
    <style>
        img#cimg{
            max-height: 10vh;
            max-width: 6vw;
        }
        #loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
    </style>
    
    <div id="loading">
        <img src="path/to/loading.gif" alt="Loading...">
    </div>

    <script src="path/to/jquery.min.js"></script>
    <script src="path/to/sweetalert2.all.min.js"></script>

    <script>
        function resizeImage(file, maxWidth, maxHeight, callback) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob(callback);
                };
            };
            reader.readAsDataURL(file);
        }

        function displayImg(input, _this) {
            if (input.files && input.files[0]) {
                resizeImage(input.files[0], 800, 800, function(blob) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#cimg').attr('src', e.target.result);
                        // Assign the resized image blob back to the input file list
                        const dataTransfer = new DataTransfer();
                        const file = new File([blob], input.files[0].name, {
                            type: blob.type
                        });
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                    };
                    reader.readAsDataURL(blob);
                });
            }
        }

        $(document).ready(function() {
            $('.text-jqte').jqte();

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
                    type: 'POST',
                    error: function(err) {
                        $('#loading').hide();
                        console.log(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    },
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
                    }
                });
            });
        });
    </script>
</div>
