<?php
/**
*类名:FeedbackAPIModel
*说明:对接feedback系统数据操作类
*author:王民伟
*date:2014-02-28
**/
class FeedbackAPIModel{
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg  = '';

	public function initDB(){
		global $dbConn;
		self::$dbConn = $dbConn;
	}

	/**
	*说明:更新订单评价
	*author:王民伟
	*date:2014-02-28
	*/
	public static function updFeedBack($comUserId, $itemId, $tranId, $comType){
		self::initDB();
		$reviews   = 0;//1:好评,2:中评,3:差评
		switch($comType){
			case 'Positive':
				$reviews = 1;
				break;
			case 'Neutral':
				$reviews = 2;
				break;
			case 'Negative':
				$reviews = 3;
				break;
			default:
				$reviews = 0;
				break;
		}
		$unSql   	 = "SELECT a.omOrderId, c.sku, c.amount FROM om_unshipped_order_userInfo AS a ";
		$unSql 		.= "JOIN om_unshipped_order_detail_extension_ebay AS b ON a.omOrderId = b.omOrderdetailId ";
		$unSql 		.= "JOIN om_unshipped_order_detail AS c ON c.omOrderId = a.omOrderId ";
		$unSql 		.= "WHERE a.platformUsername = '{$comUserId}' AND b.transId = '{$tranId}' AND b.itemId = '{$itemId}'";
		$unQuery 	 = self::$dbConn->query($unSql);
		if($unQuery){
			$unRtnData = self::$dbConn->fetch_array_all($unQuery);
			if(isset($unRtnData)){//找到数据,更新订单评价
				$orderId 	= $unRtnData[0]['omOrderId'];
				$sku     	= $unRtnData[0]['sku'];
				$unUpdSql  	= "UPDATE om_unshipped_order_detail set reviews = '{$reviews}' WHERE omOrderId = '{$orderId}' AND sku = '{$sku}'";
				$unRtnUpd  	= self::$dbConn->query($unUpdSql);
				if($unRtnUpd) {
					return $unRtnData;//返回料号、数量反写Feedback系统
				}else{
					return false;
				}
			}else{
				$sql   	 = "SELECT a.omOrderId, c.sku, c.amount FROM om_shipped_order_userInfo AS a ";
				$sql 	.= "JOIN om_shipped_order_detail_extension_ebay AS b ON a.omOrderId = b.omOrderdetailId ";
				$sql 	.= "JOIN om_shipped_order_detail AS c ON c.omOrderId = a.omOrderId ";
				$sql 	.= "WHERE a.platformUsername = '{$comUserId}' AND b.transId = '{$transId}' AND b.itemId = '{$itemId}'";
				$query 	 = self::$dbConn->query($Sql);
				if($query){
					$rtnData    = self::$dbConn->fetch_array_all($query);
					$orderId 	= $rtnData[0]['omOrderId'];
					$sku     	= $rtnData[0]['sku'];
					$updSql  	= "UPDATE om_shipped_order_detail set reviews = '{$reviews}' WHERE omOrderId = '{$orderId}' AND sku = '{$sku}'";
					$rtnUpd  	= self::$dbConn->query($updSql);
					if($rtnUpd){
						return $rtnData;//返回料号、数量反写Feedback系统
					}else{
						return false;
					}
				}else{
					self::$errCode = "001";
					self::$errMsg  = "获取数据失败";
					return false;
				}
			}
		}else{
			self::$errCode = "001";
			self::$errMsg  = "获取数据失败";
			return false;
		}
	}
}