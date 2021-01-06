<?php

ini_set('display_errors','on' );

require_once "lib/dbconnect.php";
require_once "lib/board.php";
require_once "lib/game.php"; 
require_once "lib/users.php"; 

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

if(isset($_SERVER['HTTP_X_TOKEN'])) {$input['token']=$_SERVER['HTTP_X_TOKEN'];}

switch ($r=array_shift($request))
    {
    case 'board' : 
        switch ($b=array_shift($request)) 
            {
            case '':
            case null: handle_board($method,$input);
                break;
            case 'piece':
                if(!is_numeric($request[0]))
                    {
                    header("HTTP/1.1 400 Bad Request");
                    print json_encode(['errormesg'=>"lathos syntetagmenes"]);
                    exit;
                    }
                    if(count($request)<2)
                    	$request[1]=0;
                 handle_piece($method, $request[0],$request[1],$input);
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


function handle_board($method,$input)
    {
    if($method=='GET')
        {
        show_board($input);
        }
    else if ($method=='POST')
        {
		reset_board();
        show_board($input);
        }
    }

function handle_piece($method, $row,$col,$input)
    {
    if($method=='GET')
        {
        show_piece($row,$col,$input);
        }
    else if($method=='PUT')
        {
        add_piece($row,$input['token']);
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
        case 'R':
        case 'B': handle_user($method,$b,$input);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            print json_encode(['errormesg'=>"Player $b not found"]);
            break;
        }
    }

function handle_status($method)
    {
    if($method=='GET')
        show_status();
    }
    
?>