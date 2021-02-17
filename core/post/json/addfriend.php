<?php

// ------------------------------ Adding a user to friend's list script, For the response codes, refer to reponse_codes.txt in the config folder ------------------


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *'); // to change

// include database connection
include '../../../config/sqldb.php';

$user = isset($_GET['uname']) ? $_GET['uname'] : die('ERROR: Record ID not found.'); // gets the username of the user that made the request

$new_friend = isset($_GET['friend']) ? $_GET['friend'] : die('ERROR: Record ID not found.'); // gets the username of the new friend that he wants to add


try {

		// first, i fetch the Friends row in the database, even if it's empty
	$query = "SELECT vusr_friends FROM valiss_userdata WHERE vusr_username = '$user'"; 
 
	$stmt = $con->prepare($query);

	$stmt->execute();

	$results = $stmt->fetch(PDO::FETCH_ASSOC);

	$json1 = json_encode($results['vusr_friends']);


		// then i check if that new friend's username already exists in the Friends list
	$query2 = "SELECT JSON_SEARCH($json1, 'all', '$new_friend') AS Res "; 

	$stmt2 = $con->prepare($query2);
	$stmt2->execute();
	$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

	$exists = $row2["Res"];


		// if the new friend is not already in the Friends list ..
	if ($exists === null) { 

											// ... we add him to the list
		$query3 = "UPDATE valiss_userdata SET vusr_friends = JSON_ARRAY_APPEND(vusr_friends, '$','$new_friend') WHERE vusr_username = '$user' ";

		$stmt3 = $con->prepare($query3);

		$stmt3->execute();

		echo json_encode("NFSA");

	} else { // we don't add him to the list and the frontend is notified
		echo json_encode("FAE");
	}
	
} catch (Exception $exception) {
	die('Error: ' . $exception->getMessage());
}


?>