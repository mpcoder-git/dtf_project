<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php
//форма изменения или загрузки аватарки
class Controller_Changeavatar extends Controller
{	
	//==================================================================================
	// Начальная функция
	//==================================================================================
	function action_index()
    { 
		
		$userid = 0;		
		//если в сессии нет переменной ид юзера - то переброс на чистую страницу
		if(!isset($_SESSION["session_userid"])) {
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}

		$this->db_connect();
		
		//$this->model = new Model;
		
		$parameters = $this->get_parameters('Личный кабинет - Изменение главного пароля');
		$parameters['userid'] =$userid;
		
		//генерация ключа для подписи формы быстрого ответа
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		if ($truehash > 0){		
			$countavatar = 0;
			$avatarpicture = $this->view->find_avatar($userid);
			
			if ($avatarpicture !== "") { $countavatar = 1; }
			$parameters['avatarpicture'] =$avatarpicture;
			$parameters['countavatar'] =$countavatar;
						
			$this->view->generate('changeavatar_view.php', 'template_view.php', $parameters);
			
		} 
				
    }

	//==================================================================================
	// Функция удаление аватара
	//==================================================================================
    function action_deleteavatar()
	{		
		$userid = 0;
		//если пользователь не залогинен
		if(!isset($_SESSION["session_userid"])) {
			$_SESSION['session_error'] = 1; 
			header("Location: ../error.php");	
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}
		
		$this->db_connect();
		
		$truehash = $this->get_truehash($userid);

		if ($truehash == 0){
			$_SESSION['session_error'] = 1; 
			header("Location: ../error.php");	
			exit;		
		}
		
		
		
		$target_path = ".".PATCH_AVATARS.$userid."/";
		$this->delete_dir($target_path);
		
		header("Location: /changeavatar");
		

    }
	//==================================================================================
	
	//==================================================================================
	// Функция загрузки аватара
	//==================================================================================
    function action_uploadavatar()
	{
		$userid = 0;
		//если пользователь не залогинен
		if(!isset($_SESSION["session_userid"])) {
			$_SESSION['session_error'] = 1; 
			header("Location: /error.php");	
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}
		
		$this->db_connect();
		
		//проверка подлинности пользователя по хэшу
		$truehash = $this->get_truehash($userid);

		if ($truehash == 0){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;		
		}
		
		if (!isset($_POST["postfile"])){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;	
		}
		
		if (isset($_POST["postfile"])){
			
			//проверка одноразового токена - если токен не совпадает, выводим пустую страницу
			if ($_SESSION['session_skey'] <> $_POST["skey"] ){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
			}	
			
			unset($_SESSION['session_skey']);
						
			//если поле почты имени или капчи заполнено,  то считаем что робот заполняет форму
			if (($_POST["email"] <> '') || ($_POST["captcha"] <> '') || ($_POST["name"] <> '') ){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
			}
			
			//если не совпадает номер сессии, то форма отправлена с другого места а не с сайта
			if($_POST['snumber'] != md5(md5(session_id()))) 
			{ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			
			
			#проверка переменных на существование
			if(!isset($_POST['MAX_FILE_SIZE']) || !isset($_POST['userid'])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}
			
			#проверка переменных на существование
			if(isset($_POST['MAX_FILE_SIZE']) && isset($_POST['userid'])){
				
				//проверка переменных на пустоту
				if(empty($_POST['MAX_FILE_SIZE']) || empty($_POST['userid'])){
					$_SESSION['session_error'] = 1; 
					header("Location: /error");	
					exit;
				}
				
				if(!empty($_POST['MAX_FILE_SIZE']) && !empty($_POST['userid'])){
					
					//проверки файла
					$original_filename = $_FILES['userfile']['name'];
					$temp_filename = $_FILES['userfile']['tmp_name'];
					$filesize = $_FILES['userfile']['size'];
					$filetype = $_FILES['userfile']['type'];
					
					//проверка не пустое ли название
					if (empty($_FILES['userfile']['name'])){

						$_SESSION['session_error'] = 20; //не выбран файл для загрузки
						header("Location: /error");
						exit;
					}
					
					//проверка загрузился ли файл
					if (!is_uploaded_file($temp_filename)) {
						//print "ошибка загрузки файла на сервер";
						$_SESSION['session_error'] = 24; //ошибка загрузки файла на сервер
						header("Location: /error");						
						exit;
					}
					
					
					//сообщение о превышении размеров закачиваемого файла
					if ($filesize > MAX_AVATARFILE_SIZE || $filesize == 0){
						//echo "Размер загружаемого файла превышает 100 кб! Выберите файл меньшего размера!";
						$_SESSION['session_error'] = 21; 
						header("Location: /error");
						exit;
					}
					
					//проверка Content-Type
					if(($filetype != "image/gif") && ($filetype != "image/jpg") 
						&& ($filetype != "image/jpeg") && ($filetype != "image/png")) {
					   //echo "Sorry, we only allow uploading GIF images";
						$_SESSION['session_error'] = 27; //недопустимый тип файла (не картинка)
						header("Location: /error");
					   exit;
					}
					
					//Проверка содержания файла изображения
					$imageinfo = getimagesize($temp_filename);
					if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpg' && $imageinfo['mime'] != 'image/jpeg'  && $imageinfo['mime'] != 'image/png' ) {
					  //echo "Sorry, we only accept GIF and JPEG images\n";
						$_SESSION['session_error'] = 27; //недопустимый тип файла (не картинка)
						header("Location: /error");
					  exit;
					}
					
					
					$target_path = ".".PATCH_AVATARS.$userid."/";
					//поиск каталога
					if (!file_exists($target_path)) {
						//создадим папку
						$this->create_dir($target_path);
					}
					
					//поиск каталога
					if (file_exists($target_path)) {

						//если папка найдена, то удалим аватар, потому что аватар может быть только 1
						$dir_pointer = opendir ($target_path);
						while (($res = readdir($dir_pointer))!==FALSE){
							if ($res != "." && $res != ".." && $res != "Index.php"){
								$file_for_del=$target_path.$res;
								 unlink($file_for_del); 
							}								
						}	
						closedir ($dir_pointer);

					} 
										
					//Проверка расширения загружаемого файла
					$whitelist = array("gif", "jpg", "png");
					//получить расширение файла
					$ext = pathinfo($original_filename, PATHINFO_EXTENSION);
					
					if (!in_array($ext, $whitelist)) {
						$_SESSION['session_error'] = 27; //недопустимый тип файла (не картинка)
						header("Location: /error");
						exit;
					}
					
					//если расширение загружаемого файла есть в белом листе
					if (in_array($ext, $whitelist)) {
						
						//по идее надо сделать защиту от инъекций кода в картинки
						//делаем это через создание копии файла
						
						$resaveimage = $this->image_resave($temp_filename, $original_filename, $target_path);
						
						if (!$resaveimage){ 
							$_SESSION['session_error'] = 28; //невозможно записать файл на диск
							header("Location: /error");
							exit;						
						}
						
						/*
							if ($ext == 'jpg')
							{									
								$image = imagecreatefromjpeg($temp_filename);
								if(!$image){ print 'невозможно создать картинку'; exit; }
								// получение размеров
								$width = 0; $height = 0;
								list($width, $height) = getimagesize($temp_filename);
								$new_width = $width;  $new_height = $height;
								$image_clone = imagecreatetruecolor($width, $height);
								imagecopyresampled($image_clone, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
								// Сохраняем изображение в 'simpletext.jpg'
								imagejpeg($image_clone, $target_path.$original_filename);								
							}
							
							if ($ext ==  'gif') 
							{
								$image = imagecreatefromgif($temp_filename);
								if(!$image){ print 'невозможно создать картинку'; exit; }
								// получение размеров
								$width = 0; $height = 0;
								list($width, $height) = getimagesize($temp_filename);
								$new_width = $width;  $new_height = $height;
								$image_clone = imagecreatetruecolor($width, $height);
								imagecopyresampled($image_clone, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
								// Сохраняем изображение в 'simpletext.jpg'
								imagegif($image_clone, $target_path.$original_filename);
								
							}
							
							if ($ext ==  'png') 
							{	
								$image = imagecreatefrompng($temp_filename);
								if(!$image){ print 'невозможно создать картинку'; exit; }
								// получение размеров
								$width = 0; $height = 0;
								list($width, $height) = getimagesize($temp_filename);
								$new_width = $width;  $new_height = $height;
								$image_clone = imagecreatetruecolor($width, $height);
								imagecopyresampled($image_clone, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
								// Сохраняем изображение в 'simpletext.jpg'
								imagepng($image_clone, $target_path.$original_filename);
							}	
							*/
						
						//удалим временный файл за ненадобностью
						if (file_exists($target_path.$original_filename)) {
							//если найден сохраненый файл, то временный можно удалить
							unlink($temp_filename);
						}
						//закачиваем файл
						
						//$target_path = PATCH_AVATARS.$userid."/";
						
						/*else {							
							//создадим папку
							$this->create_dir($target_path);							
						}
						
						$target_path2 = $target_path . basename( $_FILES['userfile']['name']); 
						
						if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
							//перемещение файла
							if(move_uploaded_file($_FILES['userfile']['tmp_name'], $target_path2)) {							
							} else{
								//echo "Произошла ошибка при перемещении файла в папку";
								$_SESSION['session_error'] = 23; 
								header("Location: /error");
								exit;								
							}
						}
						*/
						//создадим файл заглушку
						
						$this->create_indexphp($target_path);
						
						mysqli_close($this->con);
						header("Location: /changeavatar");
					}
					
				}
				
			}
			
			
			
		}
		
		
	
	}
	//==================================================================================
}

?>
