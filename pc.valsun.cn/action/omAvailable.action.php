<?php


/*
 * 通用action
 * ADD BY zqt 2013.9.5
 */
class OmAvailableAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 取得指定iostore记录
	 */
	function act_getTNameList($tName, $set, $where) { //表名，SET，WHERE
		$list = OmAvailableModel :: getTNameList($tName, $set, $where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg = OmAvailableModel :: $errMsg;
			return false;
		}
	}
	
	/*
	 * 取得指定iostore记录并且存入到指定的某个字段为一个一元数组
	 * add by Herman.Xi
	 * addTime 2013-09-25
	 */
	function act_getTNameList2arr($tName, $set, $where) { //表名，SET，WHERE
		$list = OmAvailableModel :: getTNameList2arr($tName, $set, $where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg = OmAvailableModel :: $errMsg;
			return false;
		}
	}

	function act_getTNameCount($tName, $where) {
		$ret = OmAvailableModel :: getTNameCount($tName, $where);
		if ($ret !== false) {
			return $ret;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg = OmAvailableModel :: $errMsg;
			return false;
		}
	}

   	/**
	 *添加指定表记录,返回 insertId
	 */
	function act_addTNameRow($tName, $set) {
		$ret = OmAvailableModel :: addTNameRow($tName, $set);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg = OmAvailableModel :: $errMsg;
			return false;
		}
	}
    
   	/**
	 *添加指定表记录,返回TRUE or FALSE
	 */
	public static function act_insertRow($tName, $set) {
	   $ret = OmAvailableModel :: insertRow($tName, $set);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg = OmAvailableModel :: $errMsg;
			return false;
		}
	}

	function act_updateTNameRow($tName, $set, $where) {
		$ret = OmAvailableModel :: updateTNameRow($tName, $set, $where);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg = OmAvailableModel :: $errMsg;
			return false;
		}
	}
	
    //add by rendahai 2013-09-27 添加公共Action的事物支持，方便内部调用 
    function begin() {
		OmAvailableModel ::begin();		
	}

	function commit() {
	   OmAvailableModel ::commit();
	}

	function rollback() {
	   OmAvailableModel ::rollback();
	}
    
    //查找指定path是否在系统存在
    function act_isCategoryExistByPath() {
        $path = $_POST['path']?trim(post_check($_POST['path'])):'';
		$count = OmAvailableModel :: getTNameCount('pc_goods_category', "WHERE path='$path'");
		if ($count) {
			return true;
		} else {
			self :: $errCode = 1;
			self :: $errMsg = '不存在此类';
			return false;
		}
	}
    
    //查找指定SPU是否在auto_create_spu中存在
    function act_isSpuExist() {
        $spu = $_POST['spu']?trim(post_check($_POST['spu'])):'';
		$count = OmAvailableModel :: getTNameCount('pc_auto_create_spu', "WHERE spu='$spu'");
		if ($count) {
			return true;
		} else {
			self :: $errCode = 1;
			self :: $errMsg = '不存在此SPU';
			return false;
		}
	}
    
    //获取子类信息
	function  act_getCategoryInfoAndIsHasChild(){
		$id   = $_POST['id'];
		$list =	CategoryModel::getCategoryList("*","where is_delete=0 and pid='{$id}'");
		if($list){
		    $count = CategoryModel::getCategoryCount("where is_delete=0 and path like '%$id-%-%'");
			return array($list,$count);
		}else{
			self::$errCode = 101;
			self::$errMsg  = 'error';
			return false;
		}
	}
	
    
}
?>
