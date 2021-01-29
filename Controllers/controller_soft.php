<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Soft extends Controller
{
	
	function action_index()
    { 
	
		$this->db_connect();
		$parameters = $this->get_parameters('Софт');		
				
		$this->view->generate('soft_view.php', 'template_view.php', $parameters);
		
    }
	
}
?>