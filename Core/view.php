<?php
class View
{
//public $template_view; // здесь можно указать общий вид по умолчанию.
/*
 параметры:
$content_file — виды отображающие контент страниц;
$template_file — общий для всех страниц шаблон;
$data — массив, содержащий элементы контента страницы. Обычно заполняется в модели.
*/	
	
    function generate($content_view, $template_view, $data = null)
    {
        
        if(is_array($data)) {
            // преобразуем элементы массива в переменные
            extract($data);
        }
        
        
        include './Views/'.$template_view;
    }
	
	//=================================================================
	// Функция для отображения даты (аналог date() только на русском)
	// синтаксис такой же как у date()
	//=================================================================
	function rus_date() {
		$translate = array(
		"am" => "дп",
		"pm" => "пп",
		"AM" => "ДП",
		"PM" => "ПП",
		"Monday" => "Понедельник",
		"Mon" => "Пн",
		"Tuesday" => "Вторник",
		"Tue" => "Вт",
		"Wednesday" => "Среда",
		"Wed" => "Ср",
		"Thursday" => "Четверг",
		"Thu" => "Чт",
		"Friday" => "Пятница",
		"Fri" => "Пт",
		"Saturday" => "Суббота",
		"Sat" => "Сб",
		"Sunday" => "Воскресенье",
		"Sun" => "Вс",
		"January" => "Января",
		"Jan" => "Янв",
		"February" => "Февраля",
		"Feb" => "Фев",
		"March" => "Марта",
		"Mar" => "Мар",
		"April" => "Апреля",
		"Apr" => "Апр",
		"May" => "Мая",
		"May" => "Мая",
		"June" => "Июня",
		"Jun" => "Июн",
		"July" => "Июля",
		"Jul" => "Июл",
		"August" => "Августа",
		"Aug" => "Авг",
		"September" => "Сентября",
		"Sep" => "Сен",
		"October" => "Октября",
		"Oct" => "Окт",
		"November" => "Ноября",
		"Nov" => "Ноя",
		"December" => "Декабря",
		"Dec" => "Дек",
		"st" => "ое",
		"nd" => "ое",
		"rd" => "е",
		"th" => "ое"
		);
		
		if (func_num_args() > 1) {
			$timestamp = func_get_arg(1);
			return strtr(date(func_get_arg(0), $timestamp), $translate);
		} else {
			return strtr(date(func_get_arg(0)), $translate);
		}
	}
	//=============================================================================
	
	//=================================================================
	// Функция преобразования даты из базы mysql в календарный вид
	//=================================================================
	function ref_date($date)
	{
		preg_match("|([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})\s([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})|",$date,$out);
		return "$out[3].$out[2].$out[1] $out[4]:$out[5]:$out[6]";
	}
	
	//=================================================================
	// Функция поиска вложений для сообщения
	//=================================================================
	function find_attaches($messageid)
	{
		$attch_path = PATCH_ATTACHES.$messageid."/";
				
		if (file_exists($attch_path)) {
		
			//перед выводом блока определим, есть ли в каталоге файлы кроме картинок
			$dir_pointer = opendir ($attch_path);
			$counttrueattach = 0;
			while (($res = readdir($dir_pointer))!==FALSE){
				if ($res != "." && $res != ".." && $res != "Index.php"){
					
					if ($this->image_valid(mime_content_type($attch_path.$res) == FALSE ) ){										
						$counttrueattach = $counttrueattach +1;
					}
				}	
			}	
	
		return $counttrueattach;
	
		}
	}
	//=================================================================
	
	//=================================================================
	// Функция поиска аватара
	//=================================================================
	function find_avatar($userid)
	{			
		$avatars_path = ".".PATCH_AVATARS.$userid."/";
		$countavatar = 0;
		$avatarpicture = "";	
		if (file_exists($avatars_path)) {
			$dir_pointer = opendir ($avatars_path);
		

			while (($res = readdir($dir_pointer))!==FALSE){
					if ($res != "." && $res != ".." && $res != "Index.php"){
						
						if ($this->image_valid(mime_content_type($avatars_path.$res) == TRUE ) ){										
							$countavatar = 1;
							$avatarpicture = $res;
							
						}
					}	
			}	
		}
	
		return $avatarpicture;
	
		
	}
	//=================================================================
	
	
	
	
	
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
	
}
?>