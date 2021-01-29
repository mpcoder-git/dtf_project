<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
//форма изменения главного пароля
class Controller_Changepass extends Controller
{	
	//==================================================================================
	// Начальная функция
	//==================================================================================
	function action_index()
    { 
		
		
		//если в сессии нет переменной ид юзера - то переброс на чистую страницу
		if(!isset($_SESSION["session_userid"])) {
			$_SESSION['session_error'] = 1; 
			header("Location: /Error");	
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}

		$this->db_connect();
		
		//$this->model = new Model;
		
		$parameters = $this->get_parameters('Личный кабинет - Изменение главного пароля');
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;		
		}
		
			
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		$this->view->generate('changepass_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
		 
				
    }
	//==================================================================================
	
	//==================================================================================
	// Функция отправки формы
	//==================================================================================
    function action_postchangepassform(){

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
		$this->model = new Model_Changepass();
		$usermodel = new Model();
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		
		if (!isset($_POST["repasssubmit"])){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		}
		
		if (isset($_POST["repasssubmit"])){
			
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
			if(!isset($_POST['newpass']) || !isset($_POST['renewpass']) || !isset($_POST['actualpass']) ) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}
			
			#проверка переменных на существование
			if(isset($_POST['newpass']) && isset($_POST['renewpass']) && isset($_POST['actualpass']) ) {
				
				#проверка переменных на пустоту - если пусто то робот
				if(empty($_POST['newpass']) || empty($_POST['renewpass']) || empty($_POST['actualpass']) ) {		
					$_SESSION['session_error'] = 1; 
					header("Location: /error");	
					exit;	
				}
				
				#проверка переменных на пустоту - если не пусты
				if(!empty($_POST['newpass']) && !empty($_POST['renewpass']) && !empty($_POST['actualpass']) ) {
					
					//если присланы данные длиннее заданых значений, то заполнял бот
					if (strlen(trim($_POST["newpass"])) > 32 || strlen(trim($_POST["renewpass"])) > 32 || strlen(trim($_POST["actualpass"])) > 32  )
					{ 
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}
					
					//длина пароля должна быть не менее 8 символов
					if (strlen(trim($_POST["newpass"])) < 8 || strlen(trim($_POST["renewpass"])) < 8 || strlen(trim($_POST["actualpass"])) < 8)
					{
						$_SESSION['session_error'] = 1; //поскольку длина проверяется формой, то меньшее значение шлет бот
						header("Location: /error");
						exit;
					}
					
					
					//если найден пробел в переменной, полагаем там инъекция (слово должно быть одним)
					if (strpos(trim($_POST["newpass"]),' ') == true || strpos(trim($_POST["renewpass"]),' ') == true || strpos(trim($_POST["actualpass"]),' ') == true)
					{ 
						$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
						header("Location: /error");
						exit; 
					}
					
					
					//подготовка переменных
					$newpass = mysqli_real_escape_string($this->con,strip_tags(trim($_POST['newpass'])));
					$renewpass = mysqli_real_escape_string($this->con,strip_tags(trim($_POST['renewpass'])));
					$actualpass = mysqli_real_escape_string($this->con,strip_tags(trim($_POST['actualpass'])));
					
					
					
					//получить пароль пользователя
					$query_result = $usermodel->select_userdata($this->con,$userid);
					$row = mysqli_fetch_row($query_result);
					$dbuserpass = $row[3];//зашифрованый пароль пользователя
					mysqli_free_result($query_result); 
					
					$encodedpass = $this->encode_pass($actualpass);
					
					if ($encodedpass != $dbuserpass){
						$_SESSION['session_error'] = 7; //Вы укакзали неверный основной пароль
						header("Location: ../Error.php");
						exit;						
					} else {
						
						if ($newpass != $renewpass){							
							$_SESSION['session_error'] = 6; //Несовпадают новый и повторный пароли
							header("Location: ../Error.php");	
							exit;							
						} else {
							
							$newpassword = $this->encode_pass($newpass);
							$this->model->update_userpass($this->con,$userid,$newpassword);
							mysqli_close($this->con);
							header("Location: /cabinet");
							exit;
						}
						
						
					}
					
					
					
				}
			}


			
		}
		
		

    }
	//==================================================================================
}

?>