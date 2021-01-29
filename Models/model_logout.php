<?php 
class Model_Logout
{
    public $query_result;
    
    public function __construct(){
        
    }
	
	public function null_hash($con, $dbuserid)
    {
		$query = "UPDATE users SET hash=null WHERE id=".$dbuserid;
		$result =mysqli_query($con,$query);
		if (!$result) {
			echo('Ошибка выполнения запроса:' . mysqli_error());
		} else {
		$this->query_result = $result;	
		}
    }
}
?>