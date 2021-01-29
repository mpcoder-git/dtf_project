<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<script>
var ws;		/* Объект веб-сокет */

/* При загрузке страницы */
window.onload = function()
{
	var ip = '127.0.0.1';
	var port = 3333;

	connect(ip, port);
}

/**
 * Соединение
 */
function connect(ip, port)
{
	ws = new WebSocket("ws://" + ip + ":" + port);
	
	ws.onopen = function()
	{
		//log("Соединение установлено.");
	};

	ws.onclose = function(event)
	{
		if (event.wasClean) {
			alert('Соединение закрыто чисто');
		} else {
			
			
			alert('Обрыв соединения. '+ 'Код: ' + event.code + ' причина: ' + event.reason); // например, "убит" процесс сервера
		}
			
	};

	ws.onmessage = function(event)
	{
		//log("Пришло сообщение «" + event.data + "».");
		var data_arr = JSON.parse(event.data);
		
		if (typeof data_arr.ideas !== 'undefined') {
			document.getElementById("signals_ideastable").innerText = '';
			document.getElementById("signals_ideastable").insertAdjacentHTML('afterbegin', data_arr.ideas);
		}
		
		if (typeof data_arr.trades !== 'undefined') {
			document.getElementById("signals_tradestable").innerText = '';
			document.getElementById("signals_tradestable").insertAdjacentHTML('afterbegin', data_arr.trades);
		}
		
		if (typeof data_arr.closedtrades !== 'undefined') {
			document.getElementById("signals_closedtradestable").innerText = '';
			document.getElementById("signals_closedtradestable").insertAdjacentHTML('afterbegin', data_arr.closedtrades);
		}
		
	};

	ws.onerror = function(error) 
	{
		
		if (trim(error.message) !== 'undefined') {		
			alert("Произошла ошибка: «" + error.message + "».");
		}
	};
}



function ShowGraph(x){
	var cell = x;
	var col = cell.cellIndex;
	var row = cell.parentNode.rowIndex;
	var idea = document.getElementById('s_table').rows[row].cells[1].innerHTML;
	var decodedidea = idea.replace(/&amp;/g, '&'); 
	var ticket = document.getElementById('s_table').rows[row].cells[3].innerHTML;
	if (col == 3) {		
		document.getElementById('signals_graphics').innerHTML = '<img src="Signalsfiles/charts/'+decodedidea+','+ticket+'.png"></img>';
		document.getElementById('signals_graphics').style.top = window.event.pageY  - 20 + "px";
		
		document.getElementById('signals_graphics').style.display = 'block';
	}	
}
</script> 

<div class="content" id="signals_content">
	<div  class="mainblock innerblock uppertext">
		<h1 class="pageTitle">Сигналы</h1>
		
		<div id="signals_info">
			
			<p>Ideas</p>
			<div id="signals_ideastable"> 
			<?php	require_once('./Views/Blocks/ideastable.php'); ?>
			</div>
			<br>
			
			<p>Trades</p>
			<div id="signals_tradestable"> 
			<?php	require_once('./Views/Blocks/tradestable.php'); ?>
			</div>
			<br>
			
			<p>Closed Trades</p>
			<div id="signals_closedtradestable"> 
			<?php	require_once('./Views/Blocks/closedtradestable.php'); ?>
			</div>
			<br>
			
			
		</div>
		
		<div id="signals_graphics">
		
		</div>
		
		
	</div>
</div>

