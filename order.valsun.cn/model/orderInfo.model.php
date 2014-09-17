<?php
class OrderInfoModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = '';

	public static function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		//self::$dbPrefix = "om_";
		mysql_query('SET NAMES UTF8');
	}

	/*
	 * 获得某个订单的信息 对应om_unshipped_order表信息
	 * $orderid  订单编号
	 */
	public static function getOrderInfo($orderid) {
		!self :: $dbConn ? self :: initDB() : NULL;
		$sql = 'select * from om_unshipped_order where id = ' . $orderid;
		//var_dump($sql); exit;
		$row = self :: $dbConn->fetch_first($sql);
		if (empty ($row)) { //没有找到信息
			self :: $errCode = '003';
			self :: $errMsg = '订单信息不存在!';
			return FALSE;
		}

		if ($row['is_delete'] == 1) { //订单已经删除
			self :: $errCode = '004';
			self :: $errMsg = '订单已经删除';
			return FALSE;
		}
		return $row;
	}

	/*
	 * 将订单状态改为异常订单
	 * $orderid  订单号
	 */
	public static function changStatusToException($orderid) {
		!self :: $dbConn ? self :: initDB() : NULL;
		$sql = 'update om_unshipped_order set orderStatus=' . EXCEPTION . ' , orderType=' .
		EXCEPTION_WHANDEL . ' where id=' . $orderid;
		//echo $sql;
		$queryresult = self :: $dbConn->query($sql);
		if (empty ($queryresult)) { //更新失败
			self :: $errCode = '005';
			self :: $errMsg = '更新状态失败！';
			return FALSE;
		}
		return TRUE;
	}

	/*
	 * 获取已经发货的订单信息
	 */
	public static function getShipedOrderInfo($orderid) {
		!self :: $dbConn ? self :: initDB() : NULL;
		$sql = 'select * from om_unshipped_order where id=' . $orderid;
		//echo $sql;
		$row = self :: $dbConn->fetch_first($sql);
		if (empty ($row)) { //没有找到信息
			self :: $errCode = '003';
			self :: $errMsg = '订单信息不存在!';
			return FALSE;
		}

		if ($row['is_delete'] == 1) { //订单已经删除
			self :: $errCode = '004';
			self :: $errMsg = '订单已经删除';
			return FALSE;
		}
		return $row;
	}

	/*
	 * 取消交易
	 */
	public static function cancelDeal($orderid, $type) {
		!self :: $dbConn ? self :: initDB() : NULL;
		$where = " where id=" . $orderid;
		$tableName = "om_unshipped_order";
		if ($type == 1) { //取消交易
			$returnStatus = array (
				'orderStatus' => C('STATEINTERCEPTSHIP'
			), 'orderType' => C('STATECANCELTRAN_PENDING'));
		}
		elseif ($type == 2) { //废弃订单
			$returnStatus = array (
				'orderStatus' => C('STATEINTERCEPTSHIP'
			), 'orderType' => C('STATERECYCLE'));
		}
		elseif ($type == 3) { //暂不寄
			$returnStatus = array (
				'orderStatus' => C('STATEPENDING'
			), 'orderType' => C('STATESENDTEMP'));
		}
		elseif ($type == 4) { //异常处理
			$returnStatus = array (
				'orderStatus' => C('STATEINTERCEPTSHIP'
			), 'orderType' => C('STATEINTERCEPTSHIP_PEND'));
		}
		if (OrderindexModel :: updateOrder($tableName, $returnStatus, $where)) {
			self :: $errCode = '200';
			self :: $errMsg = '订单更新成功';
			return FALSE;
		} else {
			self :: $errCode = '004';
			self :: $errMsg = '订单更新失败';
			return FALSE;
		}
	}

	/*
	 * 补寄订单
	 * $orderinfo 原始订单的详细信息
	 */
	public static function resendOrder($orderid, $note, $type, $old_ostatus, $old_otype) {
		!self :: $dbConn ? self :: initDB() : NULL;
		BaseModel :: begin(); //开始事务
		//$tableName = 'om_unshipped_order';
		//echo $old_ostatus; echo "<br>";
		//echo $old_otype; echo "<br>";
		$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($old_ostatus, $old_otype);
		//echo $tableName;
		if ($type == 1) {
			$updatesql = 'update ' . $tableName . ' set isBuji=1,isCopy=1 where id=' . $orderid . ' and is_delete = 0 and storeId = 1';
		} else {
			$updatesql = 'update ' . $tableName . ' set isCopy=1 where id=' . $orderid . ' and is_delete = 0 and storeId = 1';
		}
		if (!self :: $dbConn->query($updatesql)) { //更新状态失败
			BaseModel :: rollback();
			self :: $dbConn->query('SET AUTOCOMMIT=1');
			self :: $errCode = '003';
			self :: $errMsg = '订单信息不存在，无法复制!';
			return FALSE;
		}
		//产生新订单
		$where = ' WHERE id = ' . $orderid . ' and is_delete = 0 and storeId = 1';
		$orderData = OrderindexModel :: showOrderList($tableName, $where);
		$orderDetail = $orderData[$orderid]['orderDetail'];
		if (!$orderDetail) { //更新状态失败
			BaseModel :: rollback();
			self :: $dbConn->query('SET AUTOCOMMIT=1');
			return FALSE;
		}
		$insert_orderDetail = array ();
		foreach ($orderDetail as $detail) {
			$insert_orderDetailData = $detail['orderDetailData'];
			unset ($insert_orderDetailData['id']);
			$insert_orderDetailExtenData = $detail['orderDetailExtenData'];
			unset ($insert_orderDetailExtenData['omOrderdetailId']);
			$insert_orderDetail[] = array (
				'orderDetailData' => $insert_orderDetailData,
				'orderDetailExtenData' => $insert_orderDetailExtenData,

			);
		}
		$obj_order_data = $orderData[$orderid]['orderData'];
		if ($obj_order_data['isBuji'] == 2) {
			self :: $errCode = '003';
			self :: $errMsg = "补寄产生订单不能补寄!";
			BaseModel :: rollback();
			self :: $dbConn->query('SET AUTOCOMMIT=1');
			return FALSE;
		}
		if ($obj_order_data['isCopy'] == 2) {
			self :: $errCode = '003';
			self :: $errMsg = "复制产生订单不能复制!";
			BaseModel :: rollback();
			self :: $dbConn->query('SET AUTOCOMMIT=1');
			return FALSE;
		}
		unset ($obj_order_data['id']);
		$orderExtenData = $orderData[$orderid]['orderExtenData'];
		unset ($orderExtenData['omOrderId']);
		$orderUserInfoData = $orderData[$orderid]['orderUserInfoData'];
		unset ($orderExtenData['omOrderId']);
		if ($type == 1) {
			$obj_order_data['isCopy'] = 2;
			$obj_order_data['actualTotal'] = 0.00;
		} else {
			$obj_order_data['isCopy'] = 2;
			$obj_order_data['isBuji'] = 2;
			$obj_order_data['actualTotal'] = 0.00;
			$obj_order_data['orderStatus'] = C('STATEBUJI');
			$obj_order_data['orderType'] = C('STATEBUJI_DONE');
		}
		$orderNote = array (
			'content ' => $note,
			'userId' => $_SESSION['sysUserId'],
		'createdTime' => time());
		$insert_orderData = array ();
		$insert_orderData = array (
			'orderData' => $obj_order_data,
			'orderExtenData' => $orderExtenData,
			'orderUserInfoData' => $orderUserInfoData,
			'orderDetail' => $insert_orderDetail,
			'orderNote' => $orderNote
		);
		//$orderNote = $orderData['orderNote'];
		//echo $insertsql;
		//var_dump($insert_orderData); exit;
		if ($insertId = OrderAddModel :: insertAllOrderRowNoEvent($insert_orderData)) {
			//echo $split_log .= 'insert success!' . "\n"; exit;
			//var_dump($_mainId,$_spitId); exit;
			if (!OrderLogModel :: insertOrderLog($insertId, '补寄产生订单')) {
				BaseModel :: rollback();
				self :: $errCode = '001';
				self :: $errMsg = "补寄失败!";
				return false;
			}
			if (!OrderRecordModel :: insertSendRecords($orderid, $insertId)) {
				BaseModel :: rollback();
				self :: $errCode = '002';
				self :: $errMsg = "补寄失败添加记录失败!";
				return false;
			}
		} else {
			//$split_log .= '补寄新订单产生失败!' . "\n";
			BaseModel :: rollback();
			self :: $errCode = '003';
			self :: $errMsg = "补寄新订单产生失败";
			return false;
		}
		BaseModel :: commit();
		self :: $errCode = '200';
		self :: $errMsg = "补寄新订单成功！";
		return TRUE;
	}

    //根據发货時間(weighting)，帳號Id获取給定訂單表Id列表(orderStatus=2)
	public static function getTNameOrderIdByTSA($tName, $start, $end, $accountStr, $orderStatus=2, $condition = '') {
		self :: initDB();
		$sql = "select omOrderId from $tName"."_warehouse where weighTime>=$start AND weighTime <=$end";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$omOrderIdList = self :: $dbConn->fetch_array_all($query);
			$omOrderIdArr = array();
			foreach($omOrderIdList as $value){
				if(!empty($value['omOrderId'])){
					$omOrderIdArr[] = $value['omOrderId'];
				}
			}
			if(empty($omOrderIdArr)){
				$omOrderIdStr = '0';
			}else{
				$omOrderIdStr = implode(',', $omOrderIdArr);
			}
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "error";
			return false;
		}
		$sql = "SELECT id from $tName where orderStatus=$orderStatus AND id IN ($omOrderIdStr) AND ($accountStr) AND is_delete=0 ";
		if (!empty($condition)) {
			$sql .= $condition;
		}
        //echo $sql;
//        exit;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false;
		}
	}

	//获取发货单信息列表-ebay
	public static function getShipOrderList($start, $end, $accountStr, $condition = '') {
		//return true;
		self :: initDB();
		//$sql = "SELECT a.*,
//						b.account,
//						c.tracknumber,
//						d.transId,d.currency,d.feedback,d.PayPalPaymentId,d.PayPalEmailAddress,
//						e.email,e.platformUsername,e.username,e.street,e.address2,e.city,e.state,
//						e.countryname,e.zipCode,e.phone,e.landline,
//						f.actualWeight,f.actualShipping,f.packersId,f.weighTime
//						FROM om_unshipped_order AS a
//						LEFT JOIN om_account AS b ON a.accountId=b.id
//						LEFT JOIN om_order_tracknumber as c ON c.omOrderId = a.id
//						LEFT JOIN om_unshipped_order_extension_ebay as d ON d.omOrderId = a.id
//						LEFT JOIN om_unshipped_order_userInfo as e ON e.omOrderId = a.id
//						LEFT JOIN om_unshipped_order_warehouse as f ON f.omOrderId = a.id
//						where a.orderStatus=100 ";
		//echo $sql;
		$sql = "SELECT a.*,
				b.account,c.tracknumber,
				d.transId,d.currency,d.feedback,d.PayPalPaymentId,d.PayPalEmailAddress,
				e.email,e.platformUsername,e.username,e.street,e.address2,e.city,e.state,
				e.countryname,e.zipCode,e.phone,e.landline,
				f.actualWeight,f.actualShipping,f.packersId,f.weighTime
				FROM om_unshipped_order AS a
				LEFT JOIN om_account AS b ON a.accountId=b.id
				LEFT JOIN om_order_tracknumber as c ON c.omOrderId = a.id
				LEFT JOIN om_unshipped_order_extension_ebay as d ON d.omOrderId = a.id
				LEFT JOIN om_unshipped_order_userInfo as e ON e.omOrderId = a.id
				LEFT JOIN om_unshipped_order_warehouse as f ON f.omOrderId = a.id
				where a.orderStatus=2  AND (a.ShippedTime >=$start AND a.ShippedTime <=$end) AND ($accountStr) AND a.is_delete = 0 ";
        echo $sql;
        exit;
		if ($condition != '') {
			$sql .= $condition;
		}

		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false;
		}
	}

    //获取发货单信息列表——alipress
	public static function getShipOrderList2($start, $end, $accountStr, $condition = '') {
		//return true;
		self :: initDB();
		$sql = "SELECT a.*,
				b.account,c.tracknumber,
				d.transId,d.currency,d.feedback,d.PayPalPaymentId,d.PayPalEmailAddress,
				e.email,e.platformUsername,e.username,e.street,e.address2,e.city,e.state,
				e.countryname,e.zipCode,e.phone,e.landline,
				f.actualWeight,f.actualShipping,f.packersId,f.weighTime
				FROM om_unshipped_order AS a
				LEFT JOIN om_account AS b ON a.accountId=b.id
				LEFT JOIN om_order_tracknumber as c ON c.omOrderId = a.id
				LEFT JOIN om_unshipped_order_extension_aliexpress as d ON d.omOrderId = a.id
				LEFT JOIN om_unshipped_order_userInfo as e ON e.omOrderId = a.id
				LEFT JOIN om_unshipped_order_warehouse as f ON f.omOrderId = a.id
				where a.orderStatus=2  AND (a.ShippedTime >=$start AND a.ShippedTime <=$end) AND ($accountStr) AND a.is_delete = 0 ";
        //echo $sql;
//        exit;
		if ($condition != '') {
			$sql .= $condition;
		}

		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false;
		}
	}

    //获取发货单信息列表——CNDL
	public static function getShipOrderList3($start, $end, $accountStr, $condition = '') {
		//return true;
		self :: initDB();
		$sql = "SELECT a.*,
				b.account,c.tracknumber,
				d.transId,d.currency,d.feedback,d.PayPalPaymentId,d.PayPalEmailAddress,
				e.email,e.platformUsername,e.username,e.street,e.address2,e.city,e.state,
				e.countryname,e.zipCode,e.phone,e.landline,
				f.actualWeight,f.actualShipping,f.packersId,f.weighTime
				FROM om_unshipped_order AS a
				LEFT JOIN om_account AS b ON a.accountId=b.id
				LEFT JOIN om_order_tracknumber as c ON c.omOrderId = a.id
				LEFT JOIN om_unshipped_order_extension_CNDL as d ON d.omOrderId = a.id
				LEFT JOIN om_unshipped_order_userInfo as e ON e.omOrderId = a.id
				LEFT JOIN om_unshipped_order_warehouse as f ON f.omOrderId = a.id
				where a.orderStatus=2  AND (a.ShippedTime >=$start AND a.ShippedTime <=$end) AND ($accountStr) AND a.is_delete = 0 ";
        //echo $sql;
//        exit;
		if ($condition != '') {
			$sql .= $condition;
		}

		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false;
		}
	}

	//获取发货单信息列表
	public static function getMainShipOrderList($start, $end, $accountStr) {
		//return true;
		self :: initDB();
		$sql = "SELECT *,
						FROM om_unshipped_order
						where a.orderStatus=2 AND (a.ShippedTime >=$start AND a.ShippedTime <=$end) AND ($accountStr) AND a.is_delete = 0 ";

		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false;
		}
	}

	//获取发货单明细信息
	public static function getShipOrderDetailByOrderId($orderId) {
		self :: initDB();
		$sql = "SELECT * FROM om_unshipped_order_detail WHERE omOrderId = '$orderId' ORDER BY id ASC";
		//echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false;
		}
	}

	//获取发货单信息
	public static function getEbayOrderInfo($sellerAccount, $buyerAccount, $field, $condition = '') {
		self :: initDB();
		$sql = "SELECT $field
						FROM om_unshipped_order AS a
						LEFT JOIN om_unshipped_order_userInfo AS b ON a.id = b.omOrderId
						LEFT JOIN om_account AS c ON a.accountId = c.id
						WHERE b.platformUsername = '{$buyerAccount}' AND c.account='{$sellerAccount}' AND orderStatus != '400' AND  orderType != '600' " . $condition;
		//echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false;
		}
	}

	//获取发货单信息
	public static function getExpressOrderInfo($sellerAccount, $recordnumber, $field, $condition = '') {
		self :: initDB();
		$sql = "SELECT $field
						FROM om_unshipped_order AS a
						LEFT JOIN om_unshipped_order_userInfo AS b ON a.id = b.omOrderId
						LEFT JOIN om_account AS c ON a.accountId = c.id
						WHERE a.recordNumber = '{$recordnumber}' AND c.account='{$sellerAccount}' " . $condition;
		//echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false;
		}
	}
	
	//异常处理
	public static function erpgetorderinfo2() {
		self :: initDB();
		require_once WEB_PATH."api/include/functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'erp.get.orderinfo2',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/
			/*'purchaseId'		=> $purchaseId, */ //主料号
			/* API应用级输入参数 End*/
		);
		$result 	= callOpenSystem($paramArr, $url);
		$data 		= json_decode($result, true);
		//var_dump($data);
		if(!isset($data['data'])){
			return array();	
		}
		return $data['data'];
	}

}
?>