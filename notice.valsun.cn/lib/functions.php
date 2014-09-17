<?php


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
函数名称：post_check()
函数作用：对用户提交内容进行处理,防止Sql注入
参　　数：$post: 要提交的内容
返 回 值：$post: 返回过滤后的内容
*/

function post_check($post) {
    $post = trim($post);
    if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开
        $post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
    }
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


//获取ip
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
    $fp = fopen("/data/web/open.valsun.cn/log.txt", "a+");//追加写入    
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

/**
 * 功能：将$array 转换成SET语句 后面参数
 * @param array $array
 * @param string void 类似于 `a`='1',`b`='2' 
 */
function array2sql($array){
	$sql_array = array();
	foreach ($array AS $_k=>$_v){
	   if (empty($_k)){
	       continue;
       }
       $_v = trim($_v);
		//if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
	   if (ctype_digit($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){ //modified by Herman.Xi is_numeric 对十六进制数判断不了 举例：0X792496944666339
			$sql_array[] = "`{$_k}`='{$_v}'";
	   } else {
            $_v = Deal_SC($_v);
			$sql_array[] = "`{$_k}`='{$_v}'";
		}
	}
	return implode(',', $sql_array);
}
/**
 * 功能：为数据库查询语句 某些字符前加上了反斜线或变为实体字符
 * @param string $str
 * @return string
 */
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

/**
 * 
 * @param array $arr 
 * @param string $link 连接符如：,
 * @return array $str 类似于 a='1',b='2' 
 */
 

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


/**
 * 功能：将数组分页输出
 * @param array $list 输入的数组
 * @param  num $per 每页显示的条数
 * @param  str $lang 英言
 * @return array  分页数据 和 分页导航条
 * @date 2013/11/18
 */

function pageForArr($list, $per,$lang="CN"){
	if(!class_exists("Page")){
		include_once 'page.php';
	}
	//初始化键名
	$tempList = array();
	foreach($list as $val){
		$tempList[] = $val;
	}
	$list = $tempList;

	$total = count($list);
	$page = new Page($total, $per, $which="",$lang="CN");
	//计算出数组的key变化范围 start
	$limit =  $page->limit;
	$limit = explode(",",$limit);
	$limit = str_replace("Limit",'', $limit);
	$limit[0] = (int)$limit[0];
	$limit[1] = (int)$limit[1];
	$limit[1]=$limit[0]+$per-1;
	if($limit[1]>$total-1){
		$limit[1] = $total-1;
	}

	// 计算出数组的key变化范围 end
	$retList = array();
	foreach($list as $key=>$val){
		if($key>$limit[1]){
			break;
		}
		if($key>=$limit[0] && $key<=$limit[1]){
			$retList[] = $val;
		}
	}
	if($total>$per){
		return array($retList,$page->fpage());
	}else{
		return array($retList,"");
	}
}

?>
