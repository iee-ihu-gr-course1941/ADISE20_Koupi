



<?php
 
require_once "lib/dbconnect.php";
require_once "lib/board.php";
require_once "lib/game.php"; //
require_once "lib/users.php"; //


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

//print ($input");

switch ($r=array_shift($request))
    {
    case 'board' : 
        switch ($b=array_shift($request)) 
            {
            case '':
            case null: handle_board($method);
                break;
            case 'piece': handle_piece($method, $request[0],$request[1],$input);
                break;
            default: header("HTTP/1.1 404 Not Found");
                break;
            }
        break;

    case 'players' :  handle_player($method,$request,$input);
        break;

    case 'status' : 
        if(sizeof($request)==0){show_status();}
        else {header("HTTP/1.1 404 Not Found");}
        break;

    default:  header("HTTP/1.1 404 Not Found");
        exit;
    }


function handle_board($method)
    {
    if($method=='GET')
        {
        show_board();
        }
    else if ($method=='POST')
        {
		reset_board();
        }
    }

function handle_piece($method, $x,$y,$input) {
    if($method=='GET')
        {
        show_cellStatus($x,$y,$input);
        }
    else if($methos=='PUT')
        {
        add_piece($x,$y,$input);
        }
}



function handle_player($method,$request,$input)
    {
    switch ($b=array_shift($request)) 
        {
        case '':
        case null:
            if($method=='GET') {show_users($method);}
            else {header("HTTP/1.1 400 Bad Request");
                  print json_encode(['errormesg'=>"Method $method not allowed here"]);}
            break;
        case '0':
        case '1': handle_user($method,$b,$input);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            print json_encode(['errormesg'=>"Player $b not found"]);
            break;
        }
    }
 
/*function handle_player($method,$request,$input)
    {
        print_r ($request[0]);

    if($request[0]==null)
        {
        if($method=='GET')
            {
            show_players();
            }
        }
    else if($request[0]!=null)
        {
        if($method=='GET')
            {
            show_player($request[0]);
            }
        else if($method=='PUT')
            {
            setPlayerName($request[0],$input);
            }
        }
    else
        header("HTTP/1.1 404 Not Found");
  
    }*/

function handle_status($method)
    {
    if($method=='GET')
        show_status();
    }



?>