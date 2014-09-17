<?php
/**
*类名：IQC领取、待测
*功能：处理产品检测信息
*作者：hws
*
*/
class contentAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前记录列表
	function  act_getContentList($select ='*',$where){
		$list =	contentModel::getContentList($select,$where);	
		if($list){
			return $list;
		}else{
			self::$errCode = contentModel::$errCode;
			self::$errMsg  = contentModel::$errMsg;
			return array();
		}
	}
	
	//获取当前记录数量
	function act_getContentNum($where){
		//调用model层获取数据		
		$list =	contentModel::getContentNum($where);	
		if($list){
			return $list;
		}else{
			self::$errCode = contentModel::$errCode;
			self::$errMsg  = contentModel::$errMsg;
			return false;
		}
	}

	//添加评价模板
	function act_contentAdd(){
		//调用model层获取数据
		$content = isset($_POST['content']) ? trim($_POST['content']) : '';
		if ($content == '') {
			self::$errCode = '001';
			self::$errMsg  = "content参数错误！";
			return false;
		}
		$ret1 = contentModel::checkContentExsit($content);
		if ($ret1) {
			self::$errCode = '002';
			self::$errMsg  = "content已存在！";
			return false;
		}
		$data = array(
			'content'	=>	$content,
			'addUserId'	=>  $_SESSION['userId'],
			'addTime' 	=>  time(),
		);
		$ret = contentModel::insertRow($data);
		if (!$ret) {
			self::$errCode = '003';
			self::$errMsg  = "content插入失败！";
			return false;
		}	
		return 'ok';		
	}
	
	//添加评价模板
	function act_contentModify(){
		//调用model层获取数据
		$contentId	= isset($_POST['contentId']) ? trim($_POST['contentId']) : '';
		$content 	= isset($_POST['content']) ? trim($_POST['content']) : '';
		if ($contentId == '' || $content == '') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}
		$ret1 = contentModel::checkContentExsit($content);
		if ($ret1) {
			self::$errCode = '002';
			self::$errMsg  = "content已存在！";
			return false;
		}
		$data = array(
				'content'	=>	$content,
				'updateUserId'	=>  $_SESSION['userId'],
				'updateTime' 	=>  time(),
		);		
		$ret = contentModel::update($data," and id = '$contentId'");
		if (!$ret) {
			self::$errCode = '003';
			self::$errMsg  = "content插入失败！";
			return false;
		}
		return true;
	}
	
	//添加评价模板
	function act_contentDel(){
		//调用model层获取数据
		$contentId	= isset($_POST['contentId']) ? trim($_POST['contentId']) : '';		
		if ($contentId == '') {
			self::$errCode = '001';
			self::$errMsg  = "参数错误！";
			return false;
		}		
		$ret = contentModel::delete(" where id = '$contentId'");
		if (!$ret) {
			self::$errCode = '003';
			self::$errMsg  = "content插入失败！";
			return false;
		}		
		return 'ok';
		
	}
		
}


?>