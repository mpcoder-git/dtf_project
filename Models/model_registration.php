<?php 
class Model_Registration 
{
    private $core_model;
    
    public function __construct(){
		$this->core_model = new Model();    
    }
	
	//подсчет количества пользователей по ид
	public function select_countuser($con,$useremail)
    {
    	$query = $query="SELECT count(*) FROM users WHERE email='".$useremail."'";
		return $this->core_model->run_query($con,$query);
    }
	
	//вставка нового пользователя
	public function insert_newuser($con,$usernick,$username,$userotch,$userfamily,$useremail,$password)
    {
    	$query = "INSERT INTO users
			(nick, name, otchestvo, family, email, password,emailactivate, regdate) 
		VALUES('$usernick','$username', '$userotch','$userfamily', '$useremail', '$password',0, '".date("Y-m-d G:i")."')";
		return $this->core_model->run_query($con,$query);
    }
	
	

}
?>