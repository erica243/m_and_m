<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .logo {
      margin: auto;
      font-size: 20px;
      background: white;
      padding: 5px 11px;
      border-radius: 50% 50%;
      color: #000000b3;
    }
    .notification-badge {
      position: absolute;
      top: -5px;
      right: -25px;
      background-color: red;
      color: white;
      padding: 5px 8px;
      border-radius: 50%;
      font-size: 12px;
    }
    .notification-icon {
      position: relative;
      cursor: pointer;
      margin-right: 50px;
    }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
  <nav class="navbar navbar-light bg-light fixed-top" style="padding: 0; height: 3.4em">
    <div class="container-fluid mt-2 mb-2">
      <div class="col-lg-12">
        <div class="col-md-1 float-left" style="display: flex;">
          <div class="logo"></div>
        </div>
        <div class="col-md-4 float-left" style="font-size: 24px;">
          <large style="font-family: 'Dancing Script', cursive !important;"><b><?php echo $_SESSION['setting_name']; ?></b></large>
        </div>
        <div class="col-md-2 float-right" style="display: flex; align-items: center;">
          <div class="notification-icon">
            <i class="fa fa-bell"></i>
            <span class="notification-badge">0</span>
          </div>
          <a href="ajax.php?action=logout" class="text-dark"><?php echo $_SESSION['login_name']; ?> <br> <i class="fa fa-sign-out-alt"></i></a>
        </div>
      </div>
    </div>
  </nav>

  <script>
    function checkForNewOrders() {
      $.ajax({
        url: 'admin/ajax.php?action=get_notifications',
        method: 'GET',
        success: function(response) {
          var notifications = JSON.parse(response);
          var newOrdersCount = notifications.length;

          // Update notification badge
          $('.notification-badge').text(newOrdersCount);

          // Optional: Show an alert if there are new orders
          if (newOrdersCount > 0) {
            alert("You have " + newOrdersCount + " new orders!");
          }
        },
        error: function(xhr, status, error) {
          console.error('Error fetching notifications:', error);
        }
      });
    }

    setInterval(checkForNewOrders, 30000); // Check every 30 seconds

    $('.notification-icon').click(function() {
      window.location.href = '../admin/index.php?page=orders';
    });

    $(document).ready(function() {
      checkForNewOrders();
    });
  </script>
</body>
</html>
