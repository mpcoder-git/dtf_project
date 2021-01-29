<?php 
class Model_Changerd 
{
	private $core_model;
	
    public function __construct(){
    	$this->core_model = new Model();    
    }


	//обновить данные пользователя
	public function update_userdata($con,$userid,$usernick,$username,$userfamily,$userotch,$useremail)
    {
    	$query = "update users set  
			nick='".$usernick."',
			name='".$username."',
			family='".$userfamily."',		
			otchestvo='".$userotch."',
			email='".$useremail."'
			where id = ".$userid;
		return $this->core_model->run_query($con,$query);
    }
	
	//сделать емайл неактивным
	public function update_noactivateemail($con,$userid)
	{
		$query = "update users set emailactivate = 0 where id = ".$userid;
		return $this->core_model->run_query($con,$query);
	}
}
?>