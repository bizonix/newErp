<?php
/**
 * 名称：NoticeView
 * 功能：前台对接消息系统接口调用 
 * 版本：1.0
 * 日期：2013/11/09
 * 作者：wxb
 */
class NoticeView {
	/**
	 * 功能:调用发送短信接口 用于js调用 
	 * @param array $paramArr
	 * @param str $token
	 * @return void
	 * @author wxb
	 * @date: 2013/11/09
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
		$content = $_GET['content'];
		$from = $_GET['from'];
		$to = $_GET['to'];
		$type = $_GET['type'];
		
		$paramArr = array(
				/* API系统级输入参数 Start */
				'method' => 'notice.send.message',  //API名称
				'format' => 'json',  //返回格式
				'v' => '1.0',   //API版本号
				'username'	 => C('OPEN_SYS_USER'),
				/* API系统级参数 End */
				  
				/* API应用级输入参数 Start*/
				"content"=>$content,
				"from"=>$from,
				"to"=>$to,
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
	
}