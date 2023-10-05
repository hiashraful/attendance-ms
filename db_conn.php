<?php
	$host = "localhost";
	$user = "root";
	$pass = "12345";
	$db = "attendance_ms";
	
	$conn = new mysqli($host, $user, $pass, $db);
	if($conn->connect_error){
		echo "Connection error:" . $conn->connect_error;
	}
?>