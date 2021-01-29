<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Recoverypassword extends Controller
{
	
	function action_index()
    { 
	
		$this->db_connect();
		
		//$this->model = new Model_Recoverypassword();
		
		$parameters = $this->get_parameters('Восстановление пароля');		
		
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		$this->view->generate('recoverypassword_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
    }
	//================================================================================
	
	//================================================================================
	//функция отправки письма с ссылкой восстановления
	//================================================================================
	function action_sendrecoveryemail()
    {
		//если сессия есть и пользователь залогинен
		if(isset($_SESSION["session_userid"])){
		header("Location: /");
		exit;
		}
		
		if (!isset($_POST['sendrecoverybutton'])){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		if (isset($_POST['sendrecoverybutton'])){
			
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
			if(!isset($_POST["useremail"]) ){
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit;
			}
			
			//если пост переменные пустые
			if(empty($_POST['useremail']) ) {
				$_SESSION['session_error'] = 4; //Все поля должны быть заполнены!
				header("Location: /error");
				exit;
			}
			
			//если присланы данные длиннее заданых значений, то заполнял бот
			if (strlen(trim($_POST["useremail"])) > 30 )
			{ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			
			//если найден пробел в переменной, полагаем там инъекция (слово должно быть одним)
			if (strpos(trim($_POST["useremail"]),' ') == true)
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
			
			
			if(isset($_POST["useremail"]) && !empty($_POST['useremail'])){
				
				//проверка емайла на валидность
				if (!filter_var($_POST["useremail"], FILTER_VALIDATE_EMAIL)) 
				{
					$_SESSION['session_error'] = 2; //несуществующий емайл
					header("Location: /error");
					exit;
				}
				
				$useremail=$this->filter_badtext($_POST['useremail']);
				
				$this->db_connect();
				$usermodel = new Model();
				$this->model = new Model_Recoverypassword();
				
				$query_result = $this->model->select_userdataforemail($this->con, $useremail);					
				$numrows=mysqli_num_rows($query_result);				
				
				//пользователь не найден
				if($numrows==0){
					mysqli_free_result($query_result); 
					mysqli_close($this->con);
					$_SESSION['session_error'] = 3; //Неправильное имя или пароль
					header("Location: /error");
				}
				
				if($numrows!=0)
				{
					$row = mysqli_fetch_row($query_result);					
					$userid = $row[0];
					mysqli_free_result($query_result);
					
					$query_result = $this->model->select_cntrecoveryrows($this->con, $userid);
					$row = mysqli_fetch_row($query_result);				
					$recoveryrow = $row[0];
					mysqli_free_result($query_result);
					
					$accesshash = md5($this->generateCode());
					$date = date("Y-m-d H:i:s");
					//если запись о восстановлении не найдена, то создадим ее. Иначе обновим хэш и дату
					if ($recoveryrow == 0){	
																			
						$this->model->insert_newrecoveryrows($this->con,$userid,$date,$accesshash);
					
					} else {
					//если строка найдена, обновим хэш и дату запроса
						$this->model->update_recoveryrows($this->con,$userid,$date,$accesshash);					
					}
					
					$mailSMTP = new SendMailSmtpClass(SMTP_EMAIL_ADRESS, SMTP_EMAIL_PASSWORD, SMTP_EMAIL_SERVER, SMTP_EMAIL_SERVERPORT, SMTP_EMAIL_ENCODING); // создаем экземпляр класса
					
					//Шлем письмо для восстановления пароля
					$subject = 'Восстановление пароля на DayTradersFactory.com';
					//$subject = convert_cyr_string($subject,'w','k'); 			
					//$message = 'Для смены пароля Вам нужно пройти по ссылке <a href="'.DOMAIN_NAME.'recoverypassword/newpassword/'.$userid.'/'.$accesshash.'">Получить новый пароль!</a>';					
					
					// текст письма
					$message = '
					<html>
					<head>
					<title>Восстановление пароля</title>
					</head>
					<body>
					<h3>Восстановление пароля</h3>
					<p>Для смены пароля Вам нужно пройти по ссылке <a href="'.DOMAIN_NAME.'/recoverypassword/newpassword/'.$userid.'/'.$accesshash.'">Получить новый пароль!</a></p>
					</body>
					</html>
					';
					
					//$headers = "Content-Type: text/html; charset=utf-8\r\n"; 
					// от кого письмо 
					//$headers .= "From: DayTradersFactory.com <info@daytradersfactory.com>\r\n\r\n";
					
					// от кого
					$from = array(
					SMTP_EMAIL_FROMNAME, // Имя отправителя
					SMTP_EMAIL_FROMEMAIL // почта отправителя
					);
					
					//mail($email, $subject , $message, $headers);					
					$result =  $mailSMTP->send($useremail, $subject, $message , $from);
					
					if($result === true){
						$_SESSION['session_message'] = 3; //письмо с инструкцией восстановления пароля отправлено
						header("Location: /message");	
						//echo "Done";
					}else{
						$_SESSION['session_error'] = 30; //Ошибка при отправке почты
						header("Location: /error");
						//echo "Error: " . $result;
					}
					
					
					mysqli_close($this->con);	
										
					exit;	
					
				}
			}
			
			
			
		}
		
	}
	//================================================================================
	
	//================================================================================
	//функция показывает окно ввода нового пароля
	//================================================================================
	function action_newpassword($userid,$accesshash)
    {
		//если сессия есть и пользователь залогинен
		if(isset($_SESSION["session_userid"])){
		header("Location: /");
		exit;
		}
		
		if (!isset($userid) || !isset($accesshash) ){
			$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
			header("Location: /error");
			exit;
		}
		
		if (isset($userid) && isset($accesshash) ){
			
			if (empty($userid) || empty($accesshash)){
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit;
			}
			
			if (!empty($userid) && !empty($accesshash)){
				
				//верификация переменных
				//если присланы данные длиннее заданых значений, то заполнял бот
				if (strlen(trim($accesshash)) > 32 )
				{ 
					$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
					header("Location: /error");
					exit; 
				}
				
				$verifyuserid = (int)$userid;
				if ($verifyuserid == 0 ){ Route::ErrorPage404(); }
				
				$userid = $verifyuserid;
				
				$this->db_connect();
		
				$usermodel = new Model();
				$this->model = new Model_Recoverypassword();
				
				$query_result = $this->model->select_recoveryrow($this->con, $userid);
				
				//не найдено записи запроса на восстановление
				if (mysqli_num_rows($query_result)==0){
					$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
					header("Location: /error");
					exit; 
				}
				
				if (mysqli_num_rows($query_result)==1){
					$row = mysqli_fetch_row($query_result);
					$dbhash = $row[0];
					$dbcreatedata = $row[1];
					
					//сравним хэши
					if ($dbhash != $accesshash){
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}
					//хэши равны]
					if ($dbhash == $accesshash){
						
						//сравним даты начала записи и даты запроса по ссылке
						//разница не должно превышать 15 минут
						$date = date("Y-m-d H:i:s");

						if ($dbcreatedata < $date){
							
							//между отсылкой письма и нажатием на ссылку должно пройти не более 15 минут			
							$rminuts = ((strtotime($date) - strtotime($dbcreatedata)) / 60);		
							$rminuts = floor($rminuts);

							//если интервал больше 15 минут, то предупреждаем о необходимости делать запрос снова
							if ($rminuts > MAX_RECOVERY_MINUTS){								
								//время для восстановления истекло
								//echo "Время для восстановления пароля истекло. Попробуйте сделать запрос еще раз. Время до окончания   -15 минут ";
								$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
								header("Location: /error");
								exit;					
							}

							$parameters = $this->get_parameters('Восстановление пароля');
							
							$skey = $this->generateCode(32);
							$_SESSION['session_skey']=$skey;
							$parameters['skey'] = $skey;
							
							$parameters['userid'] = $userid;
							
							$this->view->generate('newpassword_view.php', 'template_view.php', $parameters);
							mysqli_close($this->con);						

							
						}	
						
					}
				}
			}
			
		}
	}
	//================================================================================
	
	//================================================================================
	//функция сохранения нового пароля
	//================================================================================
	function action_changenewpassword()
    {
		
		//если сессия есть и пользователь залогинен
		if(isset($_SESSION["session_userid"])){
		header("Location: /");
		exit;
		}
		
		if (!isset($_POST['newpasswordsubmit'])){
			$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
			header("Location: /error");
			exit;
		}
		
		if (isset($_POST['newpasswordsubmit'])){
			
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
			
						
			if (!isset($_POST['usernewpass']) || !isset($_POST['userrenewpass']) || !isset($_POST['recoveryid'])){
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit;
			}
			
			if (isset($_POST['usernewpass']) && isset($_POST['userrenewpass']) && isset($_POST['recoveryid']) ){
				
				if (empty($_POST['usernewpass']) || empty($_POST['userrenewpass']) || empty($_POST['recoveryid']) ){
					$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
					header("Location: /error");
					exit;								
				}
				
				if (!empty($_POST['usernewpass']) && !empty($_POST['userrenewpass']) && !empty($_POST['recoveryid'])){
					
					//если присланы данные длиннее заданых значений, то заполнял бот
					if (strlen(trim($_POST["usernewpass"])) > 32 || strlen(trim($_POST["userrenewpass"])) > 32)
					{ 
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}
					
					//длина пароля должна быть не менее 8 символов
					if (strlen(trim($_POST["usernewpass"])) < 8 || strlen(trim($_POST["userrenewpass"])) < 8 )
					{
						$_SESSION['session_error'] = 1; //поскольку длина проверяется формой, то меньшее значение шлет бот
						header("Location: /error");
						exit;
					}
					
					//если найден пробел в переменной, полагаем там инъекция (слово должно быть одним)
					if (strpos(trim($_POST["usernewpass"]),' ') == true || strpos(trim($_POST["userrenewpass"]),' ') == true)
					{ 
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}
					
					$newpass = $_POST['usernewpass'];
					$renewpass = $_POST['userrenewpass'];
					$userid = $_POST['recoveryid'];
								
					if ($newpass != $renewpass){
						//не совпадают новый и повторный пароли
						$_SESSION['session_error'] = 6; //не совпадают новый и повторный пароли
						header("Location: /error");	
						exit;
					}
					
					if ($newpass == $renewpass){
						
						$newpassword = $this->encode_pass(strip_tags(trim($newpass)));
						
						$this->db_connect();
		
						$usermodel = new Model();
						$this->model = new Model_Recoverypassword();
						
						//обновить пароль
						$this->model->update_userpass($this->con,$userid,$newpassword);
						
						//удалить запрос на восттановление
						$this->model->delete_recoveryrow($this->con,$userid);
						
						mysqli_close($his->con);
			
						//переброс на форму входа
						header("location: /login");
						exit;
						
					}
				}
				
			}
			
		}
			
			
			
		}
		
		
	//}
	
	
}

?>