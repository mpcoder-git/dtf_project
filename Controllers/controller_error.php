<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Error extends Controller
{	
	
	function action_index()
    { 
		
		$this->db_connect();
		//$this->model = new Model_Main();
		
		$parameters = $this->get_parameters('Ошибка');
		
		if (isset($_SESSION['session_error'])){
			
			$message = '';
		
			//1 - ошибка, которая возникает при вмешательстве роботов и хакеров
			// результат работы для них можно не показывать - просто пустая страница
			
			switch ($_SESSION['session_error']) {
				case 2:
					$message = 'Неверный формат введеного email !';
					break;
				case 3:
					$message = 'Неверный логин или пароль !';
					break;
				case 4:
					$message = 'Одно или несколько полей не заполнены !';
					break;	
				case 5:
					$message = 'Пользователь с таким email уже существует ! Попробуйте ввести другой адрес !';
					break;
				case 6:
					$message = 'Несовпадают новый и повторный пароли !';
					break;
				case 7:
					$message = 'Указан неверный  основной пароль !';
					break;
				case 8:
					$message = 'Вы не поставили галочку о согласии на сбор личной информации !';
					break;
				case 9:
					$message = 'Пользователя с таким email не существует !';
					break;	
				case 10:
					$message = 'Произошла ошибка при вставке данных в базу. Попробуйте повторить действие попозже.';
					break;
				case 20:
					$message = 'Не выбран файл для загрузки !';
					break;
				case 21:
					$message = 'Размер загружаемого файла превышает допустимый размер ! Выберите файл меньшего размера !';
					break;
				case 22:
					$message = 'Каталог не может быть создан !';
					break;
				case 23:
					$message = 'Произошла ошибка при перемещении файла в папку !';
					break;	
				case 24:
					$message = 'Ошибка загрузки файла во временную папку сервера !';
					break;
				case 25:
					$message = 'Длина пароля не должна быть меньше 8 символов !';
					break;
				case 26:
					$message = 'Текст сообщения не должен быть пустым !';
					break;
				case 27:
					$message = 'Недопустимый тип файла !';
					break;
				case 28:
					$message = 'Файл не сохранен. Невозможно записать файл на диск !';
					break;	
				
				case 30:
					$message = 'При попытке отправления письма с кодом подтверждения произошла ошибка ! Попробуйте еще раз.';
					break;
				
				
				case 40:
					$message = 'Не обнаружен номер раздела !';
					break;
				case 41:
					$message = 'Не обнаружен номер темы !';
					break;
				case 42:
					$message = 'Не обнаружен номер сообщения !';
					break;
					
			}
			
				if ($_SESSION['session_error'] > 1){
				$parameters['message'] = $message;
				$this->view->generate('error_view.php', 'template_view.php', $parameters);
				}
				
				unset($_SESSION['session_error']);
		}
		
		
		
    }
	
}

?>