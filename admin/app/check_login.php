<?php
/********************************************************
 *  ©JEROME AVECILLA -> ICT 12 DIGNIFIED S.Y 2022-2023
 * ******************************************************/
include_once('../config/db.connection.php');
session_start();

	$query = "SELECT session_id FROM users WHERE id = ?";
	$stmt = mysqli_prepare($connection, $query);
	mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);

	while($row = mysqli_fetch_assoc($result)) {
		//check if session id is equal to the db session
		//if it is not, thats means the account is logged in
		//in two different location or device
	    if($_SESSION['session_id'] != $row['session_id']) {
	        $data['output'] = 'logout';
	    } else {
	        $data['output'] = 'login';
	    }
	}

	echo json_encode($data);
