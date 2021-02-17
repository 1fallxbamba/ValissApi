<?php

// ----------------------------------------Login handler script,  for the response codes, refer to response_code.txt in the config folder------------------


// Just for test purposes !!! , HEADERs are to be changed when i deploy
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');


// include database connection file
include '../../config/sqldb.php';

$_logindata = file_get_contents("php://input");  // Retrieves the content of the posted JSON from the frontend

try {

	$logindata = json_decode($_logindata);

	@$uname = $logindata->username;   // binding
	@$passwd = $logindata->password;  // binding


	$query = "SELECT vusr_id, vusr_pwd FROM valiss_users WHERE vusr_username = '$uname'"; 

	$stmt = $con->prepare($query);

	$stmt->execute();

	$result = $stmt->fetch(PDO::FETCH_ASSOC); // retrieves id and password of the requested username


	if ($result !== false) {  // if the username exists in the database

		$fetched_passwd = $result['vusr_pwd'];  
		
		if ( password_verify($passwd, $fetched_passwd) ) { // then I check if the password is correct

			echo json_encode("USC");  // Username and password match !

		} else { 	// user exists but the password is not correct

			echo json_encode("WPWD");

		}

	} else { // if the result is emppty (=== false), then the requested username does not exsit in the database

		echo json_encode("UDNE");

	}


}
// showes error
catch (Exception $exception) {
	die('ERROR: ' . $exception->getMessage());
}


?>