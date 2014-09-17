<?php
/**
*类名：订单流程状态管理
*功能：订单流程状态信息
*作者：hws
*last modified by Herman.Xi @20131205
*/
class StatusMenuAct{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	//获取订单流程状态管理列表
	function act_getStatusMenuList($select = '*',$where=''){
		$list =	StatusMenuModel::getStatusMenuList($select,$where);
		if($list){
			self::$errCode = StatusMenuModel::$errCode;
			self::$errMsg  = StatusMenuModel::$errMsg;
			return $list;
		}else{
			self::$errCode = StatusMenuModel::$errCode;
			self::$errMsg  = StatusMenuModel::$errMsg;
			return false;
		}
	}
	
	//获取订单流程状态管理列表
	function act_getStatusMenuListById($select = '*',$where=''){
		$list =	StatusMenuModel::getStatusMenuListById($select,$where);
		if($list){
			self::$errCode = StatusMenuModel::$errCode;
			self::$errMsg  = StatusMenuModel::$errMsg;
			return $list;
		}else{
			self::$errCode = StatusMenuModel::$errCode;
			self::$errMsg  = StatusMenuModel::$errMsg;
			return false;
		}
	}
	
	//增加/修改状态
	function  act_sureAddMenu(){
		$bool				= array();
		$data 	    		= array();
		$id 	    		= trim($_POST['menuId']);
		$data['statusName'] = post_check(trim($_POST['statusName']));		
		if(empty($_POST['groupId'])){			
			$data['groupId']    = '0';
		}else{
			$data['groupId']    = $_POST['groupId'];
		}
		
		$data['sort']       = post_check(trim($_POST['sort']));
		$data['note']       = post_check(trim($_POST['note']));
		if(empty($id)){
			$data['statusCode'] = post_check(trim($_POST['statusCode']));
			$bool = StatusMenuModel::getStatusMenuList("*","where statusName='{$data['statusName']}' and is_delete=0 and storeId=1");
			if($bool){
				return 2;
			}
			return (StatusMenuModel::insertRow($data));
			$insertid = StatusMenuModel::insertRow($data);
			if($insertid){
				return 1;
			}else{
				return false;
			}
		}else{
			$bool = StatusMenuModel::getStatusMenuList("*","where id!=$id and statusName='{$data['statusName']}' and is_delete=0 and storeId=1");
			if($bool){
				return 2;
			}
			$updatedata = StatusMenuModel::update($data,"and id='$id'");
			if($updatedata){
				return 1;
			}else{
				return false;
			}
		}		
	}
	
	//删掉状态
	function  act_delMenu(){
		$id = trim($_POST['id']);
		$where = "and `id` = '$id'";
		$data  = array(
			'is_delete' => 1
		);
		if(StatusMenuModel::update($data,$where)){
			return array('state' => 'ok');   
		}else{
			return array('state' => 'no');   
		}
	}
	
	/*
	 * 根据状态获取读取状态列表(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_getOrderNameByStatus($ostatus, $otype){
		$ret = StatusMenuModel::getOrderNameByStatus($ostatus, $otype);
		self::$errCode = StatusMenuModel::$errCode;
		self::$errMsg  = StatusMenuModel::$errMsg;
		return $ret;
	}
	
	/*
	 * 根据状态获取读取状态列表(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public function act_changeOstatusId(){
		$ostatus = $_POST['ostatus'];
		$list =	StatusMenuModel::getStatusMenuList('statusCode,statusName','WHERE groupId = "'.$ostatus.'" AND is_delete=0');
		self::$errCode = StatusMenuModel::$errCode;
		self::$errMsg  = StatusMenuModel::$errMsg;
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	

	/*
	 * 获取状态分组列表，及按二级目录分组
	 * add by rdh @20140212
	 */
	public function act_getMenuGroupList(){			
		$list = StatusMenuModel::getStatusMenuList('statusName,statusCode,groupId','where is_delete = 0 ORDER BY groupId ASC,statusCode ASC');
		$grouplists = array();
		$group0 = array();
		foreach ($list as $key => $value) {
			if ($value['groupId'] == 0) {
				$group0[$value['statusCode']]['name'] = $value['statusName'];
				$group0[$value['statusCode']]['list'] = $value;
			}
		}

		foreach ($list as $k => $v) {
			foreach ($group0 as $k0 => $v0) { 
				if ($k0 == $v['groupId'] ) {
					$grouplists[$v['groupId']]['name'] = $v0['name'];
					$grouplists[$v['groupId']]['subCode'] .= $v['statusCode'].',';
					$grouplists[$v['groupId']]['list'][] = $v;
				}
			}
		}

		self::$errCode = StatusMenuModel::$errCode;
		self::$errMsg  = StatusMenuModel::$errMsg;
		if($list){
			return $grouplists;
		}else{
			return false;
		}
	}
	
	/*
	 * 提供更新状态接口
	 * add by Herman.Xi @20140514
	 */
	function  act_updateMenuStatusAPI(){
		$orderId = post_check(trim($_REQUEST['orderId']));
		$ebay_status 	= $_REQUEST['ebay_status'];
		$final_status 	= $_REQUEST['final_status'];
		$truename 		= $_REQUEST['username'];
		if (empty ($orderId)) {
			self :: $errCode = 401;
			self :: $errMsg = 'orderId is null';
			return false;
		}
		if (empty ($ebay_status)) {
			self :: $errCode = 402;
			self :: $errMsg = 'ebay_status is null';
			return false;
		}
		if (empty ($final_status)) {
			self :: $errCode = 403;
			self :: $errMsg = 'final_status is null';
			return false;
		}
		if (empty ($truename)) {
			self :: $errCode = 404;
			self :: $errMsg = 'truename is null';
			return false;
		}
		
		$status1 = getStatusMenuByOldStatus($final_status);
		$orderStatus1 = $status1[0];
		$orderType1 = $status1[1];
		$status2 = getStatusMenuByOldStatus($ebay_status);
		$orderStatus2 = $status2[0];
		$orderType2 = $status2[1];
		
		$tableName = "om_unshipped_order";
		$data['orderStatus'] = $orderStatus1;
		$data['orderType'] = $orderType1;
		$where = " where id = {$orderId} AND orderStatus = {$orderStatus2} AND orderType = {$orderType2} AND is_delete = 0 ";
		$updatedata = OrderindexModel::updateOrder($tableName,$data,$where);
		if($updatedata){
			self :: $errCode = 200;
			self :: $errMsg = 'update success';
			return true;
		}else{
			self :: $errCode = 405;
			self :: $errMsg = 'update error';
			return false;
		}
	}
	
}
?>