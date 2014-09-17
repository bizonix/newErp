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
	    	if(!isset($_SESSION['userId'])){
	    		echo 'sendResult({"error_response":{"code":"176","msg":"no login"}})';
	    		return;
	    	}
	
    	if(empty($_GET['content']) || empty($_GET['from']) || empty($_GET['to']) || empty($_GET['type'])){
    		echo 'sendResult({"error_response":{"code":"044","msg":"get param fail"}})';
    		return;
    	}
    	$content = trim($_GET['content']);
    	$from = trim($_GET['from']);
    	$to = trim($_GET['to']);
    	$type = trim($_GET['type']);
    	$callback = trim($_GET['callback']);

    	
    	include_once "../api/include/opensys_functions.php";
    	$paramArr = array(
    			/* API系统级输入参数 Start */
    			'method' => 'notice.send.message',  //API名称
    			'format' => 'json',  //返回格式
    			'v' => '1.0',   //API版本号
    			'username'	 => 'Purchase',
    			/* API系统级参数 End */
    	
    			/* API应用级输入参数 Start*/
    			"content"=>$content,
    			"from"=>$from,
    			"to"=>$to,
    			"type"=>$type,
     		    "callback"=>$callback   //用于支持js调用
    			/* API应用级输入参数 End*/
    	);
    	$res		=     callOpenSystem($paramArr);
	    if($res){
			echo $res;	    	
	    }else{
	    	echo 'sendResult({"error_response":{"code":"1120","msg":"call open fail"}})';
	    }
    }

}
?>