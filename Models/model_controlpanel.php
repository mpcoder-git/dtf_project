<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php'); 		
?>
<?php 
class Model_Controlpanel
{
    private $core_model;
    
    public function __construct(){
		$this->core_model = new Model();    
    }
	
	//работа с пользователями
	
	//выбрать данные пользователя по емайлу
    public function select_loginuserdata($con, $useremail)
    {
    	$query = "SELECT id,email,password,emailactivate FROM users WHERE email='".$useremail."' ";
		return $this->core_model->run_query($con,$query);
    }
	//обновление хэша пользователя
	public function update_hash($con, $hash, $dbuserid)
    {
		$query = "UPDATE users SET hash='".$hash."' WHERE id=".$dbuserid;
		return $this->core_model->run_query($con,$query);
    }
	
	//обнуление хэша пользователя
	public function null_hash($con, $dbuserid)
    {
		$query = "UPDATE users SET hash=null WHERE id=".$dbuserid;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать пользователей по параметрам
    public function select_searchusers($con, $column, $searchtext)
    {
    	$query = "SELECT id,family, name, otchestvo, nick, email, emailactivate FROM users WHERE ".$column." like ('%$searchtext%')";
		return $this->core_model->run_query($con,$query);
    }
	
	//активация пользователя
	public function update_useractivate($con, $dbuserid)
    {
		$query = "UPDATE users SET emailactivate=1 WHERE id=".$dbuserid;
		return $this->core_model->run_query($con,$query);
    }
	
	//работа с новостями ==========================================================
	
	//выбрать все новости и отсортировать по датам
	public function select_news($con)
    {
    	$query = "select id, newsdata, newstitle, newstext, active from news order by newsdata desc";
		return $this->core_model->run_query($con,$query);
    }
	//выбрать одну новость по номеру
	public function select_onenews($con,$newsid)
    {
    	$query = "select id, newsdata, newstitle, newstext, active from news where id=".$newsid;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбор неактивной новости
	public function select_notactivenews($con)
    {
    	$query = "select id, newsdata, newstitle, newstext, active  from news where active = 0";			
		return $this->core_model->run_query($con,$query);
    }
	
	//вставка новой новости
	public function insert_newnews($con,$date)
    {
    	$query = "INSERT INTO news (newsdata) VALUES('$date')";			
		return $this->core_model->run_query($con,$query);
    }
	
	//Обновить текст новости
	public function update_news($con,$newsid,$newstitle,$newnews,$date)
    {
    	$query = "update news set newstitle='".$newstitle."', newstext='".$newnews."', newsdata='".$date."', active=1 where id=".$newsid;	
		return $this->core_model->run_query($con,$query);
    }
	
	//удаление новости
	public function delete_news($con,$newsid)
    {
    	$query = "delete from news where id=".$newsid;		
		return $this->core_model->run_query($con,$query);
    }
	
	//==================================================================================
}
?>