<?php
/********************************************************
 *  ©JEROME AVECILLA -> ICT 12 DIGNIFIED S.Y 2022-2023
 * ******************************************************/
include_once('../admin/config/db.connection.php');
ini_set('display_errors',  1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	session_start();
	session_id($_SESSION['session_id']);
	

	// update logout_time in the database
	$username = $_SESSION['username'];
	//$logout_time = date('Y-m-d H:i:s');
	//connect to database
	// update logout_time for the current user
	$query = "UPDATE users SET logout_time = NOW(), is_logged_in = 0 WHERE uname=?";
	$stmt = mysqli_prepare($connection, $query);
	mysqli_stmt_bind_param($stmt, "s", $username);
	if(mysqli_stmt_execute($stmt)) {
		// session destroyed and logout time updated successfully
		//unset session variables
		session_unset();
		session_destroy();
		header("Location: login");
		exit();
	} else {
		// error updating logout time in database
		echo "Error updating logout time: " . mysqli_error($connection);
	}

	mysqli_stmt_close($stmt);
	mysqli_close($connection);

?>