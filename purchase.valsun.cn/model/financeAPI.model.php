<?php
/**
 *类名：FinanceAPIModel
 *功能：采购系统与财务系统交互业务数据操作类,返回数据到财务系统
 *日期: 2014-03-03
 *版本：1.0
 *作者：王民伟
 */
class FinanceAPIModel{
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg  = "";
	public static function	initDB(){
		global $dbConn;
		self::$dbConn 	= $dbConn;
	}

	//根据采购订单号获取订单信息返回财务系统
	public static function getPurOrderInfo($orderList){
		if(!empty($orderList)){
			self::initDB();
			$orderArr   = explode(',', $orderList);
			$inOrderArr = '';
			foreach($orderArr as $ordersn){
				$inOrderArr .= "'".$ordersn."',";
			}
			$inOrderArr = "(".substr($inOrderArr, 0, strlen($inOrderArr) - 1).")";
			$sql 		= "SELECT a.id, a.recordnumber, a.purchaseuser_id, ";
			$sql       .= "b.sku, b.price, b.count, b.stockqty, c.global_user_name, d.goodsCategory, e.company_name FROM ph_order AS a ";
			$sql       .= "JOIN ph_order_detail AS b ON a.id = b.po_id ";
			$sql       .= "JOIN power_global_user AS c ON a.purchaseuser_id = c.global_user_id ";
			$sql       .= "JOIN pc_goods AS d ON b.sku = d.sku ";
			$sql       .= "JOIN ph_partner AS e ON a.partner_id = e.id ";
			$sql       .= "WHERE a.recordnumber in $inOrderArr ORDER BY a.id ASC";
			$query  	= self::$dbConn->query($sql);
			if($query){
				$orderData = self::$dbConn->fetch_array_all($query);
				if(!empty($orderData)){
					return $orderData;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//返回需要同步的供应商数据行数,用于分页请求
	public static function getSupplierCount(){
		self::initDB();
		$sql 	= "SELECT count(*) AS total FROM ph_partner";
		$query  = self::$dbConn->query($sql);
		$data 	= self::$dbConn->fetch_array_all($query);
		return $data[0]['total'];
	}

	//按分页请求返回供应商信息
	public static function getSupplierInfo($page, $pagenum){
		self::initDB();
		$start	= ($page - 1) * $pagenum;
		$sql 	= "SELECT company_name, username, tel, phone, fax, QQ, e_mail, city, address FROM ph_partner ";
		$sql   .= "ORDER BY id ASC LIMIT $start, $pagenum";
		$query  = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		}else{
			return false;
		}
	}

	//返回当天采购到货入库记录数
	public static function getPurInStockCount(){
		self::initDB();
		$startTime  = strtotime(date('Y-1-1 00:00:00'));
		$endTime    = strtotime(date('Y-5-18 23:23:59'));
		$startTime  = $startTime - 60 * 60 * 24;//推迟一天,隔天临晨同步前天数据
		$endTime    = $endTime - 60 * 60 * 24;//推迟一天
		$totalnum   = 0;
		$sql    	= "SELECT count(*) AS total FROM ph_order_arrive_log ";
		$sql       .= "WHERE arrive_time between $startTime AND $endTime ";
		$query  	= self::$dbConn->query($sql);
		$data 		= self::$dbConn->fetch_array_all($query);
		return $data;

	}

	//返回当天采购到货入库信息,作为财务软件外购入库信息 2014-03-13
	public static function getPurInStockInfo($page, $pagenum){
		self::initDB();
		$startTime  = strtotime(date('Y-2-1 00:00:00'));
		$endTime    = strtotime(date('Y-5-18 23:23:59'));
		$startTime  = $startTime - 60 * 60 * 24;//推迟一天,隔天临晨同步前天数据
		$endTime    = $endTime - 60 * 60 * 24;//推迟一天
		$start		= ($page - 1) * $pagenum;
		$inSql    	= "SELECT ordersn, sku, amount, arrive_time FROM ph_order_arrive_log where ordersn='SWB140311300619' ";//LIMIT 0, 20 ";
		//$inSql     .= "WHERE arrive_time between $startTime AND $endTime ";
		$inSql     .= "LIMIT $start, $pagenum";
		$inQuery  	= self::$dbConn->query($inSql);
		$data 		= array();
		if($inQuery){
			$inData   = self::$dbConn->fetch_array_all($inQuery);
			if(!empty($inData)){
				$num 	  	= 0;
				foreach($inData as $k => $v){
					$data[$num]['ordersn'] 	 = $v['ordersn'];
					$data[$num]['insku']     = $v['sku'];
					$data[$num]['inamount']  = $v['amount'];
					$data[$num]['intime']    = $v['arrive_time'];
					$sql 	= "SELECT a.purchaseuser_id, a.partner_id, b.price, c.global_user_name, d.goodsCategory FROM ph_order AS a ";
					$sql   .= "JOIN ph_order_detail AS b ON a.id = b.po_id ";
					$sql   .= "JOIN power_global_user AS c ON a.purchaseuser_id = c.global_user_id ";
					$sql   .= "JOIN pc_goods AS d ON b.sku = d.sku ";
					$sql   .= "WHERE a.recordnumber = '{$v['ordersn']}' AND b.sku = '{$v['sku']}'";
					$query     		= self::$dbConn->query($sql);
					if($query){
						$info      	= self::$dbConn->fetch_array_all($query);
						$partner   	= $info[0]['partner_id'];//供应商
						$company    = '';
						$parInfo    = "SELECT company_name FROM ph_partner WHERE id = '{$partner}' limit 0, 1";
						$parQuery	= self::$dbConn->query($parInfo);
						if($parQuery){
							$parInfo 	= self::$dbConn->fetch_array_all($parQuery);
							$company    = '';
							if(!empty($parInfo)){
								$company    = $parInfo[0]['company_name'];
							}
						}
						$data[$num]['cost']    		= $info[0]['price'];//采购单价
						$data[$num]['category']  	= $info[0]['goodsCategory'];//分类
						$data[$num]['cguser']    	= $info[0]['global_user_name'];//采购员
						$data[$num]['company']      = $company;//供应商名称
						$num++;
					}
				}
				return $data;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//根据供应商名称返回供应商基础信息,用于预付部份金额时,没有财务软件没有供应商返回基础信息添加供应商
	public static function getSupplierInfoByName($name){
		self::initDB();
		if(!empty($name)){
			$sql 	= "SELECT * FROM ph_partner WHERE company_name = '{$name}'";
			$query  = self::$dbConn->query($sql);
			$data 	= self::$dbConn->fetch_array($query);
			if(!empty($data)){
				return $data;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//更新财务系统推送过来已付款的订单号
	public static function updHasPayOrder($orderArr){
		self::initDB();
		if(!empty($orderArr)){
			$inOrderArr = '';
			foreach($orderArr as $k => $v){
				$inOrderArr .= "'".$v['FOrderNo']."',"; 
			}
			$inOrderArr 	= "(".substr($inOrderArr, 0, strlen($inOrderArr) - 1).")";
			$nowTime 		= time();
			$upd 			= "UPDATE ph_order SET payresult = 2, prepaytime = '{$nowTime}' WHERE recordnumber IN $inOrderArr AND payresult = 1";
			$updquery  		= self::$dbConn->query($upd);
			return $updquery;
		}else{
			return false;
		}

	}
}
?>