<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Signals extends Controller
{
	
	function action_index()
    { 
	
		$this->db_connect();
		
		$parameters = $this->get_parameters('Сигналы');
		
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
				
		/*
		if(file_exists('./Signalsfiles/Ideas.txt')){
		
			$file_ideas = fopen ("./Signalsfiles/Ideas.txt", "r");
			
			$txt = fread ($file_ideas, filesize("./Signalsfiles/Ideas.txt")) or die ("Ошибка чтения файла");
			fclose($file_ideas);
			
			
			//massive
			$ideas_exparr = explode(";", $txt);
			array_pop($ideas_exparr);		
			//теперь надо записать массивы второго уровня
			

			foreach ($ideas_exparr as $value) {
				$ideas_arr[] = explode(",", $value);

			}
			
			
			$parameters['ideas'] = $ideas_arr;
			unset($ideas_exparr );
		
		}
		*/
		$this->view->generate('signals_view.php', 'template_view.php', $parameters);
		
    }
	
	
}
?>