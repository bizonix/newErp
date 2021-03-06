<?php
	
//获取数据兼容file_get_contents与curl
function vita_get_url_content($url) {
	// if(function_exists('file_get_contents')) {
		// $file_contents = file_get_contents($url);
	// } else {
	$ch = curl_init();
	$timeout = 30; 
	curl_setopt ($ch, CURLOPT_URL, $url);

	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	//}
	return $file_contents;
}


/***********************************************
 *	curl 请求
 *	@param $url		string	请求的url地址
 *	@param $vars	array	需要post的数据(key=>val)
 */
function curl($url, $vars=''){
    $ch	= curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($vars));
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	$content =	curl_exec($ch);
	curl_close($ch);
	return $content;
}

//签名函数 
function createSign ($paramArr,$token = '') { 
	$token  = C('OPEN_SYS_TOKEN'); //用户purchase token
	$sign = $token; 
	ksort($paramArr); 
	foreach ($paramArr as $key => $val) { 
	   if ($key !='' && $val !='') { 
		   $sign .= $key.$val; 
	   } 
	} 
	//echo $sign.$token."<br>";
	$sign = strtoupper(md5($sign.$token));
	return $sign; 
}


function createSignP ($paramArr,$token = '') { 
	$token  = C('OPEN_SYS_TOKEN'); //用户purchase token
	//$sign = $token; 
	$str = "";
	ksort($paramArr); 
	foreach ($paramArr as $key => $val) { 
	   if ($key !='' && $val !='') { 
		   $str .= $key.$val; 
	   } 
	} 
	$sign = strtoupper(md5($str.$token));
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
function callOpenSystem($paramArr , $url='',$type = "get"){
	//global $url,$token; 
	if (empty($url)) {
		$url 	= C('OPEN_SYS_URL');
	} else {
		$url 	= C('OPEN_SYS_URL_LOCAL');
	}
	$token  = C('OPEN_SYS_TOKEN'); //用户token
	//生成签名
	if($type == "post"){
		$sign = createSignP($paramArr,$token);
	}else{
		$sign = createSign($paramArr,$token);
	}
	//echo $sign,"<br/>";
	//组织参数
	$strParam = createStrParam($paramArr);
	$strParam .= 'sign='.$sign;
	//echo $strParam,"<br/>";
	 
	//构造Url
	$urls = $url.$strParam;
	/*
	if($_GET['debug'] == 1){
		echo $urls;
	}*/
	//连接超时自动重试3次
	$cnt=0;	
	while($cnt < 3 && @$result == false){

		if($type == "post"){
			$result = curl($urls,$paramArr); //post 方法 
		}else{
			$result = @vita_get_url_content($urls);
		}
		$cnt++;
	} 
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
