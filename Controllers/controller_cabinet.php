<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Cabinet extends Controller
{	

	function action_index()
    { 
				
		if(!isset($_SESSION["session_userid"])) {
			$_SESSION['session_error'] = 1; 
			header("Location: /Error");	
			exit;
		} else {
			$userid = $_SESSION["session_userid"];
		}

		$this->db_connect();
		
		$this->model = new Model_Cabinet();
		
		$parameters = $this->get_parameters('Личный кабинет');
		
		//блок проверки юзера по хэшу
		$truehash = $this->get_truehash($userid);
		
		if ($truehash > 0){		
			
			$user_model = new Model();			
			$query_result = $user_model->select_userdata($this->con,$userid);
			$row = mysqli_fetch_row($query_result);
								
				$nick = $row[4];
				$name = $row[5];
				$family = $row[6];
				$lessons = $row[9];
				$lesdata = $row[10];
				$otch = $row[7];
			
				$parameters['nick'] = $nick;
				$parameters['name'] = $name;
				$parameters['family'] = $family;
				$parameters['lessons'] = $lessons;
				$parameters['lesdata'] = $lesdata;
				$parameters['otch'] = $otch;
				
			
			$query_result = $this->model->select_cntbeginprofile($this->con);
			$row = mysqli_fetch_row($query_result);				
			$beginprofile = $row[0];
				
			$query_result = $this->model->select_beginprofile($this->con);
			$row = mysqli_fetch_row($query_result);				
			$bpenterdata = $row[0];	
			
			$query_result = $this->model->select_cnttradersprofile($this->con);
			$row = mysqli_fetch_row($query_result);				
			$traderprofile = $row[0];
				
			$query_result = $this->model->select_tradersprofile($this->con);
			$row = mysqli_fetch_row($query_result);				
			$tpenterdata = $row[0];	

			mysqli_free_result($query_result);
			
			$parameters['beginprofile'] = $beginprofile;
			$parameters['bpenterdata'] = $bpenterdata;
			$parameters['traderprofile'] = $traderprofile;
			$parameters['tpenterdata'] = $tpenterdata;
		
			
			
			$this->view->generate('cabinet_view.php', 'template_view.php', $parameters);
			
		} else {
			$_SESSION['session_error'] = 1; 
			header("Location: /error");	
			exit;
		}
				
    }
	



}

?>