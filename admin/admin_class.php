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

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM `users` where username = '".$username."' ");
		if($qry->num_rows > 0){
			$result = $qry->fetch_array();
			$is_verified = password_verify($password, $result['password']);
			if($is_verified){
			foreach ($result as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
			}
		}
			return 3;
	}
	function login2(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM user_info where email = '".$email."' ");
		if($qry->num_rows > 0){
			$result = $qry->fetch_array();
			$is_verified = password_verify($password, $result['password']);
			if($is_verified){
				foreach ($result as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
				$this->db->query("UPDATE cart set user_id = '".$_SESSION['login_user_id']."' where client_ip ='$ip' ");
					return 1;
			}
		}
			return 3;
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$password = password_hash($password, PASSWORD_DEFAULT);
		$data = " `name` = '$name' ";
		$data .= ", `username` = '$username' ";
		$data .= ", `password` = '$password' ";
		$data .= ", `type` = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$password = password_hash($password, PASSWORD_DEFAULT);
		$data = " first_name = '$first_name' ";
		$data .= ", last_name = '$last_name' ";
		$data .= ", mobile = '$mobile' ";
		$data .= ", address = '$address' ";
		$data .= ", email = '$email' ";
		$data .= ", password = '$password' ";
		$chk = $this->db->query("SELECT * FROM user_info where email = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO user_info set ".$data);
		if($save){
			$login = $this->login2();
			return 1;
		}
	}

	function update_profile(){
		extract($_POST);
		
		// Start building the update query
		$data = "first_name = '$first_name'";
		$data .= ", last_name = '$last_name'";
		$data .= ", mobile = '$phone'";
		$data .= ", address = '$address'";
		$data .= ", email = '$email'";
		
		$user_id = $_SESSION['login_user_id'];
		// Check if the email is already used by another user
		$chk = $this->db->query("SELECT * FROM user_info WHERE email = '$email' AND user_id != '$user_id'")->num_rows;
		if($chk > 0){
			return 2; // Email already exists
			exit;
		}
		// Proceed with updating the user profile
		$save = $this->db->query("UPDATE user_info SET $data WHERE user_id = '$user_id'");
		
		if($save){
			$login = $this->login2(); // Log the user in again to update session data
			return 1; // Success
		} 
	}
	function save_settings(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img1']['tmp_name'] != ''){
						$fname1 = strtotime(date('y-m-d H:i')).'_'.$_FILES['img1']['name'];
						$move = move_uploaded_file($_FILES['img1']['tmp_name'],'../assets/img/'. $fname1);
					$data .= ", cover_img = '$fname1' ";

		}
		if($_FILES['img2']['tmp_name'] != ''){
			$fname2 = strtotime(date('y-m-d H:i')).'_'.$_FILES['img2']['name'];
			$move = move_uploaded_file($_FILES['img2']['tmp_name'],'../assets/img/'. $fname2);
		$data .= ", home_img = '$fname2' ";

}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data." where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}

			return 1;
				}
	}

	
	function save_category(){
		extract($_POST);
		$data = " name = '$name' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO category_list set ".$data);
		}else{
			$save = $this->db->query("UPDATE category_list set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_category(){
		extract($_POST);		
		$delete = $this->db->query("DELETE FROM category_list where id = ".$id);
		$delete1 = $this->db->query("DELETE FROM product_list where category_id = ".$id);
		if($delete)
			return 1;
	}
	function save_menu(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", price = '$price' ";
		$data .= ", category_id = '$category_id' ";
		$data .= ", description = '$description' ";
		if(isset($status) && $status  == 'on')
		$data .= ", status = 1 ";
		else
		$data .= ", status = 0 ";

		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
					$data .= ", img_path = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO product_list set ".$data);
		}else{
			$save = $this->db->query("UPDATE product_list set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}

	function delete_menu(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM product_list where id = ".$id);
		if($delete)
			return 1;
	}
	function delete_cart(){
		extract($_GET);
		$delete = $this->db->query("DELETE FROM cart where id = ".$id);
		if($delete)
			header('location:'.$_SERVER['HTTP_REFERER']);
	}
	function add_to_cart(){
		extract($_POST);
		$data = " product_id = $pid ";	
		$qty = isset($qty) ? $qty : 1 ;
		// Prepare and execute the query to get the price
		$qry1 = $this->db->query("SELECT price FROM product_list WHERE id = $pid");
    
		// Fetch the price
		if ($row = $qry1->fetch_assoc()) {
			$price = $row['price']; // Corrected from 'qty' to 'price'
		} else {
			return 0; // Handle case where product is not found
		}
		
		// Append qty and price to data
		$data .= ", qty = $qty";	
		$data .= ", price = $price";	

		if(isset($_SESSION['login_user_id'])){
			$data .= ", user_id = '".$_SESSION['login_user_id']."' ";	
		}else{
			$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
			$data .= ", client_ip = '".$ip."' ";	

		}
		$save = $this->db->query("INSERT INTO cart set ".$data);
		if($save)
			return 1;
	}
	function get_cart_count(){
		extract($_POST);
		if(isset($_SESSION['login_user_id'])){
			$where =" where user_id = '".$_SESSION['login_user_id']."'  ";
		}
		else{
			$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
			$where =" where client_ip = '$ip'  ";
		}
		$get = $this->db->query("SELECT sum(qty) as cart FROM cart ".$where);
		if($get->num_rows > 0){
			return $get->fetch_array()['cart'];
		}else{
			return '0';
		}
	}

	function update_cart_qty(){
		extract($_POST);
		$data = " qty = $qty ";
		$save = $this->db->query("UPDATE cart set ".$data." where id = ".$id);
		if($save)
		return 1;	
	}

	function save_order(){
		extract($_POST);
		
		// Prepare and sanitize input
		$name = $this->db->real_escape_string($first_name . " " . $last_name);
		$address = $this->db->real_escape_string($address);
		$mobile = $this->db->real_escape_string($mobile);
		$email = $this->db->real_escape_string($email);
		$user_id = $_SESSION['login_user_id'];
		
		// Prepare the order insertion query
		$order_query = "INSERT INTO orders (name, address, mobile, email, user_id, order_date) 
						VALUES ('$name', '$address', '$mobile', '$email', '$user_id', NOW())";
		$save = $this->db->query($order_query);
		
		if($save){
			$order_id = $this->db->insert_id; // Get the last inserted order ID
	
			// Retrieve the user's cart items
			$qry = $this->db->query("SELECT * FROM cart WHERE user_id = $user_id");
			
			while($row = $qry->fetch_assoc()){
				$product_id = $row['product_id'];
				$qty = $row['qty'];
				$price = $row['price'];
				$totalPrice = $qty * $price; // Calculate total price for this item
	
				// Insert each item into the order_list table
				$item_query = "INSERT INTO order_list (order_id, product_id, qty, price, total_price) 
							   VALUES ('$order_id', '$product_id', '$qty', '$price', '$totalPrice')";
				$save2 = $this->db->query($item_query);
				
				if($save2){
					// Delete the item from the cart after saving to order_list
					$this->db->query("DELETE FROM cart WHERE id = ".$row['id']);
				}
			}
	
			// Update total amount in the orders table
			$update_total_query = "UPDATE orders 
								   SET total_amount = (SELECT SUM(total_price) FROM order_list WHERE order_id = '$order_id')
								   WHERE id = '$order_id'";
			$this->db->query($update_total_query);
	
			return $order_id; // Return the order ID
		}
	
		return 0; // Return 0 if order saving failed
	}
	
	
	function confirm_order(){
		extract($_POST);
		$stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
		$stmt->bind_param("ii", $status_id, $id); // status_id and id are both integers
		$save = $stmt->execute();
	
		if($save)
			return 1;
		else
			return 0;
	}


}