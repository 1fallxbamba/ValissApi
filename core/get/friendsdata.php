<?php


// ----------------------Script that fetches the friend's data, for the response codes, refer to reponse_codes.txt in the config folder------------------



header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *'); // to change
header('Access-Control-Allow-Methods: *');

// include database connection
include '../../config/sqldb.php';

// get the username of the friend
$user=isset($_GET['uname']) ? $_GET['uname'] : die('ERROR: Record ID not found.');

try {

	$query = "SELECT vusr_name, vusr_username, vusr_photo FROM valiss_userdata WHERE vusr_username = '$user'"; // fetch data of the given friend
 
	$stmt = $con->prepare($query);

	$stmt->execute();

	$results = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($results) {

		$friends_data = json_encode($results);

		echo $friends_data;

	} else {

		echo json_encode("EFFD"); // Error fetching the data

}
	
} catch (Exception $exception) {

	die('ERROR: ' . $exception->getMessage());
	
}

?>