<?php 
class Model_News 
{
	private $core_model;
	
    public function __construct(){
    	$this->core_model = new Model();    
    }


	//выбрать все новости
	public function select_newsall($con)
    {
    	$query = "select id, newsdata, newstitle, newstext from news where active=1 order by newsdata desc";
		return $this->core_model->run_query($con,$query);
    }
	
}
?>