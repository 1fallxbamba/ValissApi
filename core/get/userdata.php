<?php

// -------------------------- User's data fetching script. For the response codes, refer to reponse_codes.txt in the config folder ------------------------



header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *'); // to change
header('Access-Control-Allow-Methods: *');


// include database connection
include '../../config/sqldb.php';

// get the username of the user that made the request
$user = isset($_GET['uname']) ? $_GET['uname'] : die('ERROR: No parameter given.');

try {


	$query = "SELECT * from valiss_userdata WHERE vusr_username = '$user'"; 			// fetch all of his data

	$stmt = $con->prepare($query);

	//execute the query 

	$stmt->execute();

	// store retrieved row to a variable

	$results = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($results) {

		$user_data = json_encode($results);

		echo $user_data;

	} else {

		echo json_encode("EFUD"); // error fetching the user's data

	}

}

// show error 

catch(Exception $exception) {

	die('Error: ' . $exception->getMessage());

}


?>