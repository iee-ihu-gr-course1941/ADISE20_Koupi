<?php
$user='root';
$pass='';
$host='localhost';
$db = 'score4';

const ROWS = 6;
CONST COLS=7;


        $mysqli = new mysqli($host, $user, $pass, $db);


$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . 
    $mysqli->connect_errno . ") " . $mysqli->connect_error;
}?>


