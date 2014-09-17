<?php
	@session_start();
	error_reporting(-1);
	ini_set('max_execution_time', 1800);
	date_default_timezone_set('Asia/Shanghai');	
	require_once "/data/web/feedback.valsun.cn/framework.php";	
	require_once "/data/web/feedback.valsun.cn/lib/feedback/ebaylibrary/GetFeedbackAPI.php";		
	//include "/data/web/feedback.valsun.cn/lib/xmlhandle.php";	
	Core::getInstance();
	
	//require_once WEB_PATH."conf/scripts/script.config.php";	
	//$memc_obj = new Cache(C('CACHEGROUP'));
	
?>