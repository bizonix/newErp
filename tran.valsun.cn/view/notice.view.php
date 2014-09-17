<?php 
// $test = new demoView();
// $ret = $test->view_sendMessage();

class noticeView {
  /**
     * 功能:调用发送短信接口
     * @param array $paramArr
      * @param str $token
      * @return void
     * @author wxb 
     * date: 2013/11/1
     */
	function view_sendMessage(){
		if(!isset($_SESSION)){
			@session_start();
		}
		$callback = $_GET['callback'];
		if(!isset($_SESSION['userId'])){
			echo $callback.'({"error_response":{"code":"176","msg":"no login"}})';
			return;
		}
	
		if(empty($_GET['content']) || empty($_GET['from']) || empty($_GET['to']) || empty($_GET['type'])){
			echo $callback.'({"error_response":{"code":"044","msg":"get param fail"}})';
			return;
		}
		$content = trim($_GET['content']);
		$from = trim($_GET['from']);
		$to = trim($_GET['to']);
		$type = trim($_GET['type']);
	
		$paramArr = array(
				/* API系统级输入参数 Start */
				'method' => 'notice.send.message',  //API名称
				'format' => 'json',  //返回格式
				'v' => '1.0',   //API版本号
				'username'	 => C('OPEN_SYS_USER'),
				/* API系统级参数 End */
	
				/* API应用级输入参数 Start*/
				"content"=>base64_encode($content),
				"from"=>base64_encode($from),
				"to"=>base64_encode($to),
				"type"=>$type,
				"callback"=>$callback   //用于支持js调用
				/* API应用级输入参数 End*/
		);
		$res		=     callOpenSystem($paramArr,'local');
		if($res){
			echo $res;
		}else{
			echo $callback.'({"error_response":{"code":"1120","msg":"call open fail"}})';
		}
	}
	/**
	 *功能：搜索用用户名
	 *@author wxb
	 *@date 2013/12/3
	 */
	public function view_searchUser(){
		if(!isset($_SESSION)){
			@session_start();
		}
		$callback = trim($_GET['callback']);
		if(!isset($_SESSION['userId'])){
			echo $callback.'({"errCode":"176","errMsg":"no login"})';
			return;
		}
		$name = trim($_GET['name']);
		$paramArr = array(
				/* API系统级输入参数 Start */
				'method' => 'notice.searchUser',  //API名称
				'format' => 'json',  //返回格式
				'v' => '1.0',   //API版本号
				'username'	 => 'notice',
				/* API系统级参数 End */
				/* API应用级输入参数 Start*/
				"name"=>base64_encode($name),   //用于支持js调用
				"callback"=>$callback   //用于支持js调用
				/* API应用级输入参数 End*/
		);
		$res		=     callOpenSystem($paramArr);
		echo $res;
	}
}
?>