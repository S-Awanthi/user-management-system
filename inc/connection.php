 <?php  

	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = '';

	$dbname = 'ums';

	$connection = mysqli_connect('localhost', 'root', '', 'ums');
	

	//checking the connection

	if (mysqli_connect_errno()) {
		die('Database connection failed ' . mysqli_connect_error());
	}

?>