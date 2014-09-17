<?php
Class CurrencyAct extends CheckAct{
	public function __construct(){
		parent::__construct();
	}
	
	public function act_getCurrencyList(){
		return M('Currency')->getCurrencyList();
	}
	
	public function act_getCurrency(){
		$id    = isset($_GET['id']) ? $_GET['id'] : '';
		return M('Currency')->getCurrency($id);
	}
	public function act_insert(){
		$id                       = isset($_POST['currId']) ? $_POST['currId'] :'';
		$data                     = array();
		$data['currency']         = isset($_POST['currency']) ? $_POST['currency'] :'';
		$data['rates']            = isset($_POST['rates']) ? $_POST['rates'] :'';
		$data['userId']           = get_userid();
		$data['modefyTime']       = time();
		return M('Currency')->insertData($data);
	}
	public function act_update(){
		$id                       = isset($_POST['currId']) ? $_POST['currId'] :'';
		$data                     = array();
		$data['currency']         = isset($_POST['currency']) ? $_POST['currency'] :'';
		$data['rates']            = isset($_POST['rates']) ? $_POST['rates'] :'';
		$data['userId']           = 9;
		$data['modefyTime']       = time();
		return M('Currency')->updateData($id,$data);
	}
	public function act_delete(){
		
	}
}
?>