<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_PropOffice extends Controller
{
	
	function action_index()
    { 
	
		$this->db_connect();
		$parameters = $this->get_parameters('Проп трейдинг офис');		
				
		$this->view->generate('propoffice_view.php', 'template_view.php', $parameters);
		
    }
	
}
?>