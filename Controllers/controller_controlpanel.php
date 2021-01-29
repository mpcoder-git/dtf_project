<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php'); 	
?>
<?php 
class Controller_Controlpanel extends Controller
{
	
	function action_index()
    { 
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php'); 			
			
		$this->db_connect();
		//$this->model = new Model_Main();
						
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
						
		if (!isset($_SESSION['session_adminid'])){
			$parameters = $this->get_parameters('Вход для администратора');
			$parameters['skey'] = $skey;
			$this->view->generate('cplogin_view.php', 'cptemplate_view.php', $parameters);
		} else {
			$parameters = $this->get_parameters('Панель управления сайтом');
			$parameters['skey'] = $skey;
			$this->view->generate('cppanel_view.php', 'cptemplate_view.php', $parameters);	
		}
			
		
		
    }
	
	//=============================================================================================
	// Функция азлогинивания из кабинета
	//=============================================================================================
	function action_logout()
    {
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');
		
		$this->db_connect();
		$this->model = new Model_Controlpanel();
		$this->model->null_hash($this->con, $_SESSION['session_adminid']);
				
		unset($_SESSION['session_adminid']);
		unset($_SESSION['session_userhash']);
		session_destroy();
		header("location: /");
	}
	
	
	//=============================================================================================
	// Функция обработки формы логина
	//=============================================================================================
	function action_postloginform()
    {
		
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');
		
		if (!isset($_POST["login"])){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		if (isset($_POST["login"])){
		
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
			
			//если пост переменные не существуют
			if(!isset($_POST["admlogin"]) || !isset($_POST["password"])){
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit;
			}
			
			//если пост переменные пустые
			if(empty($_POST['admlogin']) || empty($_POST['password'])) {
				$_SESSION['session_error'] = 4; //Все поля должны быть заполнены!
				header("Location: /error");
				exit;
			}
			
			//если присланы данные длиннее заданых значений, то заполнял бот
			if (strlen(trim($_POST["admlogin"])) > 32 || strlen(trim($_POST["password"])) > 32)
			{ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			//длина пароля должна быть не менее 8 символов
			if (strlen(trim($_POST["password"])) < 8)
			{
				$_SESSION['session_error'] = 1; //поскольку длина проверяется формой, то меньшее значение шлет бот
				header("Location: /error");
				exit;
			}
			
			
			//если найден пробел в переменной, полагаем там инъекция (слово должно быть одним)
			if (strpos(trim($_POST["admlogin"]),' ') == true || strpos(trim($_POST["password"]),' ') == true)
			{ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			
			//если сессия есть и пользователь залогинен
			if(isset($_SESSION["session_userid"])){
			header("Location: /controlboard/panel");
			exit;
			}
			
			//пауза 1 сек для защиты от перебора паролей
			sleep(1);
			
			$this->db_connect();
			$this->model = new Model_Controlpanel();
			
			//если поля емайла и пароля существуют
			if(isset($_POST["admlogin"]) && isset($_POST["password"])){
			
				//если поля емайла и пароля не пустые
				if(!empty($_POST['admlogin']) && !empty($_POST['password'])) {
									
					//проверка емайла на валидность
					if (!filter_var($_POST["admlogin"], FILTER_VALIDATE_EMAIL)) 
					{
						$_SESSION['session_error'] = 2; //несуществующий емайл
						header("Location: /error");
						exit;
					}
					
					$useremail=$this->filter_badtext($_POST['admlogin']);
					$password=$this->encode_pass(strip_tags(trim($_POST['password'])));
					
					$query_result = $this->model->select_loginuserdata($this->con, $useremail);
					
					$numrows=mysqli_num_rows($query_result);
					
					if($numrows!=0)
					{
						$row = mysqli_fetch_row($query_result);
						$dbuserid=$row[0];
						$dbuseremail=$row[1];
						$dbpassword=$row[2];
						$emailactive=$row[3];
						
						//если введенные емайл и пароль совпадают с данными из базы
						if($useremail == $dbuseremail && $password == $dbpassword)
						{
							
							# Генерируем случайное число и шифруем его
							$hash = md5($this->generateCode(10));
							$query_result = $this->model->update_hash($this->con, $hash, $dbuserid);
							
							if ($emailactive == 1){
							
								$_SESSION['session_adminid']=$dbuserid;
								$_SESSION['session_userhash']=$hash;
								
								mysqli_free_result($query_result);
								
								header("Location: /controlpanel");
							}
							
							
						} else {
							mysqli_free_result($query_result); 
							mysqli_close($this->con);
							$_SESSION['session_error'] = 3; //Неправильное имя или пароль
							header("Location: /error");							
						}
					}
					
					
				}
			}
			
						
		}
	}
	//========================================================
	
	//=============================================================================================
	// Функция отображения списка пользователей
	//=============================================================================================
	function action_wsserver()
    {
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');			
			
		$this->db_connect();
		$this->model = new Model_Controlpanel();
						
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		if (!isset($_SESSION['session_adminid'])){
			Route::ErrorPage404();
		} else {
			$parameters = $this->get_parameters('Управление websocket сервером');
			
			
			
			$this->view->generate('cpwsserver_view.php', 'cptemplate_view.php', $parameters);


		}
			
		
	}
	//=============================================================================================
	
	
	
	
	
	//=============================================================================================
	// Функция отображения списка пользователей
	//=============================================================================================
	function action_users()
    {
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');			
			
		$this->db_connect();
		$this->model = new Model_Controlpanel();
						
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		if (!isset($_SESSION['session_adminid'])){
			Route::ErrorPage404();
		} else {
			$parameters = $this->get_parameters('Управление пользователями');
			
			if (isset($_POST['sendfindusers'])){
			
				if (isset($_POST['searchselect']) && $_POST['searchselect'] == 1) { $column = 'family';}
				if (isset($_POST['searchselect']) && $_POST['searchselect'] == 2) { $column = 'email';}
				
				if (isset($_POST['searchtext'])) { $parameters['searchtext'] =$_POST['searchtext']; } 
				if (isset($_POST['searchselect'])) { $parameters['searchselect'] =$_POST['searchselect']; }
				
				$query_result = $this->model->select_searchusers($this->con,$column,$_POST['searchtext']);		
				$parameters['searchusers_result'] =$query_result;
			}
			
			$this->view->generate('cpusers_view.php', 'cptemplate_view.php', $parameters);

			
		}
			
		
	}
	//=============================================================================================
	
	//=============================================================================================
	// Функция активации пользователей
	//=============================================================================================
	function action_useractivate($userid=0)
    {
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');
		
		if (!isset($_SESSION['session_adminid'])){
			Route::ErrorPage404();
		}
		
		if (isset($_SESSION['session_adminid'])){
		
			$this->db_connect();
			$this->model = new Model_Controlpanel();
			
			
			$this->model->update_useractivate($this->con,$userid);
						
			mysqli_close($con);			
			
			header("Location: /controlpanel/users"); 
			exit(); 
		
		}
	
	}
	//=============================================================================================
	
	//=============================================================================================
	// Функция отображения списка новостей
	//=============================================================================================
	function action_news()
    {
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');			
			
		$this->db_connect();
		$this->model = new Model_Controlpanel();
						
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		if (!isset($_SESSION['session_adminid'])){
			Route::ErrorPage404();
		} else {
			$parameters = $this->get_parameters('Управление новостями');
			
			$query_result = $this->model->select_news($this->con);		
			$parameters['news_result'] =$query_result;
			
			$this->view->generate('cpnews_view.php', 'cptemplate_view.php', $parameters);

			mysqli_free_result($query_result);
			//mysqli_close($con);
		}
			
		
	}
	//=============================================================================================
	
	//=============================================================================================
	// Функция едактирования новостей
	//=============================================================================================
	function action_newseditor($newsid=null)
    {
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');			
		
		$userid = 0;
		
		$this->db_connect();
		$this->model = new Model_Controlpanel();
						
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		//$parameters['skey'] = $skey;
		
		if (!isset($_SESSION['session_adminid'])){
			Route::ErrorPage404();
		} else {
			
			$userid = $_SESSION["session_adminid"];
			
			//если параметр не задан - значит создаем новую новость 
			if (!isset($newsid)){
				$parameters = $this->get_parameters('Создание новой новости');
				$parameters['skey'] = $skey;
				
				//проверим, не осталось ли отмененой новости
				$query_result = $this->model->select_notactivenews($this->con);
				$numrows=mysqli_num_rows($query_result);
				

				if ($numrows == 0) {
				//если нет неактивных новостей - создаем неактивную новость и запоминаем его номер
					$date = date("Y-m-d H:i:s");
					$this->model->insert_newnews($this->con,$date);
					$newsid = mysqli_insert_id($this->con);	
				} else {
				//если есть неактивные сообщения - то используем ид неактивного сообщения
					$row = mysqli_fetch_row($query_result);
					$newsid = $row[0];//код новости
				}

				$parameters['newsid'] = $newsid;
				$parameters['userid'] = $userid;
				
				$this->view->generate(CONTROL_PANEL.'cpnewseditor_view.php', CONTROL_PANEL.'cptemplate_view.php', $parameters);

			} else {
				//открываем на редактирование
				$parameters = $this->get_parameters('Редактирование новости');
				$parameters['skey'] = $skey;
				$parameters['userid'] = $userid;
				
				//проверка номера новости
				$vnid = $this->number_valid($newsid);
				if (!$vnid){ Route::ErrorPage404(); } else { $verifynid = (int)$newsid; }				
				$newsid  = $verifynid;
				
				$query_result = $this->model->select_onenews($this->con,$newsid);		
				$columns = mysqli_fetch_row($query_result);
				$nid = $columns[0];
				$ndata = $columns[1];
				$ntitle = $columns[2];
				$ntext = $columns[3];
				
				$parameters['newsid'] = $nid;
				$parameters['newsdata'] = $ndata;
				$parameters['newstitle'] = $ntitle;
				$parameters['newstext'] = $ntext;
				
				$this->view->generate('cpnewseditor_view.php', 'cptemplate_view.php', $parameters);
				
				mysqli_free_result($query_result);
				mysqli_close($con);
			}
		}
			
		
	}
	//=============================================================================================
	
	//================================================================================
	//функция отправки формы новой новости
	//================================================================================
	function action_postnewseditorform()
    {
    	
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');		
		
		$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_adminid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_adminid"];
		}
		
		
		$this->db_connect();
		$this->model = new Model_Controlpanel();
		$usermodel = new Model();
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		//если нет кнопки отправки
		if (!isset($_POST["posteditnews"])){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		}

		if (isset($_POST["posteditnews"])){

			//проверка одноразового токена - если токен не совпадает, выводим пустую страницу
			if ($_SESSION['session_skey'] <> $_POST["skey"] ){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
			}	
			
			unset($_SESSION['session_skey']);
						
			//если не совпадает номер сессии, то форма отправлена с другого места а не с сайта
			if($_POST['snumber'] != md5(md5(session_id()))) 
			{ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			
			#проверка переменных на существование
			if(!isset($_POST['newsid']) || !isset($_POST['newstitle']) || !isset($_POST['newseditor'])  ) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}

			#проверка переменных на пустоту
			if(empty($_POST['newsid']) || empty($_POST['newstitle'])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}

			#проверка переменных на пустоту
			if (trim($_POST['newseditor']) == '<br>'){
				$_SESSION['session_error'] = 26; //текст сообщения не должен быть пустым
				header("Location: /error");	
				exit;
			}

			if(isset($_POST['newsid']) && isset($_POST['newstitle']) && isset($_POST['newseditor'])  ) {
				
				//если неверный номер новости
				$newsid = $_POST['newsid'];
				
				$vnid = $this->number_valid($newsid);
				if (!$vnid){ Route::ErrorPage404(); } else { $verifynid = (int)$newsid; }				
				$newsid  = $verifynid;

				$date = date("Y-m-d H:i:s");
				$newstext = mysqli_real_escape_string($this->con,$_POST['newseditor']);
				$newstitle = mysqli_real_escape_string($this->con,$_POST['newstitle']);

				$this->model->update_news($this->con,$newsid,$newstitle,$newstext,$date);
		
						
				mysqli_close($con);			
			
				header("Location: /controlpanel/news"); 
				exit(); 

			}	

		}

    }
    //================================================================================
	
	
	
	//================================================================================
	//функция загрузки вложений
	//================================================================================
	function action_uploadattachfile()
    {
    	
		//проверка существования куки ключа и проверка его значения
		require_once('./Includes/verify_accesscookie.php');		
				
    	$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_adminid"])) {
				exit;
		} else {
			$userid = $_SESSION["session_adminid"];
		}
		
			//если поле почты имени или капчи заполнено,  то считаем что робот заполняет форму
			if (($_POST["email"] <> '') || ($_POST["captcha"] <> '') || ($_POST["name"] <> '') ){
				exit;
			}

		
			//если не совпадает номер сессии, то форма отправлена с другого места а не с сайта
			if($_POST['snumber'] != md5(md5(session_id()))) 
			{ 
				exit;
			}

			if(!isset($_POST["userid"]) || !isset($_POST["newsid"])  ) {
				exit;
			}

			//верификация переменных
			$vnid = $this->number_valid($_POST["newsid"]);
			if (!$vnid){ Route::ErrorPage404(); } else { $verifynid = (int)$_POST["newsid"]; }
			$newsid  = $verifynid;


			$vuid = $this->number_valid($_POST["userid"]);
			if (!$vuid){ Route::ErrorPage404(); } else { $verifyuid = (int)$_POST["userid"]; }	
			$puserid  = $verifyuid;


			$this->db_connect();
			$this->model = new Model_Controlpanel();
			$usermodel = new Model();

			//блок проверки юзера по хэшу
			$truehash = $this->get_truehash($userid);
		
			if ($truehash == 0){	
				exit;
			}

			//проверка загрузился ли файл
			if (!is_uploaded_file($_FILES['attachfile']['tmp_name'])) {
				print "ошибка загрузки файла на сервер";
				exit;
			}

			if (empty($_FILES['attachfile']['name'])){
				print "не выбран файл для загрузки";
				exit;
			}

			//проверка размера файла
			if ($_FILES['attachfile']['size'] > MAX_ATTACHFILE_SIZE || $_FILES['attachfile']['size'] == 0){
				print "превышен размер файла (или если он равен 0 байт)";
				exit;
			}

			//создать каталог для размещения файлов
			$target_path = PATCH_NEWS.$newsid."/";
			$this->create_dir($target_path);

			$target_path2 = $target_path . basename( $_FILES['attachfile']['name']);
				//перемещение файла
			if(!move_uploaded_file($_FILES['attachfile']['tmp_name'], $target_path2)) {
				print "ошибка при перемещении файла в папку назначения";
				exit;			
			} 

			//создать файл заглушку
			$this->create_indexphp($target_path);

			//вернуть разметку с списком вложений
			require_once("./Views/Blocks/cpnewsshowattaches.php"); 

    	
    }
    //================================================================================
	
	//================================================================================
	//функция удаления вложенго файла
	//================================================================================
	function action_deleteattachfile($newsid,$filename)
    { 
	   
		$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_adminid"])) {
			exit;
		} else {
			$userid = $_SESSION["session_adminid"];
		}
		
		//верификация переменных
		if (!isset($newsid) ) {
		print 'Не задан номер новости'; exit;		
		}
		if (!isset($filename) ) {
		print 'Не указан файл'; exit;		
		}	
		
		$vnid = $this->number_valid($newsid);
		if (!$vnid){ Route::ErrorPage404(); } else { $verifynid = (int)$newsid; }	
		$newsid  = $verifynid;
		
		$this->db_connect();
		$this->model = new Model_Controlpanel();
		$usermodel = new Model();
		
		$verifyfilename = (string)$filename;
		$verifyfilename = mysqli_real_escape_string($this->con,strip_tags(trim($verifyfilename)));
		$attch_filename = $verifyfilename;
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
	
		if ($truehash == 0){	
			exit;
		}
		
		$attch_path = PATCH_NEWS.$newsid."/";
		$this->deletfile($attch_path,$attch_filename);
		$cntf = $this->cntfiles_indir($attch_path);
		if ($cntf == 0){ 
			//	если папка пустая - то удалим папку
			$this->delete_dir($attch_path);
		}
	   
	   
	   require_once("./Views/Blocks/cpnewsshowattaches.php");
	}   
    //================================================================================ 
	
	//================================================================================
	//функция  удаления новостей
	//================================================================================
	function action_deletenews($newsid)
    {

    	$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_adminid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_adminid"];
		}

		if (!isset($newsid) ) {
		$_SESSION['session_error'] = 42; //не обнаружен номер сообщения
		header("Location: /error");	
		exit;
		}

		$vnid = $this->number_valid($newsid);
		if (!$vnid){ Route::ErrorPage404(); } else { $verifynid = (int)$newsid; }		
		$newsid  = $verifynid;

		$this->db_connect();
		$this->model = new Model_Controlpanel();
		$usermodel = new Model();

		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}

		//проверка доступа - если не тот юзер или не админ
 
		//удаляем каталог с вложениями если он есть
		$attch_path = PATCH_NEWS.$newsid."/";
		if (file_exists($attch_path)){
		$this->delete_dir($attch_path);
		}
		
		//удалить новость по номеру 
		$this->model->delete_news($this->con,$newsid);

		
		mysqli_close($con);

		header("Location: /controlpanel/news");
		exit;

    }
    //================================================================================
}
?>