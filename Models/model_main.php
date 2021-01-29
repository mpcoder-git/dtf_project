<?php 
class Model_Main
{
    
    
    public function __construct(){
        
    }
	
	//получить езультат запроса
	public function run_query($con, $query)
    {
		$result =mysqli_query($con,$query);
		if (!$result) {
			die('Ошибка выполнения запроса:' . mysqli_error());
		} else {
			return $result;
		}
    }
	
	//получить ве последних новости
	public function select_twonews($con)
    {
    	$query = "select id, newsdata, newstitle, newstext from news order by newsdata desc limit 2";
		return $this->run_query($con,$query);	
    }
	
	
	
}
?>