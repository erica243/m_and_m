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
    top: 0;
    right: 10px;
    background-color: red;
    color: white;
    padding: 5px 10px;
    border-radius: 50%;
  }
  .notification-icon {
    position: relative;
    cursor: pointer;
  }
</style>

<nav class="navbar navbar-light bg-light fixed-top" style="padding: 0; height: 3.4em">
  <div class="container-fluid mt-2 mb-2">
    <div class="col-lg-12">
      <div class="col-md-1 float-left" style="display: flex;">
        <div class="logo"></div>
      </div>
      <div class="col-md-4 float-left" style="font-size: 24px;">
        <large style="font-family: 'Dancing Script', cursive !important;"><b><?php echo $_SESSION['setting_name']; ?></b></large>
      </div>
      <div class="col-md-2 float-right">
        <a href="ajax.php?action=logout" class="text-dark"><?php echo $_SESSION['login_name'] ?> <br> <i class="fa fa-sign-out-alt"></i></a>
        <!-- Notification Icon and Badge -->
        
      </div>
    </div>
  </div>
</nav>

<script>
  // Function to check for new orders and update notification badge
  function checkForNewOrders() {
    // Simulate AJAX call to check new orders (replace with actual AJAX code)
    var newOrdersCount = 2; // Replace with actual count from server
    
    // Update notification badge
    $('.notification-badge').text(newOrdersCount);
  }

  // Check for new orders every 30 seconds (adjust as needed)
  setInterval(checkForNewOrders, 30000);

  // Handle click event on notification icon
  $('.notification-icon').click(function() {
    // Replace with actual logic to show orders
    alert('Show orders functionality here');
    // Example: Redirect to orders page
    // window.location.href = 'orders.php';
  });
</script>
