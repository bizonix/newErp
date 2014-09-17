<?php

/*
 * 出入库单显示调用Model
 * ADD BY zqt 2013.8.14
 */
class WhIoStoreModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	/**
	 * 初始化DB对象
	 */
	static public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/**
	 * 获取出入库单数据列表
	 * @param string $where
	 * @param string $sort
	 * @param string $limit
	 */
	public function getInStoreList($where, $sort='', $limit=''){
		self::initDB();
		$usermodel = UserModel::getInstance();
		$sql = "SELECT * FROM wh_iostore {$where} {$sort} {$limit}";
		$query = self::$dbConn->query($sql);
		if (!empty($query)) {
			$lists = self::$dbConn->fetch_array_all($query);
			if (empty($lists)){
				return array();
			}
			foreach ($lists AS &$list){
				$sql = "SELECT a.*,b.spu FROM wh_iostoredetail AS a LEFT JOIN pc_goods AS b ON a.sku=b.sku WHERE a.iostoreId={$list['id']}";
				$query = self::$dbConn->query($sql);
				$dlists = self::$dbConn->fetch_array_all($query);
				$list['detail'] =  is_array($dlists) ? $dlists : array();
				$list['auditlist'] = $this->getAuditRelationList($list['ordersn'], $list['invoiceTypeId'], $list['storeId']);
				$list['invoiceName'] = self::getInvoiceTypeNameById($list['invoiceTypeId']);
				$list['paymentMethods'] = self::getPaymentMethodsById($list['paymentMethodsId']);
				$list['whName'] = self::getWhNameById($list['storeId']);
			}
			return $lists; //成功， 返回列表数据
		} else {
			self::$errCode = "001";
			self::$errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 * 获取对应审核人待审核出入库单数据列表
	 * @param string $where
	 * @param string $sort
	 * @param string $limit
	 */
	public function getAuditIOStoreList($where, $sort='', $limit=''){
		self::initDB();
		$usermodel = UserModel::getInstance();
		$sql = "SELECT * FROM wh_iostore {$where} {$sort} {$limit}";
		$query = self::$dbConn->query($sql);
		if (!empty($query)) {
			$lists = self::$dbConn->fetch_array_all($query);
			if (empty($lists)){
				return array();
			}
			foreach ($lists AS &$list){
				$sql = "SELECT * FROM wh_iostoredetail WHERE iostoreId={$list['id']}";
				$query = self::$dbConn->query($sql);
				$dlists = self::$dbConn->fetch_array_all($query);
				$list['auditlist'] = $this->getAuditRelationList($list['ordersn'], $list['invoiceTypeId'], $list['storeId']);				
				$list['detail'] =  is_array($dlists) ? $dlists : array();
				$list['invoiceName'] = self::getInvoiceTypeNameById($list['invoiceTypeId']);
				$list['paymentMethods'] = self::getPaymentMethodsById($list['paymentMethodsId']);
				$list['whName'] = self::getWhNameById($list['storeId']);
			}
			return $lists; //成功， 返回列表数据
		} else {
			self::$errCode = "001";
			self::$errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 * 获取出库单数量
	 * @param string $where
	 */
	public function getIOStoreCount($where){
		self::initDB();
		$sql 	= "SELECT COUNT(*) AS count FROM wh_iostore {$where}";
		$result	= self::$dbConn->fetch_first($sql);
		if ($result) {
			return $result['count']; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 * 获取对应订单审核关系信息
	 * @param string $ordersn
	 * @param int $invoiceTypeId
	 * @param int $storeId
	 */
	public function getAuditRelationList($ordersn, $invoiceTypeId, $storeId){
		self::initDB();
		$sql = "SELECT * FROM wh_audit_relation_list WHERE invoiceTypeId={$invoiceTypeId} AND storeId={$storeId} ORDER BY auditLevel ASC, auditorId ASC";
		$query = self::$dbConn->query($sql);
		$lists = self::$dbConn->fetch_array_all($query);
		
		$sql = "SELECT * FROM wh_audit_records WHERE ordersn='{$ordersn}' ORDER BY id ASC";
		$query = self::$dbConn->query($sql);
		$recordlists = self::$dbConn->fetch_array_all($query);
		$recordreuslts = array();
		foreach ($recordlists AS $recordlist){
			$recordreuslts[$recordlist['auditRelationId']] = $recordlist;
		}
		
		$reuslts = array();
		foreach($lists AS $list){
			$reuslts[$list['auditLevel']]['audituserlist'][] = $list;
			$reuslts[$list['auditLevel']]['auditinfo'] = isset($recordreuslts[$list['id']]) ? $recordreuslts[$list['id']] :  array();
		}
		return $reuslts;
	}
	
	/**
	 * 出入库单审核通过
	 * @param int $invoiceTypeId
	 * @param int $storeId
	 */
	public function iostoreAuditPass($uid, $iostoreId){
		self::initDB();
		$now_time = time();
		$sql    = "SELECT * FROM wh_iostore WHERE id={$iostoreId}";
		$iolist = self::$dbConn->fetch_first($sql);
		if($iolist['nextoperatorId']!=$uid){
			self::$errCode = "001";
			self::$errMsg = "当前审核人不是你";
			return false;
		}
		
		$sql   = "SELECT * FROM wh_audit_relation_list WHERE invoiceTypeId={$iolist['invoiceTypeId']} AND storeId={$iolist['storeId']} ORDER BY auditLevel ASC, auditorId ASC";
		$query = self::$dbConn->query($sql);
		$lists = self::$dbConn->fetch_array_all($query);
		$count = count($lists);
		$now_key = 0;
		$auditLevel = 0;
		$auditRelationId = 0;
		foreach($lists as $key=>$list){
			if($list['auditorId']==$uid){
				$now_key = $key+1;
				$auditLevel = $list['auditLevel'];
				$auditRelationId = $list['id'];
				break;
			}
		}
		self::$dbConn->begin();
		$insert_record_sql = "INSERT INTO wh_audit_records (ordersn,auditRelationId,auditStatus,auditLevel,auditUser,auditTime) 
							VALUES('{$iolist['ordersn']}',{$auditRelationId},1,$auditLevel,$uid,$now_time)";
		$query = self::$dbConn->query($insert_record_sql);
		if(!$query){
			self::$errCode = "002";
			self::$errMsg = "插入审核记录表失败，审核失败";
			return false;
		}
		
		if($count==$now_key){
			$update_sql = "UPDATE wh_iostore SET ioStatus=2,operatorId={$uid} where id={$iostoreId}";
		}else{
			$update_sql = "UPDATE wh_iostore SET nextoperatorId={$lists[$now_key]['auditorId']} where id={$iostoreId}";
		}
		$querys = self::$dbConn->query($update_sql);
		if(!$querys){
			self::$errCode = "003";
			self::$errMsg  = "更新审核表失败，审核失败";
			self::$dbConn->rollback();
			return false;
		}
		self::$dbConn->commit();
		return true;
	}
	
	/**
	 * 出入库单审核不通过
	 * @param int $invoiceTypeId
	 * @param int $storeId
	 */
	public function iostoreAuditNoPass($uid, $iostoreId){
		self::initDB();
		$now_time = time();
		$sql    = "SELECT * FROM wh_iostore WHERE id={$iostoreId}";
		$iolist = self::$dbConn->fetch_first($sql);
		if($iolist['nextoperatorId']!=$uid){
			self::$errCode = "001";
			self::$errMsg = "当前审核人不是你";
			return false;
		}
		
		$sql   = "SELECT * FROM wh_audit_relation_list WHERE invoiceTypeId={$iolist['invoiceTypeId']} AND storeId={$iolist['storeId']} ORDER BY auditLevel ASC, auditorId ASC";
		$query = self::$dbConn->query($sql);
		$lists = self::$dbConn->fetch_array_all($query);
		$auditLevel = 0;
		$auditRelationId = 0;
		foreach($lists as $key=>$list){
			if($list['auditorId']==$uid){
				$auditLevel = $list['auditLevel'];
				$auditRelationId = $list['id'];
				break;
			}
		}
		self::$dbConn->begin();
		$insert_record_sql = "INSERT INTO wh_audit_records (ordersn,auditRelationId,auditStatus,auditLevel,auditUser,auditTime) 
							VALUES('{$iolist['ordersn']}',{$auditRelationId},2,$auditLevel,$uid,$now_time)";
		$query = self::$dbConn->query($insert_record_sql);
		if(!$query){
			self::$errCode = "002";
			self::$errMsg = "插入审核记录表失败，审核失败";
			return false;
		}
		
		$update_sql = "UPDATE wh_iostore SET ioStatus=3,operatorId={$uid} where id={$iostoreId}";
		$querys = self::$dbConn->query($update_sql);
		if(!$querys){
			self::$errCode = "003";
			self::$errMsg  = "更新审核表失败，审核失败";
			self::$dbConn->rollback();
			return false;
		}
		self::$dbConn->commit();
		return true;
	}
	
	
	/*
	 *取得指定表中的指定记录
	 */
	public static function getTNameList($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/*
	 *取得指定表中的指定记录记录数
	 */
	public static function getTNameCount($tName, $where) {
		self :: initDB();
		$sql = "select count(*) count from $tName $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['count']; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *添加指定表记录
	 */
	public static function addTNameRow($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$insertId = self :: $dbConn->insert_id($query);
			return $insertId; //成功， 返回插入的id
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *修改指定表记录
	 */
	public static function updateTNameRow($tName, $set, $where) {
		self :: initDB();
		$sql = "UPDATE $tName $set $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$affectRows = self :: $dbConn->affected_rows($query);
			return $affectRows; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据id查找出入库单类型名称
	 */
	public static function getInvoiceTypeNameById($id) {
		self :: initDB();
		$sql = "SELECT invoiceName FROM wh_invoice_type WHERE id='$id'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list[0]['invoiceName']; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
    
    /**
	 *根据ioType取得对应出入库单类型的记录
	 */
	public static function getInvoiceTypeListByioType($ioType) {
		self :: initDB();
		$sql = "SELECT * FROM wh_invoice_type WHERE ioType='$ioType'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *根据id查找类型名称
	 */
	public static function getIoTypeNameById($id) {
		self :: initDB();
		$sql = "SELECT typeName FROM wh_iotype WHERE id='$id'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list[0]['typeName']; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据ioType取得对应的记录
	 */
	public static function getIoTypeListByioType($ioType) {
		self :: initDB();
		$sql = "SELECT * FROM wh_iotype WHERE ioType='$ioType'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *根据id查找付款名称
	 */
	public static function getPaymentMethodsById($id) {
		self :: initDB();
		$sql = "SELECT  method FROM wh_payment_methods WHERE id='$id'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list[0]['method']; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *根据id查找仓库名称
	 */
	public static function getWhNameById($id) {
		self :: initDB();
		$sql = "SELECT  whName FROM wh_store WHERE id='$id'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list[0]['whName']; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 * 添加单据及详情
	 * @param array $orderArr
	 */
	public function ddIoStore($orderArr){
		self::initDB();
		$invoiceTypeId    = $orderArr['invoiceTypeId']; //出入库类型表中的id
		$ioType 	      = $orderArr['ioType']; //出/入库，只能为1或2,1为出库，2为入库
		$userId 	      = $orderArr['userId']; //申请人id
		$ordersn	      = $orderArr['ordersn']; //单据号（单据编码）
		$paymentMethodsId = $orderArr['paymentMethodsId']; //付款方式表中的id
        $note             = isset($orderArr['note'])?$orderArr['note']:''; //备注
		$companyId        = $orderArr['companyId']; //公司id
		$storeId          = $orderArr['storeId']; //仓库表中的id
		$detail           = $orderArr['detail'];  //订单详情

		if (is_numeric($invoiceTypeId)) { //出入库类型表中的id不能为空
			self :: $errCode = 402;
			self :: $errMsg = 'invoiceTypeId有误';
			return false;
		}
		if ($ioType != 1 && $ioType != 2) { //出或者入库
			self :: $errCode = 403;
			self :: $errMsg = 'ioType有误';
			return false;
		}
		if (empty ($userId)) {
			self :: $errCode = 404;
			self :: $errMsg = 'userId有误';
			return false;
		}
		if (empty ($ordersn)) {
			self :: $errCode = 405;
			self :: $errMsg = 'ordersn有误';
			return false;
		}
		if (empty ($paymentMethodsId)) {
			self :: $errCode = 406;
			self :: $errMsg = 'paymentMethodsId有误';
			return false;
		}
		if (!is_array($detail)) {
			self :: $errCode = 407;
			self :: $errMsg = 'detail有误';
			return false;
		}
		if (empty ($companyId)) { //默认公司为1赛维
			$companyId = 1;
		}
		if (empty ($storeId)) { //默认仓库为1，深圳仓库
			$storeId = 1;
		}
		
		$sql   = "SELECT * FROM wh_audit_relation_list WHERE invoiceTypeId={$invoiceTypeId} AND storeId={$storeId} ORDER BY auditLevel ASC, auditorId ASC";
		$query = self::$dbConn->query($sql);
		$lists = self::$dbConn->fetch_array_all($query);
		if(empty($lists)){
			self::$errCode = 408;
			self::$errMsg  = "找不到对应的出入库类型";
			return false;
		}
		
		$now_time = time();
		self::$dbConn->begin();
		$insert_iostore_sql = "INSERT INTO wh_iostore (invoiceTypeId,ioType,userId,createdTime,ordersn,paymentMethodsId,companyId,nextoperatorId,storeId,note) 
							VALUES({$invoiceTypeId},{$ioType},{$userId},{$now_time},'{$ordersn}',{$paymentMethodsId},{$companyId},{$lists[0]['auditorId']},{$storeId},'{$note}')";
		$insert_iostore = self::$dbConn->query($insert_iostore_sql);
		if(!$insert_iostore){
			self::$errCode = "409";
			self::$errMsg  = "插入单据表失败";
			return false;
		}else{
			$iostoreId = self::$dbConn->insert_id($insert_iostore);
		}
		
		//添加详情
		foreach($detail as $det){
			$sku    	= $det['sku']; //添加的sku
			$amount 	= $det['amount']; //对应数量
			$cost   	= $det['cost']; //成本
			$purchaseId = $det['purchaseId']; //采购员id
			
			$insert_detail_sql = "INSERT INTO wh_iostoredetail (iostoreId,sku,amount,cost,purchaseId,storeId) 
								VALUES({$iostoreId},'{$sku}',{$amount},'{$cost}',{$purchaseId},{$storeId})";
			$insert_detail 	   = self::$dbConn->query($insert_detail_sql);
			if(!$insert_detail){
				self::$errCode = "410";
				self::$errMsg  = "插入单据详情表失败";
				self::$dbConn->rollback();
				return false;
			}
		}
		self::$dbConn->commit();
		return true;
	}

}
?>
