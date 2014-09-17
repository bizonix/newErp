<?php
error_reporting(-1);
include("../api/include/functions.php");
$url = 'http://gw.open.valsun.cn:88/router/rest?';  //开放系统入口地址

$carrier = '3';
$country = 'USA';
$weight = 0.5;
$shaddr = '中国深圳';

$paramArr = array(
	/* API系统级输入参数 Start */
	'method' => 'trans.carrier.fix.get',  //API名称
	'format' => 'json',  //返回格式
		 'v' => '1.0',   //API版本号
	'username'	 => 'valsun.cn',
	/* API系统级参数 End */

	/* API应用级输入参数 Start*/
	'carrier' =>  $carrier,  //返回字段
	'country' => $country, //需要搜索的字段
    'weight' =>  $weight, //需要搜索的字段赋值
	'shaddr' => $shaddr
);

//生成签名
$sign = createSign($paramArr,$token);
//echo $sign,"<br/>";
//组织参数
$strParam = createStrParam($paramArr);

$strParam .= 'sign='.$sign;
//echo $strParam,"<br/>";

//构造Url
$urls = $url.$strParam;
//echo $urls,"<br/>";exit;

//连接超时自动重试3次
$cnt=0;
while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
//$result = file_get_contents($urls);
//$jsondata_arr	= json_decode($result,true);
echo "<pre>"; print_r($result); exit;

?>