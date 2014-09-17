<?php


/*
 * 出入库单action
 * ADD BY zqt 2013.8.20
 */
class WhIoStoreAct extends Auth {
	
	static $errCode = 0;
	static $errMsg = "";

	/**
	 * 获取入库单列表
	 */
	public function act_getInStoreList(){
		
		//查询条件初始化
		$conditions = array();
		$conditions[] = 'ioType=2';
		if (isset($_GET['type'])&&!empty($_GET['type'])){
			//$conditions[] = 'ioType=2';
		}
		if (isset($_GET['ordersn'])&&!empty($_GET['ordersn'])){
			$_ordersn = trim($_GET['ordersn']);
			if (preg_match("/^[a-z\-]*[\d]*$/i", $_ordersn)){
				$conditions[] = "ordersn='{$_ordersn}'";
			}
			unset($_ordersn);
		}
		if (isset($_GET['iostatus'])&&!empty($_GET['iostatus'])){
			$conditions[] = "ioStatus=".intval($_GET['iostatus']);
		}
		if (isset($_GET['storeid'])&&!empty($_GET['storeid'])){
			$conditions[] = "storeId=".intval($_GET['storeid']);
		}
		if (isset($_GET['invoicetypeid'])&&!empty($_GET['invoicetypeid'])){
			$conditions[] = "invoiceTypeId=".intval($_GET['invoicetypeid']);
		}
		if (isset($_GET['cStartTime'])&&!empty($_GET['cStartTime'])){
			$startTime = strtotime($_GET['cStartTime'].' 00:00:00');
			$conditions[] = "createdTime >='$startTime' ";
		}
		if (isset($_GET['cEndTime'])&&!empty($_GET['cEndTime'])){
			$endTime = strtotime($_GET['cEndTime'].' 23:59:59');
			$conditions[] = "createdTime <='$endTime' ";
		}
		$where 		= 'WHERE '.implode(' AND ', $conditions);
		$page 		= isset($_GET['page'])&&intval($_GET['page'])>0 ? intval($_GET['page']) : 1;
		$perpage 	= isset($_GET['perpage'])&&!empty($_GET['perpage']) ?  intval($_GET['perpage']) : 30;
		$sort 		= 'ORDER BY id DESC';
		$whIoStoreAct = new WhIoStoreModel();
		$total = $whIoStoreAct->getIOStoreCount($where);
		$page = new Page($total, $perpage, '', 'CN');
		$InStoreList = $whIoStoreAct->getInStoreList($where, $sort, $page->limit);
		$show_page = $total > $num ? $page->fpage(array (0,2,3,4,5,6,7,8,9)) : $page->fpage(array (0,2,3));
		$invoiceTypeList = WhIoStoreModel::getInvoiceTypeListByioType(1);
		$InStoreList['pageinfo']['total'] = $total;
		$InStoreList['pageinfo']['perpage'] = $perpage;
		$InStoreList['pageinfo']['nowpage'] = $page;
		$InStoreList['pageinfo']['show_page'] = $show_page;
		$InStoreList['typelist'] = $invoiceTypeList;
		return $InStoreList;
	}
	
	/**
	 * 获取出库单数据列表
	 */
	public function act_getOutStoreList(){
		
		//查询条件初始化
		$conditions = array();
		$conditions[] = 'ioType=1';
		if (isset($_GET['type'])&&!empty($_GET['type'])){
			//$conditions[] = 'ioType=2';
		}
		if (isset($_GET['ordersn'])&&!empty($_GET['ordersn'])){
			$_ordersn = trim($_GET['ordersn']);
			if (preg_match("/^[a-z\-]*[\d]*$/i", $_ordersn)){
				$conditions[] = "ordersn='{$_ordersn}'";
			}
			unset($_ordersn);
		}
		if (isset($_GET['iostatus'])&&!empty($_GET['iostatus'])){
			$conditions[] = "ioStatus=".intval($_GET['iostatus']);
		}
		if (isset($_GET['storeid'])&&!empty($_GET['storeid'])){
			$conditions[] = "storeId=".intval($_GET['storeid']);
		}
		if (isset($_GET['invoicetypeid'])&&!empty($_GET['invoicetypeid'])){
			$conditions[] = "invoiceTypeId=".intval($_GET['invoicetypeid']);
		}
		if (isset($_GET['cStartTime'])&&!empty($_GET['cStartTime'])){
			$startTime = strtotime(intval($_GET['cStartTime']).' 00:00:00');
			$conditions[] = "createdTime >='$startTime' ";
		}
		if (isset($_GET['cEndTime'])&&!empty($_GET['cEndTime'])){
			$endTime = strtotime(intval($_GET['cEndTime']).' 23:59:59');
			$conditions[] = "createdTime <='$endTime' ";
		}
		$where 		= 'WHERE '.implode(' AND ', $conditions);
		$page 		= isset($_GET['page'])&&intval($_GET['page'])>0 ? intval($_GET['page']) : 1;
		$perpage 	= isset($_GET['perpage'])&&!empty($_GET['perpage']) ?  intval($_GET['perpage']) : 30;
		$sort 		= 'ORDER BY id DESC';
		$whIoStoreAct = new WhIoStoreModel();
		$total = $whIoStoreAct->getIOStoreCount($where);
		$page = new Page($total, $perpage, '', 'CN');
		$InStoreList = $whIoStoreAct->getInStoreList($where, $sort, $page->limit);
		$show_page = $total > $num ? $page->fpage(array (0,2,3,4,5,6,7,8,9)) : $page->fpage(array (0,2,3));
		$invoiceTypeList = WhIoStoreModel::getInvoiceTypeListByioType(0);
		$InStoreList['pageinfo']['total'] = $total;
		$InStoreList['pageinfo']['perpage'] = $perpage;
		$InStoreList['pageinfo']['nowpage'] = $page;
		$InStoreList['pageinfo']['show_page'] = $show_page;
		$InStoreList['typelist'] = $invoiceTypeList;
		return $InStoreList;
	}
	
	/**
	 * 获取需要审核的入库单据列表
	 */
	public function act_getAuditInStoreList($uid=0){
		$uid = intval($uid);
		if(isset($_GET['uid'])&&!empty($_GET['uid'])){
			$uid = intval($_GET['uid']);
		}
		if ($uid==0){
			self :: $errCode = 001;
			self :: $errMsg  = 'uid is error';
			return false;
		}
		
		//查询条件初始化
		$conditions = array();
		$conditions[] = 'ioType=2';
		$conditions[] = 'ioStatus=1';
		if (isset($_GET['ordersn'])&&!empty($_GET['ordersn'])){
			$_ordersn = trim($_GET['ordersn']);
			if (preg_match("/^[a-z\-]*[\d]*$/i", $_ordersn)){
				$conditions[] = "ordersn='{$_ordersn}'";
			}
			unset($_ordersn);
		}
		$conditions[] = "nextoperatorId={$uid}";
		
		$where 		= 'WHERE '.implode(' AND ', $conditions);
		$sort 		= 'ORDER BY id DESC';
		$limit      = 'LIMIT 100';
		$whIoStoreAct = new WhIoStoreModel();
		$AuditInStoreList = $whIoStoreAct->getAuditIOStoreList($where, $sort, $limit);
		return $AuditInStoreList;
	}
	
	/**
	 * 获取需要审核的出库单据列表
	 */
	public function act_getAuditOutStoreList($uid=0){
		$uid = intval($uid);
		if(isset($_GET['uid'])&&!empty($_GET['uid'])){
			$uid = intval($_GET['uid']);
		}
		if ($uid==0){
			self :: $errCode = 001;
			self :: $errMsg  = 'uid is error';
			return false;
		}
		
		//查询条件初始化
		$conditions = array();
		$conditions[] = 'ioType=1';
		$conditions[] = 'ioStatus=1';
		if (isset($_GET['ordersn'])&&!empty($_GET['ordersn'])){
			$_ordersn = trim($_GET['ordersn']);
			if (preg_match("/^[a-z\-]*$[\d]*/i", $_ordersn)){
				$conditions[] = "ordersn='{$_ordersn}'";
			}
			unset($_ordersn);
		}
		$conditions[] = "nextoperatorId={$uid}";
		
		$where 		= 'WHERE '.implode(' AND ', $conditions);
		$sort 		= 'ORDER BY id DESC';
		$limit      = 'LIMIT 100';
		$whIoStoreAct = new WhIoStoreModel();
		$AuditInStoreList = $whIoStoreAct->getAuditIOStoreList($where, $sort, $limit);
		return $AuditInStoreList;
	}
	
	/**
	 * 单据审核通过
	 */
	function act_auditIoStoreOrderPass(){
		if($_SERVER['REQUEST_METHOD']=='GET'){
			$iostoreId = isset($_GET['iostoreId'])?intval($_GET['iostoreId']):0;
			$uid 	   = isset($_GET['userId'])?intval($_GET['userId']):0;
			if($iostoreId==0 || $uid==0){
				self :: $errCode = 001;
				self :: $errMsg  = '参数有误';
				return false;
			}
		}else{
			$iostoreId = intval($_POST['iostoreId']);
			$uid 	   = $_SESSION['userId'];
		}
		$audit = WhIoStoreModel::iostoreAuditPass($uid, $iostoreId);
		if($audit){
			return true;
		}else{
			self :: $errCode = WhIoStoreModel::$errCode;
			self :: $errMsg  = WhIoStoreModel::$errMsg;
			return false;
		}
	}
	
	/**
	 * 单据审核不通过
	 */
	function act_auditIoStoreOrderNoPass(){
		if($_SERVER['REQUEST_METHOD']=='GET'){
			$iostoreId = isset($_GET['iostoreId'])?intval($_GET['iostoreId']):0;
			$uid 	   = isset($_GET['userId'])?intval($_GET['userId']):0;
			if($iostoreId==0 || $uid==0){
				self :: $errCode = 001;
				self :: $errMsg  = '参数有误';
				return false;
			}
		}else{
			$iostoreId = intval($_POST['iostoreId']);
			$uid 	   = $_SESSION['userId'];
		}
		$audit = WhIoStoreModel::iostoreAuditNoPass($uid, $iostoreId);
		if($audit){
			return true;
		}else{
			self :: $errCode = WhIoStoreModel::$errCode;
			self :: $errMsg  = WhIoStoreModel::$errMsg;
			return false;
		}
	}
	
	
	/**************api接口***************/
	/**
	 * api添加单据及详细
	 */
	function act_addIoStore() {
		$orderArr = isset ($_GET['orderArr']) ? json_decode($_GET['orderArr']) : ''; 
		if (!is_array($orderArr)) {
			self :: $errCode = 401;
			self :: $errMsg = '参数有误';
			return false;
		}
		
		$insert = WhIoStoreModel::ddIoStore($orderArr);
		if (!$insert) {
			self :: $errCode = WhIoStoreModel::$errCode;
			self :: $errMsg  = WhIoStoreModel::$errMsg;
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * api获取出入库单类型
	 */
	function act_getInvoiceType() {
		$info = WarehouseManagementModel::whIoInvoicesTypeModelList('where 1');
		if (!$info) {
			self :: $errCode = WarehouseManagementModel::$errCode;
			self :: $errMsg  = WarehouseManagementModel::$errMsg;
			return false;
		} else {
			return $info;
		}
	}
	
	
	
	
	
	
	
	
	
	
	/*
	 * 取得指定iostore记录
	 */
	function act_getTNameList($tName, $set, $where) { //表名，SET，WHERE
		$list = WhIoStoreModel :: getTNameList($tName, $set, $where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = WhIoStoreModel :: $errCode;
			self :: $errMsg = WhIoStoreModel :: $errMsg;
			return false;
		}
	}

	function act_getTNameCount($tName, $where) {
		$ret = WhIoStoreModel :: getTNameCount($tName, $where);
		if ($ret !== false) {
			return $ret;
		} else {
			self :: $errCode = WhIoStoreModel :: $errCode;
			self :: $errMsg = WhIoStoreModel :: $errMsg;
			return false;
		}
	}

	function act_addTNameRow($tName, $set) {
		$ret = WhIoStoreModel :: addTNameRow($tName, $set);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = WhIoStoreModel :: $errCode;
			self :: $errMsg = WhIoStoreModel :: $errMsg;
			return false;
		}
	}

	function act_updateTNameRow($tName, $set, $where) {
		$ret = WhIoStoreModel :: updateTNameRow($tName, $set, $where);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = WhIoStoreModel :: $errCode;
			self :: $errMsg = WhIoStoreModel :: $errMsg;
			return false;
		}
	}

	//wh系统内部调用api
	//添加出入库单表头
	function act_addWhIoStoreForWh($jsonArr) { //$jsonArr为参数数组，包含的键值如下
		if (empty ($jsonArr)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		if (!is_array($jsonArr)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error array';
			return 0;
		}
		$invoiceTypeId = $jsonArr['invoiceTypeId']; //出入库类型表中的id
		$ioType = $jsonArr['ioType']; //出/入库，只能为1或2,1为出库，2为入库
		$userId = $jsonArr['userId']; //申请人id
		$ordersn = $jsonArr['ordersn']; //单据号（单据编码）
        $note = isset($jsonArr['note'])?$jsonArr['note']:''; //单据号（单据编码）
		$paymentMethodsId = $jsonArr['paymentMethodsId']; //付款方式表中的id
		$companyId = $jsonArr['companyId']; //公司id
		$storeId = $jsonArr['storeId']; //仓库表中的id

		if (empty ($invoiceTypeId)) { //出入库类型表中的id不能为空
			self :: $errCode = 0301;
			self :: $errMsg = 'empty invoiceTypeId';
			return 0;
		}
		if ($ioType != 1 && $ioType != 2) { //出或者入库
			self :: $errCode = 0401;
			self :: $errMsg = 'error ioType';
			return 0;
		}
		if (empty ($userId)) {
			self :: $errCode = 0501;
			self :: $errMsg = 'empty userId';
			return 0;
		}
		if (empty ($ordersn)) {
			self :: $errCode = 0601;
			self :: $errMsg = 'empty ordersn';
			return 0;
		}
		if (empty ($paymentMethodsId)) {
			self :: $errCode = 0701;
			self :: $errMsg = 'empty paymentMethodsId';
			return 0;
		}
		if (empty ($companyId)) { //默认公司为1赛维
			$companyId = 1;
		}
		if (empty ($storeId)) { //默认仓库为1，深圳仓库
			$storeId = 1;
		}
		$now = time();
		$tName = 'wh_iostore';
		$set = "SET invoiceTypeId='$invoiceTypeId',ioType='$ioType',userId='$userId',ordersn='$ordersn',note='$note',paymentMethodsId='$paymentMethodsId',companyId='$companyId',storeId='$storeId',createdTime='$now' ";
		$insertId = WhIoStoreModel :: addTNameRow($tName, $set);
		if (!$insertId) {
			self :: $errCode = 0801;
			self :: $errMsg = 'addRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $insertId;//返回插入的记录id
		}
	}

	//wh系统供外部调用api
	//添加出入库单表头
	function act_addWhIoStoreInWh() { //$jsonArr为参数数组，包含的键值如下
		
		$jsonArr		=	$_REQUEST['jsonArr'];
		
		$jsonArr		=	json_decode($jsonArr,true);
		//return  $jsonArr;
		if (empty ($jsonArr)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		if (!is_array($jsonArr)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error array';
			return 0;
		}
		$invoiceTypeId = $jsonArr['invoiceTypeId']; //出入库类型表中的id
		$ioType = $jsonArr['ioType']; //出/入库，只能为1或2,1为出库，2为入库
		$userId = $jsonArr['addUserId']; //申请人id
		$ordersn = $jsonArr['ordersn']; //单据号（单据编码）
		$paymentMethodsId = $jsonArr['paymentMethodsId']; //付款方式表中的id
		$companyId = $jsonArr['companyId']; //公司id
		$storeId = $jsonArr['whId']; //仓库表中的id
		$now		=	$jsonArr['createdTime'];
		if (empty ($invoiceTypeId)) { //出入库类型表中的id不能为空
			self :: $errCode = 0301;
			self :: $errMsg = 'empty invoiceTypeId';
			return 0;
		}
		if ($ioType != 1 && $ioType != 2) { //出或者入库
			self :: $errCode = 0401;
			self :: $errMsg = 'error ioType';
			return 0;
		}
		if (empty ($userId)) {
			self :: $errCode = 0501;
			self :: $errMsg = 'empty userId';
			return 0;
		}
		if (empty ($ordersn)) {
			self :: $errCode = 0601;
			self :: $errMsg = 'empty ordersn';
			return 0;
		}
		if (empty ($paymentMethodsId)) {
			self :: $errCode = 0701;
			self :: $errMsg = 'empty paymentMethodsId';
			return 0;
		}
		if (empty ($companyId)) { //默认公司为1赛维
			$companyId = 1;
		}
		if (empty ($storeId)) { //默认仓库为1，深圳仓库
			$storeId = 1;
		}
		$tName = 'wh_iostore';
		$set = "SET "
		."invoiceTypeId='$invoiceTypeId',"
		."ioType='$ioType',"
		."userId='$userId',"
		."ordersn='$ordersn',"
		."paymentMethodsId='$paymentMethodsId',"
		."companyId='$companyId',"
		."storeId='$storeId',"
		."createdTime='$now' ";
		$insertId = WhIoStoreModel :: addTNameRow($tName, $set);
		if (!$insertId) {
			self :: $errCode = 0801;
			self :: $errMsg = 'addRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $insertId;//返回插入的记录id
		}
	}
	
	
	
	
	//删除出入库单（根据iostore记录的id）
	function act_deleteWhIoStoreForWh($iostoreId) {
		try {
			TransactionBaseModel :: begin(); //开始事务
			if (empty ($iostoreId)) { //如果iostoreId为空，则抛出异常
				self :: $errCode = 0101;
				self :: $errMsg = 'empty iostoreId';
				throw new Exception('empty iostoreId');
			}
			$tName = 'wh_iostore';
			$set = "SET is_delete=1 ";
			$where = "WHERE id='$iostoreId' ";

			$affectRows = WhIoStoreModel :: updateTNameRow($tName, $set, $where); //将ioStore表记录标记为删除,is_delete=1
			if (!$affectRows) { //执行错误或者affectedRows为0则抛出异常
				self :: $errCode = 0201;
				self :: $errMsg = 'deleteRow error';
				throw new Exception('deleteRow error');
			}
			$tName = 'wh_iostoredetail';
			$where = "WHERE iostoreId='$iostoreId' ";
			$affectRowsDetail = WhIoStoreModel :: updateTNameRow($tName, $set, $where); //对应iostoreId的iostoredetail表中也标记删除对应记录
			if ($affectRowsDetail === false) { //执行错误则抛出异常（不包括影响记录为0的情况）
				self :: $errCode = 0301;
				self :: $errMsg = 'deleteDetailRow error';
				throw new Exception('deleteDetailRow error');
			}
			TransactionBaseModel :: commit(); //执行
			TransactionBaseModel :: autoCommit(); //重新设置事务为自动提交
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return 1; //返回正确
		} catch (Exception $e) {
			TransactionBaseModel :: rollback(); //回滚
			TransactionBaseModel :: autoCommit();
			return 0;
		}
	}

	//添加出入库单明细记录
	function act_addWhIoStoreDetailForWh($jsonArr) { //$jsonArr为参数数组，包含的键值如下
		if (empty ($jsonArr)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		if (!is_array($jsonArr)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error array';
			return 0;
		}
		$iostoreId = $jsonArr['iostoreId']; //出入库单据编号(id)
		$sku = $jsonArr['sku']; //添加的sku
		$amount = $jsonArr['amount']; //对应数量
		$cost = $jsonArr['cost']; //成本
		$purchaseId = $jsonArr['purchaseId']; //采购员id
		$positionId = $jsonArr['positionId'];//仓位ID

		if (empty ($iostoreId)) { //出入库单据的id不能为空
			self :: $errCode = 0301;
			self :: $errMsg = 'empty iostoreId';
			return 0;
		}
		if (empty ($sku)) {
			self :: $errCode = 0401;
			self :: $errMsg = 'empty sku';
			return 0;
		}
		if (empty ($amount)) {
			self :: $errCode = 0501;
			self :: $errMsg = 'empty amount';
			return 0;
		}
		if (empty ($cost)) {
			self :: $errCode = 0601;
			self :: $errMsg = 'empty cost';
			return 0;
		}
		if (empty ($purchaseId)) { //默认公司为1赛维
			self :: $errCode = 0701;
			self :: $errMsg = 'empty purchaseId';
			return 0;
		}
		$tName = 'wh_iostoredetail';
		$set = "SET iostoreId='$iostoreId',sku='$sku',amount='$amount',cost='$cost',purchaseId='$purchaseId',positionId='$positionId' ";
		$affectRows = WhIoStoreModel :: addTNameRow($tName, $set);
		if (!$affectRows) {
			self :: $errCode = 0801;
			self :: $errMsg = 'addRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}
	//wh供外部调用的api
	//插入数据到wh_iostoredetail
	function act_addWhIoStoreDetailInWh() { //$jsonArr为参数数组，包含的键值如下
		$jsonArr		=	$_REQUEST['jsonArr'];
		$jsonArr		=	json_decode($jsonArr,true);
		if (empty ($jsonArr)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		if (!is_array($jsonArr)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error array';
			return 0;
		}
		$iostoreId = $jsonArr['iostoreId']; //出入库单据编号(id)
		$sku = $jsonArr['sku']; //添加的sku
		$amount = $jsonArr['amount']; //对应数量
		$cost = $jsonArr['cost']; //成本
		$purchaseId = $jsonArr['purchaseId']; //采购员id
		$storeId	=$jsonArr['whId'];
		$positionId = OmAvailableModel::getSkuPositions($sku,$storeId);//仓位ID
	
		if (empty ($iostoreId)) { //出入库单据的id不能为空
			self :: $errCode = 0301;
			self :: $errMsg = 'empty iostoreId';
			return 0;
		}
		if (empty ($sku)) {
			self :: $errCode = 0401;
			self :: $errMsg = 'empty sku';
			return 0;
		}
		if (empty ($amount)) {
			self :: $errCode = 0501;
			self :: $errMsg = 'empty amount';
			return 0;
		}
		if (empty ($cost)) {
			self :: $errCode = 0601;
			self :: $errMsg = 'empty cost';
			return 0;
		}
		if (empty ($purchaseId)) { //默认公司为1赛维
			self :: $errCode = 0701;
			self :: $errMsg = 'empty purchaseId';
			return 0;
		}
		$tName = 'wh_iostoredetail';
		$set = "SET iostoreId='$iostoreId',sku='$sku',amount='$amount',cost='$cost',purchaseId='$purchaseId',positionId='$positionId' ";
		$affectRows = WhIoStoreModel :: addTNameRow($tName, $set);
		if (!$affectRows) {
			self :: $errCode = 0801;
			self :: $errMsg = 'addRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}
	
	//删除出入库单明细（根据iostore记录的id）
	function act_deleteWhIoStoreDetailForWh($iostoreId) {
		if (empty ($iostoreId)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty iostoreId';
			return 0;
		}
		$tName = 'wh_iostoredetail';
		$set = "SET is_delete=1 ";
		$where = "WHERE iostoreId='$iostoreId' ";
		$affectRows = WhIoStoreModel :: updateTNameRow($tName, $set, $where);
		if (!$affectRows) {
			self :: $errCode = 0201;
			self :: $errMsg = 'updateRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}

	//修改出入库单明细（根据iostore记录的id，只修改对应sku的数量）
	function act_updateWhIoStoreDetailForWh($iostoreId, $sku, $amount) {
		//iostoreId,表头记录id
		//待修改数量的sku
		//修改后的数量
		if (empty ($iostoreId)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty iostoreId';
			return 0;
		}
		if (empty ($sku)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'empty sku';
			return 0;
		}
		if (empty ($amount)) {
			self :: $errCode = 0301;
			self :: $errMsg = 'empty amount';
			return 0;
		}
		$tName = 'wh_iostoredetail';
		$set = "SET amount='$amount' ";
		$where = "WHERE iostoreId='$iostoreId' AND sku='$sku' ";
		$affectRows = WhIoStoreModel :: updateTNameRow($tName, $set, $where);
		if ($affectRows === FALSE) { //执行错误或者无affectedRow
			self :: $errCode = 0201;
			self :: $errMsg = 'updateRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}

	//修改出入库单状态（根据iostore记录的id）
	function act_updateWhIoStoreStatusForWh($id, $ioStatus) {
		//id
		//待修改数量的sku
		if (empty ($id)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty id';
			return 0;
		}
		$ioStatus = intval($ioStatus);
		if ($ioStatus != 0 && $ioStatus != 1) {
			self :: $errCode = 0301;
			self :: $errMsg = 'illegal ioStatus';
			return 0;
		}
		$tName = 'wh_iostore';
		$set = "SET ioStatus='$ioStatus' ";
		$where = "WHERE id='$id' ";
		$affectRows = WhIoStoreModel :: updateTNameRow($tName, $set, $where);
		if ($affectRows === FALSE) { //执行错误或者无affectedRow
			self :: $errCode = 0401;
			self :: $errMsg = 'updateRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}

	//对外接口
	//添加出入库单
	function act_addWhIoStore() {
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串(客户端要先json然后再base64))
		if (empty ($jsonArr)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error array';
			return 0;
		}
		$invoiceTypeId = $jsonArr['invoiceTypeId']; //出入库类型表中的id
		$ioType = $jsonArr['ioType']; //出/入库，只能为1或2,1为出库，2为入库
		$userId = $jsonArr['userId']; //申请人id
		$ordersn = $jsonArr['ordersn']; //单据号（单据编码）
		$paymentMethodsId = $jsonArr['paymentMethodsId']; //付款方式表中的id
        $note = isset($jsonArr['note'])?$jsonArr['note']:''; //单据号（单据编码）
		$companyId = $jsonArr['companyId']; //公司id
		$storeId = $jsonArr['storeId']; //仓库表中的id

		if (empty ($invoiceTypeId)) { //出入库类型表中的id不能为空
			self :: $errCode = 0301;
			self :: $errMsg = 'empty invoiceTypeId';
			return 0;
		}
		if ($ioType != 1 && $ioType != 2) { //出或者入库
			self :: $errCode = 0401;
			self :: $errMsg = 'error ioType';
			return 0;
		}
		if (empty ($userId)) {
			self :: $errCode = 0501;
			self :: $errMsg = 'empty userId';
			return 0;
		}
		if (empty ($ordersn)) {
			self :: $errCode = 0601;
			self :: $errMsg = 'empty ordersn';
			return 0;
		}
		if (empty ($paymentMethodsId)) {
			self :: $errCode = 0701;
			self :: $errMsg = 'empty paymentMethodsId';
			return 0;
		}
		if (empty ($companyId)) { //默认公司为1赛维
			$companyId = 1;
		}
		if (empty ($storeId)) { //默认仓库为1，深圳仓库
			$storeId = 1;
		}
		$now = time();
		$tName = 'wh_iostore';
		$set = "SET invoiceTypeId='$invoiceTypeId',ioType='$ioType',userId='$userId',ordersn='$ordersn',note='$note',paymentMethodsId='$paymentMethodsId',companyId='$companyId',storeId='$storeId',createdTime='$now' ";
		$insertId = WhIoStoreModel :: addTNameRow($tName, $set);
		if (!$insertId) {
			self :: $errCode = 0801;
			self :: $errMsg = 'addRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $insertId;//返回插入的记录id
		}
	}

	//删除出入库单（根据iostore记录的id）
	function act_deleteWhIoStore() {
		$iostoreId = isset ($_GET['iostoreId']) ? $_GET['iostoreId'] : ''; //传过来的base64编码的json字符串
		try {
			TransactionBaseModel :: begin(); //开始事务
			if (empty ($iostoreId)) { //如果iostoreId为空，则抛出异常
				self :: $errCode = 0101;
				self :: $errMsg = 'empty iostoreId';
				throw new Exception('empty iostoreId');
			}
			$tName = 'wh_iostore';
			$set = "SET is_delete=1 ";
			$where = "WHERE id='$iostoreId' ";

			$affectRows = WhIoStoreModel :: updateTNameRow($tName, $set, $where); //将ioStore表记录标记为删除,is_delete=1
			if (!$affectRows) { //执行错误或者affectedRows为0则抛出异常
				self :: $errCode = 0201;
				self :: $errMsg = 'deleteRow error';
				throw new Exception('deleteRow error');
			}
			$tName = 'wh_iostoredetail';
			$where = "WHERE iostoreId='$iostoreId' ";
			$affectRowsDetail = WhIoStoreModel :: updateTNameRow($tName, $set, $where); //对应iostoreId的iostoredetail表中也标记删除对应记录
			if ($affectRowsDetail === false) { //执行错误则抛出异常（不包括影响记录为0的情况）
				self :: $errCode = 0301;
				self :: $errMsg = 'deleteDetailRow error';
				throw new Exception('deleteDetailRow error');
			}
			TransactionBaseModel :: commit(); //执行
			TransactionBaseModel :: autoCommit(); //重新设置事务为自动提交
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return 1; //返回正确
		} catch (Exception $e) {
			TransactionBaseModel :: rollback(); //回滚
			TransactionBaseModel :: autoCommit();
			return 0;
		}
	}

	//添加出入库单明细记录
	function act_addWhIoStoreDetail() {
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty jsonArr';
			return 0;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error array';
			return 0;
		}
		$iostoreId = $jsonArr['iostoreId']; //出入库单据编号(id)
		$sku = $jsonArr['sku']; //添加的sku
		$amount = $jsonArr['amount']; //对应数量
		$cost = $jsonArr['cost']; //成本
		$purchaseId = $jsonArr['purchaseId']; //采购员id

		if (empty ($iostoreId)) { //出入库单据的id不能为空
			self :: $errCode = 0301;
			self :: $errMsg = 'empty iostoreId';
			return 0;
		}
		if (empty ($sku)) {
			self :: $errCode = 0401;
			self :: $errMsg = 'empty sku';
			return 0;
		}
		if (empty ($amount)) {
			self :: $errCode = 0501;
			self :: $errMsg = 'empty amount';
			return 0;
		}
		if (empty ($cost)) {
			self :: $errCode = 0601;
			self :: $errMsg = 'empty cost';
			return 0;
		}
		if (empty ($purchaseId)) { //默认公司为1赛维
			self :: $errCode = 0701;
			self :: $errMsg = 'empty purchaseId';
			return 0;
		}
		$tName = 'wh_iostoredetail';
		$set = "SET iostoreId='$iostoreId',sku='$sku',amount='$amount',cost='$cost',purchaseId='$purchaseId' ";
		$affectRows = WhIoStoreModel :: addTNameRow($tName, $set);
		if (!$affectRows) {
			self :: $errCode = 0801;
			self :: $errMsg = 'addRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}

	//删除出入库单明细（根据iostore记录的id）
	function act_deleteWhIoStoreDetail() {
		$iostoreId = isset ($_GET['iostoreId']) ? $_GET['iostoreId'] : ''; //传过来的base64编码的json字符串
		if (empty ($iostoreId)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty iostoreId';
			return 0;
		}
		$tName = 'wh_iostoredetail';
		$set = "SET is_delete=1 ";
		$where = "WHERE iostoreId='$iostoreId' ";
		$affectRows = WhIoStoreModel :: updateTNameRow($tName, $set, $where);
		if (!$affectRows) {
			self :: $errCode = 0201;
			self :: $errMsg = 'updateRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}

	//修改出入库单明细（根据iostore记录的id，只修改对应sku的数量）
	function act_updateWhIoStoreDetail() {
		$iostoreId = isset ($_GET['iostoreId']) ? $_GET['iostoreId'] : ''; //iostoreId,表头记录id
		$sku = isset ($_GET['sku']) ? $_GET['sku'] : ''; //待修改数量的sku
		$amount = isset ($_GET['amount']) ? $_GET['amount'] : ''; //修改后的数量
		if (empty ($iostoreId)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty iostoreId';
			return 0;
		}
		if (empty ($sku)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'empty sku';
			return 0;
		}
		if (empty ($amount)) {
			self :: $errCode = 0301;
			self :: $errMsg = 'empty amount';
			return 0;
		}
		$tName = 'wh_iostoredetail';
		$set = "SET amount='$amount' ";
		$where = "WHERE iostoreId='$iostoreId' AND sku='$sku' ";
		$affectRows = WhIoStoreModel :: updateTNameRow($tName, $set, $where);
		if ($affectRows === FALSE) { //执行错误或者无affectedRow
			self :: $errCode = 0201;
			self :: $errMsg = 'updateRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}

	//修改出入库单状态（根据iostore记录的id）
	function act_updateWhIoStoreStatus() {
		$id = isset ($_GET['id']) ? $_GET['id'] : ''; //id
		$ioStatus = $_GET['ioStatus']; //待修改数量的sku
		if (empty ($id)) {
			self :: $errCode = 0101;
			self :: $errMsg = 'empty id';
			return 0;
		}
		if (!isset ($_GET['ioStatus'])) {
			self :: $errCode = 0201;
			self :: $errMsg = 'unset ioStatus';
			return 0;
		}
		$ioStatus = intval($ioStatus);
		if ($ioStatus != 0 && $ioStatus != 1) {
			self :: $errCode = 0301;
			self :: $errMsg = 'illegal ioStatus';
			return 0;
		}
		$tName = 'wh_iostore';
		$set = "SET ioStatus='$ioStatus' ";
		$where = "WHERE id='$id' ";
		$affectRows = WhIoStoreModel :: updateTNameRow($tName, $set, $where);
		if ($affectRows === FALSE) { //执行错误或者无affectedRow
			self :: $errCode = 0401;
			self :: $errMsg = 'updateRow error';
			return 0;
		} else {
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return $affectRows;
		}
	}

}
?>
