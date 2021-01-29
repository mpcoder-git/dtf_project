<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Training extends Controller
{	
	
	function action_index()
    { 
		
		$this->db_connect();
		//$this->model = new Model_Main();
		
		$parameters = $this->get_parameters('Обучение');
		
		$this->view->generate('training_view.php', 'template_view.php', $parameters);
		
    }
	
	function action_kurs1()
    { 
		
		$this->db_connect();
		//$this->model = new Model_Main();
		
		$parameters = $this->get_parameters('Обучение - Курс 1');
		
		$this->view->generate('kurs1_view.php', 'template_view.php', $parameters);
		
    }
	
}

?>