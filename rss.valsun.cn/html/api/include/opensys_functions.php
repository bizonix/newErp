<?php
/**
 * @name : 开放系统公用函数
 * @author : guanyongjun
 * @version : 1.0
*/

//获取数据兼容file_get_contents与curl
function vita_get_url_content($url) {
	$ch 		= curl_init();
	$timeout 	= 30;
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return $file_contents;
}

//签名函数
function createSign($paramArr, $token = '') {
	$sign = $token;
	ksort($paramArr);
	foreach ($paramArr as $key => $val) {
	   if ($key !='' && $val !='') {
		   $sign .= $key.$val;
	   }
	}
	$sign = strtoupper(md5($sign.$token));
	return $sign;
}

//组参函数
function createStrParam ($paramArr) {
	$strParam = '';
	foreach ($paramArr as $key => $val) {
	   if ($key != '' && $val !='') {
		   $strParam .= $key.'='.urlencode($val).'&';
	   }
	}
	return $strParam;
}

//调用开放系统
function callOpenSystem($paramArr, $local=false) {
	$token  = '12aead0936276c4d8bbe32947b9e94b3'; //用户 token
	if($local) {
		$url = C('OPEN_SYS_URL_LOCAL');  											//开放系统内网地址;默认值
	} else {
		$url = C('OPEN_SYS_URL');  													//开放系统外网地址
	}
	//生成签名
	$sign 	= createSign($paramArr, $token);
	//echo $sign,"<br/>";
	//组织参数
	$strParam = createStrParam($paramArr);
	$strParam .= 'sign='.$sign;
	//echo $strParam,"<br/>";

	//构造Url
	$urls = $url.$strParam;
	//echo $urls;exit;

	//连接超时自动重试3次
	$cnt=0;
	while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
	return $result;
}

//解析xml函数
function getXmlData ($strXml) {
	$pos = strpos($strXml, 'xml');
	if ($pos) {
		$xmlCode=simplexml_load_string($strXml,'SimpleXMLElement', LIBXML_NOCDATA);
		$arrayCode=get_object_vars_final($xmlCode);
		return $arrayCode ;
	} else {
		return '';
	}
}

function get_object_vars_final($obj){
	if(is_object($obj)){
		$obj=get_object_vars($obj);
	}
	if(is_array($obj)){
		foreach ($obj as $key=>$value){
			$obj[$key]=get_object_vars_final($value);
		}
	}
	return $obj;
}

//获取客户端IP
function getip(){
	if (@$_SERVER["HTTP_X_FORWARDED_FOR"])
	$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else if (@$_SERVER["HTTP_CLIENT_IP"])
	$ip = $_SERVER["HTTP_CLIENT_IP"];
	else if (@$_SERVER["REMOTE_ADDR"])
	$ip = $_SERVER["REMOTE_ADDR"];
	else if (@getenv("HTTP_X_FORWARDED_FOR"))
	$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (@getenv("HTTP_CLIENT_IP"))
	$ip = getenv("HTTP_CLIENT_IP");
	else if (@getenv("REMOTE_ADDR"))
	$ip = getenv("REMOTE_ADDR");
	else
	$ip = "Unknown";
	return $ip;
}

//XML数据格式错误信息输出
function error_xml($errcode){
    header("Content-type: text/xml");
    global $opensys_err;
	$err_msg="<?xml version=\"1.0\" encoding=\"UTF-8\"?><error_response><code>{$errcode}</code><msg>{$opensys_err[$errcode]}</msg></error_response>";
	return $err_msg;
}

//JSON数据格式错误信息输出
function error_json($errcode){
    global $opensys_err;
	$err_msg='{"error_response":{"code":'.$errcode.',"msg":"'.$opensys_err[$errcode].'"}}';
	return $err_msg;
}

//XML数据格式错误信息输出
function error_xmls($errcode,$message){
    header("Content-type: text/xml");
    global $opensys_err;
	$err_msg="<?xml version=\"1.0\" encoding=\"UTF-8\"?><error_response><code>{$errcode}</code><msg>{$message}</msg></error_response>";
	return $err_msg;
}

//JSON数据格式错误信息输出
function error_jsons($errcode,$message){
    global $opensys_err;
	$err_msg='{"error_response":{"code":'.$errcode.',"msg":"'.$message.'"}}';
	return $err_msg;
}
?>
