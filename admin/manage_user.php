<?php 
include('db_connect.php');
if(isset($_GET['id'])){
$user = $conn->query("SELECT * FROM users where id =".$_GET['id']);
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
}
?>
<div class="container-fluid">
	<form action="" id="manage-user">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" class="form-control" value="<?php echo isset($meta['name']) ? $meta['name']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="form-control" value="" placeholder="Enter new password if you want to change it">
		</div>
		<div class="form-group">
			<label for="type">User Type</label>
			<select name="type" id="type" class="custom-select">
				<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected': '' ?>>Admin</option>
				<option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected': '' ?>>Staff</option>
			</select>
		</div>
		<button type="submit" class="btn btn-primary">Save</button>
		<?php if (isset($meta['id'])): ?>
			<button type="button" class="btn btn-danger" id="delete-user">Delete</button>
		<?php endif; ?>
	</form>
</div>
<script>
    $('#manage-user').submit(function(e){
        e.preventDefault();
        $.ajax({
            url:'ajax.php?action=save_user',
            method:'POST',
            data:$(this).serialize(),
            success:function(resp){
                if(resp == 1){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Data successfully saved',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an issue saving the data',
                    });
                }
            }
        });
    });

    $('#delete-user').click(function(){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url:'ajax.php?action=delete_user',
                    method:'POST',
                    data: {id: <?php echo $meta['id']; ?>},
                    success:function(resp){
                        if(resp == 1){
                            Swal.fire(
                                'Deleted!',
                                'The user has been deleted.',
                                'success'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'There was an issue deleting the user',
                            });
                        }
                    }
                });
            }
        });
    });
</script>
