<?php
/**
*类名：文件夹类型列表
*作者：Herman.Xi
*时间：2013.08.30
*/
class LibraryStatusAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取文件夹记录列表
	function act_getLibraryStatusList($select = '*',$where){
		$list =	LibraryStatusModel::getLibraryStatusList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = LibraryStatusModel::$errCode;
			self::$errMsg  = LibraryStatusModel::$errMsg;
			return false;
		}
	}
	
	//获取文件夹记录列表
	function act_getLibraryStatusGroupList($where){
		$list =	LibraryStatusModel::getLibraryStatusGroupList($where);
		if($list){
			return $list;
		}else{
			self::$errCode = LibraryStatusModel::$errCode;
			self::$errMsg  = LibraryStatusModel::$errMsg;
			return false;
		}
	}
	
	//获取文件夹记录列表
	function act_getLibraryStatusAllGroup($where){
		$list =	LibraryStatusModel::getLibraryStatusAllGroup($where);
		if($list){
			return $list;
		}else{
			self::$errCode = LibraryStatusModel::$errCode;
			self::$errMsg  = LibraryStatusModel::$errMsg;
			return false;
		}
	}
	
	//获取文件夹记录数量
	function act_getLibraryStatusNum($where){
		//调用model层获取数据
		$list =	LibraryStatusModel::getLibraryStatusNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = LibraryStatusModel::$errCode;
			self::$errMsg  = LibraryStatusModel::$errMsg;
			return false;
		}
	}
	
	//获取文件夹记录数量
	function act_getLibraryStatusGroupNum($where){
		//调用model层获取数据
		$list =	LibraryStatusModel::getLibraryStatusGroupNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = LibraryStatusModel::$errCode;
			self::$errMsg  = LibraryStatusModel::$errMsg;
			return false;
		}
	}
}


?>