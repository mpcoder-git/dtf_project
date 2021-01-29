<?php 
class Model_Job 
{
	private $core_model;
	
    public function __construct(){
    	$this->core_model = new Model();    
    }

	//функции для работы с анкетой новичка -----------------------
	
	//подсчитать количество анкет новичка для пользователя
	public function select_cntbeginnerblanks($con,$userid)
    {
    	$query = "select count(*) as cn  from beginnerprofiles where Userid=".$userid;
		return $this->core_model->run_query($con,$query);
    }
	
	//вставка анкеты новичка
	public function insert_newbeginnerblank($con,$userid,$fields)
    {
    	if(is_array($fields)) {
            // преобразуем элементы массива в переменные
            extract($fields);
        }
		$query = "INSERT INTO beginnerprofiles 
			(userid, enterdata, family, name, otchestvo, field1, field2, field3, field4, field5, field6, field7, field8, field9, field10,
			field11, field12, field13, field14, field15, field16, field17, field18, field19, field20,
			field21, field22, field23, field24, field25, agree)		 
			VALUES(".$userid.",'".date("Y-m-d",time())."', '".$family."', '".$name."', '".$otch."', '".$field1."', '".$field2."', '".$field3."', '".$field4."', '".$field5."', 
			'".$field6."', '".$field7."', '".$field8."', '".$field9."', '".$field10."',
			'".$field11."', '".$field12."', '".$field13."', '".$field14."', '".$field15."',
			'".$field16."', '".$field17."', '".$field18."', '".$field19."', '".$field20."',
			'".$field21."', '".$field122."', '".$field23."', '".$field24."', '".$field25."', 1)";
			
			
		return $this->core_model->run_query($con,$query);
    }
	
	//обновление анкеты новичка
	public function update_beginnerblank($con,$userid,$fields)
    {
    	if(is_array($fields)) {
            // преобразуем элементы массива в переменные
            extract($fields);
        }
		$query = "update beginnerprofiles set  
			enterdata='".date("Y-m-d",time())."',
			family='".$family."',
			name='".$name."',
			otchestvo='".$otch."',
			field1='".$field1."',
			field2='".$field2."',
			field3='".$field3."',
			field4='".$field4."',
			field5='".$field5."',
			field6='".$field6."',
			field7='".$field7."',
			field8='".$field8."',
			field9='".$field9."',
			field10='".$field10."',
			field11='".$field11."',
			field12='".$field12."',
			field13='".$field13."',
			field14='".$field14."',
			field15='".$field15."',
			field16='".$field16."',
			field17='".$field17."',
			field18='".$field18."',
			field19='".$field19."',
			field20='".$field20."',
			field21='".$field21."',
			field22='".$field22."',
			field23='".$field23."',
			field24='".$field24."',
			field25='".$field25."',
			agree = 1
			where userid = ".$userid;
			
			
		return $this->core_model->run_query($con,$query);
    }

    //функции для работы с анкетой трейдера -----------------------
	
	//подсчитать количество анкет новичка для пользователя
	public function select_cnttraderblanks($con,$userid)
    {
    	$query = "select count(*) as cn  from traderprofiles where Userid=".$userid;
		return $this->core_model->run_query($con,$query);
    }
	
	//вставка анкеты новичка
	public function insert_newtraderblank($con,$userid,$fields)
    {
    	if(is_array($fields)) {
            // преобразуем элементы массива в переменные
            extract($fields);
        }
		$query = "INSERT INTO traderprofiles 
			(userid, enterdata, family, name, otchestvo, field1, field2, field3, field4, field5, field6, field7, field8, field9, field10,
			field11, field12, field13, field14, field15, field16, field17, field18, field19, field20,
			field21, field22, field23, field24, agree)		 
			VALUES(".$userid.",'".date("Y-m-d",time())."', '".$family."', '".$name."', '".$otch."', '".$field1."', '".$field2."', '".$field3."', '".$field4."', '".$field5."', 
			'".$field6."', '".$field7."', '".$field8."', '".$field9."', '".$field10."',
			'".$field11."', '".$field12."', '".$field13."', '".$field14."', '".$field15."',
			'".$field16."', '".$field17."', '".$field18."', '".$field19."', '".$field20."',
			'".$field21."', '".$field122."', '".$field23."', '".$field24."', 1)";
			
			
		return $this->core_model->run_query($con,$query);
    }
	
	//обновление анкеты новичка
	public function update_traderblank($con,$userid,$fields)
    {
    	if(is_array($fields)) {
            // преобразуем элементы массива в переменные
            extract($fields);
        }
		$query = "update traderprofiles set  
			enterdata='".date("Y-m-d",time())."',
			family='".$family."',
			name='".$name."',
			otchestvo='".$otch."',
			field1='".$field1."',
			field2='".$field2."',
			field3='".$field3."',
			field4='".$field4."',
			field5='".$field5."',
			field6='".$field6."',
			field7='".$field7."',
			field8='".$field8."',
			field9='".$field9."',
			field10='".$field10."',
			field11='".$field11."',
			field12='".$field12."',
			field13='".$field13."',
			field14='".$field14."',
			field15='".$field15."',
			field16='".$field16."',
			field17='".$field17."',
			field18='".$field18."',
			field19='".$field19."',
			field20='".$field20."',
			field21='".$field21."',
			field22='".$field22."',
			field23='".$field23."',
			field24='".$field24."',
			agree = 1
			where userid = ".$userid;
			
			
		return $this->core_model->run_query($con,$query);
    }




	
}
?>