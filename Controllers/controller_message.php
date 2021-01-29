<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Message extends Controller
{	
	
	function action_index()
    { 

		
		$parameters = $this->get_parameters('Сообщение');
		
		if (isset($_SESSION['session_message'])){
			
			$message = '';
	
	
	
			switch ($_SESSION['session_message']) {
			case 2:
				$message = 'Ваше сообщение отправлено и будет рассмотрено в ближайшее время !';
				break;
			case 3:
				$message = 'На ваш email было отправлено письмо с инструкциями для смены пароля !<br>Для смены пароля у вас есть 15 минут.';
				break;
			case 4:
				$message = 'Адрес вашей электронной почты не подтверждён !<br> На ваш адрес должно прийти письмо, содержащее инструкцию для подтверждения адреса';
				break;
			case 5:
				$message = 'Вы изменили свой email !<br> Для подтверждения нового адреса вам необходимо снова залогиниться на сайт.';
				break;	
			}
			
			
			$parameters['message'] = $message;
			$this->view->generate('message_view.php', 'template_view.php', $parameters);
						
			unset($_SESSION['session_message']);
		}
		
		
		
    }
	
}

?>