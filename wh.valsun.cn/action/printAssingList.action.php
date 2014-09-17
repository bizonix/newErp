<?php
/**
 * PrintAssignListAct
 * 
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class PrintAssignListAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	

	//批量打印打标
	function  act_alreadyPrint(){
		$id_arr 	 = $_POST['id'];
		$list  = printLabelModel::updatePrintGroup($id_arr);
		if($list){
			self::$errMsg  = "打印成功！";
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "打印失败，请重试！";
			return false;
		}
	}

	function  act_deletPrint(){
		$id_arr = $_POST['id'];
		$type 	= $_POST['type'];	
		if($type==1){
			foreach($id_arr as $id){
                
				$info = packageCheckModel::selectList("where id={$id} and is_delete = 0");
				if(empty($info)){
					self::$errCode = "003";
					self::$errMsg  = "没有信息！";
					return false;
				}
                
                if($info[0]['printTime']){
					self::$errCode = "003";
					self::$errMsg  = "已打标，不能删除！";
					return false;
				}
			}
		}
		$info   = packageCheckModel::deletRecord($id_arr,$type);
		if($info){
			self::$errMsg  = "删除成功！";
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "删除失败，请重试！";
			return false;
		}
	}
	
	//批量打印打标
	function  act_getPrintListBySku(){
		$sku   = $_POST['sku'];
		$list  = printLabelModel::getPrintListBysku($sku);
		var_dump($list);
		if($list){
			self::$errMsg  = "打印成功！";
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "打印失败(请先打标)，请重试！";
			return false;
		}
	}
	
	
	
}


?>