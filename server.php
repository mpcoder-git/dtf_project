<?php
//вебсокет сервер

//системные настройки
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);		//Время выполнения скрипта безгранично
ob_implicit_flush();	//Включаем вывод без буферизации 
ignore_user_abort(true);//Выключаем зависимость от пользователя



$timelimit = 0; // если 0, то тогда безлимитно, только на сообщение, иначе кол-во секунд
$starttime = round(microtime(true),2); //время старта
$timertime = round(microtime(true),2);

$ideas_prevfiletime = 0;
$trades_prevfiletime = 0;
$closedtrades_prevfiletime = 0;

$signalspatch = './Signalsfiles/';
$ideas_filename = 'Ideas.txt';

$socket = stream_socket_server("tcp://127.0.0.1:3333", $errno, $errstr);
define ( 'READFILE', true );			//безопасность подключаемых файлов
//require_once('server_settings.php');


if (!$socket) {
	//consolemsg("ERROR socket unavailable " .$errstr. "(" .$errno. ")");
    die($errstr. "(" .$errno. ")\n");
}

$connects = array();

while (true) {
	//команды аварийной остановки
	
	 //проверяем наличие стоп файла. Если находим - прекращаем работу скрипта
    /*
	if (file_exists ("./Signalsfiles/service/stop_server")) { 
		
		//if (!file_exists ("./Signalsfiles/service/server.log")) { 
		file_put_contents('./Signalsfiles/service/server.log', date("d.m.y H:i:s", time()).' server stoped... '); 		
		//} 
		break; 
		exit; 
	
	} 
        
    if (file_exists ("./Signalsfiles/service/test_server")) { 
		
		if (!file_exists ("./Signalsfiles/service/server.log")) { 
		file_put_contents('./Signalsfiles/service/server.log', date("d.m.y H:i:s", time()).' server working... '); 		
		}  
    }
	*/
	

    //формируем массив прослушиваемых сокетов:
    $read = $connects;
    $read []= $socket;
    $write = $except = null;

    
	//4 параметр это время в секундах
	//если время = 0, то сильно большая нагрузка на процессор и результат будет сразу. рекомендовано несколько секунд
	//если время равно null то ожидание соединения будет безконечным и выполнение кода зависнет на этой строчке, дальше работать не будет
	if (!stream_select($read, $write, $except, 5)) {//ожидаем сокеты доступные для чтения (с таймаутом 5 сек)
        //break;
	
    }
	
	//file_put_contents('./Signalsfiles/service/file.log', time());
	
	
	if (in_array($socket, $read)) {//есть новое соединение то обязательно делаем handshake
        //принимаем новое соединение и производим рукопожатие:
        if (($connect = stream_socket_accept($socket, -1)) && $info = handshake($connect)) {
			//consolemsg("new connection... connect=".$connect.", info=".$info." OK");            

			//var_dump($info); 

			$connects[] = $connect;//добавляем его в список необходимых для обработки
            onOpen($connect, $info);//вызываем пользовательский сценарий
        }
        unset($read[ array_search($socket, $read) ]);
    }

    foreach($read as $connect) {//обрабатываем все соединения		
		
		$data = fread($connect, 100000);

        if (!$data) { //соединение было закрыто
			//consolemsg("connection closed...");    
						
			fclose($connect);
            unset($connects[ array_search($connect, $connects) ]);
            onClose($connect);//вызываем пользовательский сценарий
			//consolemsg("OK");    
            continue;
        }
		
		//если есть сообщение от клиента - отправляем его текст обратно
        //onMessage($connect, $data);//вызываем пользовательский сценарий
		
		//прием команды завершения скрипта OFF
		$f = decode($data); 
		if ($f['payload']=="OFF") { //shutdown by command

			fclose($socket);
			exit();		
		}
		
		if ($f['payload']=="STARTTIME") { //starttime command
			
			$realtime = round(microtime(true),2); //время текущее
			$workingtime = $realtime - $starttime; //скрипт работает секунд
			fwrite($connect, encode('Время запуска: '.date("d.m.y G:i:s",$starttime).' Скрипт проработал:'.$workingtime.' секунд'));	
		}
		
		if ($f['payload']=="TEST") { //test command
			
			fwrite($connect, encode('working v 1'));	
		}

    }

	//выполнение процедуры через 5 секунд

	if (time() > $timertime + 5) {
        $timertime = time();
		//file_put_contents('file.log', $timertime); //необязательно сделано для контроля
		$arr = array();
		$ideas_realfiletime = 0;
		$trades_realfiletime = 0;
		$closedtrades_realfiletime = 0;
		
		$findedit = 0; //найдены изменения или нет 0  - не найдено, 1 - найдено
		
		//произвести сканирование дат создания файлов и если есть новые, то отправить всем сообщение
		//if (file_exists ($_SERVER['DOCUMENT_ROOT']."/Signalsfiles/Ideas.txt")) { 
		//fwrite($connect, encode('file found');
		clearstatcache(); //чистка кэша данных файлов должна быть перед запросом времени (иначе время будет постоянным браться из кэша)
		
		$ideas_realfiletime = filemtime($_SERVER['DOCUMENT_ROOT'].'/Signalsfiles/Ideas.txt');
		$trades_realfiletime = filemtime($_SERVER['DOCUMENT_ROOT'].'/Signalsfiles/Trades.txt');
		$closedtrades_realfiletime = filemtime($_SERVER['DOCUMENT_ROOT'].'/Signalsfiles/ClosedTrades.txt');
		
		//}
		fwrite($connect, encode($closedtrades_realfiletime.' - '.$closedtrades_prevfiletime.' '.$timertime));
		
		//and $ideas_prevfiletime > 0
		
		//ideas
		if ($ideas_realfiletime > $ideas_prevfiletime ){
			
			//получить html текст выполненого файла идей
			ob_start();
			include('./Views/Blocks/ideastable.php');
			$html_ideastable = ob_get_clean();
			
			
			$arr['ideas'] = $html_ideastable;
			$findedit = 1;
			//перебор всех соединений и отправка всем сообщения
			//foreach($connects as $connect) {
				//подготовка сообщения (формирование json строки)
								
				//fwrite($connect, encode(json_encode($arr)));
				//fwrite($connect, encode($html_ideastable));
				
			//}
			
			
			//unset($ideas_arr);
			//присваиваем последнее время проверки
			$ideas_prevfiletime = $ideas_realfiletime;
			
		}
		
		//trades
		if ($trades_realfiletime > $trades_prevfiletime ){
			
			//получить html текст выполненого файла идей
			ob_start();
			include('./Views/Blocks/tradestable.php');
			$html_tradestable = ob_get_clean();
			
			
			$arr['trades'] = $html_tradestable;
			$findedit = 1;
			//перебор всех соединений и отправка всем сообщения
			//foreach($connects as $connect) {
				//подготовка сообщения (формирование json строки)
								
				//fwrite($connect, encode(json_encode($arr)));
				//fwrite($connect, encode($html_ideastable));
				
			//}
			
			
			//unset($ideas_arr);
			//присваиваем последнее время проверки
			$trades_prevfiletime = $trades_realfiletime;
			
		}
		
		//closedtrades
		if ($closedtrades_realfiletime > $closedtrades_prevfiletime ){
			
			//получить html текст выполненого файла идей
			ob_start();
			include('./Views/Blocks/closedtradestable.php');
			$html_closedtradestable = ob_get_clean();
			
			
			$arr['closedtrades'] = $html_closedtradestable;
			$findedit = 1;
			//перебор всех соединений и отправка всем сообщения
			//foreach($connects as $connect) {
				//подготовка сообщения (формирование json строки)
								
				//fwrite($connect, encode(json_encode($arr)));
				//fwrite($connect, encode($html_ideastable));
				
			//}
			
			
			//unset($ideas_arr);
			//присваиваем последнее время проверки
			$closedtrades_prevfiletime = $closedtrades_realfiletime;
			
		}
		
		//если изменения найдены - отправим массив клиентам
		if ($findedit == 1){
			
			foreach($connects as $connect) {
				//подготовка сообщения (формирование json строки)								
				fwrite($connect, encode(json_encode($arr)));				
			}
			$findedit = 0;
		}
		
		unset($arr);
				
    }


	if($timelimit!=0 && ( round(microtime(true),2) - $starttime) > $timelimit) { //Если за пределами timelimit - вырубаем процесс
 
			fclose($socket);

			exit();		
	}
	
	
}

//unreachble code
fclose($socket);
//consolemsg("socket - closed");	


//------------------------------------------------------------------------------------------------------------------------------------------------

function handshake($connect) { //Функция рукопожатия
    $info = array();

    $line = fgets($connect);
    $header = explode(' ', $line);
    $info['method'] = $header[0];
    $info['uri'] = $header[1];

    //считываем заголовки из соединения
    while ($line = rtrim(fgets($connect))) {
        if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
            $info[$matches[1]] = $matches[2];
        } else {
            break;
        }
    }

    $address = explode(':', stream_socket_get_name($connect, true)); //получаем адрес клиента
    $info['ip'] = $address[0];
    $info['port'] = $address[1];

    if (empty($info['Sec-WebSocket-Key'])) {
        return false;
    }

    //отправляем заголовок согласно протоколу вебсокета
    $SecWebSocketAccept = base64_encode(pack('H*', sha1($info['Sec-WebSocket-Key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "Sec-WebSocket-Accept:".$SecWebSocketAccept."\r\n\r\n";
    fwrite($connect, $upgrade);

    return $info;
}


 


function encode($payload, $type = 'text', $masked = false) {
    $frameHead = array();
    $payloadLength = strlen($payload);

    switch ($type) {
        case 'text':
            // first byte indicates FIN, Text-Frame (10000001):
            $frameHead[0] = 129;
            break;

        case 'close':
            // first byte indicates FIN, Close Frame(10001000):
            $frameHead[0] = 136;
            break;

        case 'ping':
            // first byte indicates FIN, Ping frame (10001001):
            $frameHead[0] = 137;
            break;

        case 'pong':
            // first byte indicates FIN, Pong frame (10001010):
            $frameHead[0] = 138;
            break;
    }

    // set mask and payload length (using 1, 3 or 9 bytes)
    if ($payloadLength > 65535) {
        $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 255 : 127;
        for ($i = 0; $i < 8; $i++) {
            $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
        }
        // most significant bit MUST be 0
        if ($frameHead[2] > 127) {
            return array('type' => '', 'payload' => '', 'error' => 'frame too large (1004)');
        }
    } elseif ($payloadLength > 125) {
        $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
        $frameHead[1] = ($masked === true) ? 254 : 126;
        $frameHead[2] = bindec($payloadLengthBin[0]);
        $frameHead[3] = bindec($payloadLengthBin[1]);
    } else {
        $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
    }

    // convert frame-head to string:
    foreach (array_keys($frameHead) as $i) {
        $frameHead[$i] = chr($frameHead[$i]);
    }
    if ($masked === true) {
        // generate a random mask:
        $mask = array();
        for ($i = 0; $i < 4; $i++) {
            $mask[$i] = chr(rand(0, 255));
        }

        $frameHead = array_merge($frameHead, $mask);
    }
    $frame = implode('', $frameHead);

    // append payload to frame:
    for ($i = 0; $i < $payloadLength; $i++) {
        $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
    }

    return $frame;
}

function decode($data){
    $unmaskedPayload = '';
    $decodedData = array();

    // estimate frame type:
    $firstByteBinary = sprintf('%08b', ord($data[0]));
    $secondByteBinary = sprintf('%08b', ord($data[1]));
    $opcode = bindec(substr($firstByteBinary, 4, 4));
    $isMasked = ($secondByteBinary[0] == '1') ? true : false;
    $payloadLength = ord($data[1]) & 127;

    // unmasked frame is received:
    if (!$isMasked) {
        return array('type' => '', 'payload' => '', 'error' => 'protocol error (1002)');
    }

    switch ($opcode) {
        // text frame:
        case 1:
            $decodedData['type'] = 'text';
            break;

        case 2:
            $decodedData['type'] = 'binary';
            break;

        // connection close frame:
        case 8:
            $decodedData['type'] = 'close';
            break;

        // ping frame:
        case 9:
            $decodedData['type'] = 'ping';
            break;

        // pong frame:
        case 10:
            $decodedData['type'] = 'pong';
            break;

        default:
            return array('type' => '', 'payload' => '', 'error' => 'unknown opcode (1003)');
    }

    if ($payloadLength === 126) {
        $mask = substr($data, 4, 4);
        $payloadOffset = 8;
        $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
    } elseif ($payloadLength === 127) {
        $mask = substr($data, 10, 4);
        $payloadOffset = 14;
        $tmp = '';
        for ($i = 0; $i < 8; $i++) {
            $tmp .= sprintf('%08b', ord($data[$i + 2]));
        }
        $dataLength = bindec($tmp) + $payloadOffset;
        unset($tmp);
    } else {
        $mask = substr($data, 2, 4);
        $payloadOffset = 6;
        $dataLength = $payloadLength + $payloadOffset;
    }

    /**
     * We have to check for large frames here. socket_recv cuts at 1024 bytes
     * so if websocket-frame is > 1024 bytes we have to wait until whole
     * data is transferd.
     */
    if (strlen($data) < $dataLength) {
        return false;
    }

    if ($isMasked) {
        for ($i = $payloadOffset; $i < $dataLength; $i++) {
            $j = $i - $payloadOffset;
            if (isset($data[$i])) {
                $unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
            }
        }
        $decodedData['payload'] = $unmaskedPayload;
    } else {
        $payloadOffset = $payloadOffset - 4;
        $decodedData['payload'] = substr($data, $payloadOffset);
    }

    return $decodedData;
}

//пользовательские сценарии:

function onOpen($connect, $info) {
	//функция срабатывает при соединении
	//consolemsg("open OK"); 

    //fwrite($connect, encode('Привет, мы соеденены'));
}

function onClose($connect) {
    //consolemsg("close OK");
}

function onMessage($connect, $data) {
    //функция отправки сообщений
	$f = decode($data);

    //consolemsg("Message:".$f['payload']);

    fwrite($connect, encode($f['payload']));//echo функция ответа
}



