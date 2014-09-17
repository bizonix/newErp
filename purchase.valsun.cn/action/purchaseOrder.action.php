<?php
include_once '/data/web/purchase.valsun.cn/lib/rabbitmq/config.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PurchaseOrderAct {
	static $errCode = 0;
	static $errMsg = "";

	public function publish_msg($data){
		$conn = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
		$ch = $conn->channel();
		$msg = new AMQPMessage(json_encode($data), array('content_type' => 'text/plain', 'delivery_mode' => 2));
		$rtn = $ch->basic_publish($msg, "inStock");
	}

	public function index(){
		global $dbConn,$dbconn;
		$status = $_GET['three_status'];
		$access_id = $_SESSION['access_id'];
		$keyWord = trim($_GET['keyWord']);
		$type = $_GET['type'];
		$page = isset($_GET['page']) ? $_GET['page'] : 0;
		if(isset($_GET['startTime'])){
			$starTime = strtotime($_GET['startTime']." 00:00:00");
		}
		if(isset($_GET['endTime'])){
			$endTime = strtotime($_GET['endTime']." 23:59:59");
		}
		$timeType = $_GET['status'];
		$condition = "";
		if(isset($keyWord) &&  $keyWord != ''){
			$where = " 1 ";
		}else{
			if(isset($_GET['startTime'])){
				$where = " 1 ";
			}else{
				$where = " a.status={$status} ";
			}
		}

		if($type == "sku" && isset($keyWord)){
			$idArr = $this->getIdfromSku($keyWord);
			if(count($idArr) > 0){
				$idStr = implode(",",$idArr);
				$condition .= " and  a.id in ({$idStr})";
			}
		}else if($type == "partner" && isset($keyWord)){
			$idArr = $this->getNewPartnerId($keyWord);
			if(count($idArr) > 0){
				$idStr = implode(",",$idArr);
				$condition .= " and  a.partner_id in ({$idStr})";
			}
		}else if(isset($keyWord) && $keyWord != ''){
			$condition .= " and  a.recordnumber='{$keyWord}'";
		}

		if(isset($_GET['search_pur']) ){
			$purid = trim($_GET['search_pur']);
			if($purid != ""){
				$condition .= " and (a.purchaseuser_id={$purid} or a.operator_id={$purid})";
			}else{
				$condition .= " and (a.purchaseuser_id={$_SESSION['sysUserId']} or a.operator_id={$_SESSION['sysUserId']})";
			}
		}else{
			$condition .= " and (a.purchaseuser_id={$_SESSION['sysUserId']} or a.operator_id={$_SESSION['sysUserId']})";
		}
		if(isset($_GET['paystatus'])){
			$condition .= " AND a.paystatus = '{$_GET['paystatus']}'";
		}
		if(isset($_GET['payresult'])){
			$condition .= " AND a.payresult = '{$_GET['payresult']}'";
		}
		if(isset($starTime) && isset($endTime)){
			$condition .= " and a.addtime > {$starTime} and a.addtime < {$endTime}";
		}else if(isset($starTime) && isset($endTime) && $timeType == "aduittime"){
			$condition .= " and a.aduittime > {$starTime} and a.aduittime < {$endTime}";
		}
		$condition .= " and a.is_delete=0 " ;
		if($page > 0){
			$page = ($page-1) * 100;
		}
		$limit = " limit {$page},100";
		$sqlStr = "select * from ph_order as a where {$where}  {$condition} order by a.id desc ";

		$sql = $dbConn->execute($sqlStr);
		$totalNum = $dbConn->num_rows($sql);
		$sql = $sqlStr."{$limit}";
		$sql = $dbConn->execute($sql);
		$orderInfo = $dbConn->getResultArray($sql);
		$data = array("totalNum"=>$totalNum,"orderInfo"=>$orderInfo);
		return $data;
	}

	public function getIdfromSku($sku){
		global $dbConn,$dbconn;
		$sql = "SELECT po_id FROM  `ph_order_detail` where sku='{$sku}'";
		$sql = $dbConn->execute($sql);
		$orderIds = $dbConn->getResultArray($sql);
		$idArr = array();
		foreach($orderIds as $item){
			$idArr[] = $item['po_id'];
		}
		return $idArr;
	}

	public function getNewPartnerId($name){
		global $dbConn;
		$sql = "SELECT id FROM `ph_partner` where company_name like '%{$name}%'";
		$sql = $dbConn->execute($sql);
		$orderIds = $dbConn->getResultArray($sql);
		$idArr = array();
		foreach($orderIds as $item){
			$idArr[] = $item['id'];
		}
		return $idArr;
	}


	//B仓订单list
	public function pickOrder(){
		global $dbConn,$dbconn;
		$status = $_GET['three_status'];
		$access_id = $_SESSION['access_id'];
		$keyWord = trim($_GET['keyWord']);
		$type = $_GET['type'];
		$page       = isset($_GET['page']) ? $_GET['page'] : 0;
		$starTime = $_GET['startTime'];
		$endTime = $_GET['endTime'];
		$timeType = $_GET['status'];
		$condition = "";
		if(isset($keyWord) &&  $keyWord != ''){
			$where = " 1 ";
		}else{
			$where = " a.status={$status} ";
		}

		if($type == "sku" && isset($keyWord)){
			$condition .= " and  b.sku='{$keyWord}'";
		}else if(isset($keyWord) && $keyWord != ''){
			$condition .= " and  a.recordnumber='{$keyWord}'";
		}

		if(isset($_GET['search_pur']) ){
			$purid = trim($_GET['search_pur']);
			if($purid != ""){
				$condition .= " and (a.purchaseuser_id={$purid} or a.operator_id={$purid})";
			}else{
				$condition .= " and (a.purchaseuser_id={$_SESSION['sysUserId']} or a.operator_id={$_SESSION['sysUserId']})";
			}
		}else{
			//$condition .= " and a.purchaseuser_id in ({$access_id})";
			//$condition .= " and a.purchaseuser_id={$_SESSION['sysUserId']}";
			$condition .= " and (a.purchaseuser_id={$_SESSION['sysUserId']} or a.operator_id={$_SESSION['sysUserId']})";
		}
		if(isset($_GET['paystatus'])){
			$condition .= " AND a.paystatus = '{$_GET['paystatus']}'";
		}
		if(isset($_GET['payresult'])){
			$condition .= " AND a.payresult = '{$_GET['payresult']}'";
		}
		if(isset($starTime) && isset($endTime)){
			$condition .= " and a.addtime > {$starTime} and a.addtime < {$endTime}";
		}else if(isset($starTime) && isset($endTime) && $timeType == "aduittime"){
			$condition .= " and a.aduittime > {$starTime} and a.aduittime < {$endTime}";
		}
		$condition .= " and a.is_delete=0 " ;
		if($page > 0){
			$page = ($page-1) * 100;
		}
		$limit = " limit {$page},100";
		$sqlStr = "select distinct a.id,a.* from ph_ow_order as a left join ph_ow_order_detail as b on a.recordnumber=b.recordnumber where {$where}  {$condition} order by a.id desc ";


		$sql = $dbConn->execute($sqlStr);
		$totalNum = $dbConn->num_rows($sql);
		$sql = $sqlStr."{$limit}";
		$sql = $dbConn->execute($sql);
		$orderInfo = $dbConn->getResultArray($sql);
		$data = array("totalNum"=>$totalNum,"orderInfo"=>$orderInfo);
		return $data;
	}


	/*
	 * @param 无 @return 错误号$errCode @return 错误码$errMsg
	 */
	public static function error() {
		self::$errCode = PurchaseOrderModel::$errCode;
		self::$errMsg = PurchaseOrderModel::$errMsg;
	}
	/**
	 *功能：改变付款状态
	 *@param array $_GET ['data']  id数组
	 *@param  int  $_GET["toPaystatus"] 付款状态
	 *@return null
	 * */
	public function changePaystatus(){
		if (empty ($_GET ['data'] ) || ! isset ( $_GET ['data'] )) {
			self::$errCode = "0126";
			self::$errMsg = "非法操作！";
			return;
		}
		$idArr = $_GET["data"]["idArr"];
		$toPaystatus = $_GET["toPaystatus"];
		$idStr = implode(",",$idArr);
		$table = C("DB_PREFIX")."order";
		$set   = "paystatus=".$toPaystatus;
		$where = " id IN (".$idStr.") ";
		$ret   = purchaseOrderModel::getUpdateExcute($table, $set, $where);
		if ($ret) {
			self::$errCode = "0334";
			self::$errMsg = "亲,移动到等待付款成功!";
		} else {
			self::$errCode = "0137";
			self::$errMsg = "移动到等待付款成功!失败";
		}
	}
	/**
	 *功能：修改订单详情 数量和到货数量
	 *@param array $_GET ['obj']   包含了修改内容的二维数组
	 *@return null
	 * */
	public function modAll(){
		global $dbConn;
		$dataArr 	= $_POST["obj"];
		$wrongIdArr = array();
		$flag = array();
		foreach($dataArr as $list){
			$id 	= $list["id"];
			$sql = "update ph_order_detail set count='{$list['count']}',price='{$list['price']}' where id={$id}";
			if($dbConn->execute($sql)){
				$log = json_encode($list);
				$log .= "操作人:{$_SESSION['userCnName']}";
				write_log("edite_order.txt",$log);
			}
			PurToWhModel::updReceiptGoodsInfo($id);//更新收货管理表订单价格和数量
		}
		$rtn['code'] = 1;
		$rtn['msg']  = "订单修改完毕。。。";
		return json_encode($rtn);
	}


	//修改深圳B仓备货订单详情
	public function modOwAll(){
		global $dbConn;
		$dataArr 	= $_POST["obj"];
		$wrongIdArr = array();
		$rtnArr = array();
		foreach($dataArr as $list){
			$id 	= $list["id"];
			$sql = "update ph_ow_order_detail set count='{$list['count']}',price='{$list['price']}' where id={$id}";
			if($dbConn->execute($sql)){
				$rtnArr[] = 1;
			}else{
				$rtnArr[] = 0;
			}
		}
		if(in_array(0,$rtnArr)){
			$rtn['code'] = 0;
			$rtn['msg']  = "订单修改fail";
		}else{
			$rtn['code'] = 1;
			$rtn['msg']  = "订单修改成功";
		}
		return json_encode($rtn);
	}

	function edit_push_order(){
		global $dbConn;
		$data = $_POST['skuObj'];
		$ordersn = $data['ordersn'];
		$sku = $data['sku'];
		$amount = $data['amount'];
		$operator = $_SESSION['sysUserId']; 
		$sql = "update ph_ow_order_detail set count='{$amount}' where recordnumber='{$ordersn}' and sku='{$sku}'";
		if($dbConn->execute($sql)){
			/***添加结束B仓备货单功能 add by wangminwei 2014-07-28***/
			$mark       = true;
			$sqlstr 	= "SELECT count, stockqty, sendqty FROM ph_ow_order_detail WHERE recordnumber = '{$ordersn}'";
			$query 		= $dbConn->execute($sqlstr);
			$detailData = $dbConn->getResultArray($query);
			if(!empty($detailData)){
				foreach($detailData AS $kk => $vv){
					$count 		= $vv['count'];
					$stockqty 	= $vv['stockqty'];
					$sendqty  	= $vv['sendqty'];
					if($count != $stockqty || $stockqty != $sendqty){
						$mark = false;
						break;
					}else{
						continue;
					}
				}
			}
			if($mark){
				$upd 	= "UPDATE ph_ow_order SET status = 5 WHERE recordnumber = '{$ordersn}'";
				$dbConn->execute($upd);
			}
			$curl = new CURL();
			$url = "http://wh.valsun.cn/openapi.php?jsonp=1&mod=syncPreGoodsOrder&act=modifyPreGoodsSku&sku={$sku}&amount={$amount}&ordersn={$ordersn}&operator={$operator}";
			$rtn = $curl->get($url,false);
		}
		return $rtn;
	}


	/**
	 *功能：删除订单
	 *@param array $_GET ['data']  订单id数组
	 *@return null
	 * */

	public function  delAll(){
		global $dbConn;
		$idArr = $_POST["idArr"];
		$idStr = implode(",",$idArr);
		//$sql = "DELETE FROM `ph_order` WHERE id in ({$idStr})";
		$sql = "update `ph_order` set is_delete=1 WHERE id in ({$idStr})";
		if($dbConn->execute($sql)){
			//$sql = "DELETE FROM `ph_order_detail` WHERE  po_id in ({$idStr})";
			$sql = "update `ph_order_detail` set is_delete=1 WHERE  po_id in ({$idStr})";
			if($dbConn->execute($sql)){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}


	/**
	 *功能：移动订单
	 *@param array $_GET ['data']  订单id数组
	 *@return null
	 * */
	public function moveOrder(){
		if (empty ($_GET ['data'] ) || ! isset ( $_GET ['data'] )) {
			self::$errCode = "0126";
			self::$errMsg = "非法操作！";
			return;
		}
		$idArr 		= $_GET["data"]["idArr"];
		$idArr 		= implode(",",$idArr);
		$toStatus 	= $_GET["toStatus"];
		$table 		= C("DB_PREFIX")."order";
		$set 		= " status=".$toStatus;
		if($toStatus == 2){//移至审核状态 add by wangminwei 2013-11-13
			$auditid = $_SESSION[C('USER_AUTH_SYS_ID')];//审核人
			$time    = time();
			$set    .= " , aduituser_id = '{$auditid}'";
			$set    .= " , aduittime = '{$time}'";
		}
		$where 	= "id IN (".$idArr.") ";
		$ret 	= PurchaseOrderModel::getUpdateExcute($table, $set, $where);
		if($ret){
			self::$errCode 	= "0202";
			self::$errMsg 	= "订单移动成功";
			/***添加采购订单信息到收货管理表 add by wangminwei 2014-09-09 Start****/
			$orderArr = PurToWhModel::getOrderSn($idArr);
			if(!empty($orderArr)){
				foreach($orderArr AS $k => $v){
					$orderSn 	= $v['recordnumber'];
					$rtnRes 	= PurToWhModel::autoAdd($orderSn);
				}
			}
			/***添加采购订单信息到收货管理表 add by wangminwei 2014-09-09 End****/
		}else {
			self::$errCode 	= "0137";
			self::$errMsg 	= "订单移动失败";
		}
	}


	//移动订单到在途
	public function moveOnWay(){
		global $dbConn;
		$rollback   = false;
		BaseModel::begin();//开始事务
		$orderObjArr = $_POST['orderObjArr'];
		$orderIdArr = array();
		$unOrderIdArr = array();
		foreach($orderObjArr as $item){
			if($item['order_type'] == 4){ // 采购补单的需要特殊处理
				$unOrderIdArr[] = $item['id'];
			}
			$orderIdArr[] = $item['id'];
		}
		$orderIdStr = implode(",",$orderIdArr);
		$sql = "update ph_order set status=3 where id in ({$orderIdStr})";
		if($dbConn->execute($sql)){
			if(count($unOrderIdArr) > 0){
				$unOrderIdStr = implode(",",$unOrderIdArr);
				$sql = "select unOrderId from ph_order_detail where po_id in ({$unOrderIdStr})";
				$sql = $dbConn->execute($sql);
				$unOrderInfo = $dbConn->getResultArray($sql);
				$unOrderIdArr = array(); // 置为空
				foreach($unOrderInfo as $item){
					$unOrderIdArr[] = $item['unOrderId'];
				}
				$pushObj = new CommonAct();
				$pushObj->setTallyIsUse($unOrderIdArr);
			}
			$orderArr = PurToWhModel::getOrderSn($orderIdStr);
			if(!empty($orderArr)){
				foreach($orderArr AS $k => $v){
					$orderSn 	= $v['recordnumber'];
					$rtnRes 	= PurToWhModel::autoAdd($orderSn);//添加采购订单信息到收货管理表 add by wangminwei 2014-05-21
				}
			}
			$rtn['errorCode'] = 0;
			$rtn['msg'] = '订单移动成功';
		}else{
			$rtn['errorCode'] = 500;
			$rtn['msg'] = '订单移动出现未知错误';
		}
		return json_encode($rtn);
	}



	public function moveOwOrder(){
		if (empty ($_GET ['data'] ) || ! isset ( $_GET ['data'] )) {
			self::$errCode = "0126";
			self::$errMsg = "非法操作！";
			return;
		}
		$idArr 		= $_GET["data"]["idArr"];
		$idArr 		= implode(",",$idArr);
		$toStatus 	= $_GET["toStatus"];
		$table 		= "ph_ow_order";
		$set 		= " status=".$toStatus;
		if($toStatus == 2){//移至审核状态 add by wangminwei 2013-11-13
			$auditid = $_SESSION[C('USER_AUTH_SYS_ID')];//审核人
			$time    = time();
			$set    .= " , aduituser_id = '{$auditid}'";
			$set    .= " , aduittime = '{$time}'";
		}
		$where 	= "id IN (".$idArr.") ";
		$ret 	= PurchaseOrderModel::getUpdateExcute($table, $set, $where);
		if($ret){
			self::$errCode 	= "0202";
			self::$errMsg 	= "订单移动成功";
		}else {
			self::$errCode 	= "0137";
			self::$errMsg 	= "订单移动失败";
		}
	}

	/**
	 *功能：删除单个订单
	 *@param string $_GET ['id']  单个订单id
	 *@return null
	 * */
	public function delOrder(){
		if (empty ( $_GET ['id'] ) || ! isset ( $_GET ['id'] )) {
			self::$errCode = "0126";
			self::$errMsg = "非法操作！";
			return;
		}
		$id = $_GET['id'];
		$table = C("DB_PREFIX")."order";
		$set = " is_delete=1";
		$where = "id=".$id;
		$ret = PurchaseOrderModel::getUpdateExcute($table, $set, $where);
		if ($ret) {
				self::$errCode = "0184";
				self::$errMsg = "订单删除成功";
			} else {
				self::$errCode = "0148";
				self::$errMsg = "订单删除失败";
			}
	}

	public function delOrder_ow(){
		if (empty ( $_GET ['id'] ) || ! isset ( $_GET ['id'] )) {
			self::$errCode = "0126";
			self::$errMsg = "非法操作！";
			return;
		}
		$id = $_GET['id'];
		$table = C("DB_PREFIX")."ow_order";
		$set = " is_delete=1";
		$where = "id=".$id;
		$ret = PurchaseOrderModel::getUpdateExcute($table, $set, $where);
		if ($ret) {
				self::$errCode = "0184";
				self::$errMsg = "订单删除成功";
			} else {
				self::$errCode = "0148";
				self::$errMsg = "订单删除失败";
			}
	}
	/**
	 *功能：审核订单
	 *@param string $_GET ['id']  单个订单id
	 *@return null
	 * */
	public function audit() {
		if (empty ( $_GET ['id'] ) || ! isset ( $_GET ['id'] )) {
			self::$errCode = "0126";
			self::$errMsg = "非法操作！";
			return;
		}
		$id 		= $_GET["id"];
		$status 	= $_GET["stauts"];
		$table 		= C("DB_PREFIX")."order";
		$set 		= " status = ". $status ;
		$where 		= "id=".$id;
		if($status == 2){
			$auditid = $_SESSION[C('USER_AUTH_SYS_ID')];//审核人
			$time    = time();
			$set    .= " , aduituser_id = '{$auditid}'";
			$set    .= " , aduittime = '{$time}'";
		}
		$ret 	    = PurchaseOrderModel::getUpdateExcute($table, $set, $where);
		if ($ret) {
			self::$errCode = "0135";
			self::$errMsg = "订单审核成功";
			/***添加采购订单信息到收货管理表 add by wangminwei 2014-09-09 Start****/
			$orderArr = PurToWhModel::getOrderSn($id);
			if(!empty($orderArr)){
				foreach($orderArr AS $k => $v){
					$orderSn 	= $v['recordnumber'];
					$rtnRes 	= PurToWhModel::autoAdd($orderSn);
				}
			}
			/***添加采购订单信息到收货管理表 add by wangminwei 2014-09-09 End****/
		} else {
			self::$errCode = "0138";
			self::$errMsg = "订单审核失败";
		}
	}
	/**
	 *功能：判断某个SKU是否存在sku_info_tmp中
	 *@param $_GET ['sku']   sku号
	 *@return null
	 * */
	public function isExistSku() {
		if (empty ( $_GET ['sku'] ) || ! isset ( $_GET ['sku'] )) {
			self::$errCode = "0126";
			self::$errMsg = "非法操作！";
			return;
		}
		$fields = "id" ;
		$table = C("DB_PREFIX")."goods";
		$where = "sku='" . $_GET ["sku"] . "'";
		$ret = PurchaseOrderModel::getResult($fields, $table,$where) ;
		if ($ret) {
			self::$errCode = "0133";
			self::$errMsg = "存在sku";
		} else {
			self::$errCode = "0136";
			self::$errMsg = "不存在sku";
		}
	}
	/**
	 *功能：列表出sku相关信息
	 *@param $sku  sku号
	 *@return null
	 * */
	public function purchasehistoryprice($sku) {
		return PurchaseOrderModel::purchasehistoryprice ( $sku );
	}
	/**
	 *功能：修改订单详情
	 *@param array $_GET ['data'] 修改的字段
	 *@param array  $_GET ['id'] 订单详情id
	 *@return null
	 * */
	public function modOrderDetail() {
		if (empty ( $_GET ['id'] ) || ! isset ( $_GET ['id'] )) {
			self::$errCode = "0126";
			self::$errMsg = "非法操作！";
			return;
		}
		$data = $_GET ["data"];
		$id = $_GET ["id"];
		$where = "  id=" . $id;
		$set = arrToLinkStr ( $data, "," );
		$table = C("DB_PREFIX")."order_detail";
		$ret = PurchaseOrderModel::getUpdateExcute($table, $set, $where);
		if ($ret) {
			self::$errCode = "0138";
			self::$errMsg = "修改成功";
		} else {
			self::$errCode = "0141";
			self::$errMsg = "修改失败";
		}
	}
	/**
	 *功能：更新订单
	 *@param string  $_GET ['dataKey']  修改的字段名窜
	 *@param string $_GET ["dataVal"] 修改的字段值窜
	 *@return null
	 * */
	public function save_all() {
		global $dbConn;
		$data = $_POST['data'];
		$dataSet = array2sql($data);
		$sql = "update ph_order set {$dataSet} where recordnumber='{$data['recordnumber']}'";
		if($dbConn->execute($sql)){
			PurToWhModel::updReceiptSupplier($data['recordnumber'], $data['partner_id']);//更新映射供应商到收货管理表
			return 1;
		}else{
			return 0;
		}
	}
	/**
	 *功能：物理删除订单详情
	 *@param string $_GET ['id']  订单详情id
	 *@return null
	 * */
	public function delPhOrderDetail() {
		global $dbConn;
		$idArr = $_POST["idArr"];
		$status = $_POST["status"];
		$idStr = implode(",",$idArr);
		if($status == 1 || $status == 2){
			$sql = "DELETE FROM `ph_order_detail` WHERE id in ({$idStr})";
		}else{
			$sql = "update  `ph_order_detail` set is_delete=1 WHERE id in ({$idStr})";
		}
		$rtn = array();
		if($dbConn->execute($sql)){
			$rtn["errorCode"] = 0;
			$rtn["msg"] = "delete success";
		}else{
			$rtn["errorCode"] = 1;
			$rtn["msg"] = "delete failure....";
		}
		return json_encode($rtn);
	}
	
	/**
	 * 物理删除海外备货单明细
	 * Enter description here ...
	 */
	public function delPhOwOrderDetail() {
		global $dbConn;
		$idArr = $_POST["idArr"];
		$idStr = implode(",",$idArr);
		$sql = "DELETE FROM  `ph_ow_order_detail` WHERE id in ({$idStr})";
		$rtn = array();
		if($dbConn->execute($sql)){
			$rtn["errorCode"] = 0;
			$rtn["msg"] = "delete success";
		}else{
			$rtn["errorCode"] = 1;
			$rtn["msg"] = "delete failure....";
		}
		return json_encode($rtn);
	}
	/**
	 *功能：获取某个订单
	 *@param string $['id']  订单详情id
	 *@return array
	 * */
	public function phOrder($id) {
		return PurchaseOrderModel::phOrder ( $id );
	}

	/**
	 *功能：获取某状态下订单数量
	 *@param string $stat  订单状态
	 *@return int 订单数量
	 * */
	public function calcNumber($status){
		global $dbConn;
		$access_id = $_SESSION['access_id'];
		if(isset($_GET['search_pur']) && $_GET['search_pur'] != ""){
			$condition =  " (purchaseuser_id={$_GET['search_pur']} or operator_id={$_GET['search_pur']})";
		}else{
			$condition = " (purchaseuser_id in('$access_id') or operator_id in ('$access_id'))";
		}
		$sql = "select count(*) as totalNum from ph_order where status={$status} and {$condition} and is_delete=0";
		if($_GET['debug'] == 1){
			echo $sql;
		}
		$sql = $dbConn->execute($sql);
		$number = $dbConn->fetch_one($sql);
		return $number['totalNum'];
	}


	public function calcOwNumber($status){
		global $dbConn;
		$access_id = $_SESSION['access_id'];
		if(isset($_GET['search_pur']) && $_GET['search_pur'] != ""){
			$condition =  " (purchaseuser_id={$_GET['search_pur']} or operator_id={$_GET['search_pur']})";
		}else{
			$condition = " (purchaseuser_id in('$access_id') or operator_id in ('$access_id'))";
		}
		$sql = "select count(*) as totalNum from ph_ow_order where status={$status} and {$condition} and is_delete=0";
		$sql = $dbConn->execute($sql);
		$number = $dbConn->fetch_one($sql);
		return $number['totalNum'];
	}


	/**
	 *功能：获取用户表信息
	 *@return $ret array 用户数据
	 * */
	public function getPurchaseUser() {
		$fields = " global_user_id as id,global_user_name as username ";
		$table = "power_global_user";
		$where = "WHERE global_user_is_delete =0 AND global_user_status = 1 AND global_user_dept = 6  ORDER BY username ASC ";
		$ret = OmAvailableAct::getTNameList($table, $fields, $where);
		if (isset ( $ret [0] ['id'] )) {
			return $ret;
		}
		self::$errCode = '0130';
		self::$errMsg = "获取采购列表失败";
	}
	/**
	 *功能：获取供应商名
	 *@return $ret array 供应商名和id数组
	 * */
	public function partnerList() {
		$fields = "id,company_name";
		$talbe = C("DB_PREFIX")."partner";
		$where = "is_delete=0";
		$ret = PurchaseOrderModel::getResult($fields, $talbe,$where) ;
		if ($ret) {
			return $ret;
		}
		self::$errCode = "0146";
		self::$errMsg  = "获取供应商资料失败";
	}
	/**
	 *功能：获取仓库名和id
	 *@return $ret array 仓库名和id数组
	 * */
	public function storeList() {
		$fields = "id,whName";
		$talbe = C("DB_PREFIX")."store";
		$where = " status = 1 ";
		$ret = PurchaseOrderModel::getResult($fields, $talbe,$where) ;
		if ($ret) {
			return $ret;
		}
		self::$errCode = "0154";
		self::$errMsg = "获取仓库资料失败";
	}
	/**
	 *功能：获取订单列表
	 *@return  array 包含订单数据 分页导航 数据总数的数组
	 * */
	public function getOrderList($where = '') {
		$total = PurchaseOrderModel::getCountOrderList ( $where );
		$perNum = 50;
		$page = new page ( $total, $perNum, $pa = "", $lang = "CN" );
		$listPage = PurchaseOrderModel::getOrderList ( $where, $page->limit );
		if ($total > $perNum) {
			$fpage = $page->fpage ( array (
					0,
					1,
					2,
					3,
					4,
					5,
					6,
					7,
					8,
					9
			) );
		} else {
			$fpage = $page->fpage ( array (
					0,
					1,
					2,
					3
			) );
		}
		return array (
				$listPage,
				$fpage,
				$total
		);
	}
	/**
	 *功能：获取某条件下的订单详情
	 *@return  array
	 * */
	public function getOrderDetaiList($where = '') {
		$lists = PurchaseOrderModel::getOrderDetaiList ( $where );
		if ($lists) {
			return $lists;
		}
		self::error ();
		return false;
	}

	public function getPurchaseidBySku($sku){
		global $dbConn;
		$sql = "SELECT purchaseId from pc_goods where sku='{$sku}' and is_delete=0";
		$sql = $dbConn->execute($sql);
		$id = $dbConn->fetch_one($sql);
		return $id['purchaseId'];
	}

	//合并B仓订单
	public function combine_order(){
		global $dbConn;
		$idArr = $_POST["idArr"];
		$idStr = implode(",",$idArr);
		$addtime = time();
		$operater = $_SESSION["userCnName"];
		$sql = "SELECT combine_sn from ph_ow_order_combine where status =0 order by addtime desc ";
		$sql = $dbConn->execute($sql);
		$num = $dbConn->fetch_one($sql);
		$rtn = array();
		if(isset($num['combine_sn'])){
			$sql = "update ph_ow_order set combine_num='{$num['combine_sn']}' where id in ({$idStr})";
			if($dbConn->execute($sql)){
				$rtn['code'] = 1;
				$rtn['msg'] = "combine success";
			}
		}else{
			$recordnumber = $this->getBorderOrderSN(); 
			$sql = "INSERT INTO `ph_ow_order_combine`( `combine_sn`, `addtime`, `operator`) VALUES ('{$recordnumber}','$addtime','{$operater}')";
			if($dbConn->execute($sql)){
				$bsql = "update ph_ow_order set combine_num='{$recordnumber}' where id in ({$idStr})";
				$rtn['code'] = 1;
				$rtn['msg'] = "combine success";
			}
		}
		return json_encode($rtn);
	}

	public function getBorderOrderSN(){
		global $dbConn;
		$operator_id = $_SESSION[C('USER_AUTH_SYS_ID')];//操作人员ID
		while(1){
			$recordnumber = "OWC".date("ymd").$operator_id.rand(100, 999);
			$sql = "SELECT count(*) as number FROM  `ph_ow_order_combine` where combine_sn='{$recordnumber}'";
			$sql = $dbConn->execute($sql);
			$num = $dbConn->fetch_one($sql);
			if($num['number'] == 0){
				break;
			}
		}
		return $recordnumber;
	}

	//推送订单
	public function push_bstock_order(){
		global $dbConn;
		//$id = $_POST['combine_id'];
		$id = $_POST['idArr'];
		//$idArr = $_POST['idArr'];
		//$idStr = implode(",",$idArr);
		$sql = " SELECT * FROM  `ph_ow_order` where id={$id} and status=3";
		$sql= $dbConn->execute($sql);
		$item = $dbConn->fetch_one($sql);
		$url = "http://wh.valsun.cn/openapi.php?jsonp=1&mod=syncPreGoodsOrder&act=syncPreGoodsOrderInfo";
		$curl = new CURL();
		$ordersn = $item['recordnumber'];
		$owner = $item['purchaseuser_id'];
		$createtime = $item['addtime'];				
		$sql = "SELECT * FROM  `ph_ow_order_detail` where recordnumber='{$ordersn}'";
		$sql= $dbConn->execute($sql);
		$orderdetail = $dbConn->getResultArray($sql);
		$data = array();
		foreach($orderdetail as $itemdetail){
			$data[$itemdetail['sku']] = $itemdetail['count'];
		}
		$postData = array(
			"orderSn" => $ordersn,
			"owner" => $owner,
			"createtime" => $createtime,
			"data" => json_encode($data)
		);
		$rtn = $curl->post($url,$postData,false);
		$rtn = json_decode($rtn,true);
		if($rtn['data']['code'] == "success"){
			$sql = "update ph_ow_order set status=4 where recordnumber='{$ordersn}'";
		    $dbConn->execute($sql);
		}

		
		return json_encode($rtn);
	}


	public function createOrder() {
		global $dbConn;
		$skulist 	= $_POST['skulist'];
		$operator_id = $_SESSION[C('USER_AUTH_SYS_ID')];//操作人员ID
		$comid      = $_SESSION[C('USER_COM_ID')];//公司ID
		$type = $_POST['type'];

		BaseModel::begin();//开始事务
		$skuComObj = new CommonAct(); //重新计算这个sku 的已订购数量
		$rollback   = false;
		foreach($skulist as $key=>$sku){
			$price      = PurchaseOrderModel::getPriceBySku($sku['sku']);//SKU单价
			//$parid      = CommonAct::actgetPartnerIdBySku($sku['sku']);//供应商ID
			$purid = $this->getPurchaseidBySku($sku['sku']);
			$parid = $this->getPartnerId($sku['sku']);//供应商ID
			$parid = $parid['partnerId'];
			$storeid = 1;//仓库ID
			//$orderSN    = PurchaseOrderModel::isExistOrdersn($storeid, $parid, $purid);//判断同供应商、采购员跟踪号是否已存在
			//
			if($type == "oversea"){
				$orderData  = $this->getOrderSN( $parid, $purid,5);//判断同供应商、采购员跟踪号是否已存在
			}else{
				$orderData  = $this->getOrderSN( $parid, $purid);//判断同供应商、采购员跟踪号是否已存在
			}
			$orderSN = $orderData['recordnumber'];
			if($key == 0 && $type == "oversea"){
				$orderSN = null;
			}
			$main    = array();
			$detail  = array();
			$skuOrderNum = $this->check_order($sku['sku']);
			if($skuOrderNum >= 1){
				continue;
			}
			if(!empty($orderSN)){//存在符合条件的跟踪号，直接插入采购订单明细
				//$detail['sku_id'] = $skuid;//SKU编号
				$detail['sku']    = $sku['sku'];
				$detail['price']  = $price;//单价
				$detail['count']  = $sku['rec'];//采购数量
				$detail['is_new']  = $sku['is_new'];// 是否是新品
				$detail['goods_recommend_count']  = $sku['rec'];//采购数量
				$detail['recordnumber'] = $orderData['recordnumber'];
				$poid  = $orderData['id'] ;//根据跟踪号取采购主订单编号
				$detail['po_id'] = $poid;
				//$rtndetail        = PurchaseOrderModel::insertDetailOrder($poid, $detail); // 添加采购订单明细
				$dataSet = array2sql($detail);
				$sql = "insert into ph_order_detail set {$dataSet}  ";
				$rtndetail = $dbConn->execute($sql);
				if($rtndetail === false){
					$rollback = true;
				}
			}else{//不存在符合条件的跟踪号重新生成
				//生成跟踪号需通过公司编号生成前缀
				$recordnumber = PurchaseOrderModel::autoCreateOrderSn($purid, $comid);//生成对应公司的采购订单跟踪号
				if(!empty ($recordnumber)) {//生成采购订单号成功
					$main['recordnumber'] 		= $recordnumber;//跟踪号
					$main['purchaseuser_id'] 	= $purid;//采购员ID
					$main['operator_id'] 		= $operator_id;//操作人员id
					$main['warehouse_id'] 		= $storeid;//仓库ID
					$main['partner_id'] 		= $parid;//供应商ID
					$main['company_id'] 		= $comid;//公司编号
					$main['addtime'] = time();
					if($type == "oversea"){
						$main['order_type'] = 5; // 给海外仓备货的订单
					}else{
						$main['order_type'] = 1; // 正常订单
					}

					$dataSet = array2sql($main);
					$sql = "insert into ph_order set {$dataSet}  ";
					$rtnmain = $dbConn->execute($sql);
					//$rtnmain = PurchaseOrderModel::insertMainOrder($main);//添加采购订单主体信息
					if($rtnmain) {//主订单添加成功
						$detail['sku']    = $sku['sku'];
						$detail['price']  = $price;//单价
						$detail['count']  = $sku['rec'];//采购数量
						$detail['goods_recommend_count']  = $sku['rec'];//采购数量
						$detail['is_new']  = $sku['is_new'];// 是否是新品
						$detail['recordnumber'] = $recordnumber;
						$poid  = PurchaseOrderModel::getOrderIdByNum($recordnumber);//根据跟踪号取采购主订单编号
						$detail['po_id'] = $poid;
						$dataSet = array2sql($detail);
						$sql = "insert into ph_order_detail set {$dataSet}  ";
						$dbConn->execute($sql);
						//$rtndetail        = PurchaseOrderModel::insertDetailOrder($poid, $detail);
						$skuComObj->calcAlert($detail['sku']); //重新计算已订购数量
						if($rtndetail === false) {
							$rollback = true;
						}
					}else{
						$rollback = true;
					}
				}else{
					$rollback = true;
				}
			}
		}
		if($rollback == false){
			BaseModel::commit();
            BaseModel::autoCommit();
            $result['msg'] = 'success';
		}else{
			BaseModel::rollback();
			BaseModel::autoCommit();
			$result['msg'] = '';
		}
		return $result;
	}

	public function check_order($sku){
		global $dbConn;
		$sql = " SELECT count(*) as num FROM `ph_order` as a left join ph_order_detail as b on a.id=b.po_id where a.status in(1,2) and a.is_delete=0 and b.sku='{$sku}' and b.is_delete=0";
		$sql = $dbConn->execute($sql);
		$number = $dbConn->fetch_one($sql);
		return $number['num'];
	}


	public function pickupOrder() {
		global $dbConn;
		$skulist = $_POST['skulist'];
		$operator_id = $_SESSION[C('USER_AUTH_SYS_ID')];//操作人员ID
		$comid      = $_SESSION[C('USER_COM_ID')];//公司ID
		$type = $_POST['type'];
		BaseModel::begin();//开始事务
		$rollback   = false;
		foreach($skulist as $key=>$sku){
			$price      = PurchaseOrderModel::getPriceBySku($sku['sku']);//SKU单价
			//$parid      = CommonAct::actgetPartnerIdBySku($sku['sku']);//供应商ID
			$purid = $this->getPurchaseidBySku($sku['sku']);
			$parid = $this->getPartnerId($sku['sku']);//供应商ID
			$parid = $parid['partnerId'];
			$storeid = 1;//仓库ID
			$orderData  = $this->getOwOrderSN( $parid, $purid);//判断同供应商、采购员跟踪号是否已存在
			$orderSN = $orderData['recordnumber'];
			if($key == 0 && $type == "oversea"){
				$orderSN = null;
			}
			$main    = array();
			$detail  = array();
			if(!empty($orderSN)){//存在符合条件的跟踪号，直接插入采购订单明细
				//$detail['sku_id'] = $skuid;//SKU编号
				$detail['sku']    = $sku['sku'];
				$detail['price']  = $price;//单价
				$detail['count']  = $sku['rec'];//采购数量
				//$detail['is_new']  = $sku['is_new'];// 是否是新品
				$detail['goods_recommend_count']  = $sku['rec'];//采购数量
				$detail['recordnumber'] = $orderData['recordnumber'];
				$poid  = $orderData['id'] ;//根据跟踪号取采购主订单编号
				$detail['po_id'] = $poid;
				$dataSet = array2sql($detail);
				$sql = "insert into ph_ow_order_detail set {$dataSet}  ";
				$rtndetail = $dbConn->execute($sql);
				if($rtndetail === false){
					$rollback = true;
				}
			}else{//不存在符合条件的跟踪号重新生成
				//生成跟踪号需通过公司编号生成前缀
				$recordnumber = PurchaseOrderModel::autoCreateOrderSn($purid, $comid);//生成对应公司的采购订单跟踪号
				if(!empty ($recordnumber)) {//生成采购订单号成功
					$main['recordnumber'] 		= $recordnumber;//跟踪号
					$main['purchaseuser_id'] 	= $purid;//采购员ID
					$main['operator_id'] 		= $operator_id;//操作人员id
					$main['warehouse_id'] 		= $storeid;//仓库ID
					$main['partner_id'] 		= $parid;//供应商ID
					$main['company_id'] 		= $comid;//公司编号
					$main['addtime'] = time();
					if($type == "oversea"){
						$main['order_type'] = 5; // 给海外仓备货的订单
					}else{
						$main['order_type'] = 1; // 正常订单
					}

					$dataSet = array2sql($main);
					$sql = "insert into ph_ow_order set {$dataSet}  ";
					$rtnmain = $dbConn->execute($sql);
					if($rtnmain) {//主订单添加成功
						$detail['sku']    = $sku['sku'];
						$detail['price']  = $price;//单价
						$detail['count']  = $sku['rec'];//采购数量
						$detail['goods_recommend_count']  = $sku['rec'];//采购数量
						//$detail['is_new']  = $sku['is_new'];// 是否是新品
						$detail['recordnumber'] = $recordnumber;
						$poid  = PurchaseOrderModel::getOrderIdByNum($recordnumber);//根据跟踪号取采购主订单编号
						$detail['po_id'] = $poid;
						$dataSet = array2sql($detail);
						$sql = "insert into ph_ow_order_detail set {$dataSet}  ";
						$dbConn->execute($sql);
						if($rtndetail === false) {
							$rollback = true;
						}
					}else{
						$rollback = true;
					}
				}else{
					$rollback = true;
				}
			}
		}
		if($rollback == false){
			BaseModel::commit();
            BaseModel::autoCommit();
            $result['msg'] = 'success';
		}else{
			BaseModel::rollback();
			BaseModel::autoCommit();
			$result['msg'] = '';
		}
		return json_encode($result);
	}




	public function getPartnerId($sku){
		global $dbConn;
		//$sql = "SELECT * from ph_user_partner_relation where sku='{$sku}' ";
		$sql = "SELECT a.partnerId ,b.company_name as companyname from ph_user_partner_relation as a left join ph_partner as b on a.partnerId=b.id where a.sku='{$sku}' ";
		$sql = $dbConn->execute($sql); 
		$info = $dbConn->fetch_one($sql);
		return $info;
	}

	public function getOrderSN($partnerId,$purchaseuser_id,$order_type=1){
		global $dbConn;
		$partnerArr = $this->getSamePartnerIds($partnerId);
		$partnerIds = implode(",",$partnerArr);
		$sql  = "SELECT id,recordnumber FROM ".C('DB_PREFIX')."order WHERE status = 1 and order_type='{$order_type}'";
		$sql .= " AND purchaseuser_id = '{$purchaseuser_id}' AND partner_id in ( {$partnerIds} )  AND is_delete = 0 order by id desc";
		$sql = $dbConn->execute($sql);
		$order = $dbConn->fetch_one($sql);
		return $order;
	}



	public function getOwOrderSN($partnerId,$purchaseuser_id){
		global $dbConn;
		$partnerArr = $this->getSamePartnerIds($partnerId);
		$partnerIds = implode(",",$partnerArr);
		$sql  = "SELECT id,recordnumber FROM ph_ow_order WHERE status = 1 ";
		$sql .= " AND purchaseuser_id = '{$purchaseuser_id}' AND is_delete = 0 order by id desc";
		//$sql .= " AND purchaseuser_id = '{$purchaseuser_id}' AND partner_id in ( {$partnerIds} )  AND is_delete = 0 order by id desc";
		$sql = $dbConn->execute($sql);
		$order = $dbConn->fetch_one($sql);
		return $order;
	}



	public function getSamePartnerIds($id){
		global $dbConn;
		$sql = "SELECT company_name from ph_partner where id='{$id}'";
		$sql = $dbConn->execute($sql);
		$nameInfo = $dbConn->fetch_one($sql);
		$sql = "select id from ph_partner where company_name='{$nameInfo['company_name']}'";
		$sql = $dbConn->execute($sql);
		$idArr = $dbConn->getResultArray($sql);
		$idArr2 = array();
		foreach($idArr as $item){
			$idArr2[] = $item['id'];
		}
		return $idArr2;
	}



	public function system_adjust_transport($where = '') {
			$total = PurchaseOrderModel::getCountAdjustTransport( $where );
			$perNum = 50;
			$page = new page ( $total, $perNum, $pa = "", $lang = "CN" );
			$listPage = PurchaseOrderModel::getAdjustTransport( $where, $page->limit );
			if ($total > $perNum) {
				$fpage = $page->fpage ( array (
						0,
						1,
						2,
						3,
						4,
						5,
						6,
						7,
						8,
						9
				) );
			} else {
				$fpage = $page->fpage ( array (
						0,
						1,
						2,
						3
				) );
			}
			return array (
					$listPage,
					$fpage,
					$total
			);
		}
	/**
	 *功能：获取某条特殊运输纪录
	 *@param array  $_POST   包含修改内容 和 id
	 *@return  array $content  二维数组
	 * */
	public function adjust_transport(){
		$_POST = $_REQUEST;//for test
		if(empty($_POST["type"]) || empty($_POST["id"])){
			self::$errCode = "0";
			self::$errMsg = "传参有误";
			return;
		}
		$type = $_POST["type"];
		$id = $_POST["id"];
		if($type == "getContent"){
			$content = PurchaseOrderModel::adjustTransportContent($id);
			if($content){
				self::$errCode = "1";
				self::$errMsg = "获取内容成功";
				return $content;
			}
			self::$errCode = "0";
			self::$errMsg = "获取内容失败";
		}
	}

	/**
	 *功能：修改特殊运输纪录
	 *@param array  $_GET['data']  包含修改内容 和 id
	 *@return  array null
	 * */
	public function adjust_transport_save(){
		if(empty($_GET['data'])){
			self::$errCode = "002";
			self::$errMsg = "非法传参";
			return;
		}
		$data = $_GET['data'];
		$id = $data['id'];
		unset($data['id']);
		$time = time();
		$modifytime = array("modifytime"=>$time);
		$data = array_merge($data,$modifytime);
		$set =  arrToLinkStr($data,",");
		$table = C("DB_PREFIX").'adjust_transport';
		$where = "is_delete = '0' AND id = '{$id}' ";
		$ret = PurchaseOrderModel::updateOneTable($table, $set,$where);
		if($ret){
			self::$errCode = "001";
			self::$errMsg = "恭喜！修改成功";
			return;
		}
		self::$errCode = "002";
		self::$errMsg = "sorry！修改失败";
		return;
	}

	/**
	 *功能：从订单系统获取超大订单
	 *@param array  $_GET 搜索条件
	 *@return  array $surpOrder
	 * */
	public function getSuperOrder(){
		 $whereDet = '';
		 $whereOrd = '';

		 !empty($_GET["ser_sku"])?$whereDet .= "sku='".$_GET["ser_sku"]."'" :null;

		 !empty($_GET["recordNumber"]) ? $whereOrd .= "recordNumber='".$_GET["recordNumber"]."'":null ;
		 if(!empty($_GET["ser_timetype"])){
		 	$timeType = $_GET["ser_timetype"];
		 	if(!empty($_GET["startTime"]) && !empty($_GET["endTime"])){
		 		$starTime = $_GET["startTime"];
		 		$starTime = strtotime($starTime." 00:00:00 ");
		 		$endTime = $_GET["endTime"];
		 		$endTime = strtotime($endTime." 23:59:59 ");
		 		$whereOrd .= "{$timeType} BETWEEN '{$starTime}' AND '{$endTime}'";
		 	}
		 }
		 $where = $whereOrd.",".$whereDet;//订单搜索条件 ，详情搜索条件
		 $where = base64_encode($where);
		 $paramArr = array(
					/* API系统级输入参数 Start */
					'method' => 'om.showSuperOrder',  //API名称
					'format' => 'json',  //返回格式
					'v' => '1.0',   //API版本号
					'username'	 => 'purchase',
					/* API系统级参数 End */
					/* API应用级输入参数 Start*/
					'where' => $where,
					/* API应用级输入参数 End*/
			);
		$surpOrder = callOpenSystem($paramArr);
		$surpOrder	= json_decode($surpOrder,true);
	    if(!empty($surpOrder)){
	    	return $surpOrder;
	    }else{
	    	return false;
	    }
	}

	/**
	 *功能：删除特殊运输方式
	 *@param $_GET['id'] 纪录id
	 *@return  null
	 * */
	public function adjust_transport_delete(){
			if(empty($_GET['id'])){
				self::$errCode = "002";
				self::$errMsg = "非法传参";
				return;
			}
			$id = $_GET['id'];
			$time = time();
			$set =  " is_delete  = '1' ,modifytime = '{$time}'";
			$table = "`".C("DB_PREFIX").'adjust_transport`';
			$where = "is_delete = '0' AND id = '{$id}' ";
			$ret = PurchaseOrderModel::updateOneTable($table, $set,$where);
			if($ret){
				self::$errCode = "001";
				self::$errMsg = "恭喜！删除成功";
				return;
			}
			self::$errCode = "002";
			self::$errMsg = "sorry！删除失败";
			return;
	}
	/**
	 *功能：特殊运输方式上下线
	 *@param $_GET['id']
	 *@return  null
	 * */
	public function adjust_transport_line(){
		if(empty($_GET['id'])){
			self::$errCode = "002";
			self::$errMsg = "非法传参";
			return;
		}
		$id = $_GET['id'];
		$type = $_GET['type'];
		$time = time();
		if($type == "down"){
			$set =  " is_show  = '0' ,modifytime = '{$time}'";
		}else{
			$set =  " is_show  = '1' ,modifytime = '{$time}'";
		}
		$table = "`".C("DB_PREFIX").'adjust_transport`';
		$where = "is_delete = '0' AND id = '{$id}' ";
		$ret = PurchaseOrderModel::updateOneTable($table, $set,$where);
		if($ret){
			self::$errCode = "001";
			self::$errMsg = "恭喜！删除成功";
			return;
		}
		self::$errCode = "002";
		self::$errMsg = "sorry！删除失败";
		return;
	}
	//add by wxb 2013/09/20
	/**
	 *功能：获取特殊运输方式数据
	 *@return  array 特殊运输方式二维数组
	 * */
	public function purchase_sku_conversion(){
		return PurchaseOrderModel::purchase_sku_conversion();
	}
	//add by wxb 2013/09/20
	/**
	 *功能：删除特殊运输方式
	 *@param $_GET['id']
	 *@return  null
	 * */
	public function del_sku_conversion(){
		if(!$_GET['id']){
			self::$errCode = "003";
			self::$errMsg = "传参有误";
			return;
		}
		$id = $_GET['id'];
		$table = C("DB_PREFIX")."sku_conversion ";
		$set = "is_delete =  '1'";
		$where = "id = '{$id}'";
		$ret = PurchaseOrderModel::updateOneTable($table, $set,$where);
		if($ret){
			self::$errCode = "001";
			self::$errMsg = "ok！删除成功";
			return;
		}
		self::$errCode = "002";
		self::$errMsg = "sorry！删除失败";
		return;
	}
	/**
	 *功能：编辑料号
	 *@param array  $_GET['data']   包含新旧料号 和添加者 id
	 *@return  null
	 * */
	public function edit_sku_conversion(){
		if(!$_GET['data']){
			self::$errCode = "003";
			self::$errMsg = "传参有误";
			return;
		}
		$data = $_GET['data'];
		$data = array_filter($data,trim);
		$id = $data['id'];
		$old_sku = $data['old_sku'];
		$new_sku = $data['new_sku'];
		$modifiedtime = time();
		$user = $data['user'];

		$table = C("DB_PREFIX")."sku_conversion ";
		$set = "old_sku =  '{$old_sku}' , new_sku = '{$new_sku}',modifiedtime = '{$modifiedtime}'";
		$set .= "  ,user = '{$user}'";
		$where = "id = '{$id}' AND is_delete = '0'";
		$ret = PurchaseOrderModel::updateOneTable($table, $set,$where);
		if($ret){
			self::$errCode = "001";
			self::$errMsg = "ok！修改成功";
			return;
		}
		self::$errCode = "002";
		self::$errMsg = "sorry！修改失败";
		return;
	}
	/**
	 *功能：添加料号
	 *@param $_GET['data']   包含新旧料号 和添加者
	 *@return  null
	 * */
	public function add_sku_conversion(){
		if(!$_GET['data']){
			self::$errCode = "003";
			self::$errMsg = "传参有误";
			return;
		}
		$data = $_GET['data'];
		$data = array_filter($data,trim);
		$old_sku = $data['old_sku'];
		$new_sku = $data['new_sku'];
		$user = $data['user'];
// 		var_dump($user);exit;
		$createdtime = time();
		$modifiedtime = time();

		$table = C("DB_PREFIX")."sku_conversion ";
		$set = "old_sku =  '{$old_sku}' , new_sku = '{$new_sku}',user = '{$user}', createdtime = '{$createdtime}',modifiedtime = '{$modifiedtime}'";
		$ret = PurchaseOrderModel::insertIntoOne($table, $set);
		if($ret){
			self::$errCode = "001";
			self::$errMsg = "添加成功";
			return;
		}else{
			self::$errCode = "002";
			self::$errMsg = "添加失败";
			return;
		}

	}
	//add by wxb 2013/09/20
	/**
	 *功能：通过旧料号获取新料号资料
	 *@para $oldSku 旧料号
	 *@return  $new_sku 新料号
	 * */
	public function showNewSku(){
		if(!$_GET['oldSku']){
			self::$errCode = "002";
			self::$errMsg = "Miss param";
			return;
		}
		$oldSku = $_GET['oldSku'];
		$table = C("DB_PREFIX")."sku_conversion ";
		$fields = "new_sku";
		$where = "old_sku = '{$oldSku}' AND is_delete = '0' LIMIT 1";
		$ret = PurchaseOrderModel::selectOneTable($table, $fields,$where);
		if($ret){
			$new_sku = $ret[0]['new_sku'];
			self::$errCode = "001";
			self::$errMsg = "success";
			return $new_sku;
		}else{
			self::$errCode = "003";
			self::$errMsg = "no this sku or delete";
			return;
		}
	}
	/**
	 *功能:根据订单编号获取采购订单主表信息
	 *@param $id
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	function getMainOrderInfo($id){
		$data = PurchaseOrderModel::getMainOrderInfo($id);
		return $data;
	}

	function getMainOwOrderInfo($id){
		global $dbConn;
		$sql = "SELECT * from ph_ow_order where id={$id}";
		$sql = $dbConn->execute($sql);
		$data = $dbConn->fetch_one($sql);
		return $data;
	}

	/**
	 *功能:根据订单编号获取采购订单明细表信息
	 *@param $id
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	function getDetailOrderInfo($po_id){
		//$data = PurchaseOrderModel::getDetailOrderInfo($poid);
		global $dbConn;
		$sql = "SELECT * from ph_order_detail where po_id='{$po_id}' and is_delete=0 order by stockqty asc ";
		$sql = $dbConn->execute($sql);
		$data = $dbConn->getResultArray($sql);
		return $data;
	}

	function getDetailOwOrderInfo($po_id){
		//$data = PurchaseOrderModel::getDetailOrderInfo($poid);
		global $dbConn;
		$sql = "SELECT * from ph_ow_order_detail where recordnumber='{$po_id}'";
		$sql = $dbConn->execute($sql);
		$data = $dbConn->getResultArray($sql);
		return $data;
	}
	/**
	 *功能:根据SKU获取每日均量等相关信息
	 *@param $sku
	 *日期:2013/11/13
	 *作者:王民伟
	 */
	 function getWarnInfoBySku($sku){
	 	//$data = PurchaseOrderModel::getWarnInfoBySku($sku);
		 global $dbConn;
		$sql    = "SELECT everyday_sale, booknums, newBookNum, sevendays, fifteendays, thirtydays, salensend, interceptnums, autointerceptnums, auditingnums, stock_qty FROM ph_sku_statistics WHERE sku = '{$sku}'";
		 $sql = $dbConn->execute($sql);
		 $skuInfo = $dbConn->fetch_one($sql);
		 return $skuInfo;
	}

	/**
	 *功能:根据SKU编号取SKU
	 *@param $skuidSKU编号
	 *@return 成功返回：sku;失败返回:false;
	 *日期:2013/08/05
	 *作者:王民伟
	 */
	public static function getSkuById($skuid){
		$data = PurchaseOrderModel::getSkuById($skuid);
		return $data;
	}

	/**
	 *功能:编辑采购订单时新增SKU
	 */
	public function insertOrderDetailInfo(){
		global $dbConn;
		$data = isset($_POST['data']) ? $_POST['data'] : '';
		$po_id = $data["po_id"];
		$sku = $data['sku'];
		$price = $data['price'];
		$count = $data['count'];
		$now = time();
		$sql = "INSERT INTO `ph_order_detail`( `po_id`, `sku`, `price`, `count`,`add_time`) VALUES ('{$po_id}','{$sku}','{$price}','{$count}','$now')";
		if($dbConn->execute($sql)){
			return 1;
		}else{
			return 0;
		}
	}

	/**
	 *功能:采购订单报表导出
	 *@param $data  订单编号数组
	 *日期:2013/11/14
	 *作者:王民伟
	 */
	public static function actExportOrder($data){
		$rtnData  = PurchaseOrderModel::exportOrder($data);
		return $rtnData;
	}

	/*下单芬哲ERP系统 Start*/
	 public static function downOrderToFinejo(){
	 	$data 		= $_GET['dataArr'];
	 	foreach($data as $orderid){
	 		$orderlist .= $orderid.',';
	 	}
	 	$orderArr = substr($orderlist, 0, strlen($orderlist) - 1);
	    $rtnData  = put_orderIdToFinejo($orderArr);//成功返回:Success;失败返回:Failure
	    if($rtnData == 'Success'){
	    	$rtnResult = PurchaseOrderModel::updateDownFinejoOrderStatus($orderArr);//更新订单为在途状态
	    	return $rtnResult;
	    }else{
	    	return $rtnData;
	    }

	 }
	//返回订单明细
	public static function getOrderInfo(){
		$orderArr   = $_GET['orderid'];
		$orderlist  = explode(',', $orderArr);
		$num        = count($orderlist);
		for($ii = 0; $ii < $num; $ii++){
			$mainData     = PurchaseOrderModel::getMainOrderInfo($orderlist[$ii]);
			$recordnumber = $mainData[0]['recordnumber'];//跟踪号
			$purname      = PurchaseOrderModel::getNameById($mainData[0]['purchaseuser_id']);//采购员名字
			$note         = $mainData[0]['note'];//备注

			$rtnMainData['recordnumber'] = $recordnumber;
			$rtnMainData['purname']      = $purname;
			$rtnMainData['note']         = $note;

			$rtnDetailData   = PurchaseOrderModel::getDetailOrderInfo($orderlist[$ii]);
			$rtnData[0]      = $rtnMainData;
			$rtnData[1]      = $rtnDetailData;
			$allData[$ii]    = $rtnData;
		}
		return $allData;
	}
	/*下单芬哲ERP系统 End*/

	//超大订单审核
	public static function auitSupperOrder(){
		$data = $_POST['data'];
		$paramArr = array(
			'method' 		=> 'order.system.auitSuperOrder',  //API名称
			'format' 		=> 'json',  //返回格式
			'v' 			=> '1.0',  //API版本号
			'username'	 	=> 'purchase',
			'orderid' 		=> $data['orderid'],
			'sku' 		    => $data['sku'],
			'type'          => $data['type'],
			'status' 		=> $data['status'],
			'pcontent'      => $data['content'],
			'purchaseId'    => $data['purid'],
			'storeId'       => '1'
		);

		$rtn 	 = callOpenSystem($paramArr);
		return $rtn;
	}

	/**
	 *根据供应商编号获取额度、预警额度、是否签约
	 *@param $id  订单编号数组
	 *日期:2013/11/27
	 *作者:王民伟
	 */
	public static function getParInfo($id){
		$data = PurchaseOrderModel::getParInfo($id);
		return $data;
	}


	//add by xiaojinhua
	public function updateCount(){
		global $dbConn;
		$orderId = $_POST["id"];
		$count = $_POST["count"];
		$sql = "update ph_order_detail set count={$count} where po_id={$orderId}";
		if($dbConn->execute($sql)){
			return 1;
		}else{
			return 0;
		}
	}

	public function updatePartner(){
		global $dbConn;
		$skulist = $_POST["skulist"];
		//$skuStr = implode("','",$skulist);
		$partnerId = $_POST["partner"]['partnerId'];
		$partnerName = $_POST["partner"]["partnerName"];
		$purchaseId = $_SESSION['sysUserId'];
		if($purchaseId == -1 || $purchaseId == ""){
			return true;
		}
		$flagArr = array();
		foreach($skulist as $sku){
			$flag = $this->checkSkuPartnerRelation($sku);
			if($flag){
				$sql = "UPDATE ph_user_partner_relation set purchaseId='{$purchaseId}',partnerId={$partnerId},companyname='{$partnerName}' where sku='{$sku}'";
			}else{
				$sql = "INSERT into ph_user_partner_relation ( `sku`, `partnerId`, `purchaseId`, `companyname`) values ('{$sku}',{$partnerId},{$purchaseId},'{$partnerName}')";
			}
			if($dbConn->execute($sql)){
				$sql = "UPDATE om_sku_daily_status set supplier='{$partnerName}' where sku='{$sku}'";
				$dbConn->execute($sql);
				$flagArr[] = 1;
			}else{
				$flagArr[] = 0;
			}
		}
		return json_encode($flagArr);
	}

	// 检查sku 和 供应商关系是否存在
	public function checkSkuPartnerRelation($sku){
		global $dbConn;
		$sql = "select count(*) as totalnum from ph_user_partner_relation where sku='{$sku}' ";
		$sql = $dbConn->execute($sql);
		$totalnum = $dbConn->fetch_one($sql);
		if($totalnum['totalnum'] > 0){
			$rtn = 1;
		}else{
			$rtn = 0;
		}
		return $rtn;
	}


	//仓库到货完结订单
	public function arriveSkuNum($sku,$num){
		global $dbConn;
		$skuArr = array();
		$sku = $_REQUEST["sku"];
		$num = $_REQUEST["num"];
		$skuArr["sku"] = $sku;
		$skuArr["num"] = $num;
		return json_encode($skuArr);
	}



	// 提供的采购订单列表
   	public function getPurchaseOrderList(){
		global $dbConn;
        $key    = isset($_GET['key']) ? trim($_GET['key']) : '';
        $type   = isset($_GET['type']) ? trim($_GET['type']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $addTime_start   = isset($_GET['addTime_start']) ? trim($_GET['addTime_start']) : '';
        $addTime_end     = isset($_GET['addTime_end']) ? trim($_GET['addTime_end']) : '';
        $auditTime_start = isset($_GET['auditTime_start']) ? trim($_GET['auditTime_start']) : '';
        $auditTime_end   = isset($_GET['auditTime_end']) ? trim($_GET['auditTime_end']) : '';
        $page            = isset($_GET['page']) ? $_GET['page']: 1;

        if($key != '') {
            if($type == 1) {
                $condition .= " AND a.recordnumber = '$key' ";
            } else if($type == 2) {
                $condition .= " AND c.sku = '$key' ";
             }
        }
        if($status != '') {
            if($status == 0) {
                $condition .= " AND a.status < '4' ";
            } else if($status == 1) {
                $condition .= " AND a.status = '4' ";
            }
        }

        if($addTime_start != ''){
             $condition .= " AND a.addtime >= '$addTime_start' ";
        }
        if($addTime_end  != ''){
             $condition .= " AND a.addtime < '$addTime_end' ";
        }
        if($auditTime_start  != ''){
             $condition .= " AND a.aduittime >= '$auditTime_start' ";
        }
        if($auditTime_end  != ''){
             $condition .= " AND a.aduittime < '$auditTime_end' ";
        }

		$field = "distinct b.po_id, a.*,c.company_name";
		$fieldTotal = "count(distinct b.po_id) as totalnum ";
		$sqlStr = "  from ph_order as a left join ph_order_detail as b ON a.id = b.po_id
				left join ph_partner as c on a.partner_id=c.id
		     	";
        $condition = ' WHERE 1';

		$sql = "select ".$fieldTotal.$sqlStr.$where.$condition;
		$sql = $dbConn->execute($sql);
		$totalnum = $dbConn->fetch_one($sql);
        $totalrow  = $totalnum["totalnum"];;

    	$pagesize 	= 100;//每页显示条数
		$pageindex  = $page;
		$limit      = "limit ".($pageindex-1)*$pagesize.",$pagesize";
        $condition .= " ORDER BY a.addtime DESC ".$limit;

		$sql = "select ".$field.$sqlStr.$where.$condition;
		$sql = $dbConn->execute($sql);
        $resultList = $dbConn->getResultArray($sql);
		$resultData = array();
		foreach($resultList as $item){
			$sql = "select sku,price,count,stockqty from ph_order_detail where po_id={$item['id']}";
			$sql = $dbConn->execute($sql);
			$detailInfo = $dbConn->getResultArray($sql);
			$item["detailInfo"] = $detailInfo;
			$resultData[] = $item;
		}
		$datalist[]= $totalrow;
		$datalist[]= $resultData;
		return json_encode($datalist);
    }


	// 采购补单

	public function autoCreateOrderSn($userid){
		global $dbConn;
		while(1){
			$recordnumber = "BD".date("ymd").$userid.rand(100, 999);
			$sql = "SELECT recordnumber FROM ph_order WHERE recordnumber = '{$recordnumber}' AND is_delete = 0";
			$sql = $dbConn->execute($sql);
			$number = $dbConn->fetch_one($sql);
			if(empty($number['recordnumber'])){
				return $recordnumber;
				break;
			}
		}

	}
	public function addOrder(){
		global $dbConn;
		$dataArr = $_POST["dataArr"];
		$now = time();
		$status = 1;//未审核的订单
		$order_type = 4;//采购补单
		$warehouse_id = 1;
		$flag = array();
		$unOrderIdArr = array();
		$operater = $_SESSION['sysUserId'];
		$skuObj = new SkuAct();
		$skuComObj = new CommonAct();
		foreach($dataArr as $item){
			$price      = PurchaseOrderModel::getPriceBySku($item['sku']);//SKU单价
			$partnerId  = CommonAct::actgetPartnerIdBySku($item['sku']);//供应商ID
			$orderData  = $this->getOrderSN($partnerId, $item['purchaseId'],4);//判断同供应商、采购员跟踪号是否已存在
			$orderSN = $orderData['recordnumber'];

			if(isset($orderSN)){//同一个供应商的订单已经存在
				$poid = $orderData['id'];
				$recordnumber = $orderSN;
			}else{
				$recordnumber = $this->autoCreateOrderSn($item['purchaseId'], 1);
				$sql = "INSERT INTO `ph_order`(`recordnumber`, `addtime`, `aduittime`,  `status`, `order_type`, `warehouse_id`, `purchaseuser_id`, `aduituser_id`, `partner_id`, `company_id`, `note`) VALUES ('{$recordnumber}',{$now},{$now},{$status},{$order_type},{$warehouse_id},{$item['purchaseId']},{$item['purchaseId']},{$partnerId},1,'异常到货采购补单')";
				if($dbConn->execute($sql)){
					$poid = PurchaseOrderModel::getOrderIdByNum($recordnumber);//根据跟踪号取采购主订单编号
				}
			}

			if(isset($poid)){
				$sql = "select id totalNum from ph_order_detail where sku='{$item['sku']}' and po_id='{$poid}' ";
				$sql = $dbConn->execute($sql);
				$detailInfo = $dbConn->fetch_one($sql); 
				if(isset($detailInfo['id'])){
					$sql = "update ph_order_detail set count=count+{$item['num']} WHERE id='{$poid}'";
				}else{
					$sql = "insert into ph_order_detail (po_id,unOrderId,recordnumber,sku,count,price,stockqty) values ({$poid},'{$item['unOrderId']}','{$recordnumber}','{$item['sku']}',{$item['num']},{$price},'{$item['num']}')";
				}
				if($dbConn->execute($sql)){
					$usql = "UPDATE `ph_sku_reach_record` SET `ordersn`='{$recordnumber}',operatime={$now}, operatorId={$operater},status = 1 WHERE id={$item['id']}";
					//$skuObj->tallySkuRecord($item['sku'],$item['num'],1); // hold 住一部分数量
					$dbConn->execute($usql);
					$skuComObj->calcAlert($item['sku']);
					$flag[] = 1;
					$unOrderIdArr[] = $item["unOrderId"]; 
				}else{
					$flag[] = 0;
				}
			}


		}
		//$pushObj = new CommonAct();
		//$pushObj->setTallyIsUse($unOrderIdArr);
		return json_encode($flag);
	}


	/*检查在途订单sku 数量*/
	public function checkSkuOnWayNum($sku){
		global $dbConn;
		//$sku = $_GET["sku"];
		$sql = "select b.count ,b.stockqty from ph_order_detail as b left join ph_order as a on b.po_id=a.id where a.status=3 and a.is_delete=0 and b.sku='{$sku}'and b.is_delete=0" ;
		$sql = $dbConn->execute($sql);
		$skuNum = $dbConn->getResultArray($sql);
		$totalnum = 0;
		$totalqty = 0;
		$total = 0;
		if(count($skuNum) != 0){
			foreach($skuNum as $itemNum){
				$totalnum += $itemNum["count"];
				$totalqty += $itemNum["stockqty"];
			}
			$total = $totalnum - $totalqty;
		}
		//$data = array("sku"=>$sku,"amount"=>$total);
		//return json_encode($data);
		return $total;
	}



	public function getUnnormalSkuReach(){
		global $dbconn;
		$starttime	= isset($_GET['addTime_start']) ? $_GET['addTime_start'] : '';
		$endtime	= isset($_GET['addTime_end']) ? $_GET['addTime_end'] : '';
		$sku 		= isset($_GET['sku']) ? $_GET['sku'] : '';
		$cguserid 	= isset($_GET['cguserid']) ? $_GET['cguserid'] : ''; //采购id
		$status		= isset($_GET['status']) ? $_GET['status'] : '';
		$partner	= isset($_GET['partner']) ? $_GET['partner'] : '';
		$page       = isset($_GET['page']) ? $_GET['page'] : 0;
		$condition 	= '';
		if (!empty($starttime) && $endtime >= $starttime){
			$serstart = strpos($starttime, ':')!==false ? strtotime($starttime) : strtotime($starttime." 00:00:00");
			$serend   = strpos($endtime, ':')!==false ? strtotime($endtime) : strtotime($endtime." 23:59:59");
			$condition  .= " AND addtime BETWEEN "."'{$serstart}'"." AND "."'{$serend}'";
		}
		if (!empty($sku)){
			$condition  .= " AND sku like '%{$sku}%'";
		}
		if ($status != '' && $status != -1){
			$condition  .= " AND status = '{$status}'";
		}else{
			$condition  .= " AND status = 0";
		}

		if($cguserid != ""){
			$condition  .= " AND purchaseId = {$cguserid}";
		}else{
			$access_id = $_SESSION['access_id'];
			$condition .= " and purchaseId in ($access_id)";
		}

		if($partner != ""){
			$condition  .= " AND partnerName like '%{$partner}%'";
		}

		// 权限控制 只有具有相关采购权限的人才能看到
		//print_r($_SESSION);
		if($page != 0){
			$page = ($page-1)*100;	
		}
		$limit = " limit {$page},100";
		$sqlStr = "SELECT * FROM  `ph_sku_reach_record` where 1 {$condition} order by id desc";
		$sql = $dbconn->execute($sqlStr);
		$totalNum = $dbconn->num_rows($sql);
		$sql = $sqlStr."{$limit}";
		$sql = $dbconn->execute($sql);
		$skuInfo = $dbconn->getResultArray($sql);
		$data = array("totalNum"=>$totalNum,"skuInfo"=>$skuInfo);
		return $data;
	}


	/*
	 * 订单取消退货
	 * */
	public function returnSku(){
		global $dbconn;
		$skuidArr = $_POST["skuidArr"];
		$userid = $_SESSION["sysUserId"];
		$now = time();
		$idStr = implode(",",$skuidArr);
		$sql = "UPDATE `ph_sku_reach_record` SET operatime={$now}, operatorId={$userid},status = 3 WHERE id in ({$idStr})";
		if($dbconn->execute($sql)){
			return 1;
		}else{
			return 0;
		}
	}

	/*
	 *提供仓库入库接口
	 *与采购订单对接
	 *
	 * */

	public function addStock(){
		error_reporting(0);
		global $dbconn,$rmqObj;
		$sku = $_REQUEST["sku"];
		$amount = $_REQUEST["amount"];
		$totalAmount = $amount;
		$intime = $_REQUEST["intime"];
		$key = trim($_REQUEST["key"]);
		if(empty($key)){
			$data["errorCode"] = 501;
			$data["msg"] = "传过来的参数缺少key";
			return json_encode($data);
		}else{
			$number = $this->check_instock($key);
			if($number > 0){
				$data["errorCode"] = 0;
				$data["msg"] = "这个key的上架已经匹配过采购订单";
				//return json_encode($data);
			}
		}
		//$this->trigger_list($sku,$amount);
		$sql = "select a.count ,a.stockqty,a.price,a.id as detail_id,a.sku, b.recordnumber,b.id from ph_order_detail as a left join ph_order as b on a.po_id=b.id where a.is_delete=0
				and b.is_delete=0
				and b.status=3 
				and a.sku='{$sku}'
				order by b.id ASC
			"; //查找在途订单sku 未到货的数量
		$sql = $dbconn->execute($sql);
		$skuInfoArr = $dbconn->getResultArray($sql);
		$now = time();
		$flag = array();
		foreach($skuInfoArr as $item){
			if($amount <= 0 || $item["count"] <= 0){ //匹配完成 跳出
				break;
			}
			$unArriveNum = $item["count"] - $item["stockqty"];
			if($unArriveNum <= $amount){ //订单的数量小于等于入库数量
				$nowNeedAmount = $unArriveNum;
			}else{
				$nowNeedAmount = $amount;
			}
			$amount = $amount - $nowNeedAmount;//入库后剩余数量
			$sql = "update ph_order_detail set stockqty=stockqty+{$nowNeedAmount} ,reach_time={$now} where id={$item['detail_id']}";

			if($dbconn->execute($sql)){ //写入批次到货记录表

				// 发送消息队列 重新计算 成本核算价
				$publish_data = array();
				$publish_data['type'] = "updatePrice";
				$publish_data['totalNum'] = $totalAmount; 
				$publish_data['number'] = $nowNeedAmount;
				$publish_data['price'] = $item['price'];
				$publish_data['sku'] = $sku;
				$publish_data['intime'] = $intime;
				$this->publish_msg($publish_data);

				if($nowNeedAmount > 0){
					$sql = "INSERT INTO `ph_order_arrive_log`(`ordersn`, `sku`, `amount`, `arrive_time`,keyWord) VALUES ('{$item['recordnumber']}','{$item['sku']}',{$nowNeedAmount},{$now},'{$key}')";
					$dbconn->execute($sql);
				}
				$this->checkOrderFinish($item["id"]);
			}else{ //如果入库不成功
				$flag[] = 0;
			}
		}

		if(in_array(0,$flag)){ //插入数据不成功
			$data["errorCode"] = 500;
			//$data["msg"] = "部分数据插入失败";
			$data["msg"] = $amount;
		}else{
			$data["errorCode"] = 0;
			//$data["msg"] = "success";
			$data["msg"] = $amount;
		}
		$log = "sku : {$sku}; 上架的总数：{$totalAmount};匹配采购订单后剩余的数量：{$amount},key:{$key}";
		$note = "sku : {$sku}; 上架的总数：{$totalAmount};匹配采购订单后剩余的数量：{$amount}";
		$remain = $amount;
		$partnerName = getPartnerBySku($sku);
		$user = getUserIdBySku($sku);
		$purchaseId = $user["purchaseId"];
		$now = time();

		if($amount == $totalAmount){
			$sql = "INSERT INTO `ph_order_arrive_log`(sku,`arrive_time`,keyWord) VALUES ('{$item['sku']}',{$now},'{$key}')";
			$dbconn->execute($sql);
		}

		if($amount > 0){
			$sql = "INSERT INTO ph_sku_reach_record(sku,purchaseId,amount,totalAmount,note,addtime,partnerName) VALUES 
				('{$sku}','{$purchaseId}',{$remain},{$totalAmount},'{$note}',{$now},'{$partnerName}')";
			write_log("inStock_new.txt",$sql);
			$dbconn->execute($sql);
		}

		return json_encode($data);
	}

	//检查这次上架是否已经存在
	public function check_instock($key){
		global $dbConn;
		$sql = "SELECT count(*) as totalNum FROM `ph_order_arrive_log` WHERE keyWord='{$key}'";
		$sql = $dbConn->execute($sql);
		$item = $dbConn->fetch_one($sql);
		return $item['totalNum'];
	}


	// 入库信息 通过消息队列发送 add by xiaojinhua
	public function sendMsgInstock($publish_data,$exchange="purchase_info_exchange"){
		global $rmqObj;
		var_dump($rmqObj);
		exit;
		$rmqObj->single_queue_publish($exchange,$publish_data);
	}
	

	//不良品记录
	public function addUnStock(){
		global $dbconn;
		$sku = $_REQUEST["sku"];
		$amount = $_REQUEST["amount"];
		$sql = "select a.count ,a.stockqty,a.id as detail_id,a.sku, b.recordnumber,b.id from ph_order_detail as a left join ph_order as b on a.po_id=b.id where a.is_delete=0
				and b.status=3 
				and a.sku='{$sku}'
				order by b.aduittime ASC
			"; //查找在途订单sku 未到货的数量
		$sql = $dbconn->execute($sql);
		$skuInfoArr = $dbconn->getResultArray($sql);
		$now = time();
		$flag = array();

		$skuObj = new SkuAct();
		$skuObj->tallySkuRecord($sku,$amount,2); 
		foreach($skuInfoArr as $item){
			if($amount <= 0){ //匹配完成 跳出
				break;
			}
			$unArriveNum = $item["count"] - $item["stockqty"];
			if($unArriveNum <= $amount){ //订单的数量小于等于入库数量
				$nowNeedAmount = $unArriveNum;
			}else{
				$nowNeedAmount = $amount;
			}
			$amount = $amount - $nowNeedAmount;//入库后剩余数量
			//$sql = "update ph_order_detail set stockqty=stockqty+{$nowNeedAmount},ungoodqty=ungoodqty+{$nowNeedAmount},reach_time={$now} where id={$item['detail_id']}";
			$sql = "update ph_order_detail set ungoodqty=ungoodqty+{$nowNeedAmount},reach_time={$now} where id={$item['detail_id']}";
			if($dbconn->execute($sql)){ //写入批次到货记录表
				if($nowNeedAmount > 0){
					$sql = "INSERT INTO `ph_order_arrive_log`(`ordersn`, `sku`, `amount`, `arrive_time`) VALUES ('{$item['recordnumber']}','{$item['sku']}',{$nowNeedAmount},{$now})";
					$dbconn->execute($sql);
				}
				$this->checkOrderFinish($item["id"]);
			}else{ //如果入库不成功
				$flag[] = 0;
			}
		}
		if(in_array(0,$flag)){ //插入数据不成功
			$data["errorCode"] = 500;
			$data["msg"] = "部分数据插入失败";
		}else{
			$data["errorCode"] = 0;
			$data["msg"] = "success";
		}
		return json_encode($data);
	}

	/*
	 * 检查订单所有料号是否完结
	 * */

	public function checkOrderFinish($orderid){
		global $dbconn;
		if(empty($orderid)){
			$orderid = $_REQUEST["orderid"];
		}
		$sql = "select count, stockqty from ph_order_detail where po_id={$orderid} and is_delete=0";
		$sql = $dbconn->execute($sql);
		$numInfo = $dbconn->getResultArray($sql);
		$flag = array();
		$now = time();
		foreach($numInfo as $item){
			if($item["stockqty"] < $item["count"]){//到货数量和订购数量比较
				return ;
			}
		}

		$sql = "update ph_order set status=4, finishtime={$now} where id={$orderid}";
		//echo $sql;
		if($dbconn->execute($sql)){
			return 1;
		}
	}

/*
	 * 获得采购id对应的供应商信息
	 */
	public function getSupplier(){
	    global $dbconn;
	    $purchaseId    = isset($_REQUEST['purchaseId']) ? intval($_REQUEST['purchaseId']) : 0;
	    $returnData    = array('code'=>0, 'msg'=>'','data'=>array());
	    if (empty($purchaseId)) {
	    	$returnData['msg']   = '缺少采购id';
	    	return json_encode($returnData);
	    }

		$sql = "select distinct a.id ,a.company_name  from ph_partner as a left join 
			ph_user_partner_relation as b on a.id=b.partnerId where b.purchaseId={$purchaseId} ";
	    $data  = $dbconn->fetch_array_all($dbconn->query($sql));
	    $returnData['data']    = $data;
	    $returnData['code']    = 1;
// 	    print_r($returnData);exit;
	    return json_encode($returnData) ;
	}

	/*
	 * 获取供应商详情
	 */
	public function getSupplierInfo(){
	    global $dbconn;
	    $Id    = isset($_REQUEST['supplierId']) ? intval($_REQUEST['supplierId']) : 0;
	    $returnData    = array('code'=>0, 'msg'=>'','data'=>array());
	    if (empty($Id)) {
	        $returnData['msg']   = '缺少供应商id';
	        return json_encode($returnData);
	    }
	     
	    $sql   = "
	    select * from ph_partner where id=$Id
	    ";
	    $data  = $dbconn->fetch_first($sql);
	    $returnData['data']    = $data;
	    $returnData['code']    = 1;
	    return json_encode($returnData);
	}
	

}

?>
