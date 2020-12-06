
var me={};
var game_status={};

$(function()
	{
	draw_empty_board();
	$('#score4_login').click( login_to_game);
	$('#chess_reset').click( reset_board);

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
	$('#score4_board'). html(t);
	}

function login_to_game()
	{
	if( $('#username').val()=='')
		{
		alert('You have to set a username first');
		return ;
		}
	var p_type=$('#ptype').val();
	//draw_empty_board();


	$.ajax({url: "score4.php/players/"+p_type,
												method: 'PUT',
												dataType: "json",
												contentType: 'application/json',
												data: JSON.stringify( {username:$('#username').val(), piece: p_type}),
												success: login_result,
												error: login_error});
	}


function reset_board()
	{
	$.ajax({url: "score4.php/board/", method: 'POST',  success: draw_empty_board });
	$('#move_div').hide();
	$('#game_initializer').show(2000);
	}

function login_error(data,y,z,c) 
	{
	var x = data.responseJSON;
	alert(x.errormsg);
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
	$.ajax({url: "score4.php/status/", success: update_status });
	}


function update_status(data)
	{
	game_status=data[0];
	update_info();
	if(game_status.p_turn==me.piece &&  me.piece!=null)
		{
		x=0;
		// do play
		$('#move_div').show(1000);
		setTimeout(function() { game_status_update();}, 15000);
		} 
	else
		{
		// must wait for something
		$('#move_div').hide(1000);
		setTimeout(function() { game_status_update();}, 4000);
		}
 	
	}


function update_info(){
	$('#game_info').html("I am Player: "+me.piece+", my name is "+me.username +'<br>Token='+me.token+'<br>Game state: '+game_status.status+', '+ game_status.p_turn+' must play now.');
	
}