 <!-- Masthead-->
 <header class="masthead">
            <div class="container h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-10 align-self-center mb-4 page-title">
                    	<h1 class="text-white">PROFILE</h1>
                        <hr class="divider my-4 bg-dark" />
                    </div>
                    
                </div>
            </div>
        </header>
		<section class="page-section" id="orders">
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
        <div class="container mt-5 pt-5">
        <h2 class="mt-5">Edit Profile Information</h2>
        <form id="signup-frm">
        <div class="card p-2">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($address); ?></textarea>
            </div>
            <button class="button btn btn-info btn-sm" id="saveProfile">Save Changes</button>
        </div>
</form>
    </div>
    
</section>

    <style>
    </style>
    <script>
        $('#signup-frm').submit(function(e){
		e.preventDefault()
        // Add confirmation alert
    if (!confirm("Are you sure you want to save your changes? You need to login once again")) {
        return; // Stop the form submission if the user clicks "Cancel"
    }
		$('#signup-frm button[type="submit"]').attr('disabled',true).html('Saving...');
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
		$.ajax({
			url:'admin/ajax.php?action=update_profile',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
		$('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');

			},
			success:function(resp){
				if(resp == 1){
        // location.href ='<?php echo isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?page=home' ?>';
        location.href ='admin/ajax.php?action=logout2';
    } else if(resp == 2){
        $('#signup-frm').prepend('<div class="alert alert-danger">Email already exists.</div>')
        $('#signup-frm button[type="submit"]').removeAttr('disabled').html('Create');
    } else {
        location.href ='admin/ajax.php?action=logout2';
    }
			}
		})
	})
    </script>
	
