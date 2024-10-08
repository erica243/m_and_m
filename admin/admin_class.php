<?php
session_start();

Class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php';
        $this->db = $conn;
    }

    function __destruct() {
        $this->db->close();
        ob_end_flush();
    }

    function login() {
        extract($_POST);
        $qry = $this->db->query("SELECT * FROM `users` WHERE username = '".$username."'");
        if($qry->num_rows > 0) {
            $result = $qry->fetch_array();
            $is_verified = password_verify($password, $result['password']);
            if($is_verified) {
                foreach ($result as $key => $value) {
                    if($key != 'password' && !is_numeric($key))
                        $_SESSION['login_'.$key] = $value;
                }
                return 1;
            }
        }
        return 3;
    }

    function login2() {
        extract($_POST);
        $qry = $this->db->query("SELECT * FROM user_info WHERE email = '".$email."'");
        if($qry->num_rows > 0) {
            $result = $qry->fetch_array();
            $is_verified = password_verify($password, $result['password']);
            if($is_verified) {
                foreach ($result as $key => $value) {
                    if($key != 'password' && !is_numeric($key))
                        $_SESSION['login_'.$key] = $value;
                }
                $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
                $this->db->query("UPDATE cart SET user_id = '".$_SESSION['login_user_id']."' WHERE client_ip = '$ip'");
                return 1;
            }
        }
        return 3;
    }

    function logout() {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:login.php");
    }

    function logout2() {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:../index.php");
    }

    function save_user() {
        extract($_POST);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data = " `name` = '$name' ";
        $data .= ", `username` = '$username' ";
        $data .= ", `password` = '$password' ";
        $data .= ", `type` = '$type' ";
        if(empty($id)) {
            $save = $this->db->query("INSERT INTO users SET ".$data);
        } else {
            $save = $this->db->query("UPDATE users SET ".$data." WHERE id = ".$id);
        }
        if($save) {
            return 1;
        }
    }

    function signup() {
        extract($_POST);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $data = " first_name = '$first_name' ";
        $data .= ", last_name = '$last_name' ";
        $data .= ", mobile = '$mobile' ";
        $data .= ", address = '$address' ";
        $data .= ", email = '$email' ";
        $data .= ", password = '$password' ";
        $chk = $this->db->query("SELECT * FROM user_info WHERE email = '$email'")->num_rows;
        if($chk > 0) {
            return 2;
            exit;
        }
        $save = $this->db->query("INSERT INTO user_info SET ".$data);
        if($save) {
            $login = $this->login2();
            return 1;
        }
    }

    function save_settings() {
        extract($_POST);
        $data = " name = '$name' ";
        $data .= ", email = '$email' ";
        $data .= ", contact = '$contact' ";
        $data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
        if($_FILES['img']['tmp_name'] != '') {
            $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
            $move = move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/'. $fname);
            $data .= ", cover_img = '$fname' ";
        }
        $chk = $this->db->query("SELECT * FROM system_settings");
        if($chk->num_rows > 0) {
            $save = $this->db->query("UPDATE system_settings SET ".$data." WHERE id =".$chk->fetch_array()['id']);
        } else {
            $save = $this->db->query("INSERT INTO system_settings SET ".$data);
        }
        if($save) {
            $query = $this->db->query("SELECT * FROM system_settings LIMIT 1")->fetch_array();
            foreach ($query as $key => $value) {
                if(!is_numeric($key))
                    $_SESSION['setting_'.$key] = $value;
            }
            return 1;
        }
    }

    function save_category() {
        extract($_POST);
        $data = " name = '$name' ";
        if(empty($id)) {
            $save = $this->db->query("INSERT INTO category_list SET ".$data);
        } else {
            $save = $this->db->query("UPDATE category_list SET ".$data." WHERE id=".$id);
        }
        if($save)
            return 1;
    }

    function delete_category() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM category_list WHERE id = ".$id);
        if($delete)
            return 1;
    }

  public function save_menu() {
    // Check if database connection is initialized
    if (!isset($this->db)) {
        return "Database connection error.";
    }

    // Extract form data
    extract($_POST);

    // Prepare data to be updated/inserted with proper escaping
    $data = "name = '" . $this->db->real_escape_string($name) . "'";
    $data .= ", price = '" . $this->db->real_escape_string($price) . "'";
    $data .= ", category_id = '" . $this->db->real_escape_string($category_id) . "'";
    $data .= ", description = '" . $this->db->real_escape_string($description) . "'";
   
    $data .= ", status = '" . ($status == 'Available' ? 'Available' : 'Unavailable') . "'"; // Handle availability

    // Handle file upload
    if (!empty($_FILES['img']['tmp_name'])) {
        $fileName = strtotime(date('m-d-Y H:i')) . '_' . $_FILES['img']['name'];
        $uploadDir = '../assets/img/';
        $uploadFile = $uploadDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($_FILES['img']['tmp_name'], $uploadFile)) {
            $data .= ", img_path = '" . $this->db->real_escape_string($fileName) . "'";
        } else {
            // Handle file upload error
            return "Failed to upload image.";
        }
    }

    // Perform insert or update
    if (empty($id)) {
        $query = "INSERT INTO product_list SET " . $data;
    } else {
        $query = "UPDATE product_list SET " . $data . " WHERE id=" . intval($id);
    }

    $save = $this->db->query($query);

    // Check for SQL errors
    if (!$save) {
        return "Database error: " . $this->db->error;
    }

    return 1;
}


    function delete_menu() {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM product_list WHERE id = ".$id);
        if($delete)
            return 1;
    }

    function delete_cart() {
        extract($_GET);
        $delete = $this->db->query("DELETE FROM cart WHERE id = ".$id);
        if($delete)
            header('location:'.$_SERVER['HTTP_REFERER']);
    }

    function add_to_cart() {
        extract($_POST);
        $data = " product_id = $pid ";    
        $qty = isset($qty) ? $qty : 1 ;
        $data .= ", qty = $qty ";    
        if(isset($_SESSION['login_user_id'])) {
            $data .= ", user_id = '".$_SESSION['login_user_id']."' ";    
        } else {
            $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
            $data .= ", client_ip = '".$ip."' ";    
        }
        $save = $this->db->query("INSERT INTO cart SET ".$data);
        if($save)
            return 1;
    }

    function get_cart_count() {
        extract($_POST);
        if(isset($_SESSION['login_user_id'])) {
            $where = " WHERE user_id = '".$_SESSION['login_user_id']."' ";
        } else {
            $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
            $where = " WHERE client_ip = '$ip' ";
        }
        $get = $this->db->query("SELECT SUM(qty) AS cart FROM cart ".$where);
        if($get->num_rows > 0) {
            return $get->fetch_array()['cart'];
        } else {
            return '0';
        }
    }

    function update_cart_qty() {
        extract($_POST);
        $data = " qty = $qty ";
        $save = $this->db->query("UPDATE cart SET ".$data." WHERE id = ".$id);
        if($save)
            return 1;    
    }
    function save_order() {
        // Use mysqli_real_escape_string to escape all inputs
        $order_number = rand(1000, 9999); // Example random order number
        $order_date = date('Y-m-d H:i:s'); // Current date and time
        $delivery_method = isset($_POST['order_type']) ? $this->db->real_escape_string($_POST['order_type']) : 'Delivery'; // Default to delivery
        $first_name = $this->db->real_escape_string($_POST['first_name']);
        $last_name = $this->db->real_escape_string($_POST['last_name']);
        $address = $this->db->real_escape_string($_POST['address']);
        $mobile = $this->db->real_escape_string($_POST['mobile']);
        $email = $this->db->real_escape_string($_POST['email']);
        $payment_method = $this->db->real_escape_string($_POST['payment_method']);
        $transaction_id = isset($_POST['transaction_id']) ? $this->db->real_escape_string($_POST['transaction_id']) : '';
    
        // Handle pickup date and time
        $pickup_date = isset($_POST['pickup_date']) && !empty($_POST['pickup_date']) ? $this->db->real_escape_string($_POST['pickup_date']) : 'N/A';
        $pickup_time = isset($_POST['pickup_time']) && !empty($_POST['pickup_time']) ? $this->db->real_escape_string($_POST['pickup_time']) : 'N/A';
    
        // Handle payment proof upload
        $payment_proof_path = '';
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/payment_proof/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
    
            $file_name = time() . '_' . basename($_FILES['payment_proof']['name']);
            $target_file = $upload_dir . $file_name;
    
            // Move the uploaded file
            if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_file)) {
                $payment_proof_path = $target_file;
            } else {
                return "Error: Could not upload payment proof.";
            }
        }
    
        // Prepare SQL query
        $sql = "INSERT INTO orders (order_number, order_date, delivery_method, name, address, mobile, email, payment_method, transaction_id, pickup_date, pickup_time, payment_proof) 
                VALUES ('$order_number', '$order_date', '$delivery_method', '$first_name $last_name', '$address', '$mobile', '$email', '$payment_method', '$transaction_id', 
                '$pickup_date', '$pickup_time', '$payment_proof_path')";
        
        // Execute query
        $save = $this->db->query($sql);
        if (!$save) {
            return "Error: " . $this->db->error;
        }
    
        $id = $this->db->insert_id; // Get the last inserted ID
    
        $qry = $this->db->query("SELECT * FROM cart WHERE user_id = " . $_SESSION['login_user_id']);
        while ($row = $qry->fetch_assoc()) {
            $product_id = $this->db->real_escape_string($row['product_id']);
            $qty = $this->db->real_escape_string($row['qty']);
            $sql2 = "INSERT INTO order_list (order_id, product_id, qty) VALUES ('$id', '$product_id', '$qty')";
    
            $save2 = $this->db->query($sql2);
            if (!$save2) {
                return "Error: " . $this->db->error;
            }
    
            // Remove item from cart
            $this->db->query("DELETE FROM cart WHERE id = " . $row['id']);
        }
    
        return 1; // Indicate success
    }
    
    
    
    function confirm_order() {
        extract($_POST);
        $date = date("m-d-Y H:i:s");
        $save = $this->db->query("UPDATE orders SET status = 1, created_at = '$date' WHERE id= ".$id);
        if($save)
            return 1;
    }

    function cancel_order() {
        extract($_POST);
        $update = $this->db->query("UPDATE orders SET status = 'Canceled' WHERE id = $id");
        if($update)
            return 1;
        else
            return 0;
    }

    // New method for deleting a user
    function delete_user() {
        extract($_POST);
        // Make sure to handle both 'users' and 'user_info' tables if necessary
        $delete = $this->db->query("DELETE FROM users WHERE id = ".$id);
        if($delete) {
            $this->db->query("DELETE FROM user_info WHERE id = ".$id); // If user_info table exists and should also be cleaned up
            return 1;
        }
    }
    // Inside admin_class.php
    public function update_delivery_status($order_id, $new_status) {
        // Escape inputs
        $order_id = $this->db->real_escape_string($order_id);
        $new_status = $this->db->real_escape_string($new_status);
    
        // Prepare SQL query to update the delivery status
        $sql = "UPDATE orders SET delivery_status = '$new_status' WHERE id = '$order_id'";
    
        // Log the SQL query for debugging
        error_log("SQL Query: " . $sql); // Log the query
    
        // Execute the query
        $update = $this->db->query($sql);
        if (!$update) {
            return "Error updating delivery status: " . $this->db->error; // Return error if the query fails
        }
    
        return "Delivery status updated successfully."; // Return success message
    }
    }
    function delete_order() {
        global $conn;
        $orderId = $_POST['id'];
        if (isset($orderId)) {
            $qry = $conn->query("DELETE FROM orders WHERE id = '$orderId'");
            return $qry ? 1 : 0; // Return 1 on success, 0 on failure
        }
        return 0; // In case id is not set
    }
    ?>
    