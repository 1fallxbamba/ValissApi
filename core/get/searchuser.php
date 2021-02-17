<?php


// -------------------------- User searching script. For the response codes, refer to reponse_codes.txt in the config folder --------------------------------



header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *'); //  to change

// include database connection
include '../../config/sqldb.php';


// get the username of the user
$username = isset($_GET['uname']) ? $_GET['uname'] : die('ERROR: Record ID not found.');

try {

	$query = "SELECT vusr_name, vusr_username, vusr_photo FROM valiss_userdata WHERE vusr_username LIKE '%$username%'"; // fetch data of the given user
	 
	$stmt = $con->prepare($query);

	$stmt->execute();

	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($results !== []) { // if we found some users
		
		$found_users = json_encode($results);

		echo $found_users;

	} else {
		echo json_encode("UNF");
	}
	
} catch (Exception $exception) {
	die('Error: ' . $exception->getMessage());
}


?>