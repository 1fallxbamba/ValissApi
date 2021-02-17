<?php

// This script is for connecting to the database 

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');

// remote database
/*
$host = "160.153.133.88";
$db_name = "ecouture_mode";
$username = "ecouture";
$password = "ecouturemode";*/

// local database

$host = "localhost";
$db_name = "valiss_db";
$db_username = "root";
$db_password = ""; 

try {

	$con = new PDO("mysql:host={$host};dbname={$db_name}", $db_username, $db_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

}

// show error
catch(PDOException $exception) {

	echo "Connection error: " . $exception->getMessage();

}
?>