<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Forum extends Controller
{
	
	//================================================================================
	function action_index()
    { 
	
		$this->db_connect();
		
		$this->model = new Model_Forum();
		
		$parameters = $this->get_parameters('Форум');		
		
		$query_result = $this->model->select_forumcatalog($this->con);		
		$parameters['forumcatalog_result'] =$query_result;
	 

		//необходим второй набор, поскольку далее первый начнет работу в цикле, а это не даст его передать дальше
		$query_result2 = $this->model->select_forumcatalog($this->con);
		
		//сформировать несколько групп результатов подзапросов
		while( $row = mysqli_fetch_assoc($query_result2) ){
			
			$subquery_result = $this->model->select_forumsubcatalog($this->con,$row['id']);
			
			$parameters['forumsubcatalog_result'.$row['id']] =$subquery_result;
			
		}
		
		

		$this->view->generate('forum_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
    }
	//================================================================================
	
	//================================================================================
	//показать список тем
	//================================================================================
	function action_viewtopics($pid=null,$page=null)
    { 
		//проверка параметра
		if (!isset($pid)){			
			$_SESSION['session_error'] = 40;
			header("Location: /error");	
			exit;
		}

		$vpid = $this->number_valid($pid);
		if (!$vpid){ 
			Route::ErrorPage404();
		 } else { 
		 	$verifypid = (int)$pid; 
		 }
		
						
		if (!isset($page)) { $verifypage = 1; }
		
		if (isset($page)) { 
			$vpage = $this->number_valid($page);
			if (!$vpage){ Route::ErrorPage404(); } else { $verifypage = (int)$page; }
			
		}
		
		//параметр верный - то продолжаем выполнение
		$this->db_connect();		
		$this->model = new Model_Forum();
		
		$parameters = $this->get_parameters('Форум - просмотр списка тем');
		
		$query_result = $this->model->select_razdelname($this->con,$verifypid);
		$row = mysqli_fetch_row($query_result);
		$sectionname = $row[0];
		$parameters['sectionname'] =$sectionname;
		
		//блок постраничной навигации - подготовка 
		$query_result = $this->model->select_cnttopics($this->con,$verifypid);
		$row = mysqli_fetch_row($query_result);
		
		$num = TOPICS_IN_PAGE; // количество тем на странице
		$topics = $row[0];		
		mysqli_free_result($query_result); 

		$total = (($topics - 1) / $num) + 1;
		$total =  intval($total);
		
		$page = intval($verifypage);
		if(empty($page) or $page < 0) $page = 1;
		if($page > $total) $page = $total;
		$start = $page * $num - $num;
		
		$parameters['page'] =$page;
		$parameters['total'] =$total;
		//----------------------------------------------
		
		$limitedtopicslist_result = $this->model->select_limitedtopicslist($this->con,$verifypid,$start,$num);
		$parameters['limitedtopicslist_result'] =$limitedtopicslist_result;

		$this->view->generate('viewtopics_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
	}
	//================================================================================	
	
	//================================================================================
	//показать список сообщений в теме
	//================================================================================
	function action_viewtopic($tid=null,$page=null)
    { 
		//проверка параметра
		if (!isset($tid)){			
			$_SESSION['session_error'] = 41;
			header("Location: /error");	
			exit;
		}
		
		$vtid = $this->number_valid($tid);
		if (!$vtid){ Route::ErrorPage404(); } else { $verifytid = (int)$tid; }
		

		if (!isset($page)) { $verifypage = 1; }
		
		if (isset($page)) { 
			$vpage = $this->number_valid($pid);
			if (!$page){ Route::ErrorPage404(); } else { $verifypage = (int)$page; }
			
			//$verifypage = (int)$page;
			//страниц должно быть не более 99
			//if ($verifypage == 0 || $verifypage > 99){ Route::ErrorPage404(); }
		}
		
		//параметр верный - то продолжаем выполнение
		$this->db_connect();		
		$this->model = new Model_Forum();
		$usermodel = new Model();
		
		$query_result = $this->model->select_rtnames($this->con,$verifytid);
		//если темы не существует - выдадим ошибку
		if (mysqli_num_rows($query_result) == 0){
			$_SESSION['session_error'] = 1;
			header("Location: /error");	
			exit;
		}
		
		$row = mysqli_fetch_row($query_result);
		
		$sectionname = $row[0];
		$topicname = $row[1];
		$pid = $row[2];
		$avtortopic = $row[3];
		mysqli_free_result($query_result); 
		
		
		$parameters = $this->get_parameters('Форум - просмотр списка сообщений');
		
		
		
		$parameters['tid'] = $verifytid;
		
		
		if(isset($_SESSION["session_userid"])) {
			$userid = $_SESSION["session_userid"];
			$parameters['userid'] = $userid;
			//получить группу
			$query_result = $usermodel->select_userdata($this->con,$userid);
			$row = mysqli_fetch_row($query_result);
			$usergroup = $row[11];
			$parameters['usergroup'] = $usergroup;
			mysqli_free_result($query_result);			
		}
		
		
		
		//генерация ключа для подписи формы быстрого ответа
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
				
		
		
		

		$parameters['sectionname'] =$sectionname;
		$parameters['topicname'] =$topicname;
		$parameters['pid'] =$pid;
		$parameters['avtortopic'] =$avtortopic;
		

		//блок постраничной навигации - подготовка 
		$query_result = $this->model->select_cntmessages($this->con,$verifytid);
		$row = mysqli_fetch_row($query_result);

		$num = TOPICS_IN_PAGE; // количество тем на странице
		$topics = $row[0];		
		mysqli_free_result($query_result); 

		$total = (($topics - 1) / $num) + 1;
		$total =  intval($total);
		
		$page = intval($verifypage);
		if(empty($page) or $page < 0) $page = 1;
		if($page > $total) $page = $total;
		$start = $page * $num - $num;
		
		$parameters['page'] =$page;
		$parameters['total'] =$total;
		//-------------------------------------------------
		
		$limitedmessageslist_result = $this->model->select_limitedmessageslist($this->con,$verifytid,$start,$num);
		$parameters['limitedmessageslist_result'] =$limitedmessageslist_result;
		
		
		
		$this->view->generate('viewtopic_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
	}
	//================================================================================
	
	//================================================================================
	//функция обрабатывает отправленную форму быстрого ответа в тему
	//================================================================================
	function action_postquickreply()
    {
		
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
		$this->model = new Model_Forum();
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		
		if (!isset($_POST["postquickreply"])){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		}
		
		if (isset($_POST["postquickreply"])){
			
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
			if(!isset($_POST['messagereply']) || !isset($_POST['topicid'])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}
			
			#проверка переменных на существование
			if(isset($_POST['messagereply']) && isset($_POST['topicid'])) 
			{
				$date = date("Y-m-d H:i:s");
				$message = mysqli_real_escape_string($this->con,$_POST['messagereply']);
				
				//если неверный номер топика
				$topicid = $_POST['topicid'];
				
				$vtid = $this->number_valid($tid);
				if (!$vtid){ Route::ErrorPage404(); } else { $verifytid = (int)$tid; }
				//$verifytid = (int)$topicid;
				//if ($verifytid == 0 || $verifytid > MAX_ID){ Route::ErrorPage404(); }
				
				$topicid = $verifytid;
				
				$query_result = $this->model->insert_qrnewmessage($this->con,$topicid,$userid,$date,$message);			
				mysqli_free_result($query_result); 
				$query_result = $this->model->select_cntmessages($this->con,$verifytid);
				$row = mysqli_fetch_row($query_result);				
				$posts = $row[0];//количество сообщений всего в теме
				mysqli_free_result($query_result); 

				$page = 1;
				if ($posts > MESSAGES_IN_PAGE){
					$total = $posts/MESSAGES_IN_PAGE;					
					$total =  ceil($total); //количество страниц					
					$page = $total;
				}
				
				mysqli_close($this->con);
				
				header("Location: /forum/viewtopic/".$topicid."/".$page); 
				exit(); 
			
			}
			
	
		}
	}	
	//================================================================================
	
	//================================================================================
	//функция редактирования названия темы
	//================================================================================
	function action_edittopicname($tid)
    {
    	$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}


    	//проверка параметра
		if (!isset($tid)){			
			$_SESSION['session_error'] = 41;
			header("Location: /error");	
			exit;
		}
		
		$vtid = $this->number_valid($tid);
		if (!$vtid){ Route::ErrorPage404(); } else { $verifytid = (int)$tid; }
		//$verifytid = (int)$tid;
		//if ($verifytid == 0 || $verifytid > MAX_ID){ Route::ErrorPage404(); }

		$this->db_connect();		
		$this->model = new Model_Forum();
				
		$parameters = $this->get_parameters('Форум - Редактирование названия темы');
		
		$parameters['tid'] = $verifytid;

		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}


		//генерация ключа для подписи формы быстрого ответа
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;

		$query_result = $this->model->select_rtnames($this->con,$verifytid);
		$row = mysqli_fetch_row($query_result);
		$topicid = $row[4];//код темы
		$topicname = $row[1];//название темы
		mysqli_free_result($query_result); 

		$parameters['topicid'] = $topicid;
		$parameters['topicname'] = $topicname;

		$this->view->generate('edittopic_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);

    }
    //================================================================================


    //================================================================================
	//функция обрабатывает отправленную форму изменения названия темы
	//================================================================================
	function action_postedittopicnameform()
    {
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
		$this->model = new Model_Forum();
		$usermodel = new Model();
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		//если нет кнопки отправки
		if (!isset($_POST["postedittopicname"])){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		}

		if (isset($_POST["postedittopicname"])){

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
			if(!isset($_POST['topicname']) || !isset($_POST['topicid'])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}

			#проверка переменных на пустоту
			if(empty($_POST['topicname']) || empty($_POST['topicid'])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}

			#проверка переменных на существование
			if(isset($_POST['topicname']) || isset($_POST['topicid'])) {

				//если неверный номер топика
				$topicid = $_POST['topicid'];
				
				$vtid = $this->number_valid($topicid);
				if (!$vtid){ Route::ErrorPage404(); } else { $verifytid = (int)$topicid; }
				//$verifytid = (int)$topicid;
				//if ($verifytid == 0 || $verifytid > MAX_ID){ Route::ErrorPage404(); }
				
				$topicid = $verifytid;

				//вычислить создавшего тему пользователя
				$query_result = $this->model->select_rtnames($this->con,$topicid);
				$row = mysqli_fetch_row($query_result);
				$autortopicid = $row[3];//код темы
				mysqli_free_result($query_result); 

				//получить группу пользователя
				$query_result = $usermodel->select_userdata($this->con,$userid);
				$row = mysqli_fetch_row($query_result);
				$usergroup = $row[11];//группа пользователя
				mysqli_free_result($query_result); 

				//если открываемое сообщение на редактирование не принадлежит автору		
				if ($autortopicid != $userid && $usergroup != 1){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");		
				exit;	
				}

				$newtopicname = mysqli_real_escape_string($this->con,strip_tags(trim($_POST['topicname'])));	
				
				//обновить название темы
				$this->model->update_topicname($this->con,$newtopicname,$topicid);
				
				mysqli_close($this->con);
				
				header("Location: /forum/viewtopic/".$topicid); 
				exit(); 

			}

		}

    }
	//================================================================================

    //================================================================================
	//функция добавления нового сообщения
	//================================================================================
	function action_newmessage($tid)
    {
    	$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}


    	//проверка параметра
		if (!isset($tid)){			
			$_SESSION['session_error'] = 41;
			header("Location: /error");	
			exit;
		}
		
		$vtid = $this->number_valid($tid);
		if (!$vtid){ Route::ErrorPage404(); } else { $verifytid = (int)$tid; }
		//$verifytid = (int)$tid;
		//if ($verifytid == 0 || $verifytid > MAX_ID){ Route::ErrorPage404(); }
		$topicid = $verifytid;

		$this->db_connect();		
		$this->model = new Model_Forum();
				
		$parameters = $this->get_parameters('Форум - Добавление нового сообщения');
		
		$parameters['tid'] = $topicid;
		$parameters['userid'] = $userid;

		//генерация ключа для подписи формы быстрого ответа
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;

		//проверим, не осталось ли отмененого сообщения
		$query_result = $this->model->select_notactivemessage($this->con,$userid);
		$numrows=mysqli_num_rows($query_result);
		

		if ($numrows == 0) {
		//если нет неактивных сообщений - создаем нактивное сообщение и запоминаем его номер
			$date = date("Y-m-d H:i:s");
			$this->model->insert_newmessage($this->con,$topicid,$userid,$date);
			$messageid = mysqli_insert_id($this->con);	
		} else {
		//если есть неактивные сообщения - то используем ид неактивного сообщения
			$row = mysqli_fetch_row($query_result);
			$messageid = $row[0];//код темы
		}

		$parameters['mid'] = $messageid;


		$this->view->generate('newmessage_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);

    }
    //================================================================================


    //================================================================================
	//функция отправки формы нового сообщения
	//================================================================================
	function action_postnewmessageform()
    {
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
		$this->model = new Model_Forum();
		$usermodel = new Model();
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		//если нет кнопки отправки
		if (!isset($_POST["postnewmessage"])){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		}

		if (isset($_POST["postnewmessage"])){

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
			if(!isset($_POST['messageid']) || !isset($_POST['topicid']) || !isset($_POST['messageeditor'])  ) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}

			#проверка переменных на пустоту
			if(empty($_POST['messageid']) || empty($_POST['topicid'])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}

			#проверка переменных на пустоту
			if (trim($_POST['messageeditor']) == '<br>'){
				$_SESSION['session_error'] = 26; //текст сообщения не должен быть пустым
				header("Location: /error");	
				exit;
			}

			if(isset($_POST['messageid']) && isset($_POST['topicid']) && isset($_POST['messageeditor'])  ) {
				//если неверный номер топика
				$topicid = $_POST['topicid'];
				
				$vtid = $this->number_valid($topicid);
				if (!$vtid){ Route::ErrorPage404(); } else { $verifytid = (int)$topicid; }
				//$verifytid = (int)$topicid;
				//if ($verifytid == 0 || $verifytid > MAX_ID){ Route::ErrorPage404(); }
				
				$topicid = $verifytid;

				//если неверный номер сообщения
				$messageid = $_POST['messageid'];
				
				$vmid = $this->number_valid($messageid);
				if (!$vmid){ Route::ErrorPage404(); } else { $verifymid = (int)$messageid; }
				//$verifymid = (int)$messageid;
				//if ($verifymid == 0 || $verifymid > MAX_ID){ Route::ErrorPage404(); }
				
				$messageid  = $verifymid;

				$date = date("Y-m-d H:i:s");
				$message = mysqli_real_escape_string($this->con,$_POST['messageeditor']);

				$this->model->update_message($this->con,$messageid,$message,$date);

				//вычислить к какой странице относится новый ид
				$query_result = $this->model->select_cntmessages($this->con,$topicid);
				$row = mysqli_fetch_row($query_result);
				$posts = $row[0];//количество сообщений всего в теме
				mysqli_free_result($query_result); 

				$page = 1;
				if ($posts > MESSAGES_IN_PAGE){
				$total = $posts/MESSAGES_IN_PAGE;				
				$total =  ceil($total); //количество страниц				
				$page = $total;
				}

				mysqli_close($con);			
			
				header("Location: /forum/viewtopic/".$topicid."/".$page); 
				exit(); 

			}	

		}

    }
    //================================================================================

	//================================================================================
	//функция редактирования сообщения
	//================================================================================
	function action_editmessage($mid)
    {
    	$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}


    	//проверка параметра
		if (!isset($mid)){			
			$_SESSION['session_error'] = 42; //не найден номер сообщения
			header("Location: /error");	
			exit;
		}
		
		$vmid = $this->number_valid($mid);
		if (!$vmid){ Route::ErrorPage404(); } else { $verifymid = (int)$mid; }
		//$verifymid = (int)$mid;
		//if ($verifymid == 0 || $verifymid > MAX_ID){ Route::ErrorPage404(); }
		$messageid = $verifymid;

		$this->db_connect();		
		$this->model = new Model_Forum();
				
		$parameters = $this->get_parameters('Форум - Редактирование сообщения');
		
		$parameters['mid'] = $messageid;
		$parameters['userid'] = $userid;

		//генерация ключа для подписи формы быстрого ответа
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		$query_result = $this->model->select_onemessage($this->con,$messageid);
		$row = mysqli_fetch_row($query_result);
		$messagetext = $row[4];//код темы
		$topicid = $row[1];//код темы

		$parameters['messagetext'] = $messagetext;
		$parameters['tid'] = $topicid;

		$this->view->generate('editmessage_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);

    }
    //================================================================================
	
	//================================================================================
	//функция отправки формы отредактированного сообщения
	//================================================================================
	function action_posteditmessageform()
    {
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
		$this->model = new Model_Forum();
		$usermodel = new Model();
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		//если нет кнопки отправки
		if (!isset($_POST["posteditmessage"])){
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		}

		if (isset($_POST["posteditmessage"])){

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
			if(!isset($_POST['messageid']) || !isset($_POST['messageeditor'])  ) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}

			#проверка переменных на пустоту
			if(empty($_POST['messageid']) ) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;			
			}

			#проверка переменных на пустоту
			if (trim($_POST['messageeditor']) == '<br>'){
				$_SESSION['session_error'] = 26; //текст сообщения не должен быть пустым
				header("Location: /error");	
				exit;
			}

			if(isset($_POST['messageid']) && isset($_POST['messageeditor'])  ) {


				//если неверный номер сообщения
				$messageid = $_POST['messageid'];
				
				$vmid = $this->number_valid($messageid);
				if (!$vmid){ Route::ErrorPage404(); } else { $verifymid = (int)$messageid; }
				//$verifymid = (int)$messageid;
				//if ($verifymid == 0 || $verifymid > MAX_ID){ Route::ErrorPage404(); }
				
				$messageid  = $verifymid;

				$date = date("Y-m-d H:i:s");
				$message = mysqli_real_escape_string($this->con,$_POST['messageeditor']);

				$this->model->update_message($this->con,$messageid,$message,$date);
				
				$query_result =  $this->model->select_onemessage($this->con,$messageid);
				$row = mysqli_fetch_row($query_result);
				$topicid = $row[1];//количество сообщений всего в теме
				mysqli_free_result($query_result);
				
				//вычислить к какой странице относится новый ид
				$query_result = $this->model->select_cntmessages($this->con,$topicid);
				$row = mysqli_fetch_row($query_result);
				$posts = $row[0];//количество сообщений всего в теме
				mysqli_free_result($query_result); 

				$page = 1;
				if ($posts > MESSAGES_IN_PAGE){
				$total = $posts/MESSAGES_IN_PAGE;				
				$total =  ceil($total); //количество страниц				
				$page = $total;
				}

				mysqli_close($con);			
			
				header("Location: /forum/viewtopic/".$topicid."/".$page); 
				exit(); 

			}	

		}

    }
    //================================================================================
	
	//================================================================================
	//функция редактирования сообщения
	//================================================================================
	function action_replyquotemessage($mid)
    {
		$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}


    	//проверка параметра
		if (!isset($mid)){			
			$_SESSION['session_error'] = 42; //не найден номер сообщения
			header("Location: /error");	
			exit;
		}
		
		$vmid = $this->number_valid($mid);
		if (!$vmid){ Route::ErrorPage404(); } else { $verifymid = (int)$mid; }
		//$verifymid = (int)$mid;
		//if ($verifymid == 0 || $verifymid > MAX_ID){ Route::ErrorPage404(); }
		$messageid = $verifymid;

		$this->db_connect();		
		$this->model = new Model_Forum();
		$usermodel = new Model();
		
		$parameters = $this->get_parameters('Форум - Ответ с цитированием');
				
		$parameters['userid'] = $userid;

		//генерация ключа для подписи формы быстрого ответа
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
						
		$query_result = $this->model->select_onemessage($this->con,$messageid);
		$row = mysqli_fetch_row($query_result);
		$messagetext = $row[4];//текст сообщения
		$topicid = $row[1];//код темы
		$avtormessage = $row[2];
		$createdata = $this->ref_date($row[3]);
		mysqli_free_result($query_result);
		
		//проверим, не осталось ли отмененого сообщения
		$query_result = $this->model->select_notactivemessage($this->con,$userid);
		$numrows=mysqli_num_rows($query_result);
		
		if ($numrows == 0) {
		//если нет неактивных сообщений - создаем нактивное сообщение и запоминаем его номер
			$date = date("Y-m-d H:i:s");
			$this->model->insert_newmessage($this->con,$topicid,$userid,$date);
			$new_messageid = mysqli_insert_id($this->con);	
		} else {
		//если есть неактивные сообщения - то используем ид неактивного сообщения
			$row = mysqli_fetch_row($query_result);
			$new_messageid = $row[0];//код темы
		}
		
		$parameters['mid'] = $new_messageid; //передать надо новый номер сообщения
		
		
		$query_result = $usermodel->select_userdata($this->con,$avtormessage);
		$row = mysqli_fetch_row($query_result);
		$nick = $row[4];
		mysqli_free_result($query_result);
		
		
		$zagolovok = "Сообщение написано ".$nick." от ".$createdata;
		$messagetext = '<br><br>'.$zagolovok.'<blockquote>'.$messagetext.'</blockquote>';
			
		$parameters['messagetext'] = $messagetext;
		$parameters['tid'] = $topicid;

		$this->view->generate('replyquotemessage_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);	
		
		
		
	}
	//================================================================================
	
    //================================================================================
	//функция отображения диалога удаления сообщения
	//================================================================================
	function action_deletemessagedialog($mid)
    {
    	$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}

		if (!isset($mid) ) {
		$_SESSION['session_error'] = 42; //не обнаружен номер сообщения
		header("Location: /error");	
		exit;
		}

		$vmid = $this->number_valid($mid);
		if (!$vmid){ Route::ErrorPage404(); } else { $verifymid = (int)$mid; }
		//$verifymid = (int)$mid ;
		//if ($verifymid == 0 || $verifymid > MAX_ID){ Route::ErrorPage404(); }
		
		$messageid  = $verifymid;

		$this->db_connect();
		$this->model = new Model_Forum();
		$usermodel = new Model();

		$parameters = $this->get_parameters('Форум - Удаление сообщения');
		
		$parameters['mid'] = $messageid;
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}

		$query_result = $this->model->select_onemessage($this->con,$messageid);
		$row = mysqli_fetch_row($query_result);
		$topicid = $row[1];//номер темы
		$parameters['tid'] = $topicid;
		mysqli_free_result($query_result); 

		//вычислить создавшего тему пользователя
		$query_result = $this->model->select_onemessage($this->con,$messageid);
		$row = mysqli_fetch_row($query_result);
		$autormessageid = $row[2];//код автора сообщения
		mysqli_free_result($query_result); 

		//получить группу пользователя
		$query_result = $usermodel->select_userdata($this->con,$userid);
		$row = mysqli_fetch_row($query_result);
		$usergroup = $row[11];//группа пользователя
		mysqli_free_result($query_result); 


		//если открываемое сообщение на редактирование не принадлежит автору		
		if ($autormessageid != $userid && $usergroup != 1){
		$_SESSION['session_error'] = 1; 
		header("Location: /error");		
		exit;	
		}		

		//рассчитать номер страницы по номеру сообщения
		$query_result = $this->model->select_numbermessage($this->con,$messageid,$topicid);
		$row = mysqli_fetch_row($query_result);
		$nummessage = $row[0];//количество сообщений всего в теме
		mysqli_free_result($query_result); 

		$page = 1;
		if ($nummessage > MESSAGES_IN_PAGE){		
			$total = $nummessage/MESSAGES_IN_PAGE;	
			$total =  ceil($total); //количество страниц	
			$page = $total;
		}
		$parameters['page'] = $page;

		$this->view->generate('deletemessagedialog_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);

    }
    //================================================================================

    //================================================================================
	//функция отображения диалога удаления сообщения
	//================================================================================
	function action_deletemessage($mid)
    {

    	$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
				$_SESSION['session_error'] = 1; 
				header("Location: /error");	
				exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}

		if (!isset($mid) ) {
		$_SESSION['session_error'] = 42; //не обнаружен номер сообщения
		header("Location: /error");	
		exit;
		}

		$vmid = $this->number_valid($mid);
		if (!$vmid){ Route::ErrorPage404(); } else { $verifymid = (int)$mid; }
		//$verifymid = (int)$mid ;
		//if ($verifymid == 0 || $verifymid > MAX_ID){ Route::ErrorPage404(); }
		
		$messageid  = $verifymid;

		$this->db_connect();
		$this->model = new Model_Forum();
		$usermodel = new Model();

		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){	
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}

		//получить номер темы из номера сообщения
		$query_result = $this->model->select_onemessage($this->con,$messageid);
		$row = mysqli_fetch_row($query_result);
		$topicid = $row[1];//код  темы
		$autormessageid = $row[2];//код автора сообщения
		mysqli_free_result($query_result);

		//проверка доступа - если не тот юзер или не админ
 

		//получить группу пользователя
		$query_result = $usermodel->select_userdata($this->con,$userid);
		$row = mysqli_fetch_row($query_result);
		$usergroup = $row[11];//группа пользователя
		mysqli_free_result($query_result); 

		//если открываемое сообщение на редактирование не принадлежит автору		
		if ($autormessageid != $userid && $usergroup != 1){
		$_SESSION['session_error'] = 1; 
		header("Location: /error");		
		exit;	
		}


		//удаляем каталог с вложениями если он есть
		$attch_path = PATCH_ATTACHES.$messageid."/";
		if (file_exists($attch_path)){
		$this->delete_dir($attch_path);
		}
		

		//получить номер предыдущего сообщения
		$query_result = $this->model->select_previdmessage($this->con,$messageid,$topicid);
		$row = mysqli_fetch_row($query_result);
		$prevmid = $row[0];//код автора темы
		mysqli_free_result($query_result);


		//удалить сообщение по номеру сообщения
		$this->model->delete_message($this->con,$messageid);

		//рассчитать номер страницы по номеру сообщения
		$query_result = $this->model->select_numbermessage($this->con,$prevmid,$topicid);
		$row = mysqli_fetch_row($query_result);
		$nummessage = $row[0]-1;//количество сообщений всего в теме
		mysqli_free_result($query_result); 

		$page = 1;
		if ($nummessage > MESSAGES_IN_PAGE){		
			$total = $nummessage/MESSAGES_IN_PAGE;	
			$total =  ceil($total); //количество страниц	
			$page = $total;
		}

		header("Location: /forum/viewtopic/".$topicid."/".$page);
		exit;

    }
    //================================================================================

    //================================================================================
	//функция загрузки вложений
	//================================================================================
	function action_uploadattachfile()
    {
    	
    	$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
				exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}
		
						
			//если поле почты имени или капчи заполнено,  то считаем что робот заполняет форму
			if (($_POST["email"] <> '') || ($_POST["captcha"] <> '') || ($_POST["name"] <> '') ){
				exit;
			}
		
			//если не совпадает номер сессии, то форма отправлена с другого места а не с сайта
			if($_POST['snumber'] != md5(md5(session_id()))) 
			{ 
				exit; 
			}

			if(!isset($_POST["userid"]) || !isset($_POST["topicid"]) || !isset($_POST["messageid"])  ) {
				exit;
			}

			//верификация переменных
			$vtid = $this->number_valid($_POST["topicid"]);
			if (!$vtid){ Route::ErrorPage404(); } else { $verifytid = (int)$_POST["topicid"]; }
			$topicid  = $verifytid;

			$vmid = $this->number_valid($_POST["messageid"]);
			if (!$vmid){ Route::ErrorPage404(); } else { $verifymid = (int)$_POST["messageid"]; }
			$mid  = $verifymid;

			$vuid = $this->number_valid($_POST["userid"]);
			if (!$vuid){ Route::ErrorPage404(); } else { $verifyuid = (int)$_POST["userid"]; }	
			$puserid  = $verifyuid;

			$this->db_connect();
			$this->model = new Model_Forum();
			$usermodel = new Model();

			//блок проверки юзера по хэшу
			$truehash = $this->get_truehash($userid);
		
			if ($truehash == 0){	
				exit;
			}
		
			
			//проверка реального существования и соответствия номеров сообщения, темы, юзера
			
			$query_result = $this->model->select_onemessage($this->con,$mid);		
			$row = mysqli_fetch_row($query_result);
			$dbtopicid = $row[1];
			$dbuserid = $row[2];
			
			if (($dbtopicid != $vtid) or ($dbuserid != $vuid) or ($dbuserid != $userid) or ($vuid !=$userid)){
				exit;
			}
			
			
			$original_filename = $_FILES['attachfile']['name'];
			$temp_filename = $_FILES['attachfile']['tmp_name'];
			$filesize = $_FILES['attachfile']['size'];
			$filetype = $_FILES['attachfile']['type'];
			
			
			if (empty($original_filename)){
				print "не выбран файл для загрузки";
				exit;
			}
			
			//проверка загрузился ли файл
			if (!is_uploaded_file($temp_filename)) {
				print "ошибка загрузки файла на сервер";
				exit;
			}

			

			//проверка размера файла
			if ($filesize > MAX_ATTACHFILE_SIZE || $filesize == 0){
				print "превышен размер файла (или он равен 0 байт)";
				exit;
			}
	
			//проверка по расширениям
			//проверка Content-Type
			if(($filetype != "image/gif") && ($filetype != "image/jpg") && ($filetype != "image/jpeg") && ($filetype != "image/png") && ($filetype != "application/x-zip-compressed") ) {
				exit;		
			}				
			
			//Проверка расширения загружаемого файла
			$whitelist = array("gif", "jpg", "png", "zip");
			//получить расширение файла
			$ext = pathinfo($original_filename, PATHINFO_EXTENSION);
			
			if (!in_array($ext, $whitelist)) {
				exit;
			}			
						
			
			//создать каталог для размещения файлов
			$target_path = '.'.PATCH_ATTACHES.$mid."/";
			$this->create_dir($target_path);

			$zipmimetypes = array("application/x-compressed", "application/x-zip-compressed", "application/zip", "multipart/x-zip");	
			
			//предполагаем если файл не архив, то он изображение
			if ((!in_array($filetype, $zipmimetypes)) && ($ext != 'zip')){
			
				//для изображений один алгоритм а для архивов - другой
				
				//Проверка содержания файла изображения
				$imageinfo = getimagesize($temp_filename);
				if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpg' && $imageinfo['mime'] != 'image/jpeg'  && $imageinfo['mime'] != 'image/png' ) {
					exit;
				}
								
				$resaveimage = $this->image_resave($temp_filename, $original_filename, $target_path);
						
				if (!$resaveimage){ 
					exit;						
				}				
			}
			
			
			//сли файл архив - то просто перемещаем его
			if ((in_array($filetype, $zipmimetypes)) && ($ext == 'zip')){
					
				$target_path2 = $target_path . basename( $original_filename);
					//перемещение файла
				if(!move_uploaded_file($temp_filename, $target_path2)) {
					print "ошибка при перемещении файла в папку назначения";
					exit;			
				} 
			}

			//создать файл заглушку
			$this->create_indexphp($target_path);

			//вернуть разметку с списком вложений
			require_once("./Views/Blocks/showattaches.php");

    	
    }
    //================================================================================
    
    //================================================================================
	//функция удаления вложенго файла
	//================================================================================
	function action_deleteattachfile($mid,$filename)
    { 
	   
		$userid = 0;
    	//для незалогиненых пользователей возможность закрыта
		if(!isset($_SESSION["session_userid"])) {
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}
		
		//верификация переменных
		if (!isset($mid) ) {
		print 'Не задан номер сообщения'; exit;		
		}
		if (!isset($filename) ) {
		print 'Не указан файл'; exit;		
		}	
		
		$vmid = $this->number_valid($mid);
		if (!$vmid){ Route::ErrorPage404(); } else { $verifymid = (int)$mid; }
		//$verifymid = (int)$mid ;
		//if ($verifymid == 0 || $verifymid > MAX_ID){ Route::ErrorPage404(); }		
		$messageid  = $verifymid;
		
		$this->db_connect();
		$this->model = new Model_Forum();
		$usermodel = new Model();
		
		$verifyfilename = (string)$filename;
		$verifyfilename = mysqli_real_escape_string($this->con,strip_tags(trim($verifyfilename)));
		$attch_filename = $verifyfilename;
		
		

		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
	
		if ($truehash == 0){	
			exit;
		}
		
		$attch_path = '.'.PATCH_ATTACHES.$messageid."/";
		$this->deletfile($attch_path,$attch_filename);
		$cntf = $this->cntfiles_indir($attch_path);
		if ($cntf == 0){ 
			//	если папка пустая - то удалим папку
			$this->delete_dir($attch_path);
		}
	   
	   
	   require_once("./Views/Blocks/showattaches.php"); 
	}   
    //================================================================================    
}

?>