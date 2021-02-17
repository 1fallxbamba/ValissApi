<?php

// -----------------------------Registration handler script,  for the response codes, refer to response_code.txt inside the config folder------------------

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *'); // to change
header('Access-Control-Allow-Methods: *');


// include database connection
include '../../config/sqldb.php';

$_newuserdata = file_get_contents("php://input"); // retriving the posted JSON object which contains the new user's info

$new_user_data = json_decode($_newuserdata);

@$user_firstname = $new_user_data->firstName;
@$user_lastname = $new_user_data->lastName;
@$user_address = $new_user_data->address;
@$user_email = $new_user_data->email;		// B I N D I N G
@$user_phone = $new_user_data->phone;
@$user_username = $new_user_data->username;
@$user_password = $new_user_data->password;
@$user_picture = $new_user_data->profilePic;


// insert the user in the user_data table
	
$query_1 = "INSERT INTO valiss_userdata SET vusr_name=:u_name, vusr_address=:u_addr, vusr_email=:u_mail, vusr_phone=:u_phone, vusr_photo=:u_pic, vusr_username=:u_uname";


try{


	$stmt_1 = $con->prepare($query_1);

	$user_fullname = $user_firstname . ' ' . $user_lastname;

	$encrypted_password = password_hash($user_password, PASSWORD_DEFAULT);

	// bind the parameters

	$stmt_1->bindParam(':u_name', $user_fullname);
	$stmt_1->bindParam(':u_addr', $user_address);
	$stmt_1->bindParam(':u_mail', $user_email);
	$stmt_1->bindParam(':u_phone', $user_phone);  // B I N D I N G
	$stmt_1->bindParam(':u_pic', $user_picture);
	$stmt_1->bindParam(':u_uname', $user_username);


	// If the first query's been executed with no error
	if( $stmt_1->execute() ) {

		$query_2 = "INSERT INTO valiss_users SET vusr_username = '$user_username' , vusr_pwd = '$encrypted_password' ";

		$stmt_2 = $con->prepare($query_2); // then I register the user in the Users table

		if ($stmt_2->execute()) {

			echo json_encode("NUSR");

		} else {

			echo json_encode("URE"); // (the user data has been inserted in the user_data table but not in the Users table)

		}

	} else {

		echo json_encode("GRE"); // (the user has not been registered in no table)

	}

}
// show error
catch(Exception $exception) {

	die('ERROR: ' . $exception->getMessage());
	
}


?>