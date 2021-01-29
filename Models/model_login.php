<?php 
class Model_Login
{
    private $core_model;
    
    public function __construct(){
		$this->core_model = new Model();    
    }

    public function select_loginuserdata($con, $useremail)
    {
    	$query = "SELECT id,email,password,emailactivate FROM users WHERE email='".$useremail."' ";
		return $this->core_model->run_query($con,$query);
    }
	
	public function update_hash($con, $hash, $dbuserid)
    {
		$query = "UPDATE users SET hash='".$hash."',enterdate='".date("Y-m-d G:i")."' WHERE id=".$dbuserid;
		return $this->core_model->run_query($con,$query);
    }
	
	public function update_mailactive($con, $dbuserid)
    {
		$query = "UPDATE users SET emailactivate=1 WHERE id=".$dbuserid;
		return $this->core_model->run_query($con,$query);
    }
	
}
?>