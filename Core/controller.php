<?php 
class Controller {
	
	public $model;
	public $view;
	public $con;    //соединение с базой
	public $header_model; //шапка сайта
	public $security_model; //шапка сайта
	
	//функция выполняется после создания объекта
	function __construct()
	{
		$this->view = new View();
		$this->header_model = new Model();
		$this->security_model = new Model();

		session_start();
		
		//блок защиты от подмены сессии ------------------------------
		$user_check = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
		if (empty($_SESSION['user_data'])){
			$_SESSION['user_data'] = $user_check;
		}
		if (strcmp($_SESSION['user_data'], $user_check)){
			session_regenerate_id();
			$_SESSION = array();
			$_SESSION['user_data'] = $user_check;
		}
		//------------------------------------------------------------
		
		//цепляем настройки
		require_once("./Config/constants.php");
		
	}
	
	function action_index()
	{
	}
	
	//=================================================================
	// Функция соединения с базой данных 
	//=================================================================
	function db_connect()
	{
		
		$this->con = mysqli_connect(DB_SERVER,DB_USER, DB_PASS, DB_NAME) or die(mysqli_connect_error());

		/* проверка подключения */
		if (mysqli_connect_errno()) {
			printf("Не удалось подключиться: %s\n", mysqli_connect_error());
			exit();
		}

		//mysql_select_db(DB_NAME) or die("Cannot select DB");
		mysqli_set_charset($this->con, "utf8");   
	}
	
	//=================================================================
	// получение параметров для хеадера
	//=================================================================
	function get_parameters($title_page) {
		
		if (!isset($_SESSION['session_userid'])) { 
			$parameters = array('title_page' => $title_page);
		} else {
			$userid = $_SESSION["session_userid"];
			$header_queryresult = $this->header_model->select_userdata($this->con,$userid);
			
			if (!$header_queryresult) {
			die('Ошибка выполнения запроса:' . mysqli_error());
			exit;
			}
			
			if (!isset($header_queryresult)) {
				unset($_SESSION['session_userid']);
				unset($_SESSION['session_userhash']);
				session_destroy();
			}
			//если пользователь не существует - чистим сессию
			if (isset($header_queryresult)) { 			
				if(mysqli_num_rows($header_queryresult) == 0){
					unset($_SESSION['session_userid']);
					unset($_SESSION['session_userhash']);
					session_destroy();
				}
			}
						
		$parameters = array('title_page' => $title_page,'header_result' => $header_queryresult);
		}
		
		return $parameters;
	}
	
	//=================================================================
	// Функция сверки хэша из базы с хэшэм из сессии
	//=================================================================
	function get_truehash($userid) {
	
		$verify = 0;
		
		$query_result = $this->security_model->select_userdata($this->con,$userid);
		if (!$query_result) {
			die('Ошибка выполнения запроса:' . mysqli_error());
			exit;
			}
		while($row=mysqli_fetch_row($query_result))
		{
			$userhash=$row[8];
		}
		mysqli_free_result($query_result);
		
		if ($userhash == $_SESSION['session_userhash']){ $verify = 1; }
		
		return $verify;
	}
	//=================================================================
	// Функция для генерации случайной строки
	// можно запускать с любым параметром длины, по умолчанию = 6
	//=================================================================
	function generateCode($length=6) {

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;  
		while (strlen($code) < $length) {

				$code .= $chars[mt_rand(0,$clen)];  
		}

		return $code;
	}
	//=================================================================
	
	
	
	/* Валидация и фильтрация данных */
	
	
	
	//=================================================================
	// Функция проверяет по миме типу является ли файл картинкой
	//=================================================================
	function image_valid($type)
	{
		$file_types = array('image/pjpeg' => 'jpg', 'image/jpeg' => 'jpg', 'image/jpeg' =>
			'jpeg', 'image/gif' => 'gif', 'image/X-PNG' => 'png', 'image/PNG' => 'png',
			'image/png' => 'png', 'image/x-png' => 'png', 'image/JPG' => 'jpg', 'image/GIF' =>
			'gif', 'image/bmp' => 'bmp', 'image/bmp' => 'BMP', 'image/x-ms-bmp' => 'bmp');
		//if (!array_key_exists($type, $file_types))
		if (in_array($type, $file_types))
		{
			return "FALSE";
		}
		else
		{
			return "TRUE";
		}
	}
	//=================================================================
	
	//=================================================================
	// Функция создания и сохранения копии изображения
	// (пересохранение удаляет скрипты из картинок, и из описаний к картинкам )
	//=================================================================
	function image_resave($source_filename, $save_filename, $target_path){
	
	// $source_filename 	имя файла источника картинки
	// $save_filename 		имя с которым будем сохранять файл
	// $target_path 		папка куда будем сохранять картинку 
	
		//получить расширение файла (полагаем, что до запуска функции был контроль расширений)
		//и функция запускается только для картинок
		$ext = pathinfo($save_filename, PATHINFO_EXTENSION);
		
		//создание образа изображения в зависимости от расширения
		switch($ext){
			case 'jpg':
				$img = imagecreatefromjpeg($source_filename);
			break;
			case 'gif':
				$img = imagecreatefromgif($source_filename);
			break;
			case 'png':
				$img = imagecreatefrompng($source_filename);
			break;
		}
		
		if(!$img){ 
			//print 'невозможно создать картинку'; 
			return false;
			exit; 
		}
		
		// получение размеров
		$width = 0; $height = 0;
		list($width, $height) = getimagesize($source_filename);
		$new_width = $width;  $new_height = $height;
		//создаем клон чистое изображение с одинаковыми высотой и шириной как у оригинала
		$image_clone = imagecreatetruecolor($width, $height);
		imagecopyresampled($image_clone, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		
		
		//сохранение нового изображения
		switch($ext):
			case 'jpg' or 'jpeg':
				$makeimg = imagejpeg($image_clone, $target_path.$save_filename);
			break;
			case 'gif':
				$makeimg = imagegif($image_clone, $target_path.$save_filename);
			break;
			case 'png':
				$makeimg = imagepng($image_clone, $target_path.$save_filename);
			break;
		endswitch;
		
		//освободим память от ненужных изображений
		imagedestroy($img);
		imagedestroy($image_clone);
		
		//если удалось сохранить файл - то вернем труе, иначе вернем фальш
		if($makeimg){
			return true;
		} else {
			return false;
		}
	}
	//=================================================================
	
	//=================================================================
	// Функция проверяет число на валидность
	//=================================================================
	function number_valid($number)
	{
		//сли число целое и больше нуля - то вернем труе
		if(!preg_match("|^[\d]+$|",$number)  )
		{
			return FALSE;
		}
		else
		{
			//return TRUE;
			if ($number > 0){ return TRUE; } else { return FALSE; }
						
		}
	}
	//=================================================================
	
	//=================================================================
	// Функция фильтр текста (строки или многострочного) 
	// удаление из текста опасных символов и команд
	//1 параметр - строка текста для обработки, 
	//2 параметр - по умолчанию =0, но если равен 1, то в тексте удаяются лишние пробелы (два делаются одним)
	//=================================================================
	function filter_badtext($inputdata,$delbs=0){
		
		$inputdata = strip_tags($inputdata);
		
		$baddata = array(
		// SYMVOLS
		"<",
		">",
		"/",
		"%",
		"?",
		"(",
		")",
		"\"",
		";",
		// HTML
		"body",
		"script",
		"object",
		"iframe",
		"img",
		"frame",
		"applet",
		"meta",
		"style",
		"form",
		"div",
		//SQL
		"UNION",
		"OUTFILE",
		"FROM",
		"SELECT",
		"WHERE",
		"SHUTDOWN",
		"UPDATE",
		"DELETE",
		"CHANGE",
		"MODIFY",
		"RENAME",
		"RELOAD",
		"ALTER",
		"GRANT",
		"DROP",
		"INSERT",
		"CONCAT",
		"cmd",
		"exec",
		"sleep",
		"benchmark",
		"--"			
		);

		//цикл полного удаления запрещенных символов в несколько проходов
		$cnt = 1;
		while ($cnt > 0){
			$inputdata = str_ireplace($baddata,'',$inputdata,$cnt);		
		}
		
		//если есть параметр, то удалим лишние пробелы (двойные заменяем на одинарные)
		if ($delbs == 1){
			//преобразовать двойные пробелы в одинарные
			$cnt = 1;
			while ($cnt > 0){
				$inputdata = str_ireplace('  ',' ',$inputdata,$cnt);				
			}				
		}
		$inputdata = trim($inputdata);
		return $inputdata;
	
	}
	//=================================================================
	
		
		
	/* Шифровка и расшифрока данных */	
		
		

	
	//=================================================================
	// Функция шифрования строки
	//=================================================================
	function encode($unencoded,$key) {
		
		//алгоритм удален в целях безопасности
		$newstr = 'abcd';
		return $newstr;//Вертаем строку
	}
	
	//=================================================================
	// Функция дешифровки
	//=================================================================
	function decode($encoded, $key){//расшифровываем
		
		//алгоритм удален в целях безопасности
		return $encoded;//Вертаем расшифрованную строку
	}
	
	//=================================================================
	// Функция шифровки пароля (возвращает 0 или строку длиной 32 символа)
	//=================================================================
	function encode_pass($password){
		
		//алгоритм удален в целях безопасности 
		$encpass = 0;
				
		return $encpass;
	}
	//=================================================================
	
	
	
	/* Работа с датами и строками текста */
	
			
	
	//=================================================================
	// Функция для составления строки из слов 
	// limit количество символов из строки
	//=================================================================
	function str_limit($input_str,$limit) 
	{ 
		//$limit до какой длинны обрезаем 
		$str_lenght = strlen($input_str);

		if ($str_lenght > $limit) 
		{ 
		  $str = substr($input_str,0,$limit); 
		  $pos = strrpos($str, " "); 
			if (!isset($pos)) 
			{$str = "....";} 
		   else 
			{
				$str = substr($str,0,$pos); $str = $str."....";
			}
			
		} else {
			$str = $input_str;
		}

		return $str; 
	} 
	//=================================================================
	
	//=================================================================
	// Функция преобразования даты из базы mysql в календарный вид
	//=================================================================
	function ref_date($date)
	{
		preg_match("|([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})\s([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})|",$date,$out);
		return "$out[3].$out[2].$out[1] $out[4]:$out[5]:$out[6]";
	}
	//=================================================================	
	
	
	/* Работа с каталогами и файлами */
	
	
	
	//=================================================================
	// Функция удаления каталога со всеми содержимыми файлами
	//=================================================================
	function delete_dir($target_path){
	
		//удаляем файлы и папку
		if (file_exists($target_path)) {
			
			$dir_pointer = opendir ($target_path);
			while (($res = readdir($dir_pointer))!==FALSE){
				if ($res != "." && $res != ".." ){
					$file_for_del=$target_path.$res;
					 unlink($file_for_del); 
				}								
			}
		}	
		closedir ($dir_pointer);

		rmdir($target_path);
	
	}
	//=================================================================
	
	//=================================================================
	// Функция создания каталога 
	//=================================================================
	function create_dir($target_path){
	
		//поиск каталога
		if (file_exists($target_path)) {
    	//если каталог существует - то ничего дальше не делаем
		} else {
			//если каталог не найден
			$directory = $target_path;
    		if(!mkdir($directory)) {
				echo "Ошибка создания каталога $directory...<br />\n";
				exit;
    		}
	
		}
	
	}
	//=================================================================
	
	//=================================================================
	// Функция подсчета количества файлов в каталоге
	//=================================================================
	function cntfiles_indir($target_path){
	
		//если папка найдена
		if (file_exists($target_path)) {
			$dir_pointer = opendir ($target_path);
			$countfiles = 0;
			while (($res = readdir($dir_pointer))!==FALSE){
				//подсчитываются все файлы кромекаталогов и индекса
				if ($res != "." && $res != ".." && $res != "Index.php"){
					$countfiles = $countfiles +1;													
				}								
			}						
			closedir ($dir_pointer);
			return $countfiles;
		}
	}
	//=================================================================	
	
	//=================================================================
	// Функция удаления файла из каталога
	//=================================================================
	function deletfile($directory,$filename) {
	
		//если папка найдена
		if (file_exists($directory)) {
			$dir_pointer = opendir ($directory);
			
			
			while (($file = readdir($dir_pointer))!==FALSE){
				
				// Если это файл и он равен удаляемому ...
				if((is_file("$directory/$file")) && ("$directory/$file" == "$directory/$filename"))
				{ 				
					// ...удаляем его.
					unlink("$directory/$file"); 
					
					// Если файла нет по запрошенному пути, возвращаем TRUE - значит файл удалён.					
					if(!file_exists($directory."/".$filename)) return $s = TRUE; 
				}											
			}						
			closedir ($dir_pointer);

		}
	}
	//=================================================================		
	
	//=================================================================
	// Функция определяет размер файла
	//=================================================================
	function filesize_get($filesize)
	{
	  
	   // Если размер переданного в функцию файла больше 1кб
	   if($filesize > 1024)
	   {
		   $filesize = ($filesize/1024);
		   // если размер файла больше одного килобайта
		   // пересчитываем в мегабайтах
		   if($filesize > 1024)
		   {
				$filesize = ($filesize/1024);
			   // если размер файла больше одного мегабайта
			   // пересчитываем в гигабайтах
			   if($filesize > 1024)
			   {
				   $filesize = ($filesize/1024);
				   $filesize = round($filesize, 1);
				   return $filesize." ГБ";   
					
			   }
			   else
			   {
				   $filesize = round($filesize, 1);
				   return $filesize." MБ";   
			   }  
				
		   }
		   else
		   {
			   $filesize = round($filesize, 1);
			   return $filesize." Кб";   
		   }
	   
	   }
	   else
	   {
		   $filesize = round($filesize, 1);
		   return $filesize." байт";   
	   } 

	}   
	//=================================================================
		
	//=================================================================
	// Функция создания файла заглушки
	//=================================================================
	function create_indexphp($target_path){
	
		if (!file_exists($target_path."Index.php")) {
		//не найден файл индекса
			//создаем индексный файл в папке если он не найден
			$filename_out=$target_path."Index.php"; 
			$f_out=fopen($filename_out,"w+t") or die("Ошибка при создании файла"); 	
			
			fwrite($f_out,"<HTML>"); 
			fwrite($f_out,"\n"."<HEAD>"); 
			//fwrite($f_out,"\n"."<TITLE>".$title."</TITLE>");  
			//fwrite($f_out,"\n"."<META NAME=\"Keywords\" CONTENT=\"".$keywords."\">"); 
			//fwrite($f_out,"\n"."<META NAME=\"Description\" CONTENT=\"".$description."\">"); 
			fwrite($f_out,"\n"."<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=http://localhost\">"); 
			fwrite($f_out,"\n"."</HEAD>"); 
			fwrite($f_out,"\n"."<BODY>"); 
			fwrite($f_out,"\n"."</BODY>"); 
			fwrite($f_out,"\n"."</HTML>"); 
			
			fclose($f_out);
				
		}
	
	}
	//=================================================================
	
	
	
	
	
	
}
?>