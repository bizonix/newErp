<?php
/**
 * 类名：CarrierManageModel
 * 功能：运输方式管理数据（CRUD）层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
 
class CarrierManageModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	private static $table		= "carrier";
	private static $tab_rel_plat= "carrierName";
		
	/**
	 * CarrierManageModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
		self::$prefix	= C('DB_PREFIX');
	}
	
	/**
	 * CarrierManageModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
	public static function modList($where, $page, $pagenum){
		self::initDB();
		$start	= ($page-1)*$pagenum;
		$sql 	= "SELECT * FROM ".self::$prefix.self::$table." WHERE $where LIMIT $start,$pagenum";
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
	 * CarrierManageModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($where){
		self::initDB();
		$sql = "SELECT count(*)	FROM ".self::$prefix.self::$table." WHERE $where";
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
	 * CarrierManageModel::modModify()
	 * 返回某个运输方式的信息
	 * @param integer $id 运输方式ID
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
	 * CarrierManageModel::ModaddCarrierManage()
	 * 添加运输方式信息保存到数据库
	 * @param array $data 数据集
	 * @return bool 
	 */
	public static function ModaddCarrierManage($data){
		self::initDB();
		self::$dbConn->begin();//开启事务
		$plat_arr = $data['plat_arr'];
		$ship_add = $data['ship_add'];
		unset($data['plat_arr']);
		unset($data['ship_add']);
		$sql 	= array2sql($data);
		$sql 	= "INSERT INTO `".self::$prefix.self::$table."` SET ".$sql; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$carrierId 	= self::$dbConn->insert_id();           
			if ($carrierId) {
				$platFlag		= self::addTransportRelation($carrierId,$plat_arr);//添加与平台关系
				$shipAddFlag	= self::addShippingAddressRelation($carrierId,$ship_add);//添加与发货地址关系
				if ($platFlag === true && $shipAddFlag === true) {
					self::$dbConn->commit();
					return $carrierId;
				} else {
					self::$dbConn->rollback();
					self::$errCode	= 10002;
					self::$errMsg	= "插入关系表数据失败";
					return false;
				}
			} else {
				self::$dbConn->rollback();
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$dbConn->rollback();
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * CarrierManageModel::pf_exists()
	 * 检查平台是否存在
	 * @param integer $pfid 平台ID
	 * @param integer $cid 运输方式ID
	 * @return integer 总数量 
	 */
	public static function pf_exists($cid, $pfid){
		self::initDB();
		$sql = "SELECT count(*)	FROM trans_carrierName WHERE carrierId={$cid} AND platformId={$pfid}";
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
	 * CarrierManageModel::shipAdd_exists()
	 * 检查发货地址对应的运输方式是否存在
	 * @param integer $aid 发货地址ID
	 * @param integer $cid 运输方式ID
	 * @return integer 总数量 
	 */
	public static function shipAdd_exists($cid, $aid){
		self::initDB();
		$sql = "SELECT count(*)	FROM trans_address_carrier_relation WHERE carrierId={$cid} AND addressId={$aid}";
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
	
	//修改平台与运输方式关系表
	public static function updateTransportRelation($carrierId, $platformArr){
		self::initDB();
		self::$dbConn->begin();//开启事务
		$platFlag	= true;
		foreach ($platformArr AS $platform) {
			$res	= self::pf_exists($carrierId, $platform);
			if (empty($res)) {
				$sql	= "INSERT INTO trans_carrierName SET carrierId='$carrierId', platformId = '$platform'";
				$query	= self::$dbConn->query($sql);
				if (!$query) {
					self::$dbConn->rollback();
					self::$errCode 	= 10000;
					self::$errMsg  	= "修改平台与运输方式关系时失败";
					$platFlag	= false;
					break;
				}
			}
		}
		if ($platFlag === true) {
			self::$dbConn->commit();
		} else {
			self::$dbConn->rollback();
		}
		return $platFlag;
	}
	
	//添加平台与运输方式关系表
	public static function addTransportRelation($carrierId, $platformArr){
		self::initDB();
		self::$dbConn->begin();//开启事务
		$platFlag	= true;
		foreach ($platformArr AS $platform) {
			$sql	= "INSERT INTO trans_carrierName SET carrierId='$carrierId', platformId = '$platform'";
			$query	= self::$dbConn->query($sql);
			if (!$query) {
				self::$dbConn->rollback();
				self::$errCode 	= 10000;
				self::$errMsg  	= "添加平台与运输方式关系时失败";
				$platFlag	= false;
				break;
			}
		}
		if ($platFlag === true) {
			self::$dbConn->commit();
		} else {
			self::$dbConn->rollback();
		}
		return $platFlag;
	}
	
	//添加发货地址与运输方式关系表
	public static function updateShippingAddressRelation($carrierId, $ship_add){
		self::initDB();
		self::$dbConn->begin();//开启事务
		$counts	= self::shipAdd_exists($carrierId, $ship_add);
		if (!$counts) {		
			$sql 	= "INSERT INTO trans_address_carrier_relation SET addressId = '{$ship_add}',carrierId='{$carrierId}'";
		} else {
			$sql 	= "UPDATE trans_address_carrier_relation SET addressId = '{$ship_add}' WHERE carrierId='{$carrierId}'";
		}
		$query	= self::$dbConn->query($sql);
		if ($query) {
			self::$dbConn->commit();
			return true;
		} else {
			self::$dbConn->rollback();
			self::$errCode 	= 10000;
			self::$errMsg  	= "修改发货地址与运输方式关系时出错";
			return false;
		}
	}
	
	//添加发货地址与运输方式关系表
	public static function addShippingAddressRelation($carrierId, $ship_add){
		self::initDB();
		self::$dbConn->begin();//开启事务
		$sql 	= "INSERT INTO trans_address_carrier_relation SET carrierId='$carrierId', addressId = '$ship_add'";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			self::$dbConn->commit();
			return true;
		} else {
			self::$dbConn->rollback();
			self::$errCode 	= 10000;
			self::$errMsg  	= "添加发货地址与运输方式关系时出错";
			return false;
		}
	}
	
	/**
	 * CarrierManageModel::ModUpdateCarrierManage()
	 * 更新运输方式信息
	 * @param integer $id 运输方式ID
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function ModUpdateCarrierManage($id, $data){
		self::initDB();
		$plat_arr = $data['plat_arr'];
		$ship_add = $data['ship_add'];
		unset($data['plat_arr']);
		unset($data['ship_add']);
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefix.self::$table."` SET ".$sql." WHERE id = {$id}"; 
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$platFlag	= self::delCarrierPlatForm($id, $plat_arr);//删除平台关系
			$platFlag	= self::updateTransportRelation($id, $plat_arr);//修改与平台关系
			$shipAddFlag= self::updateShippingAddressRelation($id, $ship_add);//修改与发货地址关系
			return true;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * CarrierManageModel::delCarrierManage()
	 * 运输方式的启用和禁用
	 * @param integer $id 运输方式ID
	 * @return bool
	 */
	public static function delCarrierManage($id, $status=1){
		self::initDB();
		$sql	= "UPDATE `".self::$prefix.self::$table."` SET is_delete = $status WHERE id = {$id}";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if ($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "更新数据状态失败";
				return false;
			}
		} else {
            self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句失败！";
			return false;
		}
	}
	
	/**
	 * CarrierManageModel::delCarrierPlatForm()
	 * 删除运输方式的平台关系
	 * @param integer $ids 平台ID
	 * @param integer $carrierId 运输方式ID
	 * @return bool
	 */
	public static function delCarrierPlatForm($carrierId, $ids){
		self::initDB();
		$ids	= implode(",",$ids);
		$sql	= "DELETE FROM `".self::$prefix.self::$tab_rel_plat."` WHERE carrierId = {$carrierId} AND platformId NOT IN({$ids})";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if ($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "删除关系失败";
				return false;
			}
		} else {
            self::$errCode	= 10000;
			self::$errMsg	= "执行SQL语句失败！";
			return false;
		}
	}
	
	/**
	 * CarrierManageModel::listCarrierPlatForm()
	 * 列出符合条件的数据并分页显示
	 * @param integer $id 运输方式ID
	 * @return array 
	 */
	public static function listCarrierPlatForm($id){
		self::initDB();
		$sql 	= "SELECT platformId FROM ".self::$prefix.self::$tab_rel_plat." WHERE carrierId = {$id}";
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