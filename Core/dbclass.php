<?php
class DBClass
{
    public $con;    		//соединение с базой
	public $query_result; 	//результат выполнения запроса
	
	//=================================================================
	// Функция соединения с базой данных 
	//=================================================================
	public function db_connect()
	{
		//загрузить настройки
		require("./Config/constants.php");

		$this->con = new mysqli(DB_SERVER,DB_USER, DB_PASS, DB_NAME) or die(mysqli_connect_error());

		/* проверка подключения */
		if (mysqli_connect_errno()) {
			printf("Не удалось подключиться: %s\n", mysqli_connect_error());
			exit();
		}

		//установка кодировки
		//mysqli_set_charset($this->con, "utf8");
		$this->con->set_charset("utf8");
	}
	
	//=================================================================
	// Функция закрытия соединения с базой данных 
	//=================================================================
	public function db_close()
	{
		$this->con->close();
	}
	
	//=================================================================
	// Функция выполнения запроса и получения результата 
	//=================================================================
	public function run_query($query)
	{
		$result = $this->con->query($query);
		if (!$result) {
			die('Ошибка выполнения запроса:' . mysqli_error());
			exit;
		}
				
		return $result;
	}
	
}
?>