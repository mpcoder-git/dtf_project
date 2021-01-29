<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php 
class Controller_Main extends Controller
{	
	
	function action_index()
    { 
		


		$this->db_connect();
		$this->model = new Model_Main();
		
		$parameters = $this->get_parameters('Главная');
		
		//уникальный одноразовый токен
		$skey = $this->generateCode(32);
		$_SESSION['session_skey']=$skey;
		$parameters['skey'] = $skey;
		
		
		$query_result = $this->model->select_twonews($this->con);
		
		$i=0;
		$arrtitle = array();
		$arrdate = array();
		$arrnewstext = array();
		
		while( $row = mysqli_fetch_assoc($query_result) ){
			$i=$i+1;
			$arrtitle[$i]= $row['newstitle'];
			$arrdate[$i]= $row['newsdata'];
			$arrnewstext[$i]= $row['newstext'];
		}
				
		$arrtitle1 = $arrtitle[1];
		$arrtitle2 = $arrtitle[2];		
		$arrdate1 = $arrdate[1];
		$arrdate2 = $arrdate[2];
		$arrnewstext1 = $arrnewstext[1];
		$arrnewstext2 = $arrnewstext[2];
		
		mysqli_free_result($query_result);
		
		$splittext1 = $this->str_limit(strip_tags($arrnewstext1,'<br>'),250);
		$splittext2 = $this->str_limit(strip_tags($arrnewstext2,'<br>'),250);
		
		
		
		//формируем массив переменных		
		$parameters['arrtitle1'] = $arrtitle1;
		$parameters['arrtitle2'] = $arrtitle2;
		$parameters['arrdate1'] = $arrdate1;
		$parameters['arrdate2'] = $arrdate2;
		$parameters['arrnewstext1'] = $arrnewstext1;
		$parameters['arrnewstext2'] = $arrnewstext2;
		$parameters['splittext1'] = $splittext1;
		$parameters['splittext2'] = $splittext2;
		
		
		$this->view->generate('main_view.php', 'template_view.php', $parameters);
		
    }
	
}

?>