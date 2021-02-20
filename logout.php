 
 <?php  

 	session_start();

 	//remove all the values in the assosiative array, 'SESSION'....set the session as an empty array
 	$_SESSION = array();


 	//Check whether any cookie is saved under the session name in the browser
 	if (isset($_COOKIE[session_name()])) {
 		setcookie(session_name(), '', time()-86400, '/');
 	}

 	//Close the session
 	session_destroy();

 	//Redirect to home page after logout
 	header('Location: index.php?logout=yes');  	

 ?>