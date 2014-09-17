<?php
/**
 * 功能：公共函数
 * 版本：1.0
 * 日期：2013/12/07
 * 作者：管拥军
 */

//用户提交内容过滤
function post_check($post) {
    $post = trim($post);
    if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开
        $post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
    } else {
		$post = mysql_real_escape_string($post);
    }
    return $post;
}

//函数名称过滤
function inject_check($sql_str) {
    return eregi('select|insert|and|or|update|delete|\'|/*|*|../|./|union|into|load_file|outfile', $sql_str); //进行过滤
}

//获取IP地址
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

//数组转SQL函数
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

//处理特殊字符配合array2sql函数使用
function Deal_SC($str){
	$str  = str_replace("'","&acute;",$str);
	$str  = str_replace("\"","&quot;",$str);
	$tes = array("=" , ")" , "(" , "{", "}");
	foreach($tes as $v){
		$str = str_replace($v,"",$str);
	}
	return addslashes($str);
}

//重定向函数封装
function redirect_to( $location = NULL ) {
  if ($location != NULL) {
	header("Location: {$location}");
    exit;
  }
}

//给数组内容增加逗号分隔
function map_commar($str) {
	return "'".$str."'";	
}

//去掉数组内容两端的空格
function map_trim($str) {
	return trim($str);	
}

//创建目录
function mkdirs($path) {
	$path_out = preg_replace('/[^\/.]+\/?$/', '', $path);
	if (!is_dir($path_out)) {
		mkdirs($path_out);
	}	
	mkdir($path);
}

//追加写文件
function write_a_file($file, $data) {
	$tmp_dir = dirname($file);
	if (!is_dir($tmp_dir)) {
		mkdirs($tmp_dir);
	}
	if (!$handle = fopen($file, 'a')) {
		return false;
	}
	if (flock($handle, LOCK_EX)) {
		if (fwrite($handle, $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

//写文件
function write_w_file($file, $data) {
	$tmp_dir = dirname($file);
	if (!is_dir($tmp_dir)) {
		mkdirs($tmp_dir);
	}
	if (!$handle = fopen($file, 'w')) {
		return false;
	}
	if (flock($handle, LOCK_EX)) {
		if (fwrite($handle, $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

//读文件
function read_file($file) {
	if (!is_file($file)) {
		return false;
	}
	return file_get_contents($file);
}

//读取文件后清空文件
function read_and_empty_file($file) {
	if (!is_file($file)) {
		return false;
	}
	$contents = file_get_contents($file);
	if (!$handle = fopen($file, 'w')) {
		return false;
	}
	return $contents;
}

//提示信息
function show_message($obj,$message,$url="") {
	$obj->assign('message',$message);   
	$obj->assign('url',$url);   
	$obj->display('showMessage.htm');	
}
?>