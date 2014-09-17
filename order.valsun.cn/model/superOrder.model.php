<?php
/*
 * 名称：SuperOrderModel
 * 功能：对超大订单的处理
 * 版本：v 1.0
 * 日期：2013/09/11
 * 作者：wxb
 * */
class SuperOrderModel{
	public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	public static $dbPrefix = "";
	
	public function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
		self::$dbPrefix = "om_";
		mysql_query('SET NAMES UTF8');
	}		
	public static function index($idStr){//超大订单确认
		!self::$dbConn ? self::initDB() : null;
		$set  = " orderStatus = '".C('STATEOVERSIZEDORDERS')."',orderType = '".C('STATEOVERSIZEDORDERS_PEND')."' ";//???
		$sql = "UPDATE ".self::$dbPrefix."unshipped_order  SET {$set}";
		$sql .= "WHERE is_delete = '0' AND id in({$idStr})";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$num =self::$dbConn->affected_rows($query);
			$numNeed = substr_count($idStr,",")+1;
			if($num > $numNeed){
				return  false;	//成功， 返回影响行数
			}
			return true;
		}else{
			return false;
		}
	}
	public static function api_getOrdIdByDetWhe($whereDetail){//通过详情获取订单的id 数组
		!self::$dbConn ? self::initDB() : null;
		$whereDetail = "WHERE is_delete = '0' AND ".$whereDetail;
		$sql = "SELECT omOrderId FROM ".self::$dbPrefix."unshipped_order_detail {$whereDetail}";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if(!$ret){
				return false;
			}
		}else{
			return false;
		}
		$orderId = array();
		foreach($ret as $retVal){
			$orderId[] = $retVal["omOrderId"];
		}
		if(empty($orderId)){
			return false;
		}
		return $orderId;
	}
	
	public static function showOrderAPI($purchaseId, $storeId = 1){ // 提供超大订单数据
		!self::$dbConn ? self::initDB() : null;
		//echo time(); echo "<br>";
		//!self::$dbConn ? self::initDB() : null;
		//取出两个where 下的所有订单
		/*if(!empty($whereDetail)){
			$orderIdStr =implode(",",self::api_getOrdIdByDetWhe($whereDetail));
		}
		if(!empty($whereOrder)){
			$whereOrder  = "AND ".$whereOrder;
		}*/
		/*if(!empty($orderIdStr)){
			$whereOrder .= " AND  id in ({$orderIdStr})";
		}*/
		
		$showOrder   = array();
		$ordersql 	 =  'SELECT         a.id as orderid, a.accountId, b.sku, b.amount
						FROM 			om_unshipped_order AS a 
						LEFT JOIN       om_unshipped_order_detail AS b 
						ON 			    b.omOrderId = a.id 
						WHERE			a.orderStatus = '.C('STATEOVERSIZEDORDERS').' 
						AND				a.orderType   != '.C('STATEOVERSIZEDORDERS_CONFIRM').' 
						AND 			a.is_delete=0 
						AND				b.is_delete=0 
						AND 			a.storeId= '.$storeId;
		//echo $ordersql; echo "<br>"; exit;
		$query	     =	self::$dbConn->query($ordersql);
		$orders      =	self::$dbConn->fetch_array_all($query);
		//echo count($orders); exit;
		//echo time(); echo "<br>"; exit;
		foreach($orders as $ordervalue){
			$orderid = $ordervalue['orderid'];
			$sku = $ordervalue['sku'];
			//$amount = $ordervalue['amount'];
			//echo $sku; echo "<br>";
			$skus = GoodsModel::get_realskuinfo($sku);
			//var_dump($skus); echo "<br>"; exit;
			foreach($skus as $_sku => $_num){
				$_skuinfo = GoodsModel::getSkuinfoByPurchaseId($_sku, $purchaseId);
				//var_dump($_skuinfo); echo "<br>"; exit;
				if($_skuinfo){
					$auditRecord = CommonModel::getRecordsOrderAudit($orderid, $_sku);
					$accountInfo = OmAccountModel::accountInfo($accountId);
					//var_dump($accountInfo); echo "<br>"; exit;
					//echo $_sku; echo "<br>";
					$nosaleand = CommonModel::getpartsaleandnosendall($_sku);
					//var_dump($nosaleand); echo "<br>"; exit;
					$ordervalue['accountId'] = $accountInfo['account'];
					$ordervalue['sku'] = $_skuinfo;
					$ordervalue['auditRecord'] = $auditRecord;
					$ordervalue['nosaleand'] = $nosaleand;
					//var_dump($ordervalue); echo "<br>";
					$showOrder[]   = $ordervalue;
				}
			}
		}
		if(!empty($showOrder)){
			return json_encode($showOrder);	
		}else{
			return false;	
		}  
	}
	
	/*
	 * 功能：审核订单
	 * 新建接口 add by Herman.Xi @ 20131121
	 * orderid 订单id
	 * sku 料号
	 * type = 1  审核操作， 2 添加留言
	 * status = 1 审核通过， 2 拦截
	 * purchaseId 采购人员id
	 * note 留言内容
	 * */
	public static function auditOrder($orderid, $sku, $type, $status, $purchaseId, $note = '', $storeId = 1){
		//exit;
		!self::$dbConn ? self::initDB() : NULL;
		$res = array();
		//var_dump($orderid, $sku, $type, $status, $purchaseId);
		$ordercheck		= "select * from om_unshipped_order where id = {$orderid} and is_delete = 0 and storeId = ".$storeId;	
		$ordercheck		= self::$dbConn->query($ordercheck);
		$ordercheck		= self::$dbConn->fetch_array($ordercheck);
		if(empty($ordercheck)){
			self::$errCode	= 502;
			self::$errMsg	= '未找到对应的订单!';
			return false;
		}else{
			$notecontent = '';
			if(!empty($note)){
				$notecontent = " , note = '{$note}' ";
			}
			$c_sql = "select * from om_records_order_audit where omOrderId={$orderid} and sku='{$sku}' ";
			//echo $c_sql;
			$c_sql = self::$dbConn->query($c_sql);
			$c_sql =self::$dbConn->fetch_array_all($c_sql);
			//var_dump($c_sql);
			if(empty($c_sql)){
				$sql = "INSERT INTO om_records_order_audit SET omOrderId={$orderid},sku='{$sku}',auditUser='{$purchaseId}',auditTime=".time().",lastModified=".time().",auditStatus='{$status}'".$notecontent;
				//echo $sql;
			}else{
				$sql = "UPDATE om_records_order_audit SET lastModified=".time().",auditStatus='{$status}'".$notecontent." WHERE omOrderId={$orderid} AND sku='{$sku}'";
			}
			if(self::$dbConn->query($sql)){
				//echo "insert auit log";	echo "<br>";
			}
			//BaseModel :: begin(); //开始事务
			/*if($type=='1'){*/
				$ch_sql = "SELECT a.sku,a.omOrderId as detail_id FROM om_unshipped_order_detail AS a WHERE a.omOrderId='{$orderid}' ";
				$ch_sql = self::$dbConn->query($ch_sql);
				$check_array =self::$dbConn->fetch_array_all($ch_sql);
				
				$isend     = true;
				$status_arr    = array();
				$array_sku = array();
				//echo "<pre>";
				//var_dump($check_array);
				foreach($check_array AS $check_sku){
					$array_sku = GoodsModel::get_realskuinfo($check_sku['sku']);
					//var_dump($array_sku);
					foreach($array_sku as $key_sku=>$num){
						$compare_sql = "SELECT sku,auditStatus FROM om_records_order_audit WHERE omOrderId='{$orderid}' and sku='{$key_sku}' ";
						//echo $compare_sql; echo "<br>";
						$compare_sql = self::$dbConn->query($compare_sql);
						$compare_sql =self::$dbConn->fetch_array($compare_sql);
						if (empty($compare_sql)){
							$isend = false;
							break;
						}else if (!in_array($compare_sql['auditStatus'],$status_arr)){
							array_push($status_arr,$compare_sql['auditStatus']);
						}
					}
				}
				//var_dump($isend);
				//var_dump($status_arr); exit;
				if ($isend){
					if (in_array(1, $status_arr)&&in_array(2, $status_arr)){
						$updateArr = array('orderStatus'=> C('STATEOVERSIZEDORDERS'), 'orderType'=> C('STATEOVERSIZEDORDERS_PA'));
					}else if (in_array(1, $status_arr)){
						if($ordercheck['calcWeight'] > 2){
							$updateArr = array('orderStatus'=> C('STATEPENDING'), 'orderType'=> C('STATEPENDING_OW'));
						}else{
							$updateArr = array('orderStatus'=> C('STATEOVERSIZEDORDERS'), 'orderType'=> C('STATEOVERSIZEDORDERS_TA'));
						}
					}else if (in_array(2, $status_arr)){
						$updateArr = array('orderStatus'=> C('STATEOVERSIZEDORDERS'), 'orderType'=> C('STATEOVERSIZEDORDERS_WB'));
					}/*else{
						
					}*/
					//var_dump($updateArr);
					//$sql = "UPDATE om_unshipped_order SET ".array2sql($updateArr)." where id={$orderid} AND orderStatus = '".C('STATEOVERSIZEDORDERS')."' AND orderType = '".C('STATEOVERSIZEDORDERS_PEND')."' AND is_delete = 0 ";
					$sql = "UPDATE om_unshipped_order SET ".array2sql($updateArr)." where id={$orderid} AND orderStatus = '".C('STATEOVERSIZEDORDERS')."' AND is_delete = 0 ";
					//$log_data .= $sql."\n";
					if(self::$dbConn->query($sql)){
						//echo "update order success";
					}/*else{
						echo "update order error";	
					}*/
					//BaseModel :: commit();
					//BaseModel :: autoCommit();
				}else{
					/*self::$errCode	= 040;
					self::$errMsg	= '审核异常,有审核通过!';
					return false;*/
					//$log_data .= "isend is false!\n";
				}
			/*}else */if($type=='2'){
				$sql = "UPDATE om_records_order_audit SET lastModified=".time().",note='{$note}' WHERE omOrderId={$orderid} AND sku='{$sku}' ";
				if(self::$dbConn->query($sql)){
					//BaseModel :: commit();
					//BaseModel :: autoCommit();
					self::$errCode	= 200;
					self::$errMsg	= '添加备注成功!';
					return true;
				}else{
					self::$errCode	= 003;
					self::$errMsg	= '添加备注失败!';
					return false;
				}
			}
			self::$errCode	= 200;
			self::$errMsg	= '审核完成!';
			return true;
		}
	}
	
	/*
	 * 功能：select 查询单表
	 * @$where sql语句的其它部分
	 * return 返回二结果
	 * 不支持关联查询
	 * */
	public static function selectResult($table,$fields,$where = ''){
		!self::$dbConn ? self::initDB() : null;
		if(!empty($where)){
			$where = " WHERE ".$where; 
		}
		$sql = " SELECT {$fields} FROM {$table}  {$where}";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			return false;
		}
	}
	/*
	 * 单独插入某条数据
	 * */
	public static function InsertIntOne($table,$valBef,$valAft){
		!self::$dbConn ? self::initDB() : null;

		$sql = "INSERT INTO `{$table}` ( {$valBef} ) VALUES ( {$valAft} ) ";
		$query	 =	self::$dbConn->query($sql);
// 		echo $sql;
		if($query){
			$num =self::$dbConn->affected_rows();
			if($num==1){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public static function updateOne($table,$set,$where = ''){
		!self::$dbConn ? self::initDB() : null;
		if(!empty($where)){
			$where = "WHERE ".$where;
		}
		$sql = "UPDATE {$table} SET {$set} {$where}";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$num =self::$dbConn->affected_rows();
			if($num==1){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	/**
	 * 获取超大部分审核通过订单信息
	 * */
	public static function partPackage($idStr){
		!self::$dbConn ? self::initDB() : null;
		$sql = "SELECT uo.id AS orderId,uo.recordNumber AS ordeRecNum,uo.storeId AS orderStoreId,uo.is_delete AS order_is_delete,uo.*,uod.*  FROM ".self::$dbPrefix."unshipped_order AS uo LEFT JOIN ";
		$sql .= self::$dbPrefix."unshipped_order_detail AS uod ON uod.omOrderId = uo.id   ";
		$sql .= " WHERE uo.is_delete = '0' AND uod.is_delete = '0' AND uo.id in ({$idStr})";
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			if($ret){
				return $ret;
			}
				return false;
		}
		return false;
	}

}
?>