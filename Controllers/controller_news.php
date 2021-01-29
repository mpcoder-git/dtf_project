<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php');	
?>
<?php 
class Controller_News extends Controller
{
	
	function action_index()
    { 

		$this->db_connect();
		
		$this->model = new Model_News();
		
		$parameters = $this->get_parameters('Новости');		
		$query_result = $this->model->select_newsall($this->con);
		
		$parameters['news_result'] =$query_result;
		
		$this->view->generate('news_view.php', 'template_view.php', $parameters);
		mysqli_close($this->con);
    }
	
}

?>