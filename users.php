<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php  

//Checking if a user is logged in 
if (!isset($_SESSION['user_id']) && !isset($_SESSION['first_name'])) {
	header('Location: index.php ');
}


$user_list = '';

//Getting the list of users from the database
$query = "SELECT * FROM user WHERE is_deleted = 0 ORDER BY last_login DESC";

//Store the resultset in a variable
$users = mysqli_query($connection, $query);

verify_query($users);

	while ($user = mysqli_fetch_assoc($users)) {	//$user = one user,

	$user_list .= "<tr>";
		$user_list .= "<td> {$user['id']} </td>";
		$user_list .= "<td> {$user['first_name']} {$user['last_name']} </td>";
		$user_list .= "<td> {$user['email']} </td>";
		$user_list .= "<td> {$user['last_login']} </td>";

		$user_list .= "<td colspan=2> <a id=editbtn href=\"modify-user.php?user_id={$user['id']}\" > 
						<i class=\"fa fa-pencil mr-1\" aria-hidden=\"true\"></i> 
							Edit </a></td>";

		$user_list .= "<td> <a id=deletebtn href=\"delete-user.php?user_id={$user['id']}\" 
						onclick=\"return confirm('Are you sure want to delete this user?');\" > 
							<i class=\"fa fa-trash-o mr-1\" aria-hidden=\"true\"></i> 
								Delete </a></td>";

		$user_list .= "</tr>";

	}
?>


<!-- -------------------------------------- Form starts here ---------------------------------------------- -->


<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>User List - UMS</title>
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
				<div class="col-10"><h3 >Users List</h3></div>
				<div class="col-2">
					<a class="btn btn-success" href="add-user.php" role="button" id="add-userbtn">
						<i class="fa fa-plus-circle mr-1"></i> Add User 
					</a>
				</div>
			</div>	
			<hr>

			<?php 
				if (empty($errors) && isset($_POST['update_pword_btn'])) {
					echo '<p class="info">Password has been changed !</p> ';
				}
			?>			

			<div class="table-responsive">
				<table class="table table-hover table-striped">
					<tr>
						<th>ID</th>
						<th>FULL NAME</th>
						<th>EMAIL</th>
						<th>LAST LOGIN</th>
						<th colspan="2">ACTION</th>
						<th></th>
					</tr>

					<?php echo $user_list; ?>

				</table>
			</div>

			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		
		</div>
	</body>
	
</html>