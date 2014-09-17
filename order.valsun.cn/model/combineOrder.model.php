<?php
/*
* 合并订单
* ADD BY chenwei 2013.9.11
*/
class CombineOrderModel{	
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";

	//db初始化
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		mysql_query('SET NAMES UTF8');
	}
	
	//合并订单操作
	public static function combineOrder($orderIdArr){
		self::initDB();
		BaseModel::begin();	//开始事务
		
		$serchSql 		  = "SELECT * FROM om_unshipped_order WHERE id in ('".join("','",$orderIdArr)."') and is_delete = 0 and storeId = 1 ";
		$querySql 		  =  self::$dbConn->query($serchSql);
		$serchSqlArr	  =  self::$dbConn->fetch_array_all($querySql);
		
		//判断一：订单数量统计
		if(count($serchSqlArr)<2){
			self :: $errCode = "1111";
			self :: $errMsg  = "合并订单最少需要选择两个或两个以上的订单!";
			return false;
		}
		
		$platfrom = omAccountModel::getPlatformSuffixById($serchSqlArr[0]['platformId']);
		$extension = $platfrom['suffix'];//获取后缀名称
		
		$temporderStatus = "";//相同状态一
		$temporderStatus2 = "";//相同状态二 
		$userinfo       = array();//订单相同条件 
		$orderSn		= array();//订单编号
		$onlineTotal	= 0;//线上总价
		$actualTotal	= 0;//实际收款总价
		$calcWeight     = 0;//估算重量，单位是kg
		$calcShipping   = 0;//估算运费
		foreach($serchSqlArr as $selectArr){
			$orderSn[] 		= $selectArr['id'];
			$onlineTotal 	+= 	$selectArr['onlineTotal'];
			$actualTotal	+= 	$selectArr['actualTotal'];
			$calcWeight		+=  $selectArr['calcWeight'];
			$calcShipping   +=  $selectArr['calcShipping'];
			
			//判断二：订单被其他人 <锁定> 订单判断
			if($selectArr['isLock'] == 1){
				self :: $errCode = "2222";
				self :: $errMsg  = "订单[".$selectArr['id']."]已经被 [".UserModel::getUsernameById($selectArr['lockUser'])."] 锁定，不能合并操作。";
				return false;
			}
			
			//判断三：已合并订单，无法再次合并判断
			if(in_array($selectArr['combineOrder'], array(1,2))){
				self :: $errCode = "3333";
				self :: $errMsg  = "订单[".$selectArr['id']."]已经有订单合并操作，不能重复订单合并。";
				return false;
			}
			
			//判断四：已合并包裹订单，无法合并判断
			if(in_array($selectArr['combinePackage'], array(1,2))){
				self :: $errCode = "4444";
				self :: $errMsg  = "订单[".$selectArr['id']."]是合并包裹订单，不能订单合并操作。";
				return false;
			}
			
			//判断五：订单信息不相同判断
			$userinfsql 		  = "SELECT * FROM om_unshipped_order_userInfo WHERE omOrderId = {$selectArr['id']}";
			$userinfsql	  		  =  self::$dbConn->fetch_first($userinfsql);
			$tempArr = array();			
			$tempArr['accountId'] = trim($selectArr['accountId']);
			$tempArr['platformUsername'] = trim($userinfsql['platformUsername']);
			$tempArr['username'] = trim($userinfsql['username']);
			$tempArr['countryName'] = trim($userinfsql['countryName']);
			$tempArr['state'] = trim($userinfsql['state']);
			$tempArr['city'] = trim($userinfsql['city']);
			$tempArr['street'] = trim($userinfsql['street']);
			$tempArr['currency'] = trim($userinfsql['currency']);//币种判断
			
			if(!empty($userinfo) && $userinfo != $tempArr){
				self :: $errCode = "5555";
				self :: $errMsg  = "订单信息不相同，无法合并订单操作。";
				return false;
			}
			$userinfo = $tempArr;//订单信息相同，进入下次比较。
			
			//判断六：同状态判断
			$orderStatus    = "";//订单状态一
			$orderType      = "";//订单状态二
			$orderStatus = $selectArr['orderStatus'];
			$orderType = $selectArr['orderType'];
			if(!empty($temporderStatus) && $temporderStatus != $orderStatus){
				self :: $errCode = "6666";
				self :: $errMsg  = "订单不在同一文件夹，无法合并订单操作。";
				return false;
			}
			$temporderStatus = $orderStatus;
			
			if(!empty($temporderStatus2) && $temporderStatus2 != $orderType){
				self :: $errCode = "6666";
				self :: $errMsg  = "订单不在同一文件夹，无法合并订单操作。";
				return false;
			}
			$orderExtensql 		  = "SELECT * FROM om_unshipped_order_extension_".$extension." WHERE omOrderId = {$selectArr['id']}";
			$orderExtensql	  	  =  self::$dbConn->fetch_first($orderExtensql);
			
			$temporderStatus2 = $orderType;
		}
		$insertOrder = array();
		
		$insertOrder['orderData'] = $serchSqlArr[0];
		
		$insert_userinfo = $userinfsql;
		unset($insert_userinfo['omOrderId']);
		$insertOrder['orderUserInfoData'] = $insert_userinfo;
		
		$insert_orderExtensql = $orderExtensql;
		unset($insert_orderExtensql['omOrderId']);
		$insertOrder['orderExtenData'] = $insert_orderExtensql;
		//$insertOrder['orderNote'] = $userinfsql;
		unset($insertOrder['orderData']['id']);
		$insertOrder['orderData']['onlineTotal'] = $onlineTotal;
		$insertOrder['orderData']['actualTotal'] = $actualTotal;
		$insertOrder['orderData']['calcWeight'] = $calcWeight;
		$insertOrder['orderData']['calcShipping'] = $calcShipping;
		$insertOrder['orderData']['orderAddTime'] = time();
		$insertOrder['orderData']['combineOrder'] = 2;
		$insertOrder['orderData']['orderAttribute'] = 3;
		
		//$insertOrder['orderDetail'] = array();
		$detailSql 		  = "SELECT * FROM om_unshipped_order_detail WHERE omOrderId in ('".join("','",$orderIdArr)."') and is_delete = 0 and storeId = 1 ";
		$detailSql 		  =  self::$dbConn->query($detailSql);
		$detailSqlArr	  =  self::$dbConn->fetch_array_all($detailSql);
		$orderDetail = array();
		foreach($detailSqlArr as $value){
			//$orderDetailData = array();
			//$orderDetailExtenData = array();
			
			$obj_orderDetail = $value;
			unset($obj_orderDetail['id']);
			unset($obj_orderDetail['omOrderId']);
			$orderDetailData = $obj_orderDetail;
			
			$detailExtenSql 		  = "SELECT * FROM om_unshipped_order_detail_extension_".$extension." WHERE omOrderdetailId = '".$value['id']."' ";
			$detailExtenSql 		  =  self::$dbConn->query($detailExtenSql);
			$detailExtenSqlArr	  	  =  self::$dbConn->fetch_array($detailExtenSql);
			$obj_orderDetailExten 	  = $detailExtenSqlArr;
			unset($obj_orderDetailExten['omOrderdetailId']);
			$orderDetailExtenData 	  = $obj_orderDetailExten;
			
			$orderDetail[] = array('orderDetailData' => $orderDetailData,'orderDetailExtenData' => $orderDetailExtenData);
		}
		$insertOrder['orderDetail'] = $orderDetail;
		//var_dump($insertOrder); exit;
		if($insertId = OrderAddModel :: insertAllOrderRowNoEvent($insertOrder)){
			//echo $split_log .= 'insert success!' . "\n"; exit;
			//var_dump($_mainId,$_spitId); exit;
			if(!OrderLogModel::insertOrderLog($insertId, '合并产生新订单')){
				BaseModel :: rollback();
				self :: $errCode = '001';
				self :: $errMsg = "合并失败!";
				return false;
			}
			if(!OrderRecordModel::insertCombineRecord($serchSqlArr[0]['id'],$insertId)){
				BaseModel :: rollback();
				self :: $errCode = '002';
				self :: $errMsg = "合并订单失败添加记录失败!";
				return false;
			}
			$updateOrder = array();
			$updateOrder['is_delete'] = 1;
			$updateOrder['combineOrder'] = 1;
			if(!OrderindexModel::updateOrder("om_unshipped_order",$updateOrder," WHERE id in ('".join("','",$orderSn)."')")){
				BaseModel :: rollback();//事物回滚
				self :: $errCode = "0012";
				self :: $errMsg  = "合并更新原始订单失败!";
				return false;	
			}
		}else{
			//$split_log .= '补寄新订单产生失败!' . "\n";
			BaseModel :: rollback();
			self :: $errCode = '003';
			self :: $errMsg = "合并新订单产生失败";
			return false;
		}
		BaseModel::commit();
		self :: $errCode = '200';
		self :: $errMsg = "合并新订单成功！";
		return TRUE;
	}
}
?>