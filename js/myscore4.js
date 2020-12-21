
var me={token:null,piece_color:null};
var game_status={};
var board={};
var last_update=new Date().getTime();
var timer=null;

$(function()
	{
	draw_empty_board();
	fill_board();
	$('#score4_login').click( login_to_game);
	$('#score4_reset').click( reset_board);
	$('#do_move').click( do_move);
	game_status_update();
	}
);


function draw_empty_board()
	{
	var t='<table id="score4_table">';
	for(var i=1;i<=7;i++)
		{
		t += '<tr>';
		for(var j=1;j<=6;j++)
			{
			t += '<td  id="square_'+i+'_'+j+'"></td>';
			}
		t+='</tr>';
		}
	t+='</table>';
	$('#score4_board').html(t);
	}

function fill_board()
	{
	$.ajax({url: "score4.php/board/",
	        headers: {"X-Token": me.token},
		    success: fill_board_by_data});
	}

function fill_board_by_data(data)
	{
	board=data;	
	for(var i=0;i<data.length;i++)
		{
		var o = data[i];
		var id= '#square_'+o.x+'_'+o.y;
		var c = (o.piece_color!=null)?o.piece_color:'';
		var im = (o.piece_color!=null)?'<img class="piece_color" src="imgs/'+c+'_piece.png">':'';
		$(id).addClass('square').html(im);
		}
	if(me.piece_color!=null && game_status.p_turn==me.piece_color)
		{
		$('#move_div').show(1000);
		}
	else 
		{
		$('#move_div').hide(1000);
		}
	}

function login_to_game()
	{
	if( $('#username').val()=='')
		{
		alert('You have to set a username first');
		return ;
		}
	var p_color=$('#pcolor').val();
	fill_board();

	$.ajax({url: "score4.php/players/"+p_color,
												method: 'PUT',
												dataType: "json",
												contentType: 'application/json',
												data: JSON.stringify( {username:$('#username').val(), piece_color: p_color} ),
												success: function(data) {  login_result(data) },
												error: function(data){ login_error(data)} }) ;
	 }


function reset_board()
	{
	$.ajax({url: "score4.php/board/", headers: {"X-Token": me.token}, method: 'POST',  success: fill_board_by_data });
	$('#move_div').hide();
	$('#game_initializer').show(2000);
	}

function login_error(data) 
	{
	var x = data.responseJSON;
	alert(x.errormesg);
	}


function login_result(data)
	{
	me = data[0];
	$('#game_initializer').hide();
	update_info();
	game_status_update();
	}

function game_status_update()
	{
	clearTimeout(timer);
	$.ajax({url: "score4.php/status/", success: update_status, headers: {"X-Token": me.token}});
	}


function update_status(data)
	{
	last_update=new Date().getTime();
	var game_stat_old = game_status;
	game_status=data[0];
	update_info();
	clearTimeout(timer);
	if(game_status.p_turn==me.piece_color &&  me.piece_color!=null) 
		{
		x=0;
		// do play
		if(game_stat_old.p_turn!=game_status.p_turn)
			{
			fill_board();
			}
		$('#move_div').show(1000);
		timer=setTimeout(function() { game_status_update();}, 15000);
		} 
	else
		{
		// must wait for something
		$('#move_div').hide(1000);
		timer=setTimeout(function() { game_status_update();}, 4000);
		}
 	
	}


function update_info()
	{
	$('#game_info').html("I am Player: "+me.piece_color+", my name is "+me.username +'<br>Token='+me.token+'<br>Game state: '+game_status.status+', '+ game_status.p_turn+' must play now.');
	}


function do_move()
	{
	var s = $('#the_move').val();
	//var a = s.trim().split(/[ ]+/);
	//a[1]=0;//
	if(s=="")
		{
		alert('is null');
		return;
		}
	$.ajax({url: "score4.php/board/piece/0/"+s,
			method: 'PUT',
			dataType: "json",
			contentType: 'application/json',
			data: JSON.stringify( { x:8, y: 8})  ,
			headers: {"X-Token": me.token},
			success: move_result,
			error: login_error }) ;
	}


 /*function AjaxFailAlert(jqXHR, textStatus) {
	if (jqXHR.readyState === 0 || jqXHR.status === 0) return;
	else if (jqXHR.status == 404)
		{alert("Requested page not found. [404]");}
	else if (jqXHR.status == 500)
		{alert("Internal Server Error [500].");}
	else if (textStatus === "parsererror")
		{alert("Requested JSON parse failed.");}
	else if (textStatus === "timeout")
		{alert("Time out error.");}
	else if (textStatus === "abort")
		{alert("Ajax request aborted.");}
	else
		{alert("Uncaught Error.\n" + jqXHR.responseText);}
}*/



function move_result(data){
	game_status_update();
	fill_board_by_data(data);
}