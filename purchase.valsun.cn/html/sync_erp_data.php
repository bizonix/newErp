<?php
include "config.php";
include "open_function.php";

$paramArr= array(
	'method'	 => 'erp.get.skuData',  //API名称
	'format'	 => 'json',  //返回格式
	'v'			 => '1.0',   //API版本号/
	'username'   => 'Purchase',
	'pusername' => 'vipchen',
	'data' => $data 
);
$data 	= callOpenSystem($paramArr,"local");
print_r($data);
?>
