<!-- --------Start the session---------- -->
<?php session_start(); ?>

<!-- ------- Import the database connection --------- -->
<?php require_once('inc/connection.php'); ?>

<!-- ------- Import the function file --------- -->
<?php require_once('inc/functions.php'); ?>



<?php  

// Check for the form submission 
if (isset($_POST['sub_btn'])){
		
	$errors = array();   

	//Check if the uname and pword has been entered
	if (!isset($_POST['e_mail']) or strlen(trim($_POST['e_mail'])) < 1) {
		$errors = 'Invalid/missing username';	
	}

	if (!isset($_POST['pword']) or strlen(trim($_POST['pword'])) < 1) {
		$errors = 'Invalid/missing password';	
	}


	if (empty($errors)) {
		#for preventing from sql injection
		$password 	= mysqli_real_escape_string($connection, $_POST['pword']); 
		$email 		= mysqli_real_escape_string($connection, $_POST['e_mail']);

		//Encrypted password
		$hashed_password = sha1($password);

		//prepare database query
		$query = "SELECT * FROM user WHERE email = '{$email}' AND password = '{$hashed_password}' 
					AND is_deleted = 0 LIMIT 1";

		//Store query results in a variable
		$result_set = mysqli_query($connection, $query);

		verify_query($result_set);

			if (mysqli_num_rows($result_set) == 1) {	//1 valid user found
				
				//Display username after loggin					
				$user = mysqli_fetch_assoc($result_set);

					$_SESSION['user_id'] = $user['id'];
					$_SESSION['first_name'] = $user['first_name'];

					//Store current time (last loggin)
					$query = "UPDATE user SET last_login = NOW()";
					$query .= "WHERE id = {$_SESSION['user_id']} LIMIT 1";

					$result_set = mysqli_query($connection, $query);

					verify_query($result_set);

					header('Location: users.php');
			}

			else{
				$errors = 'Invalid username or password';
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
			<div class="jumbotron">
				<h2>User Management System</h2>		
			</div>
		
			<div class="col-md-12">					
				<form action="index.php" method="POST">
					
					<fieldset>
					<legend>
							<h2 id="signup-title">Sign in</h2><hr>
						</legend>

						<?php 
							if (isset($errors) && !empty($errors)) {
								echo '<p class="error">Invalid username or password</p>';
							}
						?>

						<?php  
							if (isset($_GET['logout'])) {
								echo '<p class="info">You have successfully logged out</p>';
							}
						?>

						<div class="mt-2">
							<label for="email"> Email  </label>
							<input class="form-control" type="email" name="e_mail" id="login-email"
								placeholder="Enter Email Address">
						</div>

						<div class="mt-3">
							<label for="pword"> Password  </label>
							<input class="form-control" type="password" name="pword" id="login-pword"
								placeholder="Enter your Password">
						</div>

						<div class="mt-5">
							<button class="btn btn-primary btn-md" type="submit" name="sub_btn" id="loginbtn">
								<i class="fa fa-sign-in mr-1" aria-hidden="true"></i> Login
							</button>
						</div>

					</fieldset>				

				</form>
				<hr>
						
				<div class="col-12 mt-4 ml-0 p-0">
					<h6 style="text-align:right">
						<i class="fa fa-arrow-circle-right" aria-hidden="true">	</i> Don't have an account? Sign up 
							<a href="register.php"> here 
								<i class="fa fa-smile-o" aria-hidden="true"></i>
							</a>
					</h6>
				</div>
			
			</div>
		</div>

		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	</body>
</html>


<!-- ------------- Close the database conection ---------------- -->
<?php mysqli_close($connection); ?>