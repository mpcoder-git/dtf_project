<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Login extends Controller
{
	//================================================================================
	//функция по умолчанию - отображает страницу авторизации
	//================================================================================
	function action_index()
    { 

		$this->db_connect();
		//$this->model = new Model_Login();
		
		$parameters = $this->get_parameters('Вход');
		
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		$ukey = $this->encode_pass($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].session_id());
		$parameters['ukey'] = $ukey;
		
		$this->view->generate('login_view.php', 'template_view.php', $parameters);
		
    }

	
	//================================================================================
	//функция обрабатывает отправленную форму авторизации
	//================================================================================
	function action_postloginform()
    { 
	
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
			
			//проверка уникального кода пользователя (ип адрес, юзер агент, номер сессии) кто отправил тот и должен принять
			if ($this->encode_pass($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].session_id()) <> $_POST["ukey"] ){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
			}	
			
			
			//если пост переменные не существуют
			if(!isset($_POST["useremail"]) || !isset($_POST["password"])){
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit;
			}
			
			//если пост переменные пустые
			if(empty($_POST['useremail']) || empty($_POST['password'])) {
				$_SESSION['session_error'] = 4; //Все поля должны быть заполнены!
				header("Location: /error");
				exit;
			}
			
			//если присланы данные длиннее заданых значений, то заполнял бот
			if (strlen(trim($_POST["useremail"])) > 30 || strlen(trim($_POST["password"])) > 30)
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
			if (strpos(trim($_POST["useremail"]),' ') == true || strpos(trim($_POST["password"]),' ') == true)
			{ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			
			//если сессия есть и пользователь залогинен
			if(isset($_SESSION["session_userid"])){
			header("Location: /");
			exit;
			}
			
			//пауза 1 сек для защиты от перебора паролей
			sleep(1);
			
			$this->db_connect();
			$this->model = new Model_Login();
			
			//если поля емайла и пароля существуют
			if(isset($_POST["useremail"]) && isset($_POST["password"])){
			
				//если поля емайла и пароля не пустые
				if(!empty($_POST['useremail']) && !empty($_POST['password'])) {
									
					//проверка емайла на валидность
					if (!filter_var($_POST["useremail"], FILTER_VALIDATE_EMAIL)) 
					{
						$_SESSION['session_error'] = 2; //несуществующий емайл
						header("Location: /error");
						exit;
					}
					
					$useremail=$this->filter_badtext($_POST['useremail']);
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
							//print $hash.' '.$dbuseremail; exit;
							$query_result = $this->model->update_hash($this->con, $hash, $dbuserid);
							
							//адрес почты не подтвержден
							if ($emailactive == 0){
								
								$mailSMTP = new SendMailSmtpClass(SMTP_EMAIL_ADRESS, SMTP_EMAIL_PASSWORD, SMTP_EMAIL_SERVER, SMTP_EMAIL_SERVERPORT, SMTP_EMAIL_ENCODING); // создаем экземпляр класса
								
								
								//отправить письмо						
								$subject = 'Активации Email на DayTradersFactory.com';							
								//$message = 'Для подтверждения email адреса Вам нужно пройти по ссылке <a href="'.DOMAIN_NAME.'/login/emailactivate/'.$dbuserid.'/'.$hash.'">Подтвердить адрес электронной почты!</a>';
								
								// текст письма
								$message = '
								<html>
								<head>
								  <title>Активация адреса электроной почты</title>
								</head>
								<body>
								  <h3>Активация адреса электроной почты</h3>
								  <p>Для подтверждения email адреса Вам нужно пройти по ссылке <a href="'.DOMAIN_NAME.'/login/emailactivate/'.$dbuserid.'/'.$hash.'">Подтвердить адрес электронной почты!</a></p>
								</body>
								</html>
								';
								
								
								//$headers = "Content-Type: text/html; charset=utf-8\r\n"; 
								//$headers .= "From: DayTradersFactory.com <info@daytradersfactory.com>\r\n\r\n";
								
								// от кого
								$from = array(
								SMTP_EMAIL_FROMNAME, // Имя отправителя
								SMTP_EMAIL_FROMEMAIL // почта отправителя
								);
								
								
								//отправка письма
								//mail($useremail, $subject , $message, $headers);
								$result =  $mailSMTP->send($useremail, $subject, $message , $from);
								if($result === true){
									$_SESSION['session_message'] = 4; //не подтвержден емайл
									header("Location: /message");
									//echo "Done";
								}else{
									$_SESSION['session_error'] = 30; //Ошибка при отправке почты
									header("Location: /error");
									//echo "Error: " . $result;
								}
								
														
								exit;
							} else {
								
								//адрес активирован - заходим в кабинет
								$_SESSION['session_userid']=$dbuserid;
								$_SESSION['session_userhash']=$hash;
			 
								mysqli_free_result($this->model->query_result);
								header("Location: /cabinet");
							}
							
						} else {
								mysqli_free_result($query_result); 
								mysqli_close($this->con);
								$_SESSION['session_error'] = 3; //Неправильное имя или пароль
								header("Location: /error");
							
						}
					
					
				
					} else {

							mysqli_free_result($query_result); 
							mysqli_close($this->con);
							$_SESSION['session_error'] = 3; //пользователь не существует
							header("Location: /error");	
					}
				
				} 

			
			}

		} else {
		//если не существует кнопки login
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		
		}
		
    
	}
	//===============================================================================
	
	//================================================================================
	//функция активации почтового адреса
	//================================================================================
	function action_emailactivate($userid,$userhash)
    { 
		//параметры не заданы
		if (!isset($userid) || !isset($userhash)){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		if (isset($userid) && isset($userhash)){
			
			$verifyuserid = (int)$userid;
			$userid = $verifyuserid;
			
			$this->db_connect();		
			$this->model = new Model_Login();
			$usermodel = new Model();
			
			$query_result = $usermodel->select_userdata($this->con,$userid);
			$row = mysqli_fetch_row($query_result);
			$dbuserid = $row[0];
			$dbhash = $row[8];
			
			//пользователь не найден
			if (mysqli_num_rows($query_result)==0){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}
			
			//если запись найдена
			if (mysqli_num_rows($query_result)==1){
				
				if ($dbhash != $userhash){
					$_SESSION['session_error'] = 1; 
					header("Location: /error");	
					exit;
				}
								
				if ($dbhash == $userhash){
					//активируем адрес
					$this->model->update_mailactive($this->con, $dbuserid);
					
					$_SESSION['session_userid']=$dbuserid;
					$_SESSION['session_userhash']=$dbhash;
					
					header("location: /cabinet");
					exit;
					
				}
				
			}
		}
	}
	//===============================================================================
}

?>