<?php

require_once "dbconnect.php";

/*
$GLOBALS['$pin']=read_boa();
$GLOBALS['$color'] = array_column($pin, 'piece_color');
$GLOBALS['$x'] = array_column($pin, 'x');
$GLOBALS['$y']=array_column($pin, 'y');*/


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



function add_piece($x,$token)
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

	check_move($x,$color);
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

function do_move($row,$col)
	{
	global $mysqli;
	$sql = 'call `place_piece`(?,?);';
	$st = $mysqli->prepare($sql);
	$st->bind_param('ii',$row,$col);
	$st->execute();

	header('Content-type: application/json');
	print json_encode(read_board(), JSON_PRETTY_PRINT);
	}

function check_move($inpc,$ccolor)
	{
	$pin=read_board();
	$pinSize=count($pin);
	$color = array_column($pin, 'piece_color');
	$row = array_column($pin, 'row');
	$col=array_column($pin, 'col');
	if($inpc>max($row) || $inpc<0 )
		{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"lathos syntetagmenes"]);
		exit;
		}
	if($color[$inpc-1]!=null)
		{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"h sthlh ayth exei hdh gemisei"]);
		exit;
		}

	for($i=$inpc-1+((ROWS-1)*COLS);$i>=0;$i=$i-COLS)
		{
		if(is_null($color[$i]))	
			{
			do_move($row[$i],$col[$i]);
			/*//print_r ($pin[$i]);
			print ($row[$i]." ".$col[$i]." ".$i);
			print_r ($pin);*/

		if(check_victory($row[$i],$col[$i],$ccolor))
					{
						//update ti vasi me ended to status
				//		print("nikises");
					//exit("niiikises");
					}
			exit;
			}
		continue;
		}
	}


function check_victory($row,$col,$color)
	{
	$pin=read_board();
//	$row = array_column($pin, 'row');
//	$col=array_column($pin, 'col');
//	$color = array_column($pin, 'piece_color');
	$board=[];


	for($i=1;$i<=ROWS;$i++)
		{
		for($j=1;$j<=COLS;$j++)
			{
			$board[$i][$j]=$pin[ ($i-1)*COLS + $j-1]['piece_color'];
			}
		}

		//print_r($board);



	//orizontia
	$count=0;
	for($c=max($col-3,1) ; $c<=min($col+3,COLS);$c++) 
		{
		if($board[$row][$c]==$color)
			{
			$count++;
			if($count==4)
				return true;
			}
		else
			$count=0;
		}

	//$max=max($x[$i]-3,1);
	//$min=min(x[$i]+3,6);


	//katheti
	$count=0;
	//print (max($row-3,1) . " " .min($row+3,ROWS) );

	for($r=max($row-3,1);$r<=min($row+3,ROWS);$r++)
		{
		if($board[$r][$col]==$color)
			{
			$count++;
			if($count==4)
				return true;
			}
		else
			$count=0;
	//	print("$count $color|");
		}
	

	//diagonios panw aristera pros katw deksia
	$count=0;
	for($d1=-min($row-1,$col-1,3 );$d1<=min(ROWS-$row,COLS-$col,3);$d1++)
		{
		if($board[$row+$d1][$col+$d1]==$color)
			{
			$count++;
			if($count==4)
				return true;
			}
		else
			$count=0;

		}



	//diagonios panw deksia pros katw aristera
	$count=0;
	for($d2=-min($row-1,COLS-$col,3 );$d2<=min(ROWS-$row,$col-1,3);$d2++)
		{
		if($board[$row+$d2][$col-$d2]==$color)
			{
			$count++;
			if($count==4)
				return true;
			}
		else
			$count=0;

		}
		return false;

	}

?>