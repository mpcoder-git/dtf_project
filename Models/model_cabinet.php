<?php 
class Model_Cabinet
{
    private $core_model;

    public function __construct(){
    	$this->core_model = new Model();    
    }
	
	
	//получить количество записей в таблице новичков
	public function select_cntbeginprofile($con)
    {
    	$query = "select count(*) as cnt  from beginnerprofiles where Userid=".$_SESSION['session_userid'];
		return $this->core_model->run_query($con,$query);	
    }
	
	//получить данные в таблице новичков
	public function select_beginprofile($con)
    {
    	$query = "select enterdata from beginnerprofiles where Userid=".$_SESSION['session_userid'];
		return $this->core_model->run_query($con,$query);
    }
	
	//получить колоичество записей в таблице трейдеров
	public function select_cnttradersprofile($con)
    {
    	$query = "select count(*) as cnt  from traderprofiles where Userid=".$_SESSION['session_userid'];
		return $this->core_model->run_query($con,$query);
    }
	
	//получить данные в таблице трейдеров
	public function select_tradersprofile($con)
    {
    	$query = "select enterdata from traderprofiles where Userid=".$_SESSION['session_userid'];
		return $this->core_model->run_query($con,$query);
    }

}
?>