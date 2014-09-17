<?php
class PaymentOrderModel {
	public static $dbConn;
	public static $prefix;
	public static $errCode = 0;
	public static $errMsg = "";
	public static $table;
	public static function	initDB(){
		global $dbConn;
		self::$dbConn = $dbConn;
		self::$prefix  =  C("DB_PREFIX");
		self::$table   =  self::$prefix."order";;
	}
	public static function error($errCode,$errMsg){
		self::$errCode=$errCode;
		self::$errMsg=$errMsg;
		return false;
	}
	public static function	getOrderList($where="",$limit=""){
		self::initDB();
		if($where!=''){
			$where=" and ".$where;
		}
		$where=" where  po.is_delete=0 ".$where;
		$sql="SELECT DISTINCT
 					po.warehouse_id,
					po.company_id,
					po.note,
					po.aduituser_id,
					po.order_type,
					po.id,
					po.recordnumber,
					po.status,
					po.addtime,
					po.finishtime,
					po.paymethod,
					po.paystatus,
					po.purchaseuser_id,
					po.partner_id,
 				    po.aduittime,
 				    po.deliverytime,
 				    po.img
				FROM
					".self::$prefix."order AS po
				LEFT JOIN `".self::$prefix."order_detail` AS pd ON po.id = pd.po_id
 				".$where."  ".$limit;
		$query=self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			// 			var_dump($sql,$ret);
			return $ret;
		}
		self::error("01","获取采购订单列表失败！");
		return false;
	}
	public static function countByStatus($stat, $powerlist){
		self::initDB();
		$sql = "SELECT count(*) as total FROM ".self::$prefix."order WHERE is_delete=0 AND paystatus=".$stat;
		if(!empty($powerlist)){
			$sql .= " AND purchaseuser_id in ('{$powerlist}')";
		}
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret[0]['total'];
		}
		self::error("01","获取采购订单列表失败！");
		return false;
	}
	
	/**
	 * PaymentOrderModel::addPayment()
	 * 保存财务付款记录
	 * @param array $data 数组
	 * @return  bool
	 */
    public static function addPayment($data){
		self::initDB();
		$sql	= array2sql($data);
		$sql 	= "INSERT INTO ".self::$prefix."pay_order"." SET ".$sql;               
		$query	=	self::$dbConn->query($sql);
		if($query){
			$affectedrows = self::$dbConn->affected_rows();           
			if($affectedrows){
				$sqlstat	= "";
				$sqlstat	= "UPDATE ".self::$prefix."order"." SET paystatus = '3' WHERE id = '{$data['order_id']}'";
				$query		= self::$dbConn->query($sqlstat);
				return $affectedrows;
			}else {
				self::$errCode	= "0002";
				self::$errMsg	= "付款记录保存失败";
				return false;
			}			
		}else {
			self::$errCode	= "0002";
			self::$errMsg	= "付款记录保存失败";
			return false;
		}
	}
	//更新付款截图 add wangminwei 2013-11-09
   public static function updateImg($recordnumber, $img){
   		self::initDB();
   		$sql   = "UPDATE ph_order SET img = '{$img}' WHERE recordnumber = '{$recordnumber}'";
   		$query = self::$dbConn->query($sql);
   		if($query){
   			$num = self::$dbConn->affected_rows();
   			if($num >= 0){
   				return true;
   			}else{
   				return false;
   			}
   		}
   		return false;
   }
}
?>