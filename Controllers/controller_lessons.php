<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Lessons extends Controller
{
	
	function action_index()
    { 
		
		if(!isset($_SESSION["session_userid"])) {
			header("location: /registration");
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}

		
		$this->db_connect();
		
		$this->model = new Model_Lessons();
		$usermodel = new Model();
		
		$parameters = $this->get_parameters('Занятия');		
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		$query_result = $usermodel->select_userdata($this->con,$userid);
		$row = mysqli_fetch_row($query_result);
		$lessons =  $row[9];
		$parameters['lessons'] =$lessons;
		
		$this->view->generate('lessons_view.php', 'template_view.php', $parameters);
		
    }
	
	//================================================================================
	//функция показывает текст урока
	//================================================================================
	function action_lesson($number)
    { 
		
		$userid = 0;
		if(!isset($_SESSION["session_userid"])) {
			$_SESSION['session_error'] = 1; 
			header("location: /error");
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}
		
		if (!isset($number)) {
			header("location: /lessons");
			exit;			
		} else {
			
			$verifynumber = (int)$number;
			if ($verifynumber == 0 || $verifynumber > 10){ Route::ErrorPage404(); }
		
			$_SESSION['number_lesson'] = $verifynumber;
		}
	
		$this->db_connect();
		
		//$this->model = new Model_Lessons();
		$usermodel = new Model();
		
		$parameters = $this->get_parameters('Занятиe '.$number);
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash == 0){
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
		
		//проверка по номеру задания (не может быть больше дозволеного)
		//нельзя начать урок, пока не изучен предыдущий
		$query_result = $usermodel->select_userdata($this->con,$userid);
		$row = mysqli_fetch_row($query_result);
		$ready_lessonnumber = $row[9]; //пройденый урок
		mysqli_free_result($query_result);
		
		if ($number > $ready_lessonnumber+1){
			$_SESSION['session_error'] = 1;  
			header("location: /error");
			exit;
		}
		
				
		//файл с текстом урока
		$lessonfile = ".".PATCH_LESSONS."lesson".$number.".php";

		$parameters['lessonfile'] = $lessonfile;
		$parameters['number'] = $number;
		
		
		$this->view->generate('lesson_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
	}
	
	//================================================================================
	//функция проведения тестирования
	//================================================================================
	function action_test()
    { 
		
		$userid = 0;
		if(!isset($_SESSION["session_userid"])) {
			$_SESSION['session_error'] = 1; 
			header("location: /error");
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}
		
		//номер теста
		if (!isset($_SESSION['number_lesson'])) {
		header("location: /lessons");	
		} else {
		$number=$_SESSION['number_lesson'];	
		}
		
		$numberlesson = $_SESSION['number_lesson'];
		$this->db_connect();
		
		$this->model = new Model_Lessons();
		$usermodel = new Model();
		
		//узнать количество вопросов в занятии
		$query_result = $this->model->select_cntquestion($this->con,$numberlesson);
		$row = mysqli_fetch_row($query_result);
		$questionscount = $row[0]; //количество вопросов
		mysqli_free_result($query_result);
		
		if ($questionscount > 0) {
			
			$parameters = $this->get_parameters('Тест к занятию № '.$number);
			
			
			//если небыло ответа - то обнуляем номер вопроса
			if (!isset($_POST['answer'])) {
			unset($_SESSION['vopros_number']);
			unset($_SESSION['test_result']);
			}
			
			//если номер вопроса не задан, по умолчанию принимаем его = 1
			if (!isset($_SESSION['vopros_number'])) {
			$_SESSION['vopros_number']=1;
			} else {
			//$numberquestion = $_SESSION['vopros_number'];
			$_SESSION['vopros_number'] = $_SESSION['vopros_number'] + 1;		
			}
					
			$numberquestion = $_SESSION['vopros_number'];
			
			//выбрать вопрос
			$query_result = $this->model->select_question($this->con,$numberlesson,$numberquestion);
			$row = mysqli_fetch_row($query_result);
			$question = $row[0]; //вопрос		
			$parameters['question'] = $question;
			mysqli_free_result($query_result);		
					
			//выбрать ответы
			$query_result = $this->model->select_answers($this->con,$numberlesson,$numberquestion);
			$parameters['answers_result'] = $query_result;
			
			
			//при получении отправленой формы подсчитываем баллы правильные ответы
			if (isset($_POST['answer'])) {
				//получить баллы за отправленый ответ
				$numberanswer = $_POST['answer'];
				//print $numberlesson.' '.$numberquestion.' '.$numberanswer;
				$balls_result = $this->model->select_answerballs($this->con,$numberlesson,$numberquestion-1,$numberanswer);
				$row = mysqli_fetch_row($balls_result);
				$balls = $row[2]; //баллы за правильный ответ
				mysqli_free_result($balls_result);
				
				
				if (!isset($_SESSION['test_result'])){ $_SESSION['test_result']  = 0; }
				if (isset($_SESSION['test_result'])){ $_SESSION['test_result']  =$_SESSION['test_result'] + $balls; }
				
				//последний вопрос
				if ($numberquestion-1 == $questionscount){
					//если есть хоть один неправильный ответ - то перебрасываем на начало урока
					if ($_SESSION['test_result'] < $questionscount){
						//чистим сессии
						unset($_SESSION['vopros_number']);
						unset($_SESSION['test_result']);
						unset($_SESSION['number_lesson']);
						header("location: /lessons/lesson/".$number);
					} else {
						$this->model->update_userlesson($this->con,$numberlesson,$userid);
						
						//чистим сессии
						unset($_SESSION['vopros_number']);
						unset($_SESSION['test_result']);
						unset($_SESSION['number_lesson']);
						header("location: /cabinet");
						exit;					
					}
				}
				
			}
			
			$this->view->generate('lessontest_view.php', 'template_view.php', $parameters);
			mysqli_free_result($query_result);
			mysqli_close($this->con);
		}
		
		
		//если вопросов нет - просто сохраним
		if ($questionscount == 0) {
			
			$this->model->update_userlesson($this->con,$numberlesson,$userid);
						
			//чистим сессии
			unset($_SESSION['vopros_number']);
			unset($_SESSION['test_result']);
			unset($_SESSION['number_lesson']);
			mysqli_close($this->con);
			header("location: /cabinet");
			exit;	
		}
	}
}

?>