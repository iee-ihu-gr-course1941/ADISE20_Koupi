<?php
$host='localhost';
$db = 'score4';
require_once "db_upass.php";

$user=$DB_USER;
$pass=$DB_PASS;




        $mysqli = new mysqli($host, $user, $pass, $db);


if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . 
    $mysqli->connect_errno . ") " . $mysqli->connect_error;
}?>