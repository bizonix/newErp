<?php
/*
 * 名称：markShippedModel
 * 功能：订单状态日志，操作日志
 * 版本：v 1.0
 * 日期：2013/12/10
 * 作者：Herman.xi
 * */
class MarkShippingModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	'';
	public	static $orderLogTable	=	'om_mark_shipping';
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	/*
	 * 插入超大订单拆分记录(最新版)
	 * last modified by Herman.Xi @20131205
	 */
	public static function insertOrderLog($omOrderId, $note){
		!self::$dbConn ? self::initDB() : null;
		$data = array('operatorId'=>$_SESSION['sysUserId'], 'omOrderId'=>$omOrderId, 'note' => $note, 'createdTime'=>time());
        $string = array2sql($data);
		//var_dump($string); exit;
		$sql = "INSERT INTO `".self::$orderLogTable."` SET ".$string;
		//echo $sql; exit;
		$query	=	self::$dbConn->query($sql);
		if($query){
			self :: $errCode = "200";
			self :: $errMsg = "插入成功";
			return true;
		}else{
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	public static function insert_mark_shipping($omOrderId){
		self::initDB();
		$sql = "SELECT omOrderId FROM om_mark_shipping WHERE omOrderId={$omOrderId}";
		$handle	= self::$dbConn->query($sql);
		$num    = self::$dbConn->num_rows($handle);
		$datetime = time();
		if($num==0){
			$sql        = "SELECT orderStatus,accountId FROM om_unshipped_order WHERE id={$omOrderId}";
			$handle	    = self::$dbConn->query($sql);
			$order_info	= self::$dbConn->fetch_array($handle);
			
			if (empty($order_info)){
				return false;
			}
			
			$sql = "INSERT INTO om_mark_shipping SET omOrderId={$omOrderId}, orderStatus={$order_info['orderStatus']}, account='{$order_info['accountId']}', addTime='{$datetime}'";
			self::$dbConn->query($sql);
		}
		return true;
	}
	
	public static function pop_mark_shipping_order($omOrderId,$account){
		self::initDB();
		$sql='UPDATE om_mark_shipping SET status=1 WHERE omOrderId='.$omOrderId.' AND account="'.$account.'" AND status = 0 ';
		self::$dbConn->query($sql);
		return true;
	}
	
	public static function update_order_shippedmarked_time($omOrderId){
		global $mctime;
		$tableName = "om_unshipped_order";
		$where = " where id = {$omOrderId} and storeId = 1 and is_delete = 0 ";
		$returnStatus0 = array('marketTime'=>$mctime);
		return OrderindexModel::updateOrder($tableName,$returnStatus0,$where);
	}
	
	//标记发货函数
	public static function just_mark_order_shipped($tran_datas){
		global $api_cs,$mctime;
		//self::initDB();
		//获取订单明细
		/*$order_detail_sql='SELECT 	ebay_itemid,sku,ebay_tid FROM ebay_orderdetail 
						   WHERE	ebay_ordersn="'.$ebay_ordersn.'" ';
		
		$order_detail	= self::$dbConn->query($order_detail_sql);
		$order_detail	= self::$dbConn->fetch_array_all($order_detail);*/
		var_dump($tran_datas);//标记发货已完成暂时不开放
		return true;
		foreach($tran_datas as $tran_data){
			$sku = $tran_data['sku'];
			$tran = $tran_data['tran'];
			$itemid = $tran['itemid'];
			$tid = $tran['tid'];
			echo "itemid:".$itemid."\t tid:".$tid."\t sku:".$sku."\n";		
			
			$mark_res=$api_cs->just_mark_order_shipped($tran);
			
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($mark_res);
			
			$Ack = $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
			
			if($Ack == "Success"){	
				echo "  已标记发出\n";
				return true;
				//$dbConn->query($sb);
			}else{
				return false;
				echo "  标记发出失败 ACK=$Ack \n";
			}
		}
	}
	
	//更新发货信息(trackno)到ebay
	public static function update_order_shippingdetail_to_ebay($tran_datas){
		global $api_cs,$mctime;
		//获取订单明细
		/*$order_detail_sql='SELECT 	ebay_itemid,sku,ebay_amount,ebay_tid	FROM ebay_orderdetail 
						   WHERE	ebay_ordersn="'.$ebay_ordersn.'" ';
		
		$order_detail	= $dbConn->query($order_detail_sql);
		$order_detail	= $dbConn->fetch_array_all($order_detail);*/
		
		var_dump($tran_datas);//上传跟踪号已完成暂时不开放
		return true;
		foreach($tran_datas as $tran_data){
			$sku = $tran_data['sku'];
			$tran = $tran_data['tran'];
			$itemid = $tran['itemid'];
			$tid = $tran['tid'];
			$ebay_carrier = $tran['ebay_carrier'];
			$ebay_tracknumber = $tran['ebay_tracknumber'];
			
			echo "itemid:".$itemid."\t tid:".$tid."\t sku:".$sku."\n";
			echo "carrier:".$ebay_carrier."\t trackno:".$ebay_tracknumber."\n";
			
			$mark_res=$api_cs->update_order_shippingdetail_to_ebay($tran);
			
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($mark_res);
			
			$Ack	 	= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
			
			if($Ack == "Success"){						
				echo "  更新shippingdetail成功\n";
				return true;
			}else{
				echo "  更新shippingdetail失败 ACK=$Ack \n";
				return false;
			}
		}
	}
	//更新发货信息订单编号到ebay
	public static function update_ebayid_shippingdetail_to_ebay($tran_datas){
		global $api_cs,$mctime;
		//获取订单明细
		/*$order_detail_sql='SELECT 	ebay_itemid,sku,ebay_amount,ebay_tid	FROM ebay_orderdetail 
						   WHERE	ebay_ordersn="'.$ebay_ordersn.'" ';
		
		$order_detail	= $dbConn->query($order_detail_sql);
		$order_detail	= $dbConn->fetch_array_all($order_detail);*/
		
		var_dump($tran_datas);//上传跟踪号已完成暂时不开放
		return true;
		foreach($tran_datas as $tran_data){
			$sku = $tran_data['sku'];
			$tran = $tran_data['tran'];
			$itemid = $tran['itemid'];
			$tid = $tran['tid'];
			$ebay_carrier = $tran['ebay_carrier'];
			$ebay_tracknumber = $tran['ebay_tracknumber'];
			
			echo "itemid:".$itemid."\t tid:".$tid."\t sku:".$sku."\n";
			echo "carrier:".$ebay_carrier."\t trackno:".$ebay_tracknumber."\n";
			
			$mark_res=$api_cs->update_order_shippingdetail_to_ebay($tran);
			
			$responseDoc = new DomDocument();	
			$responseDoc->loadXML($mark_res);
			
			$Ack	 	= $responseDoc->getElementsByTagName('Ack')->item(0)->nodeValue;
			
			if($Ack == "Success"){			
				echo "  更新shippingdetail成功\n";
				return true;
			}else{
				echo "  更新shippingdetail失败 ACK=$Ack \n";
				return false;
			}
		}
	}
	
}
?>