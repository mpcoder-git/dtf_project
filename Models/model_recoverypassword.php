<?php 
class Model_Recoverypassword 
{
	private $core_model;
	
    public function __construct(){
    	$this->core_model = new Model();    
    }


	//выбрать данные пользователя по емайлу
	public function select_userdataforemail($con,$useremail)
    {
    	$query = "SELECT id,email,emailactivate, password, nick, name, family, otchestvo,hash,lessons,lessondata,groupusers FROM users WHERE email='".$useremail."' ";
		return $this->core_model->run_query($con,$query);
    }
	
	//подсчитать количество строк восстановления
	public function select_cntrecoveryrows($con,$userid)
    {
    	$query = "select count(*) as cnt  from recoverypass where Userid=".$userid;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбор строки восстановления по ид
	public function select_recoveryrow($con,$userid)
    {
    	$query = "SELECT accesshash, createdata FROM recoverypass WHERE userid=".$userid;
		return $this->core_model->run_query($con,$query);
    }
	
	
	//вставка новой строки восстановления
	public function insert_newrecoveryrows($con,$userid,$date,$accesshash)
    {
    	$query = "INSERT INTO recoverypass (userid, createdata, accesshash) VALUES($userid, '$date','$accesshash')";
		return $this->core_model->run_query($con,$query);
    }
	
	//обновление строки восстановления
	public function update_recoveryrows($con,$userid,$date,$accesshash)
    {
    	$query = "update recoverypass set accesshash='".$accesshash."', createdata='".$date."' where Userid=".$userid;
		return $this->core_model->run_query($con,$query);
    }
	
	//удалить строку восстановления
	public function delete_recoveryrow($con,$userid)
    {
    	$query = "DELETE from recoverypass  WHERE userid=".$userid;
		return $this->core_model->run_query($con,$query);
    }
	
	
	//обновить пароль пользователя
	public function update_userpass($con,$userid,$newpassword)
    {
    	$query = "UPDATE users SET password='".$newpassword."' WHERE id=".$userid;
		return $this->core_model->run_query($con,$query);
    }
}
?>