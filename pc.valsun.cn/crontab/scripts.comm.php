<?php
	@session_start();
	error_reporting(-1);
	header("Content-type: text/html; charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once "/data/web/erpNew/pc.valsun.cn/framework.php";
	Core::getInstance();
	//require_once WEB_PATH."conf/scripts/script.config.php";

	$memc_obj = new Cache(C('CACHEGROUP'));