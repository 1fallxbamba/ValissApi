<?php

/**
 		ValissTransaction Class :  this class contains all Valiss' transactions methods (Send, Request[Approve, Deny])
 		@author : Cheikh Ahmadou Bamba M. Fall
 */
 		
class ValissTransaction
{

	private static $_sqlConnexion;

	public function __construct()
	{

		self::_connect();

	}

	private static function _connect()
	{

		$host = "localhost";
		$db_name = "valiss_db";
		$username = "root";
		$password = "";

		try {

			self::$_sqlConnexion = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
		}

		// show error
		catch(PDOException $exception) {
			echo "Connection error: " . $exception->getMessage();
		}

	}

	private function authenticate($user)
	{

		$uname = $user->sender;   // binding
		$pwd = $user->password;

		$query = "SELECT vusr_pwd FROM valiss_users WHERE vusr_username = '$uname'"; 

		$stmt = self::$_sqlConnexion->prepare($query);

		$stmt->execute();

		$query_result = $stmt->fetch(PDO::FETCH_ASSOC); // retrieves id and password of the requested username


		if ($query_result !== false) {  // if the username exists in the database

			$fetched_pwd = $query_result['vusr_pwd'];  
			
			if ( password_verify($pwd, $fetched_pwd) ) { // then I check if the password is correct

				return true;  // Username and password match !

			} else { 	// user exists but the password is not correct

				return false;

			}

		} else { // if the result is emppty (=== false), then the requested username does not exsit in the database

			return false;

		}

	}

	private function createTransaction($type, $amount, $from, $to, $motive = '', $status = 'PENDING') 
	{


		$code = uniqid($from.'_');

		$query = "INSERT INTO valiss_transactions SET vtrans_code='$code', vtrans_type='$type', vtrans_from='$from', vtrans_to='$to', vtrans_amount='$amount', vtrans_motive='$motive', vtrans_status='$status'"; 

		$stmt = self::$_sqlConnexion->prepare($query);

		$stmt->execute();


	}

	private function updateTransaction($code) ///remove
	{
		$query = "UPDATE valiss_transactions SET vtrans_type = 'REQUEST-APPROVED', vtrans_status = 'SENT' WHERE vtrans_code = '$code'";

		$stmt = self::$_sqlConnexion->prepare($query);

		$stmt->execute();
	}

	public function sendValiss($sending_data)
	{

		$sender = $sending_data->sender;
		$receiver = $sending_data->receiver;
		$amount = (int)$sending_data->amount;

		if ($this->authenticate($sending_data)) {

			$query = "SELECT vusr_balance from valiss_userdata WHERE vusr_username = '$sender'"; 

			$fetch_sender_balance = self::$_sqlConnexion->prepare($query);

			$fetch_sender_balance->execute();

			$query_result = $fetch_sender_balance->fetch(PDO::FETCH_ASSOC);

			$sender_balance = (int)$query_result['vusr_balance'];

			if ($sender_balance === 0) {

				$response = json_encode(array('STATUS' => 'No-Valiss-Error' , 'CODE' => 'UHNV', 'DESCRIPTION' => 'User Has No Valiss : Can\'t send Valiss, the sender\'s balance is 0'));

				echo $response;

			} else if ($amount > $sender_balance) { // will be TOTAL_AMOUNT (amount + fees)

				$response = json_encode(array('STATUS' => 'Insufficient-Valiss-Error' , 'CODE' => 'UHIV', 'DESCRIPTION' => 'User Has Insufficient Valiss : Can\'t send Valiss, the sending amount is greater than sender\'s balance'));

				echo $response;

			} else {

				$new_sender_balance = $sender_balance - $amount;

				$query2 = "UPDATE valiss_userdata SET vusr_balance = $new_sender_balance WHERE vusr_username = '$sender' ";

				$update_sender_balance = self::$_sqlConnexion->prepare($query2);

				try {

					if ($update_sender_balance->execute()) {

						$query3 = "SELECT vusr_balance from valiss_userdata WHERE vusr_username = '$receiver'"; 

						$fetch_receiver_balance = self::$_sqlConnexion->prepare($query3);

						$fetch_receiver_balance->execute();

						$query_result = $fetch_receiver_balance->fetch(PDO::FETCH_ASSOC);

						$receiver_balance = (int)$query_result['vusr_balance'];

						$new_receiver_balance = $receiver_balance + $amount;

						$query4 = "UPDATE valiss_userdata SET vusr_balance = $new_receiver_balance WHERE vusr_username = '$receiver' ";

						$update_receiver_balance = self::$_sqlConnexion->prepare($query4);

						if ($update_receiver_balance->execute()) {

							$response = json_encode(array('STATUS' => 'Send-Success' , 'CODE' => 'VSS', 'DESCRIPTION' => 'Valiss Sent Successfully'));

							echo $response;

							$this->createTransaction("SEND", $amount, $sender, $receiver, '', 'SENT');

						} else {

							$response = json_encode(array('STATUS' => 'Unexpected-Valiss-Error' , 'CODE' => 'UNEX', 'DESCRIPTION' => 'Due to an unexpected error the receiver\'s account could not receive the Valiss'));

							echo $response;

						}

					} else {

						$response = json_encode(array('STATUS' => 'Unexpected-Valiss-Error' , 'CODE' => 'UNEX', 'DESCRIPTION' => 'Due to an unexpected error, the sender could not send the requested Valiss '));

						echo $response;

					}
					
				} catch (Exception $e) {

					$response = json_encode(array('STATUS' => 'Unexpected-Valiss-Error' , 'CODE' => 'UNEX', 'DESCRIPTION' => 'Due to an unexpected error the sending operation can not be processed'));

					echo $response;
					
				}


			}

		} else {

			$response = json_encode(array('STATUS' => 'Valiss-Authentication-Error' , 'CODE' => 'UAF', 'DESCRIPTION' => 'User Authentication Failed : the password is incorrect')); // gotta correct it for external use

			echo $response;

		}

	}

	public function requestValiss($request_data)
	{

		$requester_uname = $request_data->requester;
		$receiver_uname = $request_data->receiver;
		$request_amount = $request_data->amount;


		if (!empty($request_data->motive)) {

			$request_motive = $request_data->motive ;

		} else {

			$request_motive = '';

		}

		try {

			$this->createTransaction('REQUEST', $request_amount, $requester_uname, $receiver_uname, $request_motive);

			$response = json_encode(array('STATUS' => 'Request-Success' , 'CODE' => 'VRSS', 'DESCRIPTION' => 'Valiss Request Sent Successfully'));

			echo $response;
			
		} catch (Exception $e) {

			$response = json_encode(array('STATUS' => 'Unexpected-Valiss-Error' , 'CODE' => 'UNEX', 'DESCRIPTION' => 'Due to an unexpected error, the Valiss request could not be sent'));

			echo $response;
			
		}


	}

	public function approveRequest($request_data)
	{

		// do the sending with the new boolean paramaeter, then update the request in the transaction table

	}

	public function denyRequest($code)
	{

		$trans_code = $code->code;

		$status = 'DENIED';

		$query = "UPDATE valiss_transactions SET vtrans_status = '$status' WHERE vtrans_code = '$trans_code' ";

		$deny_request = self::$_sqlConnexion->prepare($query);

		try {

			$deny_request->execute();

			$response = json_encode(array('STATUS' => 'Deny-Request-Success' , 'CODE' => 'VRDS', 'DESCRIPTION' => 'Valiss Request Denied Successfully'));

			echo $response;

			
		} catch (Exception $e) {

			$response = json_encode(array('STATUS' => 'Unexpected-Valiss-Error' , 'CODE' => 'UNEX', 'DESCRIPTION' => 'Due to an unexpected error, the Valiss request could not be denied'));

			echo $response;
			
		}

	}




}

?>