<?php
/**
 * 类名：ShippingAddressModel
 * 功能：发货地址管理数据（CRUD）层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
 
class ShippingAddressModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "shipping_address";
	private static $rel_table	= "seller";
		
	/**
	 * ShippingAddressModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * ShippingAddressModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql	= "SELECT
					a.id,
					a.addressNameCn,
					a.addressNameEn,
					a.addressCode,
					a.createdTime,
					b.sellerName
					FROM
					".self::$prefix.self::$table." AS a
					INNER JOIN ".self::$prefix.self::$rel_table." AS b ON a.sellerId = b.id
					WHERE $where AND is_delete = 0
					ORDER BY id DESC LIMIT $start,$pagenum";
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
	 * ShippingAddressModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql = "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE $where AND is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($result=self::$dbConn->query($sql)) {
			$data=self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * ShippingAddressModel::isExistSeller()
	 * 返回某个条件统计的大卖家数量
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function isExistSeller($where){
		self::initDB();
		$sql = "SELECT id FROM ".self::$prefix.self::$rel_table." WHERE $where LIMIT 1";
		$query	= self::$dbConn->query($sql);
		if ($result=self::$dbConn->query($sql)) {
			$data=self::$dbConn->fetch_array($result);
			return $data['id'];
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * ShippingAddressModel::modModify()
	 * 返回某个发货地址的信息
	 * @param integer $id 发货地址ID
	 * @return array 结果集数组
	 */
	public static function modModify($id){
		self::initDB();
		$sql 	= "SELECT * FROM ".self::$prefix.self::$table." WHERE id = {$id} AND is_delete = 0 LIMIT 1";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * ShippingAddressModel::getAddByCarrierId()
	 * 根据运输方式ID返回发货地址信息
	 * @param integer $carrierId 运输方式ID
	 * @return array 结果集数组
	 */
	public static function getAddByCarrierId($carrierId){
		self::initDB();
		$sql 	= "SELECT
					b.addressNameCn,
					b.id
					FROM 
					trans_address_carrier_relation AS a
					INNER JOIN trans_shipping_address AS b ON a.addressId = b.id
					WHERE a.carrierId = {$carrierId} AND a.is_delete = 0 LIMIT 1";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * ShippingAddressModel::addShippingAddress()
	 * 添加发货地址信息保存到数据库
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function addShippingAddress($data){
		self::initDB();
		$res	= 0;
		$where	= "(addressNameCn = '{$data['addressNameCn']}' OR addressNameEn = '{$data['addressNameEn']}')"; 
        $res	= self::modListCount($where);
		if ($res > 0) {
			self::$errCode	= 10002;
			self::$errMsg	= "添加失败：发货地址已存在，中文名或英文名都不能重复！";
			return false;
		}
		$where	= "sellerName = '{$data['sellerName']}'"; 
        $res	= self::isExistSeller($where);
		if (empty($res)) {
			$sql 	= "INSERT INTO `".self::$prefix.self::$rel_table."` SET sellerName = '{$data['sellerName']}'"; 
			$query	= self::$dbConn->query($sql);
			if ($query) {
				$rows 	= self::$dbConn->insert_id();           
				if (!$rows) {
					self::$errCode	= 10001;
					self::$errMsg	= "插入大卖家数据失败";
					return false;
				} else {
					$data["sellerId"] = $rows;
				}
			} else {
				self::$errCode	= 10000;
				self::$errMsg	= "执行SQL语句出错";
				return false;
			}
		} else {
			$data["sellerId"] = $res;
		}
		unset($data['sellerName']);
		$sql 	= array2sql($data);
		$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if ($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入发货地址数据失败";
				return false;
			}
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * ShippingAddressModel::updateShippingAddress()
	 * 更新发货地址信息
	 * @param integer $id 发货地址ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateShippingAddress($id, $data){
		self::initDB();
		$res	= 0;
		// $where	= "(addressNameCn = '{$data['addressNameCn']}' OR addressNameEn = '{$data['addressNameEn']}')"; 
        // $res	= self::modListCount($where);
		// if ($res > 0) {
			// self::$errCode	= 10002;
			// self::$errMsg	= "更新失败：发货地址已存在相应的中文名称或英文名称！";
			// return false;
		// }
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE id = {$id}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * StockInvoiceModel::delShippingAddress()
	 * 发货地址删除
	 * @param integer $id 发货地址ID
	 * @return bool
	 */
	public static function delShippingAddress($id){
		self::initDB();
		$sql	= "UPDATE `".self::$prefix.self::$table."` SET is_delete = 1 WHERE id = {$id}";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if ($rows) {
				return $res;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "删除数据失败";
				return false;
			}
		} else {
            self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句失败！";
			return false;
		}
	}
}
?>