<?php
/*
 * 名称：OrderModifyAct
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * */
include_once WEB_PATH.'model/orderModify.model.php';
include_once WEB_PATH.'model/omAvailable.model.php';
include_once WEB_PATH.'model/common.model.php';
class OmBlackListAct{
	public static $errCode = 0;
	public static $errMsg = '';
	
	//获取对应订单详情
	public function act_index(){
		$platformId	=	$_REQUEST['platformId'];
		$account	=  OmAvailableModel::getTNameList('om_account','id,platformId,account','WHERE is_delete=0 AND platformId = '.$platformId);
		if($account){
			return $account;
		} else {
			self :: $errCode	= '300';
			self :: $errMsg		= '无法获得对应平台用户名信息';
			return false;
		}
	}
	
	/*
	 *将黑名单数据存入数据库
	 */
	public static function insertBlackList($data, $table) {
		self :: initDB();
		$key	=	array();
		$value	=	array();
		foreach($data as $k => $v){
			$key[]	=	$k;
			$value[]=	$v;
		}
		$sql = "INSERT INTO ".$table." (".implode(',',$key).") VALUE ('".implode("','",$value)."')";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			return true; //成功， 返回真
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	
	/*
	 * 把黑名单记录存进数据库
	 */
	function act_insertBlackList($data, $table) { //表名，SET，WHERE
		$key	=	array();
		$value	=	array();
		foreach($data as $k => $v){
			$key[]	=	$k;
			$value[]=	$v;
		}
		$set = " (".implode(',',$key).") VALUE ('".implode("','",$value)."')";	
		$ret = OmAvailableModel :: addTNameRow($table, $set);
		if($ret){
			return true;
		} else {
			self :: $errCode = OmAvailableModel :: $errCode;
			self :: $errMsg = OmAvailableModel :: $errMsg;
			return false;
		}
	}	
}