<?php
/**
 * 类名：PackageModel
 * 功能：采购通用调用model方法
 * 版本：1.0
 * 日期：2013/11/11
 * 作者：管拥军
 */
 
class PackageModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}
	
	/**
	 * PackageModel::fixPartnerId()
	 * 修复某些状态订单的供应商ID
	 * @param string $status 订单状态
	 * @return  array
	 */
	public static function fixPartnerId($status){
		self::initDB();
		$data	= array();
		$values	= array();
		$ids	= "";
		$sql 	= "SELECT DISTINCT(a.partner_id) FROM `ph_order` as a 
					LEFT JOIN `ph_partner` as b ON a.partner_id = b.id
					WHERE a.`status` IN({$status}) AND b.company_name IS NULL";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			foreach	($res as $v) {
				$oldid		= $v['partner_id'];
				$sql 	= "SELECT id,company_name FROM `ebay_partner` WHERE `id` = {$oldid}";
				$query	= self::$dbConn->query($sql);
				$res	= self::$dbConn->fetch_array($query);
				if (empty($res)) {
					//未匹配到旧供应商数据先保存再按SKU修复
					$sql 	= "SELECT id FROM `ph_order` WHERE `partner_id` = {$oldid} LIMIT 1";
					$query	= self::$dbConn->query($sql);
					$res	= self::$dbConn->fetch_array($query);
					$orderid= isset($res['id']) ? $res['id'] : "";
					echo $oldid,"===<br/>";
					if	(!empty($orderid)) {
						//找到某个订单的某个料号
						$sql 	= "SELECT sku FROM `ph_order_detail` WHERE `po_id` = {$orderid} LIMIT 1";
						$query	= self::$dbConn->query($sql);
						$res	= self::$dbConn->fetch_array($query);
						$sku	= isset($res['sku']) ? $res['sku'] : "";
						if (empty($sku)) continue;
						//根据料号找供应商
						$sql 	= "SELECT * FROM `pc_goods_partner_relation` WHERE `sku` = '{$sku}' LIMIT 1";
						$query	= self::$dbConn->query($sql);
						$res	= self::$dbConn->fetch_array($query);
						$newid	= isset($res['partnerId']) ? $res['partnerId'] : "";
						if (empty($newid)) continue;
						$sql 	= "UPDATE `ph_order` SET partner_id = {$newid} WHERE id = {$orderid}";
						echo $sql,"<br/>";
						$query	= self::$dbConn->query($sql);
					}
					array_push($data,$oldid);
				} else {
					$cname	= isset($res['company_name']) ? $res['company_name'] : "";
					if (empty($cname)) continue;
					$sql 	= "SELECT id FROM `ph_partner` WHERE company_name = '{$cname}'";
					$query	= self::$dbConn->query($sql);
					$res	= self::$dbConn->fetch_array($query);
					$newid	= isset($res['id']) ? $res['id'] : "";
					if (empty($newid)) continue;
					$sql 	= "UPDATE `ph_order` SET partner_id = {$newid} WHERE partner_id = {$oldid}";
					echo $sql,"<br/>";
					$query	= self::$dbConn->query($sql);
				}
			}
			print_r($data);
			return count($data);
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * PackageModel::getPackageOrders()
	 * 获取采购下给供应商的在途订单
	 * @param date $addTime 下单日期
	 * @return  array
	 */
	public static function getPackageOrders($addTime, $debug){
		self::initDB();
		$sql 	= "SELECT a.*,b.global_user_name,c.company_name,c.username FROM `ph_order` as a 
					INNER JOIN `power_global_user` as b ON a.purchaseuser_id = b.global_user_id
					INNER JOIN `ph_partner` as c ON a.partner_id = c.id
					WHERE a.`status` IN(3,4) AND (a.`addtime` >= {$addTime} OR a.`finishtime` >= {$addTime})";
		if ($debug) return $sql;
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * PackageModel::getPackageOrdersBySkuTime()
	 * 获取SKU到货时间更新的订单
	 * @param date $addTime 日期
	 * @return  array
	 */
	public static function getPackageOrdersBySkuTime($addTime){
		self::initDB();
		//$sql 	= "SELECT * FROM `ph_order_detail` WHERE `reach_time` >= {$addTime} GROUP BY po_id";
		$sql 	= "SELECT a.*,b.goodsName,b.id as goodsId,b.goodsCost FROM `ph_order_detail` as a
					INNER JOIN `pc_goods` as b ON a.sku = b.sku
					WHERE a.`reach_time` >= {$addTime}";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}

	/**
	 * PackageModel::getPackageOrderDetails()
	 * 获取一个或多个订单详情
	 * @param string $poid 订单号
	 * @return  array
	 */
	public static function getPackageOrderDetails($poid){
		self::initDB();
		$sql 	= "SELECT a.*,b.goodsName,b.id as goodsId,b.goodsCost FROM `ph_order_detail` as a
					INNER JOIN `pc_goods` as b ON a.sku = b.sku
					WHERE a.po_id IN ({$poid})";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}	
}
?>
