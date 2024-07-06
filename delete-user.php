<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/functions.php'); ?>

<?php  

//Checking if a user is logged in
if (!isset($_SESSION['user_id'])) {	
	header('Location: index.php ');
}

	if (isset($_GET['user_id'])) {
		
		$user_id = mysqli_real_escape_string($connection, $_GET['user_id']);
		
		//should not delete the current logged user,, check with current session user id
		if ( $user_id == $_SESSION['user_id'] ) {
			header('Location: users.php?err=cannot_delete_current_user');
		}
		
		else{
			//Delete user only from system not from the db
			//$query = "UPDATE user SET is_deleted = 1 WHERE id = {$user_id} LIMIT 1";
			
			//delete user from db too
			$query = "DELETE FROM user WHERE id = {$user_id} LIMIT 1";

			$result = mysqli_query($connection, $query);

			if ($result) {
				//user daleted
				header('Location: users.php?msg=user_deleted');
			}
			else{
				header('Location: users.php?err=delete_failed');
			}
		}
	}
	else{
		header('Location: users.php');
	}
 
