<?php
include_once WEB_PATH."model/noticeApi.model.php";

class NoticeApiNewAct extends Auth{
    public static $errCode	=	0;
	public static $errMsg	=	"";

	/*
	 *功能：调用开放系统，获取所有可发送人名字列表
	*/
	public function act_getUserList() {
		$userFrom 	  = trim($_GET['userFrom']);

		$paramArr = array(
				/* API系统级输入参数 Start */
				'method' 	=> 'notice.userList.get',  					//API名称
				'format' 	=> 'json',  								//返回格式
				'v' 		=> '1.0',   								//API版本号
				'username'	=> 'notice',
				/* API系统级参数 End */

				/* API应用级输入参数 Start*/
				"userFrom"	=> $userFrom,
				/* API应用级输入参数 End*/
		);
		$res = callOpenSystem($paramArr);
		if($res) {
			return json_decode($res, true);								//json_decode()对json格式的字符串进行解码
		} else {
			return $callback.'({"errCode":"1120","errMsg":"call open fail"})';
		}
	}

	/*
	 *功能：调用开放系统，获取可发送短信数量接口
	*/
	function act_smsSurNum() {
		$from 		= trim($_GET['from']);

		$paramArr = array(
				/* API系统级输入参数 Start */
				'method' 	=> 'notice.smsSurNum.get',  						//API名称
				'format' 	=> 'json',  								//返回格式
				'v' 		=> '1.0',   								//API版本号
				'username'	=> 'notice',
				/* API系统级参数 End */

				/* API应用级输入参数 Start*/
				"from"		=> $from,
				/* API应用级输入参数 End*/
		);
		$res = callOpenSystem($paramArr);
		if($res) {
			return json_decode($res, true);								//json_decode()对json格式的字符串进行解码
		} else {
			return $callback.'({"errCode":"1120","errMsg":"call open fail"})';
		}
	}

    /*
     * 功能:调用开放系统，发送消息接口
     */
    function act_sendMessage() {
    	if(!isset($_SESSION)) {
    		@session_start();
    	}

    	if(empty($_GET['content']) || empty($_GET['from']) || empty($_GET['to']) || empty($_GET['type'])) {
    		echo $callback.'({"errCode":"044","errMsg":"get param fail"})';
    		return;
    	}

    	$content 	= trim($_GET['content']);
    	$from 		= trim($_GET['from']);
    	$to 		= trim($_GET['to']);
    	$type 		= trim($_GET['type']);

    	$paramArr = array(
    			/* API系统级输入参数 Start */
    			'method' 	=> 'notice.send.get',  						//API名称
    			'format' 	=> 'json',  								//返回格式
    			'v' 		=> '1.0',   								//API版本号
    			'username'	=> 'notice',
    			/* API系统级参数 End */

    			/* API应用级输入参数 Start*/
    			"content"	=> $content,
    			"from"		=> $from,
    			"to"		=> $to,
    			"type"		=> $type,
    			/* API应用级输入参数 End*/
    	);
    	$res = callOpenSystem($paramArr);
    	if($res) {
    		return json_decode($res, true);								//json_decode()对json格式的字符串进行解码
    	} else {
    		return $callback.'({"errCode":"1120","errMsg":"call open fail"})';
    	}
    }

    /*
     * 功能： 调用开放系统，获取某个用户最近发送的N条消息接口
    */
    public function act_SendList() {
    	$from	  = trim($_GET['from']);
    	$page	  = trim($_GET['page']);
    	$type	  = trim($_GET['type']);

    	$paramArr = array(
    			/* API系统级输入参数 Start */
    			'method' 	=> 'notice.show.get',  						//API名称
    			'format' 	=> 'json',  								//返回格式
    			'v' 		=> '1.0',   								//API版本号
    			'username'	=> 'notice',
    			/* API系统级参数 End */

    			/* API应用级输入参数 Start*/
    			"from"		=> $from,
    			"page"		=> $page,
    			"type"		=> $type,
    			/* API应用级输入参数 End*/
    	);
    	$res = callOpenSystem($paramArr);
    	if($res) {
    		return  json_decode($res);
    	} else {
    		return json_decode('{"errCode":"1120","errMsg":"call open fail"}');
    	}
    }
}
?>
