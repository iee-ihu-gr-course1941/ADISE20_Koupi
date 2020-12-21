<?php

require "dbconnect.php";

function show_board($input)
	{
	global $mysqli;
	$b=current_color($input['token']);
	header('Content-type: application/json');
	print json_encode(read_board(), JSON_PRETTY_PRINT);
	exit;
	}

function reset_board()
	{
	global $mysqli;
	$sql='call clean_board()';
	$mysqli->query($sql);
	}


function show_piece($x,$y,$input)
	{
	global $mysqli;
	$sql=" SELECT x, y, piece_color FROM board WHERE x=$x and y=$y ";
	$st=$mysqli->prepare($sql);	

	$st->execute();
	$res=$st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
	}



function add_piece($x,$y,$token)
	{
	global $mysqli;
	if($token==null || $token=='')
		{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"token is not set."]);
		exit;
		}
	$color = current_color($token);
	if($color==null )
		{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"You are not a player of this game."]);
		exit;
		}
	$status = read_status();
	if($status['status']!='started') {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Game is not in action."]);
		exit;
	}


	if($status['p_turn']!=$color) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"It is not your turn."]);
		exit;
	}

	check_move($y);
	exit;
	}

function read_board()
	{
	global $mysqli;
	$sql = 'select * from board';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	return($res->fetch_all(MYSQLI_ASSOC));
	}

function do_move($x,$y)
	{
	global $mysqli;
	$sql = 'call `place_piece`(?,?);';
	$st = $mysqli->prepare($sql);
	$st->bind_param('ii',$x,$y);
	$st->execute();

	header('Content-type: application/json');
	print json_encode(read_board(), JSON_PRETTY_PRINT);
	}

function check_move($inpy)
	{
	$pin=read_board();
	$pinSize=count($pin);
	$color = array_column($pin, 'piece_color');
	$x = array_column($pin, 'x');
	$y=array_column($pin, 'y');
	if($inpy>max($y) || $inpy<0 )
		{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"lathos syntetagmenes"]);
		exit;
		}
	if($color[$inpy-1]!=null)
		{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"h sthlh ayth exei hdh gemisei"]);
		exit;
		}

	for($i=$inpy-1+36;$i>=0;$i=$i-6)
		{
		if(is_null($color[$i]))	
			{
			do_move($x[$i],$y[$i]);
			exit;
			}
		continue;
		}
	}

?>