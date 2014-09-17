<?php

include_once __DIR__.'/../../framework.php';
Core::getInstance(); // 初始化框架对象
if(!isset($_FILES['attach'])){
	echo "<script>";
	echo "top.alertify.error('附件上传失败',2000)";
	echo "</script>" ;
	exit;
}
$id = isset($_GET['mid']) ? $_GET['mid'] : '';
$attach_size=$_FILES['attach']['size'];
$attach_name=$_FILES['attach']['name'];
$atname_tmp ='';
if(strlen($attach_name)>40){
	$atname_tmp = substr($attach_name, 0,39).'···';
} else {
	$atname_tmp = $attach_name;
}
switch ($_FILES['attach']['error']){
	case '0' : 
		if($attach_size>1048576){
			echo "<script>";
			echo "top.alertify.error('附件大小不能超过1M!',2000);";
			echo "</script>";
			exit;
		} 
			break;
	case '1' :
		echo "<script>";
		echo "top.alertify.error('超过了附件大小php.ini中即系统设定的大小',2000)";
		echo "</script>" ;
		exit;
	case '2' :
		echo "<script>";
		echo "top.alertify.error('超过了附件大小MAX_FILE_SIZE 选项指定的值',2000);";
		echo "</script>" ;
		exit;
	case '3' :
		echo "<script>";
		echo "top.alertify.error('附件只有部分被上传',2000)";
		echo "</script>" ;
		exit;
	case '4' :
		echo "<script>";
		echo "top.alertify.error('没有附件被上传',2000)";
		echo "</script>" ;
		exit;
	case '5' :
		echo "<script>";
		echo "top.alertify.error('上传附件大小为0',2000)";
		echo "</script>";
		exit;
}
$attach = WEB_PATH.'html/upload/'.$id.$_FILES['attach']["name"];

$msg_obj = new amazonmessageModel();
$res     = $msg_obj->insertAttachPath($id, $attach);
echo $res;
if(move_uploaded_file($_FILES['attach']['tmp_name'],$attach)&&$res){
	echo "<script>";
	echo "top.alertify.success('附件上传成功',2000);";
	echo "top.$('#attachname').html('$atname_tmp');";
	echo "</script>" ; 
		
} else {
	echo "<script>";
	echo "top.alertify.error('附件上传失败',2000);";
	echo "</script>" ; 
	exit;
}





?>