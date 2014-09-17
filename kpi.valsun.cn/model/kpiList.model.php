<?php
class KpiListModel{
    public static $dbConn;
    public static $errCode = 0;
    public static $errMsg = "";
    public function initDB(){
		global $dbConn;
		self::$dbConn	=	$dbConn;
    }
	public static function getScanRecordList($start,$end){
	    $start = strtotime($start);
		$end = strtotime($end);
	    self::initDB();
		$sql = "SELECT a.ebay_id,a.ebay_ordersn,a.is_main_order,a.is_sendreplacement,a.combine_package,a.scantime,a.ebay_currency,a.ebay_carrier,a.packagingstaff,a.packinguser,a.ebay_tracknumber,a.ebay_countryname,a.orderweight2,a.ebay_total,a.ebay_shipfee,b.user   
		        FROM ebay_order as a 
				LEFT JOIN ebay_order_scan_record as b on a.ebay_id=b.ebay_id 
				WHERE a.ebay_combine !='1' AND (a.scantime BETWEEN {$start} AND {$end}) AND a.ebay_status=2 AND  b.is_show=0 AND b.is_scan=1 
				";
		/*$sql = "SELECT a.ebay_id,a.sku,a.amount,a.user,b.scantime,b.ebay_total,b.orderweight2,b.ebay_carrier,b.ebay_currency,b.ebay_tracknumber,b.ebay_shipfee,b.ebay_ordersn,b.packagingstaff,b.packinguser  
				FROM ebay_order_scan_record as a left JOIN ebay_order as b on a.ebay_id=b.ebay_id 
				WHERE (a.scantime BETWEEN {$start} AND {$end}) AND b.ebay_status=2 ";*/
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			//self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;
		}
	}
	public static function getReviewListById($id){
		self::initDB();
		$sql = "SELECT user FROM ebay_order_review where ebay_id={$id}";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function getScanRecordById($id){
		self::initDB();
		$sql = "SELECT user FROM ebay_order_scan_record where ebay_id={$id} and is_show=0";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			self::$dbConn->free_result($query);
			return $ret['user'];
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function selectOrder($ebay_id){
		self::initDB();
		$sql = "SELECT ebay_id,ebay_ordersn,orderweight2,ordershipfee,ebay_carrier,packagingstaff,packinguser,ebay_tracknumber,ebay_countryname,ebay_currency,ebay_total,ebay_shipfee,scantime FROM ebay_order where ebay_id={$ebay_id}";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function getOrderDetailList($ebay_ordersn){
		self::initDB();
		$sql = "SELECT a.sku,a.ebay_amount,b.goods_location FROM ebay_orderdetail as a left join ebay_goods as b on a.sku=b.goods_sn WHERE a.ebay_ordersn='{$ebay_ordersn}'";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;		
		}
	}

	public static function getOrderOutList($start,$end){
		$start = strtotime($start);
		$end = strtotime($end);
		self::initDB();
		/*$sql = "SELECT a.ebay_id,a.sku,a.amount,a.user,b.scantime,b.ebay_total,b.orderweight2,b.ebay_carrier,b.ebay_currency,b.ebay_tracknumber,b.ebay_shipfee,b.ebay_ordersn,b.packagingstaff,b.packinguser  
				FROM ebay_order_scan_record as a left JOIN ebay_order as b on a.ebay_id=b.ebay_id 
				WHERE (a.scantime BETWEEN {$start} AND {$end}) AND b.ebay_status=2 group by a.ebay_id";
		*/
		/*$sql = "SELECT a.ebay_id,b.sku,b.amount,b.user,b.scantime,a.ebay_total,a.orderweight2,a.ebay_carrier,a.ebay_currency,a.ebay_tracknumber,a.ebay_shipfee,a.ebay_ordersn,a.packagingstaff,a.packinguser  
				FROM ebay_order as a 
				LEFT JOIN ebay_order_scan_record as b on a.ebay_id=b.ebay_id 
				WHERE a.ebay_combine !='1' AND (b.scantime BETWEEN {$start} AND {$end}) AND a.ebay_status=2 AND  b.is_show=0 
				";*/
		$sql = "SELECT ebay_id,sku,amount,user,scantime FROM ebay_order_scan_record WHERE scantime BETWEEN {$start} AND {$end} AND is_show=0 AND is_scan=1";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			
			return $ret;
			
		}else{ 
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function getOrderOutList_test($start,$end){
		$start = strtotime($start);
		$end = strtotime($end);
		self::initDB();
		$sql = "SELECT a.ebay_id,a.sku,a.amount,a.user,b.scantime,b.ebay_total,b.orderweight2,b.ebay_carrier,b.ebay_currency,b.ebay_tracknumber,b.ebay_shipfee,b.ebay_ordersn,b.packagingstaff,b.packinguser  
				FROM ebay_order_scan_record as a left JOIN ebay_order as b on a.ebay_id=b.ebay_id 
				WHERE (b.scantime BETWEEN {$start} AND {$end}) and b.ebay_carrier in ('中国邮政挂号','香港小包挂号','EUB','Global Mail','德国邮政挂号','新加坡小包挂号','中国邮政平邮','香港小包平邮','俄速通挂号','俄速通大包','俄速通平邮')";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			//echo $sql;
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{ 
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	
	//快递配货记录
	public static function getOrderOutList_express($start,$end){
		$start = strtotime($start);
		$end = strtotime($end);
		self::initDB();
		$sql = "SELECT a.ebay_id,a.sku,a.amount,a.user,b.scantime,b.ebay_total,b.orderweight2,b.ebay_carrier,b.ebay_currency,b.ebay_tracknumber,b.ebay_shipfee,b.ebay_ordersn,b.packagingstaff,b.packinguser  
				FROM ebay_order_scan_record as a left JOIN ebay_order as b on a.ebay_id=b.ebay_id 
				WHERE (a.scantime BETWEEN {$start} AND {$end}) and b.ebay_carrier not in ('中国邮政挂号','香港小包挂号','EUB','Global Mail','德国邮政挂号','新加坡小包挂号','中国邮政平邮','香港小包平邮','俄速通挂号','俄速通大包','俄速通平邮')";
		
		$query = self::$dbConn->query($sql);
		if($query){
			//echo $sql;
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{ 
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function getReviewList($start,$end){
		$start = strtotime($start);
		$end = strtotime($end);
		self::initDB();
		$sql = "SELECT a.ebay_id,a.sku,a.user,a.amount,a.scantime FROM ebay_order_review as a  
				WHERE a.scantime BETWEEN {$start} AND {$end} and status=1";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	
	//复核记录
	public static function getReviewList_test($start,$end){
		$start = strtotime($start);
		$end = strtotime($end);
		self::initDB();
		$sql = "SELECT a.ebay_id,a.sku,a.user,a.amount,a.scantime,b.ebay_ordersn,b.ebay_total,b.orderweight2,b.ebay_carrier,b.ebay_currency,b.ebay_tracknumber,b.ebay_shipfee,b.ebay_ordersn,b.packagingstaff,b.packinguser 
				FROM ebay_order_review as a left join ebay_order as b on a.ebay_id=b.ebay_id 
				WHERE b.scantime BETWEEN {$start} AND {$end} and a.status=1 and b.ebay_carrier in ('中国邮政挂号','香港小包挂号','EUB','Global Mail','德国邮政挂号','新加坡小包挂号','中国邮政平邮','香港小包平邮','俄速通挂号','俄速通大包','俄速通平邮')";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function getPackageList($start,$end){
		$start = strtotime($start);
		$end = strtotime($end);
		self::initDB();
		$sql = "SELECT * FROM `ebay_order_scan_package` as a  
				WHERE a.scantime BETWEEN {$start} AND {$end} and is_scan=1";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function getOrderList($start,$end){
		$start = strtotime($start);
		$end = strtotime($end);
		self::initDB();
		$sql = "SELECT ebay_id,ebay_ordersn,orderweight2,ordershipfee,ebay_carrier,packagingstaff,packinguser,ebay_tracknumber,ebay_countryname,ebay_currency,ebay_total,ebay_shipfee,scantime FROM ebay_order  
				WHERE (scantime BETWEEN {$start} AND {$end}) and ebay_status=2 and ebay_combine !=1 and ebay_carrier in ('中国邮政挂号','香港小包挂号','EUB','Global Mail','德国邮政挂号','新加坡小包挂号','中国邮政平邮','香港小包平邮','俄速通挂号','俄速通大包','俄速通平邮')";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	
	//包装记录
	public static function getOrderList_express($start,$end){
		$start = strtotime($start);
		$end = strtotime($end);
		self::initDB();
		$sql = "SELECT ebay_id,ebay_ordersn,orderweight2,ebay_account,ordershipfee,ebay_carrier,packagingstaff,packinguser,ebay_tracknumber,ebay_countryname,ebay_currency,ebay_total,ebay_shipfee,scantime FROM ebay_order  
				WHERE scantime is not null and (scantime BETWEEN {$start} AND {$end}) and ebay_status=2 and ebay_combine !=1 and ebay_carrier not in ('中国邮政挂号','香港小包挂号','EUB','Global Mail','德国邮政挂号','新加坡小包挂号','中国邮政平邮','香港小包平邮','俄速通挂号','俄速通大包','俄速通平邮')";
		
		//echo $sql;
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function getInnerAccount(){
		self::initDB();
		$sql = "select * from ebay_account where ebay_platform='天猫哲果' or ebay_platform='天猫芬哲' or ebay_platform='国内销售部'";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			self::$dbConn->free_result($query);
			
			return $ret;
			
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	} 
	public static function pda_user($jobnum){
		self::initDB();
		$sql = "select * from ebay_pda_user where jobnumber = '{$jobnum}' ";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			self::$dbConn->free_result($query);
			
			return $ret;
			
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	} 
	public static function pda_jobnum($username){
		self::initDB();
		$sql = "select * from ebay_pda_user where username = '{$username}' ";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			self::$dbConn->free_result($query);			
			return $ret;
			
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	public static function jobnum($username){
		self::initDB();
		$sql = "select * from ebay_user where username = '{$username}' ";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array($query);
			self::$dbConn->free_result($query);
			
			return $ret;
			
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
	}
	function func_readlog_splitorder($ebay_id){
		//ebay_order_splitorder 已经升级作为一些操作的记录表,读取这个表的记录信息
		//add by Herman.Xi @ 20130309
		self::initDB();
		$sql = "select * from ebay_splitorder as es where split_order_id = '$ebay_id' ";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_row($query);
			self::$dbConn->free_result($query);
			if($ret){
				return $ret['mode'];
			}
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
    }
	function judge_is_splitorder($ebay_id){
		//判断订单是否为拆分订单
		self::initDB();
		$es_sql = "select * from ebay_splitorder as es where split_order_id = '$ebay_id' and mode in(0,5) ";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->num_rows($query);
			self::$dbConn->free_result($query);
			return $ret;
		}else{
			$errCode = "8001";
			$errMsg = "query error!";
			return false;			
		}
		//return $dbcon->num_rows($result);
    }
}
?>