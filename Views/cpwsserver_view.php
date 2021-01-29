<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php'); 	
?>
		<script>
var ws;		/* Объект веб-сокет */

/* При загрузке страницы */
window.onload = function()
{
	/* Кнопка «Соединиться» */
	document.getElementById("connect").onclick = function()
	{
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		
		 connect(ip, port);
		return false;
	};
	
	/* Кнопка «Отсоединится» */
	document.getElementById("close").onclick = function()
	{
		ws.close();		
		return false;
	};
	
	/* Кнопка «Отправить сообщение» */
	document.getElementById("send").onclick = function()
	{
		var message = document.getElementById("message").value;
		
		send(message);
		
		return false;
	};
};

/**
 * Функция логирования
 */
function log(str)
{
	document.getElementById("log").innerText += str + "\n";	
}

/**
 * Соединение
 */
function connect(ip, port)
{
	ws = new WebSocket("ws://" + ip + ":" + port);
	
	ws.onopen = function()
	{
		log("Соединение установлено.");
	};

	ws.onclose = function(event)
	{
		log("Соединение закрыто. Код «" + event.code + "».");
	};

	ws.onmessage = function(event)
	{
		log("Пришло сообщение «" + event.data + "».");
	};

	ws.onerror = function(error) 
	{
		log("Произошла ошибка: «" + error.message + "».");
	};
}

/**
 * Отправить сообщение
 */
function send(str)
{
	ws.send(str);
	log("Отправлено сообщение «" + str + "».");
}
		</script>
<div class="content">

	<div  class="mainblock innerblock">
	
		<h1 class="pageTitle">WebSocket клиент</h1>
		<div class="centeredblock">
			<form class="mainform">
				<p>
				<input id="ip" type="text" name="ip" class="maininput" value="127.0.0.1" placeholder="IP-адрес"/>
				<input id="port" type="text" name="port" class="maininput" value="3333" placeholder="Порт"/>
				</p>
				<p>
				<button id="connect" class="mainbutton">Соединиться</button>
				<button id="close" class="mainbutton">Отсоединиться</button>
				</p>
				<p>
				<textarea id="message" class="maintextarea" placeholder="Сообщение"></textarea>
				</p>
				<p>
				<button id="send" class="mainbutton">Отправить сообщение</button>
				</p>
				<p>
				<label>Лог</label>
				<div class="log"><pre id="log"></pre></div>
				</p>
			</form>
		</div>

	
		
	</div>
</div>