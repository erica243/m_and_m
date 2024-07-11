<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin | M&M Cake Ordering System</title>

  <?php include('./header.php'); ?>
  <?php include('./db_connect.php'); ?>
  <?php 
  session_start();
  if(isset($_SESSION['login_id']))
  header("location:index.php?page=home");

  $query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
    foreach ($query as $key => $value) {
      if(!is_numeric($key))
        $_SESSION['setting_'.$key] = $value;
    }
  ?>
</head>
<style>
  body{
    width: 100%;
    height: calc(100%);
    /*background: #007bff;*/
  }
  main#main{
    width:100%;
    height: calc(100%);
    background:white;
  }
  #login-right{
    position: absolute;
    right:0;
    width:40%;
    height: calc(100%);
    background:white;
    display: flex;
    align-items: center;
    justify-content: center; /* Added to center the card horizontally */
    background:#000;
  }
  #login-left{
    position: absolute;
    left:0;
    width:60%;
    height: calc(100%);
    background:#00000061;
    display: flex;
    align-items: center;
  }
  #login-right .card{
    margin: auto;
    width: 100%; /* Added to make sure the card doesn't overflow */
    max-width: 400px; /* Optional: limit the max width of the card */
  }
  .logo {
      margin: auto;
      font-size: 8rem;
      background: white;
      border-radius: 50% 50%;
      height: 29vh;
      width: 13vw;
      display: flex;
      align-items: center;
  }
  .logo img{
    height: 80%;
    width: 80%;
    margin: auto
  }
  #login-left {
    background: url(./../assets/img/<?php echo $_SESSION['setting_cover_img'] ?>);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center center;
  }
  #login-left:before{
    content:"";
    position:absolute;
    top:0;
    left:0;
    height:100%;
    width:100%;
    backdrop-filter:brightness(.8);
    z-index:1;
  }
  #login-left .d-flex{
    position: relative;
    z-index: 2;
  }
  #login-left h1{
    font-family: 'Dancing Script', cursive !important;
    font-weight:bolder;
    font-size:4.5em;
    color:#fff;
    text-shadow: 0px 0px 5px #000;
  }
</style>

<body>
  <main id="main" class=" bg-dark">
    <div id="login-left" class="">
      <div class="h-100 w-100 d-flex justify-content-center align-items-center">
          <h1 class="text-center"><?= $_SESSION['setting_name'] ?> - Admin Site</h1>
      </div>
    </div>
    <div id="login-right">
      <div class="card col-md-8">
        <div class="card-body">
          <form id="login-form" >
            <div class="form-group">
              <label for="username" class="control-label">Username</label>
              <input type="text" id="username" name="username" autofocus class="form-control">
            </div>
            <div class="form-group">
              <label for="password" class="control-label">Password</label>
              <input type="password" id="password" name="password" class="form-control">
            </div>
            <div class="form-group text-center">
              <a href="./../" class="text-dark">Back to Website</a>
            </div>
            <center><button class="btn-sm btn-block btn-wave col-md-4 btn-dark">Login</button></center>
          </form>
        </div>
      </div>
    </div>
  </main>

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

</body>
<script>
  $('#login-form').submit(function(e){
    e.preventDefault()
    $('#login-form button[type="button"]').attr('disabled',true).html('Logging in...');
    if($(this).find('.alert-danger').length > 0 )
      $(this).find('.alert-danger').remove();
    $.ajax({
      url:'ajax.php?action=login',
      method:'POST',
      data:$(this).serialize(),
      error:err=>{
        console.log(err)
        $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Something went wrong!',
        });
      },
      success:function(resp){
        if(resp == 1){
          Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            showConfirmButton: false,
            timer: 1500
          });
          setTimeout(function(){
            location.href ='index.php?page=home';
          }, 1500);
        }else{
          Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: 'Username or password is incorrect.',
          });
          $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
        }
      }
    })
  })
</script>
</html>
