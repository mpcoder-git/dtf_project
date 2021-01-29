<?php 
class Model_Lessons 
{
	private $core_model;
	
    public function __construct(){
    	$this->core_model = new Model();    
    }

	//выбрать вопрос
	public function select_question($con,$numberlesson,$numberquestion)
    {
    	$query = "select question  from testsquestions where Parenttest=".$numberlesson." and numberquestion=".$numberquestion;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать количество вопросов в занятии
	public function select_cntquestion($con,$numberlesson)
    {
    	$query = "select count(*) as cn  from testsquestions where Parenttest=".$numberlesson;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать ответы
	public function select_answers($con,$numberlesson,$numberquestion)
    {
    	$query = "select  numberanswer, answer, correctanswer  from testsanswers where Parenttest=".$numberlesson." and parentquestion=".$numberquestion;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать баллы выбраного ответа
	public function select_answerballs($con,$numberlesson,$numberquestion,$numberanswer)
    {
    	$query = "select  numberanswer, answer, correctanswer  from testsanswers where Parenttest=".$numberlesson." and parentquestion=".$numberquestion." and numberanswer=".$numberanswer;
		return $this->core_model->run_query($con,$query);
    }
	
	//выбрать ответы
	public function update_userlesson($con,$number, $userid)
    {
    	$query = "update users set lessons=".$number.", lessondata='".date("Y-m-d")."' where id=".$userid;
		return $this->core_model->run_query($con,$query);
    }
}
?>