<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Job extends Controller
{	
	
	function action_index()
    { 
		
		$this->db_connect();
		
		$parameters = $this->get_parameters('Работа');
		
		$this->view->generate('job_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
    }
	
	//================================================================================
	//функция вывод анкеты начинающего
	//================================================================================
	function action_beginnerblank()
    {
		$userid = 0;
		if(!isset($_SESSION["session_userid"])) {
			header("location: /registration");
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}
		
		$this->db_connect();
		$this->model = new Model();
		
		$parameters = $this->get_parameters('Анкета начинающего трейдера');
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		//одноразовый ключ подписи форм
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
				
		$query_result = $this->model->select_userdata($this->con,$userid);
		$row = mysqli_fetch_row($query_result);
		$useremail =  $row[1];
		$parameters['useremail'] =$useremail;
		
		$this->view->generate('beginnerblank_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
	}
	
	//================================================================================
	//функция сохранения анкеты новичка
	//================================================================================
	function action_postbeginnerblankform()
    {
		//если отсутсвтует кнопка отправки
		if (!isset($_POST["savebeginnerblank"])){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");
			exit;
		}
		
		//непоставлена галочка о согласии сбора информации
		if (!isset($_POST["agree"])){
			
			$_SESSION['session_error'] = 8;
			header("location:../error.php");
			exit;
		}
		
		if (isset($_POST["savebeginnerblank"])){
			
			if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");
				exit;
			} else {
				$userid = $_SESSION["session_userid"];
			}
			
			// Стандартный блок проверок (защита формы) --------------------------------------
			
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
			
			//конец стандартный блок проверок ------------------------------------------------------
			
			//если пост переменные не существуют
			if(	!isset($_POST["family"]) || !isset($_POST["username"]) || !isset($_POST["otch"]) || 
				!isset($_POST["field1"]) || !isset($_POST["field2"]) || !isset($_POST["field3"]) ||
				!isset($_POST["field4"]) || !isset($_POST["field5"]) || !isset($_POST["field6"]) ||
				!isset($_POST["field7"]) || !isset($_POST["field8"]) || !isset($_POST["field9"]) ||
				!isset($_POST["field10"]) || !isset($_POST["field11"]) || !isset($_POST["field12"]) ||
				!isset($_POST["field13"]) || !isset($_POST["field14"]) || !isset($_POST["field15"]) ||				
				!isset($_POST["field16"]) || !isset($_POST["field17"]) || !isset($_POST["field18"]) ||
				!isset($_POST["field19"]) || !isset($_POST["field20"]) || !isset($_POST["field21"]) ||
				!isset($_POST["field22"]) || !isset($_POST["field23"]) || !isset($_POST["field24"]) ||
				!isset($_POST["field25"]) || !isset($_POST["agree"]) 
			){
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit;
			}
			
			//если пост переменные пустые
			if(	empty($_POST["family"])  || empty($_POST["username"]) || empty($_POST["otch"])|| 
				empty($_POST["field1"]) || empty($_POST["field2"]) || empty($_POST["field3"]) ||
				empty($_POST["field4"]) || empty($_POST["field5"]) || empty($_POST["field6"]) ||
				empty($_POST["field7"]) || empty($_POST["field8"]) || empty($_POST["field9"]) ||
				empty($_POST["field10"]) || empty($_POST["field11"]) || empty($_POST["field12"]) ||
				empty($_POST["field13"]) || empty($_POST["field14"]) || empty($_POST["field15"]) ||				
				empty($_POST["field16"]) || empty($_POST["field17"]) || empty($_POST["field18"]) ||
				empty($_POST["field19"]) || empty($_POST["field20"]) || empty($_POST["field21"]) ||
				empty($_POST["field22"]) || empty($_POST["field23"]) || empty($_POST["field24"]) 
				 
			){
				$_SESSION['session_error'] = 1; //Все поля должны быть заполнены!
				header("Location: /error");
				exit;
			}
			
			//если присланы данные длиннее заданых значений, то заполнял бот
			if(	strlen(trim($_POST["family"])) > 20 || strlen(trim($_POST["username"])) > 20 || strlen(trim($_POST["otch"])) > 20 || 
				strlen(trim($_POST["field1"])) > 50 || strlen(trim($_POST["field2"])) > 50 || strlen(trim($_POST["field3"])) > 50 ||
				strlen(trim($_POST["field4"])) > 50 || strlen(trim($_POST["field5"])) > 50 || strlen(trim($_POST["field6"])) > 50 ||
				strlen(trim($_POST["field7"])) > 50 || strlen(trim($_POST["field8"])) > 50 || strlen(trim($_POST["field9"])) > 50 ||
				strlen(trim($_POST["field10"])) > 50 || strlen(trim($_POST["field11"])) > 50 || strlen(trim($_POST["field12"])) > 50 ||
				strlen(trim($_POST["field13"])) > 50 || strlen(trim($_POST["field14"])) > 50 || strlen(trim($_POST["field15"])) > 50 ||				
				strlen(trim($_POST["field16"])) > 50 || strlen(trim($_POST["field17"])) > 50 || strlen(trim($_POST["field18"])) > 50 ||
				strlen(trim($_POST["field19"])) > 50 || strlen(trim($_POST["field20"])) > 50 || strlen(trim($_POST["field21"])) > 50 ||
				strlen(trim($_POST["field22"])) > 50 || strlen(trim($_POST["field23"])) > 50 || strlen(trim($_POST["field24"])) > 50 
				 
			){ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			
			//выполнить поиск в переменных недопустимых команд из sql
						
			$this->db_connect();
			
			//подготовка переменных - вырезание тегов и экранирование
			$family = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["family"])));
			$name = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["username"])));
			$otch = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["otch"])));
			
			$field1 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field1"])));
			$field2 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field2"])));
			$field3 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field3"])));
			$field4 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field4"])));
			$field5 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field5"])));
			$field6 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field6"])));
			$field7 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field7"])));
			$field8 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field8"])));
			$field9 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field9"])));
			$field10 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field10"])));
			$field11 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field11"])));
			$field12 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field12"])));
			$field13 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field13"])));
			$field14 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field14"])));
			$field15 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field15"])));
			$field16 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field16"])));
			$field17 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field17"])));
			$field18 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field18"])));
			$field19 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field19"])));
			$field20 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field20"])));
			$field21 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field21"])));
			$field22 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field22"])));
			$field23 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field23"])));
			$field24 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field24"])));
			$field25 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field25"])));
			
			
			$fields = array();
			$fields['family'] = $family;
			$fields['name'] = $name;
			$fields['otch'] = $otch;
			$fields['field1'] = $field1;
			$fields['field2'] = $field2;
			$fields['field3'] = $field3;
			$fields['field4'] = $field4;
			$fields['field5'] = $field5;
			$fields['field6'] = $field6;
			$fields['field7'] = $field7;
			$fields['field8'] = $field8;
			$fields['field9'] = $field9;
			$fields['field10'] = $field10;
			$fields['field11'] = $field11;
			$fields['field12'] = $field12;
			$fields['field13'] = $field13;
			$fields['field14'] = $field14;
			$fields['field15'] = $field15;
			$fields['field16'] = $field16;
			$fields['field17'] = $field17;
			$fields['field18'] = $field18;
			$fields['field19'] = $field19;
			$fields['field20'] = $field20;
			$fields['field21'] = $field21;
			$fields['field22'] = $field22;
			$fields['field23'] = $field23;
			$fields['field24'] = $field24;
			$fields['field25'] = $field25;
			
			
			
			$this->model = new Model_Job();
			
			//есть ли анкета новичка
			$query_result = $this->model->select_cntbeginnerblanks($this->con,$userid);
			$row = mysqli_fetch_row($query_result);
			$profilecount =  $row[0];
				
			//если записи не найдено то вставим новую иначе просто обновим данные
			if ($profilecount == 0) {			
				$query_result2 = $this->model->insert_newbeginnerblank($this->con,$userid,$fields);				
			} else {
				$query_result2 = $this->model->update_beginnerblank($this->con,$userid,$fields);
			}
			
			mysqli_close($this->con);
			header("location: /cabinet");
		}
	
	}
	
	
	
	//================================================================================
	//функция вывод анкеты трейдера
	//================================================================================
	function action_traderblank()
    {
		
    	$userid = 0;
		if(!isset($_SESSION["session_userid"])) {
			header("location: /registration");
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}

		$this->db_connect();
		$this->model = new Model();
		
		$parameters = $this->get_parameters('Работа');
		

		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		//одноразовый ключ подписи форм
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
				
		$query_result = $this->model->select_userdata($this->con,$userid);
		$row = mysqli_fetch_row($query_result);
		$useremail =  $row[1];
		$username =  $row[5];
		$userfamily =  $row[6];
		$userotch =  $row[7];
		
		$parameters['username'] =$username;
		$parameters['userfamily'] =$userfamily;
		$parameters['userotch'] =$userotch;
		$parameters['useremail'] =$useremail;



		$this->view->generate('traderblank_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
	}
	
	
	//================================================================================
	//функция сохранения анкеты новичка
	//================================================================================
	function action_posttraderblankform()
    {
		//если отсутсвтует кнопка отправки
		if (!isset($_POST["savetraderblank"])){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");
			exit;
		}
		
		//непоставлена галочка о согласии сбора информации
		if (!isset($_POST["agree"])){
			
			$_SESSION['session_error'] = 8;
			header("location:../Error.php");
			exit;
		}
		
		if (isset($_POST["savetraderblank"])){
			
			if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");
				exit;
			} else {
				$userid = $_SESSION["session_userid"];
			}
			
			// Стандартный блок проверок (защита формы) --------------------------------------
			
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
			
			//конец стандартный блок проверок ------------------------------------------------------
			
			//если пост переменные не существуют
			if(	!isset($_POST["family"]) || !isset($_POST["username"]) || !isset($_POST["otch"]) || 
				!isset($_POST["field1"]) || !isset($_POST["field2"]) || !isset($_POST["field3"]) ||
				!isset($_POST["field4"]) || !isset($_POST["field5"]) || !isset($_POST["field6"]) ||
				!isset($_POST["field7"]) || !isset($_POST["field8"]) || !isset($_POST["field9"]) ||
				!isset($_POST["field10"]) || !isset($_POST["field11"]) || !isset($_POST["field12"]) ||
				!isset($_POST["field13"]) || !isset($_POST["field14"]) || !isset($_POST["field15"]) ||				
				!isset($_POST["field16"]) || !isset($_POST["field17"]) || !isset($_POST["field18"]) ||
				!isset($_POST["field19"]) || !isset($_POST["field20"]) || !isset($_POST["field21"]) ||
				!isset($_POST["field22"]) || !isset($_POST["field23"]) || !isset($_POST["field24"]) ||
				 !isset($_POST["agree"]) 
			){
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit;
			}
		
			//если пост переменные пустые
			if(	empty($_POST["family"])  || empty($_POST["username"]) || empty($_POST["otch"])|| 
				empty($_POST["field1"]) || empty($_POST["field2"]) || empty($_POST["field3"]) ||
				empty($_POST["field4"]) || empty($_POST["field5"]) || empty($_POST["field6"]) ||
				empty($_POST["field7"]) || empty($_POST["field8"]) || empty($_POST["field9"]) ||
				empty($_POST["field10"]) || empty($_POST["field11"]) || empty($_POST["field12"]) ||
				empty($_POST["field13"]) || empty($_POST["field14"]) || empty($_POST["field15"]) ||				
				empty($_POST["field16"]) || empty($_POST["field17"]) || empty($_POST["field18"]) ||
				empty($_POST["field19"]) || empty($_POST["field20"]) || empty($_POST["field21"]) ||
				empty($_POST["field22"]) || empty($_POST["field23"]) 
				 
			){
				$_SESSION['session_error'] = 1; //Все поля должны быть заполнены!
				header("Location: /error");
				exit;
			}
			
			//если присланы данные длиннее заданых значений, то заполнял бот
			if(	strlen(trim($_POST["family"])) > 20 || strlen(trim($_POST["username"])) > 20 || strlen(trim($_POST["otch"])) > 20 || 
				strlen(trim($_POST["field1"])) > 50 || strlen(trim($_POST["field2"])) > 50 || strlen(trim($_POST["field3"])) > 50 ||
				strlen(trim($_POST["field4"])) > 50 || strlen(trim($_POST["field5"])) > 50 || strlen(trim($_POST["field6"])) > 50 ||
				strlen(trim($_POST["field7"])) > 50 || strlen(trim($_POST["field8"])) > 50 || strlen(trim($_POST["field9"])) > 50 ||
				strlen(trim($_POST["field10"])) > 50 || strlen(trim($_POST["field11"])) > 50 || strlen(trim($_POST["field12"])) > 50 ||
				strlen(trim($_POST["field13"])) > 50 || strlen(trim($_POST["field14"])) > 50 || strlen(trim($_POST["field15"])) > 50 ||				
				strlen(trim($_POST["field16"])) > 50 || strlen(trim($_POST["field17"])) > 50 || strlen(trim($_POST["field18"])) > 50 ||
				strlen(trim($_POST["field19"])) > 50 || strlen(trim($_POST["field20"])) > 50 || strlen(trim($_POST["field21"])) > 50 ||
				strlen(trim($_POST["field22"])) > 50 || strlen(trim($_POST["field23"])) > 50 
				 
			){ 
				$_SESSION['session_error'] = 1; //универсальный код под случаи, когда не надо уведомлять пользователя
				header("Location: /error");
				exit; 
			}
			
			//выполнить поиск в переменных недопустимых команд из sql
			
			
			
			$this->db_connect();
			
			//подготовка переменных - вырезание тегов и экранирование
			$family = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["family"])));
			$name = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["username"])));
			$otch = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["otch"])));
			
			$field1 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field1"])));
			$field2 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field2"])));
			$field3 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field3"])));
			$field4 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field4"])));
			$field5 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field5"])));
			$field6 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field6"])));
			$field7 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field7"])));
			$field8 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field8"])));
			$field9 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field9"])));
			$field10 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field10"])));
			$field11 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field11"])));
			$field12 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field12"])));
			$field13 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field13"])));
			$field14 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field14"])));
			$field15 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field15"])));
			$field16 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field16"])));
			$field17 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field17"])));
			$field18 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field18"])));
			$field19 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field19"])));
			$field20 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field20"])));
			$field21 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field21"])));
			$field22 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field22"])));
			$field23 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field23"])));
			$field24 = 	mysqli_real_escape_string($this->con,strip_tags(trim($_POST["field24"])));
			
			
			
			$fields = array();
			$fields['family'] = $family;
			$fields['name'] = $name;
			$fields['otch'] = $otch;
			$fields['field1'] = $field1;
			$fields['field2'] = $field2;
			$fields['field3'] = $field3;
			$fields['field4'] = $field4;
			$fields['field5'] = $field5;
			$fields['field6'] = $field6;
			$fields['field7'] = $field7;
			$fields['field8'] = $field8;
			$fields['field9'] = $field9;
			$fields['field10'] = $field10;
			$fields['field11'] = $field11;
			$fields['field12'] = $field12;
			$fields['field13'] = $field13;
			$fields['field14'] = $field14;
			$fields['field15'] = $field15;
			$fields['field16'] = $field16;
			$fields['field17'] = $field17;
			$fields['field18'] = $field18;
			$fields['field19'] = $field19;
			$fields['field20'] = $field20;
			$fields['field21'] = $field21;
			$fields['field22'] = $field22;
			$fields['field23'] = $field23;
			$fields['field24'] = $field24;
			
			
			
			
			$this->model = new Model_Job();
			
			//есть ли анкета новичка
			$query_result = $this->model->select_cnttraderblanks($this->con,$userid);
			$row = mysqli_fetch_row($query_result);
			$profilecount =  $row[0];
				
			//если записи не найдено то вставим новую иначе просто обновим данные
			if ($profilecount == 0) {			
				$query_result2 = $this->model->insert_newtraderblank($this->con,$userid,$fields);				
			} else {
				$query_result2 = $this->model->update_traderblank($this->con,$userid,$fields);
			}
			
			mysqli_close($this->con);
			header("location: /cabinet");
		}
	
	}
	

	
}

?>