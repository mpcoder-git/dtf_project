<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Logout extends Controller
{	
	
	function action_index()
    { 
		
		$this->db_connect();
		$this->model = new Model_Logout();
		$this->model->null_hash($this->con, $_SESSION['session_userid']);
		
		
		unset($_SESSION['session_userid']);
		unset($_SESSION['session_userhash']);
		session_destroy();
		header("location: /Login");		
    }
	
}

?>