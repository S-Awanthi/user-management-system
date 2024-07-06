<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php  

//Checking if a user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['first_name'])) {	
	header('Location: index.php ');		//if not logged		
}

	$errors = array();

	$first_name = '';
	$last_name = '';
	$email = '';
	$password = '';

	
	if (isset($_POST['sub_btn'])) {
		 
		//If the form has submit, then save the values in the global variables
		$first_name = $_POST['first_name'] ;
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		$password = $_POST['password'];

		
		// Checking fields (using user defined function)
		$req_fields = array('first_name', 'last_name', 'email', 'password');
		
		//Combine two arrays($errors) and display errors
		$errors = array_merge($errors, check_req_fields($req_fields));   //2 parameters; 1st and 2nd array

		//Email address validation	
		if (!is_email($_POST['email'])) {
			$errors[] = 'You have entered an invalid email address <br>';
		}

		//Checking max length of fields (using assosiative array and a function)
		$max_len_fields = array('first_name' => 50, 'last_name' => 100, 'email' => 100, 'password' => 40);
		$errors = array_merge($errors, check_max_length($max_len_fields));

		//Checking if email is already exists
		$email = mysqli_real_escape_string($connection, $_POST['email']);
		$query = "SELECT * FROM user WHERE email = '{$email}' AND is_deleted = 0 LIMIT 1 ";
		$result_set = mysqli_query($connection, $query);

		if ($result_set) {
			if (mysqli_num_rows($result_set) == 1) {
				$errors[] = 'Email address already exists';
			}
		}



		if (empty($errors)) {
			//no errors found......then add new record
			$first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
			$last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
			$password = mysqli_real_escape_string($connection, $_POST['password']);
			//email address is already sanitized

			$hashed_password = sha1($password);

			$query = "INSERT INTO user (";
			$query .= "first_name, last_name, email, password, is_deleted";
			$query .= ") VALUES (";
			$query .= "'{$first_name}', '{$last_name}', '{$email}', '{$hashed_password}', 0";
			$query .= ")";
			

			$result = mysqli_query($connection, $query);

			if ($result) {
				//query success....redirect to users.php
				// header("Location: users.php?user_added=true");
			}
			else{
				$errors[] = 'Failed to add new record'; 
			} 


		}
	}
?>


<!-- -------------------------------------- Form starts here ---------------------------------------------- -->


<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>Log In - UMS</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="css/main.css">
	</head>

	<body>
		<div class="container">
			<div class="header">
				<!-- Access the session variable to display username -->
				<div class="loggedin">Hello   <?php echo '<b><font color=purple>'. $_SESSION['first_name'] .' </font></b>'; ?> 				
				</div>
					<button class="btn btn-dark ml-4" onclick="return confirm('Are you sure want to logout?')";>
						<a id="logout-btn"  href="logout.php"> Logout</a>
					</button>
			</div>

			<div class="jumbotron" style="padding:1px, margin:10px">
				<h2>Welcome to User Management System</h2>		
			</div>	

			<div class="row">
				<div class="col-10"><h3 >Add New User</h3></div>
				<!-- <div class="col-2">
					<a style="float: right" href="users.php" role="button">
						<i class="fa fa-hand-o-left mr-1"></i> User List 
					</a>
				</div> -->
			</div>	
			<hr>	

			
			<div class="col-md-12">	
				<form action="add-user.php" method="POST">

					<fieldset>
						<?php 
							if (empty($errors) && isset($_POST['sub_btn'])) {
								echo '<p class="info">User has been added successfully</p>';
							}
						?>
									
						<?php
							if (!empty($errors)) {
								display_errors($errors);
							}
						?>					

						<div class="row">
						<div class="col-12">
								<a style="float: right" href="users.php" role="button">
									<i class="fa fa-hand-o-left mr-1"></i> User List 
								</a>
							</div>
						</div>

						<div class="row">
							<div class="col-6 mt-2">
								<label for="first_name">First Name  </label>
								<input class="form-control" type="text" name="first_name" 
									placeholder="Enter first name">
							</div>

							<div class="col-6 mt-2">
								<label for="last_name">Last Name </label>
								<input class="form-control" type="text" name="last_name" 
									placeholder="Enter last name">
							</div>
						</div>

						<div class="mt-3">
							<label for="email">Email </label>
							<input class="form-control" type="email" name="email" 
								placeholder="Enter email address">
						</div>

						<div class="mt-3">
							<label for="password">Password </label>
							<input class="form-control" type="password" name="password" id="password" 
								placeholder="Enter password">	
						</div>

						<div class="mt-3">
							<input type="checkbox" name="password" id="showpassword"> <br>
							<label for=""> Show Password</label>						
						</div>

						<div class="mt-1">
							<button class="btn btn-success btn-md" type="submit" name="sub_btn" 
								id="add-user-btn">
								<i class="fa fa-user-plus mr-1" aria-hidden="true"></i> Add User
							</button>
						</div>

					</fieldset>

				</form>	
			</div>


			<!-- Show password -->
			<script src="js/jquery.js"></script>
			<script>
				$(document).ready(function(){

					$('#showpassword').click(function(){
						
						if ( $('#showpassword').is(':checked') ) {   //if check box is ticked
							$('#password').attr('type','text');       //change the input type to 'text'
						}
						else{
							$('#password').attr('type','password');		//keep input type as it is
						}
					});
				});
			</script>
			

			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
			</div>
	</body>
</html>