<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php'); 	
?>
<?php 
class Model_Forum 
{
	private $core_model;
	
	//================================================================================
    public function __construct(){
    	$this->core_model = new Model();    
    }
	//================================================================================
	

	//выбрать главные разделы

	public function select_forumcatalog($con)
    {
    	$query = "select id, sectionname from forumcatalog where parentid=0 order by sortnumber desc";
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать подразделы главного раздела
	public function select_forumsubcatalog($con,$spid)
    {
    	$query = "select id, sectionname, (select count(*)  from forumtopics ft where ft.catalogid=fc.id) as topicscnt from forumcatalog fc where parentid=".$spid." order by sortnumber asc";
		return $this->core_model->run_query($con,$query);
    }
	
	//================================================================================
	// Работа с темами
	//================================================================================
	
	//выбрать заголовок темы
	public function select_razdelname($con,$pid)
    {
    	$query = "select Sectionname from forumcatalog where Id=".$pid;
		return $this->core_model->run_query($con,$query);
    }
		
	//выбрать лимитированный список тем 
	public function select_limitedtopicslist($con,$id,$start,$num)
    {
    	$query = "select id, topicname, createdata, (select count(*) as cnt  from forummessages where topicid=ft.id) as cntmessages from forumtopics ft where catalogid=".$id." order by createdata desc LIMIT ".$start.",".$num;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать количество тем 
	public function select_cnttopics($con,$pid)
    {
    	$query = "SELECT COUNT(*) as cntt FROM forumtopics where catalogid=".$pid;
		return $this->core_model->run_query($con,$query);
    }
	
	//Обновить название темы
	public function update_topicname($con,$newtopicname,$tid)
    {
    	$query = "update forumtopics set topicname='".$newtopicname."' where id=".$tid;	
		return $this->core_model->run_query($con,$query);
    }

	//================================================================================
	//работа с сообщениями
	//================================================================================
	
	//получить заголовки темы и раздела 
	public function select_rtnames($con,$tid)
    {
    	$query = "select fc.Sectionname, ft.Topicname, fc.Id, ft.Userid, ft.Id from forumtopics ft join forumcatalog fc on fc.Id=ft.Catalogid where ft.Id=".$tid;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать количество сообщений 
	public function select_cntmessages($con,$tid)
    {
    	$query = "SELECT COUNT(*) as cntm FROM forummessages where active=1 and topicid=".$tid;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать лимитированный список сообщений
	public function select_limitedmessageslist($con,$tid,$start,$num)
    {
    	$query = "select fm.id, fm.message, fm.userid, fm.createdata, us.nick, us.groupusers  from forummessages fm join users us on us.id=fm.userid where fm.topicid=".$tid." and active=1 order by fm.createdata  asc LIMIT ".$start.", ". $num;
		return $this->core_model->run_query($con,$query);
    }
	
	//вставка нового сообщения
	public function insert_newmessage($con,$topicid,$userid,$date)
    {
    	$query = "INSERT INTO forummessages (topicid, userid, createdata) VALUES($topicid,$userid, '$date')";			
		return $this->core_model->run_query($con,$query);
    }
	
    //вставка нового быстрого сообщения
	public function insert_qrnewmessage($con,$topicid,$userid,$date,$message)
    {
    	$query = "INSERT INTO forummessages (topicid, userid, createdata,message,active) VALUES($topicid,$userid, '$date','$message',1)";			
		return $this->core_model->run_query($con,$query);
    }


	//выбор неактивного сообщения
	public function select_notactivemessage($con,$userid)
    {
    	$query = "select id, topicid, userid, createdata, message, active  from forummessages where active = 0 and userid = ".$userid;			
		return $this->core_model->run_query($con,$query);
    }
	
	//Обновить текст сообщения
	public function update_message($con,$messageid,$newmessage,$date)
    {
    	$query = "update forummessages set message='".$newmessage."', createdata='".$date."', active=1 where id=".$messageid;	
		return $this->core_model->run_query($con,$query);
    }

    //выбор одного сообщения
	public function select_onemessage($con,$messageid)
    {
    	$query = "select id, topicid, userid, createdata, message, active  from forummessages where id = ".$messageid;			
		return $this->core_model->run_query($con,$query);
    }

    //получить порядковый номер сообщения в теме
	public function select_numbermessage($con,$messageid,$topicid)
    {
    	$query = "SELECT count(*)+1 as cntp FROM forummessages WHERE active=1 and  id < ".$messageid." and topicid=".$topicid;			
		return $this->core_model->run_query($con,$query);
    }

    //получить предпоследний номер сообщения в теме
	public function select_previdmessage($con,$messageid,$topicid)
    {
    	$query = "SELECT max(id) AS id FROM forummessages WHERE active=1 and id < ".$messageid." and topicid=".$topicid;			
		return $this->core_model->run_query($con,$query);
    }


    //удаление сообщения
	public function delete_message($con,$messageid)
    {
    	$query = "delete from forummessages where id=".$messageid;		
		return $this->core_model->run_query($con,$query);
    }



}
?>