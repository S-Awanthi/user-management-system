<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php  

//Checking if a user is logged in  
if (!isset($_SESSION['user_id']) && !isset($_SESSION['first_name'])) {	
	header('Location: index.php ');		
}

	$errors = array();

	$first_name = '';
	$last_name = '';
	$email = '';
	$password = '';
	$user_id = '';

	if (isset($_GET['user_id'])) {
		
		//Getting the user information
		$user_id = mysqli_real_escape_string($connection, $_GET['user_id']);
		
		$query = "SELECT * FROM user WHERE id = {$user_id} LIMIT 1";

		$result_set = mysqli_query($connection, $query);

		if ($result_set) {
			
			if (mysqli_num_rows($result_set) == 1) {
				//Valid user found
				$view_result = mysqli_fetch_assoc($result_set);

				//Display records from the database
				$first_name = $view_result['first_name'];
				$last_name = $view_result['last_name'];
				$email = $view_result['email'];
			}
			else{
				header('Location: users.php?err=user_not_found');
			}
		}
		else{
			header('Location: users.php?err=query_failed');
		}
	}

	//Check user has submit the form
	if (isset($_POST['update_btn'])) {
		 
		//If the form has submit, then store the values 
		$first_name = $_POST['first_name'] ;
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		$user_id = $_POST['user_id'];
		
		$req_fields = array('user_id', 'first_name', 'last_name', 'email');
		
		if ( !is_email($_POST['email'])) {
			$errors[] = 'You have entered an invalid email address <br>';
		}

		$errors = array_merge($errors, check_req_fields($req_fields));   

		$max_len_fields = array('first_name' => 50, 'last_name' => 100, 'email' => 100);
		$errors = array_merge($errors, check_max_length($max_len_fields));
	


		//Checking if email is already exists
		$email = mysqli_real_escape_string($connection, $_POST['email']);
		$query = "SELECT * FROM user WHERE email = '{$email}' AND id != '{$user_id}' LIMIT 1";
		$result_set = mysqli_query($connection, $query);

		if ($result_set) {
			if (mysqli_num_rows($result_set) == 1) {
				$errors[] = 'Email address already exists';
			}
		}


		if (empty($errors)) {
			//no errors found......then update record
			$first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
			$last_name = mysqli_real_escape_string($connection, $_POST['last_name']);			
			//email address is already sanitized

			$hashed_password = sha1($password);

			$query = "UPDATE user SET ";
			$query .= "first_name = '{$first_name}' , ";	
			$query .= "last_name = '{$last_name}' , ";	
			$query .= "email = '{$email}' ";
			$query .= "WHERE id = {$user_id} LIMIT 1";			

			$result = mysqli_query($connection, $query);

			if ($result) {
				//query success....redirect to users.php
				// header("Location: users.php?user_modified=true");
			}
			else{
				$errors[] = 'Failed to modify the record'; 
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
		<title>Update User- UMS</title>
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
				<div class="col-10"><h3>Update User Details </h3> 
				</div>
				<!-- <div class="col-2">
					<a style="float: right" href="users.php" role="button">
						<i class="fa fa-hand-o-left mr-1"></i> User List 
					</a>
				</div> -->
			</div>	
			<hr>
			

			<?php  
				if (!empty($errors)) {
					display_errors($errors);
				}
			?>


			<div class="col-md-12">	
				<form action="modify-user.php" method="POST">

					<fieldset>	
						<?php 
							if (empty($errors) && isset($_POST['update_btn'])) {
								echo '<p class="info">User has been updated successfully</p> ';
							}
						?>						
						<input type="hidden" name="user_id" value="<?php echo $user_id ?>"> 
												
						<div class="row">
						<div class="col-12">
								<a id="back-list" style="float: right" href="users.php" role="button">
									<i class="fa fa-hand-o-left mr-1"></i> User List 
								</a>
							</div>
						</div>

						<div class="row">						
							<div class="col-6 mt-0">
								<label for="">First Name </label>
								<input class="form-control" type="text" name="first_name" 
									<?php echo 'value="' .$first_name. '"'; ?>>
							</div>

							<div class="col-6 mt-0">
								<label for="">Last Name </label>
								<input class="form-control" type="text" name="last_name" 
									<?php echo 'value="' .$last_name. '"'; ?>>
							</div>
						</div>

						<div class="mt-3">
							<label for="">Email </label>
							<input class="form-control" type="email" name="email" 
								<?php echo 'value="' .$email. '"'; ?>>
						</div>	
						
						<div class="mt-4">
							<label for="">Password :</label> 
							<a id="changepword" href="change-password.php?user_id=<?php echo $user_id; ?>"> | Change Your Password Here |</a>
						</div>

						<div class="mt-3">
							<button class="btn btn-info btn-md" type="submit" name="update_btn" id="update-btn" 
								onclick="return confirm('Are you sure want to update user?')";>
								<i class="fa fa-pencil mr-1" aria-hidden="true"></i> Update User
							</button>
						</div>					

					</fieldset>
				</form>	
			</div>
			

			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		
		</div>
	</body>
</html>