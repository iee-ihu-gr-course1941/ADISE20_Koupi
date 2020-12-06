


<?php

require "dbconnect.php";


function show_board()
	{
	global $mysqli;
	$sql = "SELECT x, y, piece FROM board";
	$st=$mysqli->prepare($sql);

	$st->execute();
	$res=$st->get_result();


	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
	}


function reset_board()
	{
	global $mysqli;
	$sql='call clean_board()';
	$mysqli->query($sql);
	show_board();
	}

function show_cellStatus($x,$y,$input)
	{
	global $mysqli;
	$sql=" SELECT x, y, piece FROM board WHERE x=$x and y=$y ";
	$st=$mysqli->prepare($sql);
	//print $sql;
	

	$st->execute();
	$res=$st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);

	}



function add_piece($x,$y,$input)
	{
	global $mysqli;
	$sql='call place_piece($x,$y)';
	$mysqli->query($sql);

	show_cellStatus($x,$y,$input);	
	}

/*function show_status()
	{
	global $mysqli;
	$sql = "SELECT * FROM game_status";
	$st=$mysqli->prepare($sql);

	$st->execute();
	$res=$st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
	}*/




//print json_encode($result->fetch_all(MYSQLI_ASSOC)

?>