<?php

// ----------------------Username validatior script, for the response codes, refer to reponse_codes.txt in the config folder-------------------------------


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');  // to change
header('Access-Control-Allow-Methods: *');


// include database connection
include '../../config/sqldb.php';


try {

	$username = isset($_GET['uname']) ? $_GET['uname'] : die('ERROR: No parameter given.');


	$query = "SELECT vusr_id from valiss_userdata WHERE vusr_username = '$username'"; // check if the given username exists already in the database

	$stmt = $con->prepare($query);

	//execute the query 

	$stmt->execute();

	// store retrieved row to a variable

	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($result) {

		echo json_encode("UNA"); // unsername is not available

	} else {

		echo json_encode("UIA"); // username is available

	}

}

// show error 

catch(exception $exception) {

	die('Error: ' . $exception->getMessage());

}


?>