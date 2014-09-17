<?php
/*
 * 名称：OrderModifyAct
 * 功能：订单修改查看操作
 * 版本：v 1.0
 * 日期：2013/09/12
 * 作者：zyp
 * */
class OrderModifyModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	public static $dbPrefix = "";
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		self::$dbPrefix = "om_";
		mysql_query('SET NAMES UTF8');
	}
	public static function index($tableName,$where){//数据查找
		!self::$dbConn ? self::initDB() : NULL;
		
		$set  = "";//???
		//var_dump(array($id, $ostatus));
		//if($ostatus != C('STATEHASSHIPPED')){
			/*$orderForm					=	'om_unshipped_order';
			$orderDetailForm			=	'om_unshipped_order_detail';
			$orderDetailExtensionForm	=	'om_unshipped_order_detail_extension';
			$orderExtensionForm			=	'om_unshipped_order_extension';
			$orderUserInfoForm			=	'om_unshipped_order_userInfo';
			$orderWarehouseForm			=	'om_unshipped_order_warehouse';*/
		/*} else {
			$orderForm					=	'om_shipped_order';
			$orderDetailForm			=	'om_shipped_order_detail';
			$orderDetailExtensionForm	=	'om_shipped_order_detail_extension';
			$orderExtensionForm			=	'om_shipped_order_extension';
			$orderUserInfoForm			=	'om_shipped_order_userInfo';
			$orderWarehouseForm			=	'om_shipped_order_warehouse';
		}*/
		$platfrom = omAccountModel::getPlatformSuffixById($orderData['platformId']);
		$extension = $platfrom['suffix'];//获取后缀名称
		$orderExtensionForm .= '_'.$extension;
		$orderDetailExtensionForm .= '_'.$extension;
		
		$sql = "SELECT
					da.*,
					db.reviews,
					db.sku,
					db.amount,
					db.itemPrice,
					db.id AS datailId,
					dc.*,
					dd.platform,
					de.transId,
					de.PayPalPaymentId,
					de.feedback,
					de.currency,
					de.PayPalEmailAddress,
					df.account,
					df.email AS accountEmail,
					dh.content,
					dh.userId,
					dh.createdTime,
					di.actualWeight,
					di.actualShipping
				FROM
					".$orderForm." AS da
				LEFT JOIN ".$orderDetailForm." AS db ON db.omOrderId = da.id
				LEFT JOIN ".$orderUserInfoForm." AS dc ON dc.omOrderId = da.id
				LEFT JOIN om_platform AS dd ON dd.id	= da.platformId
				LEFT JOIN ".$orderExtensionForm." AS de ON de.omOrderId	= da.id
				LEFT JOIN om_account AS df ON df.id	= da.accountId
				LEFT JOIN om_order_notes AS dh ON dh.omOrderId	= da.id
				LEFT JOIN ".$orderWarehouseForm." AS di ON di.omOrderId	= da.id
				WHERE
					da.id	=	".$id;
		//echo $sql; echo "<br>";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}
	public static function judegLock($id){//数据查找
		!self::$dbConn ? self::initDB() : null;
		$set  = "";//???
		$sql = "SELECT * FROM om_unshipped_order WHERE id	=	".$id;
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret	=	self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			self :: $errCode = "002";
			self :: $errMsg = "查不到该订单!";
			return false;
		}
	}
	
	/**
	 *修改指定表记录(只要sql运行成功就算成功)
	 */
	public static function updateTName($tName, $set, $where) {
		self :: initDB();
		$sql = "UPDATE $tName $set $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			return true; //成功， 返回真
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 *修改指定表记录(只要sql运行成功就算成功)
	 */
	public static function batchMove($data, $where) {
		self :: initDB();
		$string = array2sql($data);
		$tName = 'om_unshipped_order';
		$sql = "UPDATE $tName SET $string $where";
		//echo $sql; exit;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			self :: $errCode = "200";
			self :: $errMsg = "修改成功";
			return true; //成功， 返回真
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
}
?>