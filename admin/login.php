<!DOCTYPE html>
<html lang="en">

<head>
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

  <style>
    body {
      width: 100%;
      height: calc(100%);
    }
    main#main {
      width: 100%;
      height: calc(100%);
      background: white;
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
		background-image: linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%);
background-image: linear-gradient(to top, #a8edea 0%, #fed6e3 100%);
	}
    #login-left {
      position: absolute;
      left: 0;
      width: 60%;
      height: calc(100%);
      background: #00000061;
      display: flex;
      align-items: center;
    }
    #login-right .card {
      margin: auto;
      width: 100%;
      max-width: 400px;
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
    .logo img {
      height: 80%;
      width: 80%;
      margin: auto;
    }
    #login-left {
      background: url(./../assets/img/<?php echo $_SESSION['setting_cover_img'] ?>);
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center center;
    }
    #login-left:before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      backdrop-filter: brightness(.8);
      z-index: 1;
    }
    #login-left .d-flex {
      position: relative;
      z-index: 2;
    }
    #login-left h1 {
      font-family: 'Dancing Script', cursive !important;
      font-weight: bolder;
      font-size: 4.5em;
      color: #fff;
      text-shadow: 0px 0px 5px #000;
    }
    .show-password {
      cursor: pointer;
      color: #007bff;
      font-size: 0.875rem;
    }
  </style>

</head>

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
          <form id="login-form">
            <div class="form-group">
              <label for="username" class="control-label">Username</label>
              <input type="email" id="username" name="username" autofocus class="form-control">
            </div>
            <div class="form-group">
              <label for="password" class="control-label">Password</label>
              <div class="input-group">
                <input type="password" id="password" name="password" class="form-control">
                <div class="input-group-append">
                  <span class="input-group-text show-password" id="password-toggle">
                    <i class="fa fa-eye"></i>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group text-center">
    <a href="./../" class="text-dark">Back to Website</a>
</div>
<center><button class="btn-sm btn-block btn-wave col-md-4 btn-dark">Login</button></center>
<div class="form-group text-center">
    <a href="forgot_password.php" class="text-dark">Forgot Password?</a>
</div>

      </div>
    </div>
  </main>

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#password-toggle').click(function() {
        var passwordField = $('#password');
        var passwordFieldType = passwordField.attr('type');
        if (passwordFieldType === 'password') {
          passwordField.attr('type', 'text');
          $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
          passwordField.attr('type', 'password');
          $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
        }
      });

      $('#login-form').submit(function(e) {
        e.preventDefault();
        $('#login-form button[type="button"]').attr('disabled', true).html('Logging in...');
        if ($(this).find('.alert-danger').length > 0)
          $(this).find('.alert-danger').remove();
        $.ajax({
          url: 'ajax.php?action=login',
          method: 'POST',
          data: $(this).serialize(),
          error: err => {
            console.log(err);
            $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
          },
          success: function(resp) {
            if (resp == 1) {
              location.href = 'index.php?page=home';
            } else {
              $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
              $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
            }
          }
        });
      });
    });
  </script>
</body>

</html>
