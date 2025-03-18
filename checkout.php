<?php
include 'admin/db_connect.php';
// var_dump($_SESSION);
$chk = $conn->query("SELECT * FROM cart where user_id = {$_SESSION['login_user_id']} ")->num_rows;
if($chk <= 0){
    echo "<script>alert('You don\'t have an Item in your cart yet.'); location.replace('./')</script>";
}
?>
  <header class="masthead">
            <div class="container h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-10 align-self-center mb-4 page-title">
                    	<h1 class="text-white">CHECKOUT</h1>
                        <hr class="divider my-4 bg-dark" />
                    </div>
                    
                </div>
            </div>
        </header>
    <div class="container">
        
<?php
 if (isset($_SESSION['login_user_id'])) {
    $user_id = $_SESSION['login_user_id'];    
    
    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT first_name, last_name, email, mobile, address FROM user_info WHERE user_id = '$user_id'");
    $stmt->execute();
     // Bind result variables
     $stmt->bind_result($first_name, $last_name, $email, $phone,$address);
    
     // Fetch the data
     if ($stmt->fetch()) {
         // Use the variables directly after fetching
         $user = [
             'first_name' => $first_name,
             'last_name' => $last_name,
             'email' => $email,
             'phone' => $phone,
             'address' => $address
         ];
     } else {
         echo "No user found.";
     }
} else {
    header("Location: index.php?page=home");
    exit(); // Redirect to home if not logged in
}

?>
        <div class="card">
            <div class="card-body">
                <form action="" id="checkout-frm">
                    <h4>Confirm Delivery Information</h4>
                    <div class="form-group">
                        <label for="" class="control-label">Firstname</label>
                        <input type="text" id="first_name" name="first_name" required="" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>">
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Lastname</label>
                        <input type="text" id="last_name" name="last_name" required="" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>">
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Contact</label>
                        <input type="text" id="user_contact" name="mobile" required="" class="form-control" value="<?php echo htmlspecialchars($phone); ?>">
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Address</label>
                        <textarea cols="30" rows="3" id="user_address" name="address" required="" class="form-control"><?php echo htmlspecialchars($address); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Email</label>
                        <input type="email" id="user_email" name="email"  required="" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                    </div>  

                    <div class="text-center">
                        <button class="btn btn-block btn-outline-dark">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script>
        (function() {
            emailjs.init("d0pxHBpMCkJEotECi"); // Replace with your encrypted user ID
        })();
    
      $(document).ready(function(){
    $('#checkout-frm').submit(function(e){
        e.preventDefault(); // Prevent default form submission

        start_load(); // Assuming you have this function to show a loading spinner

        $.ajax({
            url: "admin/ajax.php?action=save_order",
            method: 'POST',
            data: $(this).serialize(), // Serialize the form data
            success: function(resp){
                if(resp > 0){ // Check if a valid order_id is returned
                    alert_toast("Order successfully placed with Order ID: " + resp, 'success');

                     // Send email with order details
                     const userEmail = document.getElementById("user_email").value;
                     const userContact = document.getElementById("user_contact").value;
                     const firstname = document.getElementById("first_name").value;
                     const lastname = document.getElementById("last_name").value;
                     const userAddress = document.getElementById("user_address").value;
                     var emailParams = {
                            order_id: resp, // This is the order ID from the response
                            user_email: userEmail,// Assuming you have a field for the user's email
                            user_contact: userContact,
                            first_name: firstname,
                            last_name: lastname,
                           // user_address: userAddress,
                     };
                        
                        emailjs.send('service_egwtozc', 'template_1bfsbu1', emailParams)
                            .then(function(response) {
                                console.log('SUCCESS!', response.status, response.text);
                            }, function(error) {
                                console.error('FAILED...', error);
                            });


                    setTimeout(function(){
                        location.replace('index.php?page=home');
                    }, 1500);
                } else {
                    alert_toast("Failed to place the order. Please try again.", 'error');
                }
            },
            error: function(){
                alert_toast("An error occurred while placing the order. Please try again.", 'error');
            }
        });
    });
});

    </script>