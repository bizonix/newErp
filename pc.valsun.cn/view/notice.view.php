<?php

// $test = new demoView();
// $ret = $test->view_sendMessage();

class NoticeView {
	/**
	   * 功能:调用发送短信接口
	   * @param array $paramArr
	    * @param str $token
	    * @return void
	   * @author wxb
	   * date: 2013/11/1
	   */
	function view_sendMessage() {
		if (!isset ($_SESSION)) {
			@ session_start();
		}
        
        $content = trim($_GET['content']);
		$from = trim($_GET['from']);
		$to = trim($_GET['to']);
		$type = trim($_GET['type']);
        $callback = trim($_GET['callback']);
        
		if (!isset ($_SESSION['userId'])) {
			echo $callback . '({"errCode":"176","errMsg":"no login"})';
			return;
		}

		if (empty ($_GET['content']) || empty ($_GET['from']) || empty ($_GET['to']) || empty ($_GET['type'])) {
			echo $callback . '({"errCode":"044","errMsg":"get param fail"})';
			return;
		}
		
		$paramArr = array (
			"content" => $content,
			"from" => $from,
			"to" => $to,
			"type" => $type,
			"callback" => $callback
		);
		$res = UserCacheModel :: getOpenSysApi('notice.send.message', $paramArr, 'gw88',false);
        //print_r('111');
//        exit;
		if ($res) {
			echo $res;
		} else {
			echo $callback . '({"errCode":"1120","errMsg":"call open fail"})';
		}
	}

}
?>