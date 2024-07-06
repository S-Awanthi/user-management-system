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
	$user_id = '';


	if (isset($_GET['user_id'])) {
		
		//Getting user information
		$user_id = mysqli_real_escape_string($connection, $_GET['user_id']);
		
		$query = "SELECT * FROM user WHERE id = {$user_id} LIMIT 1";

		$result_set = mysqli_query($connection, $query);

		if ($result_set) {
			
			if (mysqli_num_rows($result_set) == 1) {				
				$view_result = mysqli_fetch_assoc($result_set);		//Valid user found

				//Display records from the database
				$first_name = $view_result['first_name'];
				$last_name = $view_result['last_name'];
				$email = $view_result['email'];
			}
			else{				
				header('Location: users.php?err=user_not_found');	//User not found
			}
		}
		else{
			header('Location: users.php?err=query_failed');
		}
	}


	
	if (isset($_POST['update_pword_btn'])) {
		 
		$user_id = $_POST['user_id'];
		$password = $_POST['password'];
		
		
		$req_fields = array('user_id', 'password');		
	
		$errors = array_merge($errors, check_req_fields($req_fields));   	

		$max_len_fields = array('password' => 40);		//Validations
		$errors = array_merge($errors, check_max_length($max_len_fields));



		if (empty($errors)) {

			//no errors found......then add new record
			$password = mysqli_real_escape_string($connection, $_POST['password']);
			$hashed_password = sha1($password);
			
			$query = "UPDATE user SET ";
			$query .= "password = '{$hashed_password}' ";
			$query .= "WHERE id = {$user_id} LIMIT 1";			

			$result = mysqli_query($connection, $query);

			if ($result) {
				//query success....redirect to users.php
				// header("Location: users.php?user_modified=true");				
			}
			else{
				$errors[] = 'Failed to update the password'; 
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
		<title>Change Password - UMS</title>
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
						<a id="logout-btn" href="logout.php"> Logout</a>
					</button>
			</div>

			<div class="jumbotron" style="padding:1px, margin:10px">
				<h2>Welcome to User Management System</h2>		
			</div>	

			<div class="row">
				<div class="col-10"><h3>Change Password </h3> 
				</div>
				
			</div>	
			<hr>			
			
			<?php  
				if (!empty($errors)) {
					display_errors($errors);
				}
			?>

			<div class="col-md-12">	
				<form action="change-password.php" method="POST">

					<fieldset>						
						<?php 
							if (empty($errors) && isset($_POST['update_pword_btn'])) {
								echo '<p class="info">Password has been changed successfully</p> ';
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
									<?php echo 'value="' .$first_name. '"'; ?> disabled>
							</div>

							<div class="col-6 mt-0">
								<label for="">Last Name </label>
								<input class="form-control" type="text" name="last_name" 
									<?php echo 'value="' .$last_name. '"'; ?> disabled>
							</div>
						</div>

						<div class="mt-3">
							<label for="">Email </label>
							<input class="form-control" type="email" name="email" 
								<?php echo 'value="' .$email. '"'; ?> disabled>
						</div>	

						<div class="mt-3">
							<label for="">New Password </label>
							<input class="form-control" type="password" name="password" id="new-password" 
								placeholder="Enter New Password">
						</div>						

						<div class="mt-3">
							<input type="checkbox" name="password" id="showpassword"><br>
							<label for=""> Show Password</label>						
						</div>

						<div class="mt-0">
							<button class="btn btn-info btn-md" type="submit" name="update_pword_btn" 
								id="update-pword-btn" onclick="return confirm('Are you sure want to change the password?')";>
								<i class="fa fa-pencil mr-1" aria-hidden="true"></i> Change Password
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
			
		</div>
	</body>
</html>