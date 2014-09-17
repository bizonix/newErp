<?php

/*
 * om通用Model
 * ADD BY zqt 2013.9.5
 */
 
class OrderRefundModel{
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	public static $table = "om_order_refund";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}
	/*
	 *取得指定表中的指定记录
	 *废弃使用
	 */
	public static function getTNameList($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			self :: $errCode = "200";
			self :: $errMsg = "获取数据成功";
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 *取得指定表中的指定记录
	 */
	public static function getOrderRefundNums($where) {
		self :: initDB();
		$sql = "select count(*) as nums from om_order_refund $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
		$query = self :: $dbConn->query($sql);
		$ret = self :: $dbConn->fetch_array($query);
		if ($ret) {
			self :: $errCode = "200";
			self :: $errMsg = "获取数据成功";
			return $ret['nums']; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 *取得指定表中的指定记录
	 *废弃使用
	 */
	public static function getOrderRefundList2($where) {
		self :: initDB();
		$sql = "select * from om_order_refund $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
		$query = self :: $dbConn->query($sql);
		$ret = self :: $dbConn->fetch_array_all($query);
		if ($ret) {
			self :: $errCode = "200";
			self :: $errMsg = "获取数据成功";
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return array(); //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 *取得指定表中的指定记录
	 */
	public static function getOrderRefundList($select, $where){
		self :: initDB();
		$sql = "select $select from ".self::$table." $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			self :: $errCode = "200";
			self :: $errMsg = "获取数据成功";
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 *取得指定表中的指定记录
	 */
	public static function getOrderInfo($tableName, $orderId, $storeId = 1){
		self :: initDB();
		$where = " WHERE id = $orderId and is_delete = 0 and storeId = ".$storeId;
		$orderList = OrderindexModel::showOrderList($tableName, $where);
		$orderData = $orderList[$orderId];
		
		$orderInfo = array();
		
		/*$table = " `om_unshipped_order` a , `om_unshipped_order_detail` b , `om_unshipped_order_extension_ebay` c,`om_unshipped_order_userInfo` d,`om_platform` e ";
        $field = " a.id,a.recordNumber,a.accountId,a.platformId,a.ordersTime,a.paymentTime,a.actualTotal,a.calcShipping,b.sku,b.amount,b.itemPrice,c.PayPalPaymentId,c.currency,d.platformUsername,e.id as platformId,e.platform ";
        $where = " WHERE a.id = '$id' AND a.id = b.omOrderId AND a.id = c.omOrderId AND a.id = d.omOrderId AND a.platformId = e.id ";*/
		
		if($orderData){
			$platformListById = omAccountModel::platformListById();
			$orderInfo['id'] = $orderId;
			$orderInfo['recordNumber'] = $orderData['orderData']['recordNumber'];
			$orderInfo['accountId'] = $orderData['orderData']['accountId'];
			$orderInfo['platformId'] = $orderData['orderData']['platformId'];
			$orderInfo['ordersTime'] = $orderData['orderData']['ordersTime'];
			$orderInfo['paymentTime'] = $orderData['orderData']['paymentTime'];
			$orderInfo['actualTotal'] = $orderData['orderData']['actualTotal'];
			$orderInfo['calcShipping'] = $orderData['orderData']['calcShipping'];
			$orderInfo['countryName'] =$orderData['orderUserInfoData']['countryName'];
			$orderInfo['PayPalPaymentId'] = $orderData['orderExtenData']['PayPalPaymentId'];
			$orderInfo['currency'] = $orderData['orderExtenData']['currency'];
			$orderInfo['platformUsername'] = $orderData['orderUserInfoData']['platformUsername'];
			$orderInfo['platform'] = $platformListById[$orderData['orderData']['platformId']];
			$orderInfo['detail'] = array();
			if($orderData['orderDetail']){
				foreach($orderData['orderDetail'] as $detailData){
					//$detail = array();
					$detail['sku'] = $detailData['orderDetailData']['sku'];
					$detail['amount'] = $detailData['orderDetailData']['amount'];
					$detail['itemPrice'] = $detailData['orderDetailData']['itemPrice'];
					$orderInfo['detail'][] = $detail;
				}
			}
			
			//$accountId = $orderInfo['accountId'];
			if(!$orderInfo['accountId']){
				self::$errCode	= 004;
				self::$errMsg	= '对应账号ID为空！';
				return FALSE;
			}
			if($orderInfo['platformId'] == 1){
				//$accountInfo = OrderRefundModel::getTNameList($table, $field, $where);
				$accountInfo = self::getAccountInfo($orderInfo['accountId']);
				if(!$accountInfo) {
					self::$errCode  = 005;
					self::$errMsg   = '没有对应PayPal账号信息！'; 
					return FALSE;
				}
				
				$orderInfo['paypalAccount1'] = $accountInfo['account1'];
				$orderInfo['pass1']          = $accountInfo['pass1'];
				$orderInfo['signature1']     = $accountInfo['signature1'];
				$orderInfo['paypalAccount2'] = $accountInfo['account2'];
				$orderInfo['pass2']          = $accountInfo['pass2'];
				$orderInfo['signature2']     = $accountInfo['signature2'];
			}
		}
		self::$errCode  = 200;
        self::$errMsg   = '获取信息成功';
		return $orderInfo;
	}
	
	/*
	 * 获取账号和paypal付款信息
	 */
	public static function getAccountInfo($accountId) {
		self :: initDB();
		$sql = "SELECT b.account1, b.pass1, b.signature1, b.account2, b.pass2, b.signature2
				FROM `om_account` a JOIN `om_paypal` b 
				ON a.account = b.ebayaccount
				WHERE a.id = '$accountId' ";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/*
	 *取得指定表中的指定记录记录数
	 *废弃
	 */
	public static function getTNameCount($tName, $where) {
		self :: initDB();
		$sql = "select count(*) count from $tName $where";
        //echo $sql;
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
		$sql = "INSERT INTO $tName SET $set";
       //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$insertId = self :: $dbConn->insert_id($query);
			return $insertId; //成功， 返回插入的id
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败! sql= $sql ";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *修改指定表记录
	 *要废弃
	 */
	public static function updateTNameRow($tName, $set, $where) {
		self :: initDB();
		$sql = "UPDATE $tName SET $set $where";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			self :: $errCode = "200";
			self :: $errMsg = "修改成功!";
		    return true;	
			//$affectRows = self :: $dbConn->affected_rows($query);
			//return $affectRows; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败! sql= $sql ";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 *修改指定表记录
	 */
	public static function updateOrderRefund($set, $where) {
		self :: initDB();
		$sql = "UPDATE ".self::$table." SET $set $where";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			self :: $errCode = "200";
			self :: $errMsg = "修改成功!";
		    return true;
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败! sql= $sql ";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}


	//获取退款所有信息
	public static function getAllOrderRefundList($where){
		self :: initDB();
		$sql = "SELECT a.*,b.sku,b.amount,b.actualPrice FROM om_order_refund AS a LEFT JOIN om_order_refund_detail AS b ON a.id = b.orderRefundId ".$where;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			self :: $errCode = "200";
			self :: $errMsg = "修改成功!";
		    return true;
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败! sql= $sql ";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

}
?>
