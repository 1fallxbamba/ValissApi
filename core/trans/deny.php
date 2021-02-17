<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');


include 'vTransactions.php';

$_data = file_get_contents("php://input");

$transaction_code = json_decode($_data);


try {

	$transaction = new ValissTransaction();

	$transaction->denyRequest($transaction_code);

} catch (Exception $e) {

	$response = json_encode(array('STATUS' => 'Unexpected-Valiss-Error' , 'CODE' => 'UNEX', 'DESCRIPTION' => 'Due to an unexpected error the operation can not be processed'));

	echo $response;
	
}




?>