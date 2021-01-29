<?php 
class Model_Changepass 
{
	private $core_model;
	
    public function __construct(){
    	$this->core_model = new Model();    
    }


	//обновить пароль пользователя
	public function update_userpass($con,$userid,$newpassword)
    {
    	$query = "UPDATE users SET password='".$newpassword."' WHERE id=".$userid;
		return $this->core_model->run_query($con,$query);
    }
	
}
?>