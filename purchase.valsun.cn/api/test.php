<?php
require_once "opensys_functions.php";
$paramArr = array(
	/* API系统级输入参数 Start */
		'method' => 'getApiGlobalUser',  //API名称
		'format' => 'json',  //返回格式
		'v' => '1.0',   //API版本号
		'username'	 => 'purchase',
		/* API系统级参数 End */
		/* API应用级输入参数 Start*/
		'po_id' => $po_id,
		/* API应用级输入参数 End*/
);
$test		= callOpenSystem($paramArr);
var_dump($test);
echo "<hr/>";
$test	= json_decode($test,true);
var_dump($test);
