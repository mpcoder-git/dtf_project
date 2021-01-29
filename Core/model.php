<?php
class Model
{
	
	//получить езультат запроса
	public function run_query($con, $query)
    {
		$result =mysqli_query($con,$query);
		if (!$result) {
			die('Ошибка выполнения запроса');
		} else {
			return $result;
		}
    }


	//выбор данных о пользователе
	public function select_userdata($con,$userid)
    {
    	$query = "SELECT id,email,emailactivate, password, nick, name, family, otchestvo,hash,lessons,lessondata,groupusers FROM users WHERE Id=".$userid;
    	return $this->run_query($con, $query);
    }



}
?>