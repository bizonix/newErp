<?php


/*
函数名称：post_check()
函数作用：对用户提交内容进行处理,防止Sql注入
参　　数：$post: 要提交的内容
返 回 值：$post: 返回过滤后的内容
*/

function post_check($post) {
    if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开
        $post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
    }
    $post = str_replace("_", "_", $post); // 把 '_'过滤掉
    $post = str_replace("%", "%", $post); // 把 '%'过滤掉
    $post = nl2br($post); // 回车转换
    $post = htmlspecialchars($post); // html标记转换
    return $post;
}

/*
函数名称：inject_check()
函数作用：检测提交的值是不是含有SQL注射的字符，防止注射，保护服务器安全
参　　数：$sql_str: 提交的变量
返 回 值：返回检测结果，ture or false
*/
function inject_check($sql_str) {
    return eregi('select|insert|and|or|update|delete|\'|/*|*|../|./|union|into|load_file|outfile', $sql_str); //进行过滤
}

function getCateTitleOrId($type="*",$val=' '){
	global $dbConn;
	$link=$dbConn->link;
	$where=	"where id={$val}";
	if(trim($type)=="*"){
		$type="*";
		$where=" ";
	}
	$sql="select ".$type." from opensys_api_categories ".$where;
	$query=mysql_query($sql,$link);
	if($query){
	   $ret	=	$dbConn->fetch_array_all($query);
		if(trim($type)=="*"){
			return  $ret?$ret:"无此分类";
		}
		return isset($ret[0][$type])?($ret[0][$type]):"无此分类";				
	}
}

function getClientIP(){
    if ($_SERVER['REMOTE_ADDR']) {
        $cip = $_SERVER['REMOTE_ADDR'];  
    } elseif (getenv("REMOTE_ADDR")) {
        $cip = getenv("REMOTE_ADDR");  
    } elseif (getenv("HTTP_CLIENT_IP")) {
        $cip = getenv("HTTP_CLIENT_IP");  
    } else {  
    $cip = "unknown";  
    }  
    return $cip;  
}

function writeLog($msg) {  
    $time = Date('Y-m-d h:i:s'); 
    $ip   = getClientIP();     
    $info = "$time--$ip ------>$msg";
    //echo  $info;
    $fp = fopen("/data/web/erpNew/pc.valsun.cn/log.txt", "a+");//追加写入    
    if($fp) { 
        $flag=fwrite($fp, $info."\r\n"); 
        if(!$flag) {
            echo "写入文件失败<br>";             
        }               
    } else {         
        echo "打开文件失败"; 
    } 
    
    fclose($fp); 
}

function logOut() {
    session_start();
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();     
}


function array2sql($array){
	$sql_array = array();
	foreach ($array AS $_k=>$_v){
	   if (empty($_k)){
	       continue;
       }
       $_v = trim($_v);
		//if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
	   if (ctype_digit($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){ //modified by Herman.Xi is_numeric 对十六进制数判断不了 举例：0X792496944666339
			$sql_array[] = "`{$_k}`={$_v}";
	   } else {
            $_v = Deal_SC($_v);
			$sql_array[] = "`{$_k}`='{$_v}'";
		}
	}
	return implode(',', $sql_array);
}


function Deal_SC($str){
	//处理特殊字符,add by　Herman.Xi @ 20130307
	$str  = str_replace("'","&acute;",$str);
	$str  = str_replace("\"","&quot;",$str);
	$tes = array("=" , ")" , "(" , "{", "}");
	foreach($tes as $v){
		$str = str_replace($v,"",$str);
	}
	return addslashes($str);
}


/*
 *edit by wxb  
 *2013/07/05
 *
 * 
 **/

function queryResult($sql){
	global $dbConn;
	$link=$dbConn->link;
	$query=mysql_query($sql,$link);
	if($query){
		$ret	=	$dbConn->fetch_array_all($query);
		return $ret;
	}
}
/*
 *edit by wxb  
 *2013/07/05
 *
 * 
 **/

function queryExcute($sql,$returnType=''){
	global $dbConn;
	$link=$dbConn->link;
	$query=mysql_query($sql,$link);
	if($return=="id"){
		return $dbConn->insert_id();
	}else{
		return $query;
	}
}
/*
 *edit by wxb  
 *2013/07/05
 *
 * 
 **/

function arrToLinkStr($arr,$link){
	if(is_array($arr)){
		$str=' ';
		$count=0;
		foreach($arr as $key=>$val){
				++$count;
				if($count===1){
					$str.=$key."=".$val;
				}else{
					$str.=" {$link} ".$key."=".$val;
				}		
		}
		return $str;
	}
		return "not array";
}

function getBarcode($barcode){
	include_once WEB_PATH.'lib/Barcode.class.php';
	$barcode = new BarCode($barcode,$barcode);
	$barcode->createBarCode();
}

function round_num($f, $n){
	$num = pow(10, $n);
	$intn = intval(round($f*$num));
	$r = $intn/$num;
	$r = $r + 0.00001;
	return str_replace(',', '', number_format($r,2));
}

function get_sku_imgName($name) {
	if(strpos($name,"*")) {
		$nameArr = explode("*",$name);
		$name = $nameArr[1];
	}
	$pattern1 = '/(^0)(\d+)/i';
	$pattern2 = '/(\w+)_(\w+)(_M|_S|_L|_XL|_XXL|_XXXL)$/i';
	$pattern3 = '/(\w+)(_M|_S|_L|_XL|_XXL|_XXXL)$/i';
	$pattern4 = '/(\w+)_(\w+)(_\d+)$/i';
	if(preg_match($pattern1,$name)){
		$replacement1 = '${2}';
		$trueName = preg_replace($pattern1, $replacement1, $name);
	}else if(preg_match($pattern2,$name)){
		$replacement2 = '${1}_${2}';
		$trueName = preg_replace($pattern2, $replacement2, $name);
	}else if(preg_match($pattern3,$name)){
		$replacement3 = '${1}';
		$trueName = preg_replace($pattern3, $replacement3, $name);
	}else if(preg_match($pattern4,$name)){
		$replacement4 = '${1}_${2}';
		$trueName = preg_replace($pattern4, $replacement4, $name);
	}else {
		$trueName = $name;
	}

	$url_file = "http://192.168.200.200:9998/imgs/".$trueName."-G.jpg";

	$ch = curl_init(); 
	$timeout = 10; 
	curl_setopt ($ch, CURLOPT_URL, $url_file); 
	curl_setopt($ch, CURLOPT_HEADER, 1); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 

	$contents = curl_exec($ch);
	if (preg_match("/404/", $contents)){
		if(strpos($trueName,"_")) {
			$nameArr = explode("_",$trueName);
			$trueName = $nameArr[0];
		}
		return $trueName;
	}else{
		return $trueName;
	}

}

/**
 * @name : 开放系统公用函数
 * @author : guanyongjun
 * @version : 1.0
*/
	
//获取数据兼容file_get_contents与curl
function vita_get_url_content($url) {
	if(function_exists('file_get_contents')) {
		$file_contents = file_get_contents($url);
	} else {
	$ch = curl_init();
	$timeout = 30; 
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	}
	return $file_contents;
}

//签名函数 
function createSign ($paramArr,$token = '') { 
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
?>