var me={token:null,piece_color:null};
var game_status={};
var board={};
var last_update=new Date().getTime();
var timer=null;

$(function()
	{
	draw_empty_board();
	fill_board(null);
	$('#do_move').button().click( do_move);
	game_status_update();
	$("#score4_reset").button().click(reset_board).hide();
	$("#score4_login").button().click(login_to_game);

	}
);


function draw_empty_board()
	{
	var t='<table id="score4_table">';
	for(var i=1;i<=6;i++)
		{
		t += '<tr>';
		for(var j=1;j<=7;j++)
			{
			t += '<td  id="square_'+i+'_'+j+'"></td>';
			}
		t+='</tr>';
		}
	t+='</table>';
	$('#score4_board').html(t);
	}

function fill_board(game_status)
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
		var id= '#square_'+o.row+'_'+o.col;
		var c = (o.piece_color!=null)?o.piece_color:'';

		if(o.piece_color!=null)
			$(id).css("background-image", "url(imgs/"+c+"_piece.png)");
		else
			$(id).css("background-image", "");
		}

	if(me.piece_color!=null && game_status.p_turn==me.piece_color)
		{
		$('#move_div').show(1000);
		$('#the_move').val("");
		}
	else 
		{
		$('#move_div').hide(1000);
		}
	if(game_status.status=="ended")
		{
		clearTimeout(timer);
		if(game_status.result=="D")
			{
			endOfGame("draw","");
			}
		else
			{
			endOfGame("win",game_status.result);
			}
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
	$.ajax({url: "score4.php/players/"+p_color,
												method: 'PUT',
												dataType: "json",
												contentType: 'application/json',
												data: JSON.stringify( {username:$('#username').val(), piece_color: p_color} ),
												success: function(data) {  login_result(data) },
												error: function(data){ login_error(data)} }) ;
	 }



 function endOfGame(status,res)
 	{
	$.ajax({url: "score4.php/players/",
									method: 'GET',
									dataType: "json",
									contentType: 'application/json',
									success: function(data) {  
									if(status=="win")
										{
										$("<div id='r'  title='Τέλος παιχνιδιού'>Νικητής είναι ο "+(data[0].piece_color==res?data[0].username +"("+data[0].piece_color+")" : data[1].username+"("+data[1].piece_color+")")
										+"</div>").dialog({buttons:{"ok":function(){ reset_board() ;$(this).dialog("close");}}});
										}
									else
										{
										$("<div id='r'  title='Τέλος παιχνιδιού'>Οι παίκτες "+data[0].username+", "+data[1].username+" ήρθαν ισσόπαλοι."
											+"</div>").dialog({buttons:{"ok":function(){reset_board();$(this).dialog("close");}}});
										}

										 },
										error: function(data){ login_error(data)} }) ;
	 }
 	


function reset_board()
	{
	$('#move_div').hide();
	game_status.result=null;
	game_status.p_turn=null;
	game_status.status=null;

	$('#game_info').html("");
	$.ajax({url: "score4.php/board/", headers: {"X-Token": me.token}, method: 'POST',  success: fill_board_by_data });
	me.token=null;
	me.piece_color=null;
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
	//update_info();

	if(game_status.status=="started")
		{
		$('#game_info').html('Σειρά του '+game_status.p_turn+' να παίξει');
		$('#score4_reset').show();
		}
	
	if(game_status.status=="initialized")
		{
		if(me.token!=null)
			{
			$('#score4_reset').show();
			$('#game_info').html("Σύνδεση επιτυχής. Αναμονή για τον 2ο παίκτη...");
			}
		}
	if(game_status.status=="not active" || game_status.status=="aborted")
		{
		console.log(me.token);

		$('#game_info').html("");
		$('#score4_reset').hide();
		$("#move_div").hide();
		if(me.token!=null)
			{
			clearTimeout(timer);

			$("<div id='alert1' title='Τέλος παιχνιδιού'>Ο άλλος παίκτης εγκατέλειψε το παιχνίδι.</div>").dialog({buttons:{"Επανεκκίνηση":function(){ 	
							$('#game_initializer').show(2000); 
							me.token=null;
							$(this).dialog("close");
						 }}});
			return ;
			}
		
		}

	if(game_status.status=="ended")
		{
		$('#score4_reset').hide();
		$('#game_info').html("");
		}

	if(game_status.p_turn==me.piece_color &&  me.piece_color!=null) 
		{
		if(game_stat_old.p_turn!=game_status.p_turn)
			{
			fill_board(game_status);
			}
		timer=setTimeout(function() { game_status_update();}, 1000);
		$('#move_div').show(1000);
		}
	else if( me.piece_color!=null)
		{
		if(game_status.status=="ended")
			{
			fill_board(game_status);
			}
		$('#move_div').hide(1000);
		timer=setTimeout(function() { game_status_update();}, 1000);
		}

	}


function do_move()
	{
	var s = $('#the_move').val();
	if(s=="")
		{
		alert('is null');
		return;
		}
	$.ajax({url: "score4.php/board/piece/"+s,//
			method: 'PUT',
			dataType: "json",
			contentType: 'application/json',
			headers: {"X-Token": me.token},
			success: move_result,
			error: login_error }) ;
	}

function move_result(data){
	game_status_update();
	fill_board_by_data(data);
}