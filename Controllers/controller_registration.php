<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Registration extends Controller
{	
	//=====================================================================================
	//Функция по умолчанию - отображает страницу регистрации
	//=====================================================================================
	function action_index()
    { 
		
		$this->db_connect();
		$this->model = new Model_Registration();
		
		$parameters = $this->get_parameters('Регистрация нового пользователя');
		
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		$ukey = $this->encode_pass($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].session_id());
		$parameters['ukey'] = $ukey;
				
		$this->view->generate('registration_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
    }
	
	//=====================================================================================
	//функция обрабатывает отправленную форму регистрации
	//=====================================================================================
	function action_postregform()
    { 
		
		if (!isset($_POST["sendregistrationform"])){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		if (isset($_POST["sendregistrationform"])){
			
			//проверка токена - если токен не совпадает, выводим пустую страницу
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
			if(!isset($_POST['usernick']) || !isset($_POST['username']) || !isset($_POST['userfamily']) || 
				!isset($_POST['userotch']) || !isset($_POST['useremail']) || !isset($_POST['password']) || !isset($_POST['repassword'])) {
			
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 			
			}
			//если пост переменные пустые
			if(empty($_POST['usernick']) || empty($_POST['username']) || empty($_POST['userfamily']) || 
				empty($_POST['userotch']) || empty($_POST['useremail']) || empty($_POST['password']) || empty($_POST['repassword'])) {
				
				$_SESSION['session_error'] = 4; //не все поля заполнены
				header("Location: /error");
				exit;
			}
			
			//если присланы данные длиннее заданых значений, то заполнял бот
			if (mb_strlen(trim($_POST["usernick"]),'UTF-8') > 20 || mb_strlen(trim($_POST["username"]),'UTF-8') > 20 || mb_strlen(trim($_POST["userfamily"]),'UTF-8') > 20  ||
				mb_strlen(trim($_POST["userotch"]),'UTF-8') > 20 || mb_strlen(trim($_POST["useremail"]),'UTF-8') > 30 || mb_strlen(trim($_POST["password"]),'UTF-8') > 30 || mb_strlen(trim($_POST["repassword"]),'UTF-8') > 30  )
			{ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			
			//длина пароля должна быть не менее 8 символов
			if (strlen(trim($_POST["password"])) < 8)
			{
				$_SESSION['session_error'] = 1; //поскольку форма проверяет длину, только бот может прислать меньшее значение
				header("Location: /error");
				exit;
			}
			
			//если найден пробел в переменной, полагаем там инъекция (слово должно быть одним)
			if (strpos(trim($_POST["usernick"]),' ') == true || strpos(trim($_POST["username"]),' ') == true || strpos(trim($_POST["userfamily"]),' ') == true ||
				strpos(trim($_POST["userotch"]),' ') == true || strpos(trim($_POST["useremail"]),' ') == true || strpos(trim($_POST["password"]),' ') == true || strpos(trim($_POST["repassword"]),' ') == true )
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
			
			$this->db_connect();
			$this->model = new Model_Registration();
			
			
			#проверка переменных на существование
			if(!empty($_POST['usernick']) || !empty($_POST['username']) || !empty($_POST['userfamily']) || !empty($_POST['userotch']) || !empty($_POST['useremail']) || !empty($_POST['password']) || !empty($_POST['repassword'])) {
				
				//подготовка переменных
				$usernick=$this->filter_badtext($_POST['usernick']);
				$username=$this->filter_badtext($_POST['username']);
				$userfamily=$this->filter_badtext($_POST['userfamily']);
				$userotch=$this->filter_badtext($_POST['userotch']);
				$useremail=$this->filter_badtext($_POST['useremail']);
				$password=strip_tags(trim($_POST['password']));
				$repassword=strip_tags(trim($_POST['repassword']));
				
				#  делаем двойное шифрование
				$password = $this->encode_pass($password);
				$repassword = $this->encode_pass($repassword);
				
				$query_result = $this->model->select_countuser($this->con,$useremail);
				
				$row = mysqli_fetch_row($query_result);
				$countusers = $row[0];
				
				mysqli_free_result($query_result);
				
				if($countusers==0 && $password == $repassword)//
				{
					//вставим нового пользователя
					$query_result = $this->model->insert_newuser($this->con,$usernick,$username,$userotch,$userfamily,$useremail,$password );
					if($query_result){
						//аккаунт успешно создан
						mysqli_close($this->con);
						header("Location: /login"); 
						exit();
					} else {
						$_SESSION['session_error'] = 10; //ошибка вставки данных в базу
						header("Location: /error");
						exit;
					}
					
					
				} else {
					$_SESSION['session_error'] = 5; //пользователь с таким емайлов уже есть
					header("Location: /error");
					exit; 
				}	
				
			
			} 
			
			
		}
	}
}

?>