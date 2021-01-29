<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Contacts extends Controller
{
	
	function action_index()
    { 
		
		$this->db_connect();
		
		$parameters = $this->get_parameters('Контакты');
		
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		$this->view->generate('contacts_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
    }
	
	//==================================================================================
	// Функция отправки формы обратной связи
	//==================================================================================
    function action_feedback()
	{
		
		if (!isset($_POST["sendfeedbackform"])){
			$_SESSION['session_error'] = 1;
			header("location: /error");
			exit;
		}
		
		
		
		if (isset($_POST["sendfeedbackform"])){
			
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
			if(!isset($_POST['postusername']) || !isset($_POST['postuseremail']) || !isset($_POST['textmessage']) ) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}
			
			if(isset($_POST['postusername']) && isset($_POST['postuseremail']) && isset($_POST['textmessage']) ) {
				
				if(empty($_POST['postusername']) || empty($_POST['postuseremail']) || empty($_POST['textmessage']) ) {					
					$_SESSION['session_error'] = 1; 
					header("Location: /error");	
					exit;						
				}
			
				if(!empty($_POST['postusername']) && !empty($_POST['postuseremail']) && !empty($_POST['textmessage']) ) {
			
					//если присланы данные длиннее заданых значений, то заполнял бот
					if (mb_strlen(trim($_POST["postusername"]),'utf-8') > 20 || mb_strlen(trim($_POST["postuseremail"]),'utf-8') > 30)
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
					
					$this->db_connect();
					//подготовка переменных - прогон через фильтры
					$useremail=$this->filter_badtext($_POST['postuseremail']);
					$username=$this->filter_badtext($_POST['postusername'],1);
					
					$textmessage=mysqli_real_escape_string($this->con,strip_tags(trim($_POST['textmessage'])));
					
					mysqli_close($this->con);
					
					//отправляем на почту сообщение
					
					$mailSMTP = new SendMailSmtpClass(SMTP_EMAIL_ADRESS, SMTP_EMAIL_PASSWORD, SMTP_EMAIL_SERVER, SMTP_EMAIL_SERVERPORT, SMTP_EMAIL_ENCODING); // создаем экземпляр класса
					
					$email = 'info@daytradersfactory.com';					
					$subject = 'Сообщение из формы обратной связи DayTradrsFactory.com';
					
					$message =	"<html> 
								<head></head> 
								<body> 
								Вам пришло сообщение из формы обратной связи<br>
								Автор сообщения: ".$username."<br>
								Электронная почта отправителя: ".$useremail."<br><br>
								Текст сообщения:<br><br>
								".$textmessage."
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