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

/**
简单的重定向函数 add by xiaojinhua
*/
function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
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
    $log_path = WEB_PATH.'log.txt'; 
    $ip   = getClientIP();     
    $info = "$time--$ip ------>$msg";
    //echo  $info;
    $fp = fopen($log_path, "a+");//追加写入    
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

function write_log($file, $data){
	
	$truename = $_SESSION['userName'];
	$config_path = dirname(dirname(__FILE__)).'/log';
	list($filepath, $filename) = explode ( '/', $file );
	$dirPath = $config_path.'/'.$filepath;
	if (!is_dir($dirPath)){
    	mkdir( $dirPath);
    }
    $readpath = $dirPath.'/'.$filename;
	if (!$handle=fopen($readpath, 'a')) {
         return false;
    }
    if(flock($handle, LOCK_EX)) { 
	    if (fwrite($handle, $truename.'===='.$data) === FALSE) {
	        return false;
	    }
	    flock($handle, LOCK_UN);
    }
    fclose($handle);
    return true;
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

function tep_selectHTML($ebay_topmenus, $id, $selected, $showall=true){
	$selectHTML = "<select id=\"$id\" name=\"$id\">";
	if($showall){ $selectHTML .= "<option value=\"all\">All</option>";}
	foreach($ebay_topmenus as $key => $value){
		$selectHTML .= "<option value=\"$key\"";
		if($key != 'all' && $key == $selected){ $selectHTML .= " selected=\"selected\""; }
		$selectHTML .= " >$value</option>";
	}
	$selectHTML .= "</select>";
	echo $selectHTML;
}

function tep_selectHTML_show($ebay_topmenus, $id, $name, $selected, $showall=true){
	$selectHTML = "<select id='".$id."' name='".$name."'>";
	if($showall){ $selectHTML .= "<option value=''>请选择</option>";}
	foreach($ebay_topmenus as $key => $value){
		$selectHTML .= "<option value='".$key."'";
		if($key != '' && $key == $selected){ $selectHTML .= " selected='selected'"; }
		$selectHTML .= " >".$value."</option>";
	}
	$selectHTML .= "</select>";
	return $selectHTML;
}

?>