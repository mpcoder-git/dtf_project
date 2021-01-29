<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
//форма изменения регистрационных данных
class Controller_Changerd extends Controller
{	
	
	function action_index()
    { 
		
		
		
		if(!isset($_SESSION["session_userid"])) {
			$_SESSION['session_error'] = 1; 
			header("Location: /Error");	
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}

		$this->db_connect();
		
		//$this->model = new Model;
		
		$parameters = $this->get_parameters('Личный кабинет - Изменение регистрационных данных');
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
					
		if ($truehash > 0){		
			
			$user_model = new Model();			
			$query_result = $user_model->select_userdata($this->con,$userid);
			$row = mysqli_fetch_row($query_result);
								
				$nick = $row[4];
				$name = $row[5];
				$family = $row[6];
				$otch = $row[7];
				$email = $row[1];
			
				$parameters['nick'] = $nick;
				$parameters['name'] = $name;
				$parameters['family'] = $family;
				$parameters['otch'] = $otch;
				$parameters['email'] = $email;
				
				$skey = $this->generateCode(32);
				$_SESSION['session_skey']=$skey;
				$parameters['skey'] = $skey;				
						
			$this->view->generate('changerd_view.php', 'template_view.php', $parameters);
			mysqli_close($this->con);
		} 
				
    }
	//==================================================================================
	
	//==================================================================================
	// Функция отправки формы
	//==================================================================================
    function action_postchangerdform(){

		$userid = 0;
		//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}
		
		
		$this->db_connect();
		$this->model = new Model_Changerd();
		$usermodel = new Model();
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		
		if (!isset($_POST["rdsubmit"])){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		if (isset($_POST["rdsubmit"])){
		
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
			if(!isset($_POST['usernick']) || !isset($_POST['username']) || !isset($_POST['userfamily']) 
										|| !isset($_POST['userotch']) || !isset($_POST['useremail']) ) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}
			
			#проверка переменных сли все существуют
			if(isset($_POST['usernick']) && isset($_POST['username']) && isset($_POST['userfamily']) 
										&& isset($_POST['userotch']) && isset($_POST['useremail']) ) 
			{
				
				#проверка переменных на пустоту
				if(empty($_POST['usernick']) || empty($_POST['username']) || empty($_POST['userfamily']) 
											|| empty($_POST['userotch']) || empty($_POST['useremail']) ) 
				{
					$_SESSION['session_error'] = 1; 
					header("Location: /error");	
					exit;			
				}
				
				#проверка переменных если все не пусты
				if(!empty($_POST['usernick']) && !empty($_POST['username']) && !empty($_POST['userfamily']) 
											&& !empty($_POST['userotch']) && !empty($_POST['useremail']) ) 
				{
					
					//если присланы данные длиннее заданых значений, то заполнял бот
					if (mb_strlen(trim($_POST["usernick"]),'UTF-8') > 20 || mb_strlen(trim($_POST["username"]),'UTF-8') > 20 || mb_strlen(trim($_POST["userfamily"]),'UTF-8') > 30
											|| mb_strlen(trim($_POST["userotch"]),'UTF-8') > 30 || mb_strlen(trim($_POST["useremail"]),'UTF-8') > 30)
					{ 
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}

					//если найден пробел в переменной, полагаем там инъекция (слово должно быть одним)
					if (strpos(trim($_POST["username"]),' ') == true || strpos(trim($_POST["userfamily"]),' ') == true
										|| strpos(trim($_POST["userotch"]),' ') == true || strpos(trim($_POST["useremail"]),' ') == true)
					//strpos(trim($_POST["usernick"]),' ') == true ||	для ника допустимо 2 слова				{ 
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}
					
					//проверка емайла на валидность
					if (!filter_var($_POST["useremail"], FILTER_VALIDATE_EMAIL)) 
					{
						$_SESSION['session_error'] = 2; //несуществующий емайл
						header("Location: /error");
						exit;
					}
					
					//подготовка переменных
					$usernick = $this->filter_badtext($_POST['usernick']);
					$username = $this->filter_badtext($_POST['username']);
					$userfamily = $this->filter_badtext($_POST['userfamily']);
					$userotch = $this->filter_badtext($_POST['userotch']);
					$useremail = $this->filter_badtext($_POST['useremail']);
					
					//получить почту пользователя
					$query_result = $usermodel->select_userdata($this->con,$userid);
					$row = mysqli_fetch_row($query_result);
					$dbuseremail= $row[1];//пароль пользователя
					mysqli_free_result($query_result); 
					
					$var_quit = 0;
					//сверим старый и введенный емайл, если отличаются - то деактивируем емайл
					if ($dbuseremail != $useremail){
						$this->model->update_noactivateemail($this->con,$userid);
						$var_quit = 1;
					}
					
					//обновим пользовательские данные
					$this->model->update_userdata($this->con,$userid,$usernick,$username,$userfamily,$userotch,$useremail);
					
					mysqli_close($this->con);
					
					if ($var_quit == 0){						
						header("Location: /cabinet");
						exit;
					}
					
					if ($var_quit == 1){
						$_SESSION['session_message'] = 5; //предупреждение о смене емайл адреса
						header("Location: /message");
					}
					
				}
		
			}

		}
	}
//}

?>