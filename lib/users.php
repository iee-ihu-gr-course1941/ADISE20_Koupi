
<?php

function show_user($b)
	{
	global $mysqli;
	$sql = 'select username,piece from players where piece=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$b);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
	}
	


function set_user($b,$input)
	{
		//print $b;
		//print_r( $input);
		//exit;
	if(!isset($input['username']))
		{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"No username given"]);
		exit;
		}
print_r ($input);

	$username=$input['username'];
	global $mysqli;
	$sql='select count(*) as c from players where piece=? and username is not null';
	//$sql = "UPDATE FROM players SET username=$input WHERE username=$b";
	$st=$mysqli->prepare($sql);
	$st->bind_param('s',$b);
	$st->execute();
	$res=$st->get_result();

	$r=$res->fetch_all(MYSQLI_ASSOC);
	//print_r ($r);
	//exit;
	if($r[0]['c']>0)
		{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Player $b is already set. Please select another piece."]);
		exit;
		}

		//print $username;
		//print $b;
	//$sql='update players set username=?,token=md5(CONCAT(?,NOW())) where piece=? ';
	$sql='insert into players(username,token,piece) values(?,md5(CONCAT(?,NOW())),?)';

	$st2=$mysqli->prepare($sql);
	$st2->bind_param('sss',$username,$username,$b);
	$st2->execute();

	update_game_status();
	$sql='select * from players where piece=?';
	$st=$mysqli->prepare($sql);
	$st->bind_param('s',$b);
	$st->execute();
	$res=$st->get_result();

	header('Content-type: application/json');
		print_r ($res->fetch_all(MYSQLI_ASSOC));

	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
	//print_r ($r);
	}

function show_users()
	{
	global $mysqli;
	$sql = 'select username,piece_type from players';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
	}

function handle_user($method, $b,$input)
	{
	if($method=='GET') 
		{
		show_user($b);
		} 
	else if($method=='PUT')
		{
    	set_user($b,$input);
		}
	}



	?>