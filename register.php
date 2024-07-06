<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php  

	$errors = array();

	$first_name = '';
	$last_name = '';
	$email = '';
	$password = '';

	//Check user has submit the form
	if (isset($_POST['sub_btn'])) {
		 
		//If the form has submit, then store the values 
		$first_name = $_POST['first_name'] ;
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		$password = $_POST['password'];

		
		$req_fields = array('first_name', 'last_name', 'email', 'password');

		if (!is_email($_POST['email'])) {
			$errors[] = 'Invalid email address <br>';
		}
		
		//Combine two arrays($errors) and display errors
		$errors = array_merge($errors, check_req_fields($req_fields));   

		$max_len_fields = array('first_name' => 50, 'last_name' => 100, 'email' => 100, 'password' => 40);
		$errors = array_merge($errors, check_max_length($max_len_fields));


		//Checking if email is already exists
		$email = mysqli_real_escape_string($connection, $_POST['email']);

		$query = "SELECT * FROM user WHERE email = '{$email}' LIMIT 1";

		$result_set = mysqli_query($connection, $query);

		if ($result_set) {
			if (mysqli_num_rows($result_set) == 1) {
				$errors[] = 'Email address already exists';
			}
		}



		if (empty($errors)) {
			//no errors found......then register
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
				// header("Location: index.php");
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
		<title>Register - UMS</title>
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
				<form action="register.php" method="POST">

					<fieldset>
						<?php 
							if (empty($errors) && isset($_POST['sub_btn'])) {
								echo '<p class="info">Congratulations! Now you are a member</p>';
							}
						?>
									
						<?php
							if (!empty($errors)) {
								display_errors($errors);
							}
						?>

						<legend>
							<h2 id="signup-title">Sign up</h2><hr>
						</legend>
						
						<div class="row">
						<div class="col-6 mt-2">
							<label for="first_name">First Name  </label>
							<input class="form-control" type="text" name="first_name" placeholder="Enter first name">
						</div>

						<div class="col-6 mt-2">
							<label for="last_name">Last Name </label>
							<input class="form-control" type="text" name="last_name" placeholder="Enter last name">
						</div>
						</div>

						<div class="mt-3">
							<label for="email">Email  </label>
							<input class="form-control" type="email" name="email" placeholder="Enter email address">
						</div>

						<div class="mt-3">
							<label for="password">Password </label>
							<input class="form-control" type="password" name="password" id="password" placeholder="Enter password">	
						</div>

						<div class="mt-3">
							<input type="checkbox" name="password" id="showpassword"><br>
							<label for=""> Show Password</label>						
						</div>

						<div class="mt-0">
							<button class="btn btn-primary btn-md" type="submit" name="sub_btn" id="signupbtn">
								<i class="fa fa-user-plus mr-1" aria-hidden="true"></i> Register Now
							</button>
						</div>

					</fieldset>

				</form><hr>

				<div class="col-12 mt-4 ml-0 p-0">
					<h6 style="text-align:right">
						<i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Already have an account? Log in 
							<a href="index.php"> here 
								<i class="fa fa-smile-o" aria-hidden="true"></i>
							</a>
					</h6>
				</div>
				<br><br>
			
		</div>	


		<!-- Show password  -->
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

	</body>
</html>