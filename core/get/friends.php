<?php

// ---------------------------------- Friends getter script. For the response codes, refer to reponse_codes.txt in the config folder ----------------------


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');

// include database connection
include '../../config/sqldb.php';


//get the username of the user that made the request
$username = isset($_GET['uname']) ? $_GET['uname'] : die('ERROR: Record ID not found.');


try {


	$query = "SELECT vusr_friends FROM valiss_userdata WHERE vusr_username = '$username'"; // retrives the friends of that user
	 
	$stmt = $con->prepare($query);

	$stmt->execute();

	$results = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($results['vusr_friends'] !== null) {

		$friends = json_encode($results["vusr_friends"]);

		echo $friends;

	} else {

		echo json_encode("UHNF"); // the user has no friends

	}

}

catch (Exception $exception) {
	die('ERROR: ' . $exception->getMessage());
}






?>