<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Partners extends Controller
{
	
	function action_index()
    { 
	
		$this->db_connect();
		
		$parameters = $this->get_parameters('Партнёрам');
		
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
				
				
		$this->view->generate('partners_view.php', 'template_view.php', $parameters);
		
    }
	
	//==================================================================================
	// Функция отправки формы 
	//==================================================================================
    function action_postpartnersform()
	{
		/* Установка внутренней кодировки в UTF-8 */
		mb_internal_encoding("UTF-8");
		
		if (!isset($_POST["sendpartnersform"])){
			$_SESSION['session_error'] = 1;
			header("location: /error");
			exit;
		}
				
		if (isset($_POST["sendpartnersform"])){
			
			//проверка одноразового токена - если токен не совпадает, выводим пустую страницу
			if ($_SESSION['session_skey'] <> $_POST["skey"] ){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
			}	
			
			unset($_SESSION['session_skey']);
			//			
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
			if(!isset($_POST['postusername']) || !isset($_POST['postuseremail']) || !isset($_POST['postuserphone']) || !isset($_POST['postusercity'])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}
			
			if(isset($_POST['postusername']) && isset($_POST['postuseremail']) && isset($_POST['postuserphone']) && isset($_POST['postusercity'])) {
				
				if(empty($_POST['postusername']) || empty($_POST['postuseremail']) || empty($_POST['postuserphone']) || empty($_POST['postusercity']) ) {					
					$_SESSION['session_error'] = 1; 
					header("Location: /error");	
					exit;						
				}
			
				if(!empty($_POST['postusername']) && !empty($_POST['postuseremail']) && !empty($_POST['postuserphone']) && !empty($_POST['postusercity']) ) {
					
					//если присланы данные длиннее заданых значений, то заполнял бот
					if (mb_strlen(trim($_POST["postusername"]),'utf-8') > 20 || mb_strlen(trim($_POST["postuseremail"]),'utf-8') > 30 || 
					mb_strlen(trim($_POST["postuserphone"]),'utf-8') > 20 || mb_strlen(trim($_POST["postusercity"]),'utf-8') > 30   )
					{ 
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}
					
					//если найден пробел в переменной, полагаем там инъекция (слово должно быть одним)
					if (strpos(trim($_POST["postuseremail"]),' ') == true )
					{ 
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}
					
					//непоставлена галочка о согласии сбора информации
					if (!isset($_POST["agree"])){			
						$_SESSION['session_error'] = 8;
						header("location: /error");
						exit;
					}
					
					//проверка емайла на валидность
					if (!filter_var($_POST["postuseremail"], FILTER_VALIDATE_EMAIL)) 
					{
						$_SESSION['session_error'] = 2; //неверный емайл
						header("Location: /error");
						exit;
					}
					
					//подготовка переменных - прогон через фильтры
					$useremail=$this->filter_badtext($_POST['postuseremail']);
					$username=$this->filter_badtext($_POST['postusername'],1);
					
					$userphone=$this->filter_badtext($_POST['postuserphone'],1);
					$usercity=$this->filter_badtext($_POST['postusercity'],1);
					
					//отправляем на почту сообщение
					
					$mailSMTP = new SendMailSmtpClass(SMTP_EMAIL_ADRESS, SMTP_EMAIL_PASSWORD, SMTP_EMAIL_SERVER, SMTP_EMAIL_SERVERPORT, SMTP_EMAIL_ENCODING); // создаем экземпляр класса
					
					$email = 'info@daytradersfactory.com';					
					$subject = 'Сообщение из формы партнеров DayTradrsFactory.com';
					
					$message =	"<html> 
								<head></head> 
								<body> 
								Вам пришло сообщение из формы партнеров<br>
								Автор сообщения: ".$username."<br>
								Электронная почта отправителя: ".$useremail."<br><br>
								Номер телефона отправителя: ".$userphone."<br><br>
								Город отправителя: ".$usercity."<br><br>
								
								</body> 
								</html> 
								";

					//$headers = "Content-Type: text/html; charset=utf-8\r\n"; 
					// от кого письмо 
					//$headers .= "From: DayTradersFactory.com <info@daytradersfactory.com>\r\n\r\n";
					
					// от кого
					$from = array(
					SMTP_EMAIL_FROMNAME, // Имя отправителя
					SMTP_EMAIL_FROMEMAIL // почта отправителя
					);
					
					
					//mail($email, $subject , $message, $headers);
					$result =  $mailSMTP->send($email, $subject, $message , $from);
					
					if($result === true){
						$_SESSION['session_message'] = 2; //сообщение отправлено
						header("Location: /message");
						//echo "Done";
					}else{
						$_SESSION['session_error'] = 30; //Ошибка при отправке почты
						header("Location: /error");
						//echo "Error: " . $result;
					}
					
					exit;
					
				}
				
			
			
			}
			
		}
	
	
	}
}
?>