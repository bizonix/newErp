<?php

/*
 * om通用Model
 * ADD BY zqt 2013.9.5
 */
class OmAvailableModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		//mysql_query('SET NAMES UTF8');
	}
	/*
	 *取得指定表中的指定记录
	 */
	public static function getTNameList($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
	 *取得指定表中的指定记录并且存入到单个数组中
	 */
	public static function getTNameList2arr($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			$ret2 = array();
			foreach($ret as $val){
				$ret2[] = $val[$select];
			}
			return $ret2; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/*
	 *取得指定表中的指定记录记录数
	 */
	public static function getTNameCount($tName, $where) {
		self :: initDB();
		$sql = "select count(*) count from $tName $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['count']; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *添加指定表记录,返回insertId
	 */
	public static function addTNameRow($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$insertId = self :: $dbConn->insert_id($query);
			return $insertId; //成功， 返回插入的id
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 *添加指定表记录,返回ture
	 */
	public static function addTNameRow1($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			return true; //成功， 返回插入的id
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
    
   	/**
	 *添加指定表记录
	 */
	public static function insertRow($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			return TRUE; //成功，
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
    
	/**
	 *添加指定表记录2
	 */
	public static function insertRow2($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
        //echo $sql;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			return self :: $dbConn->insert_id(); //成功，返回插入ID
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *修改指定表记录
	 */
	public static function updateTNameRow($tName, $set, $where) {
		self :: initDB();
		$sql = "UPDATE $tName $set $where";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$affectRows = self :: $dbConn->affected_rows($query);
			return $affectRows; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
    
    /**
	 *根据平台id取得其名称
	 */
	public static function getPlatformById($id) {
		self :: initDB();
		$sql = "SELECT platform from om_platform WHERE id='$id'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['platform']; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 *根据仓位id、订单状态取得订单信息
	 *$orderStatus as 12,33
	 */
	public static function getOrderList($positionId,$sku,$orderStatus) {
		self :: initDB();
		$sql = "select a.amount,a.positionId,a.id from wh_shipping_orderdetail as a join wh_shipping_order as b 
				on a.shipOrderId=b.id
				where a.positionId={$positionId} and a.sku='{$sku}' and b.orderStatus in ($orderStatus) and b.storeId=1 and b.is_delete=0";
		//echo $sql;die;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 *根据sku、仓库id获取仓位信息
	 */
	public static function getSkuPositions($sku,$storeId) {
		self::initDB();
		$sql     =  "select b.pName from `wh_product_position_relation` as a 
					left join `wh_position_distribution` as b on a.positionId=b.id 
					left join `pc_goods` as c on a.pId=c.id where a.type=1 and a.is_delete=0 and c.sku='{$sku}' and a.storeId='{$storeId}'";
		$query	 =	self::$dbConn->query($sql);		
		if($query){
			$ret =self::$dbConn->fetch_array_all($query);
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}
	
	/**
	 *根据仓库id获取无仓位SKU信息
	 */
	public static function getSkuListUnPositions($spu, $storeId) {
		self::initDB();
		$whereArr = array();
		//$whereArr[] = 'a.type=1';
		$whereArr[] = 'b.is_delete=0 AND (a.positionId=0 OR a.positionId IS NULL)';
		if($storeId)$whereArr[] = 'a.storeId='.$storeId;
		if($spu)$whereArr[] = "b.spu='".$spu."'";
		$wheresql = implode(' AND ', $whereArr);
		$sql     =  "select b.spu,b.sku from `pc_goods` as b 
					left join `wh_product_position_relation` as a on a.pId=b.id AND a.is_delete=0 where ".$wheresql;
		$query	 =	self::$dbConn->query($sql);
		
		if($query){
			$ret['list'] = self::$dbConn->fetch_array_all($query);
			//无仓位虚拟料号
			$whereArr[] = "d.combineSku IS NOT NULL";
			$wheresql = implode(' AND ', $whereArr);		
			$combinesql     =  "select d.combineSpu,d.combineSku from `pc_goods` as b 
						left join `wh_product_position_relation` as a on a.pId=b.id 
						left join `pc_sku_combine_relation` as c on c.sku=b.sku 
						left join `pc_goods_combine` as d on d.combineSku=c.combineSku where ".$wheresql;
			$combinequery	 =	self::$dbConn->query($combinesql);
			if($combinequery){
				$ret['combine'] = self::$dbConn->fetch_array_all($combinequery);
			}
			return $ret;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;	
		}
	}	
	
	/**
	 *更新点货表良品数
	 */
	public static function updateTallying($batchNum, $sku, $ichibanNums, $ichibanTime = '') {
		self :: initDB();
		self :: begin();
        $ichibanTime    =   $ichibanTime ? $ichibanTime : time();
		$sql = "update wh_tallying_list set ichibanNums={$ichibanNums}, ichibanTime={$ichibanTime} where batchNum='$batchNum' and sku='$sku'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$select_sql   = "select num from wh_tallying_list where batchNum='$batchNum' and sku='$sku'";
			$select_query = self::$dbConn->query($select_sql);
			$select_list  = self::$dbConn->fetch_array_all($select_query);
			$num 		  = $select_list[0]['num']-$ichibanNums;
			$update_sql	  =	"update wh_sku_location set arrivalInventory=arrivalInventory-{$num} where sku='$sku'";
			$update_query =	self::$dbConn->query($update_sql);		
			if($update_query){
				self :: commit();
				return true;	
			}else{
				self :: rollback();
				self :: $errCode = "003";
				self :: $errMsg = "修改失败";
				return false; //失败则设置错误码和错误信息， 返回false
			}
			
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 *根据sku获取仓库信息
	 */
	public static function getSkuStores($sku) {
		self::initDB();
		$skuinfo = get_realskuinfo($sku);
		if(empty($skuinfo)){
			self::$errCode =	"003";
			self::$errMsg  =	"error";
			return false;
		}
		foreach($skuinfo as $sku=>$num){
			$sql     =  "select a.storeId,b.whName from `wh_sku_location` as a 
					left join `wh_store` as b on a.storeId=b.id 
					where a.sku='$sku'";
			$query	 =	self::$dbConn->query($sql);		
			if($query){
				$ret =self::$dbConn->fetch_array_all($query);
				return $ret;	//成功， 返回列表数据
			}else{
				self::$errCode =	"003";
				self::$errMsg  =	"error";
				return false;	
			}
		}
	}
	
	/**
	 *采购修改异常点货为正常
	 */
	public static function updateTallyingStatus($orderArr){
		self :: initDB();
		self :: begin();
		foreach($orderArr as $id){
			$sql   = "update wh_tallying_list set entryStatus=0 where id={$id}";
			$query = self :: $dbConn->query($sql);
			if($query){
				$list	  = packageCheckModel::selectList("where id={$id}");
				$num 	  = $list[0]['num'];	
				$sku  	  = $list[0]['sku'];
				$skulocation = packageCheckModel::selectStore($sku);
				if(!empty($skulocation)){
					$storeinfo = packageCheckModel::updateStore($sku,$num);
				}else{
					$storeinfo = packageCheckModel::insertStore($sku,$num);
				}
				if(!$storeinfo){
					self :: rollback();
					return false;
				}
			}else{
				self :: $errCode = "003";
				self :: $errMsg  = "修改失败";
				self :: rollback();
				return false;
			}
		}
		self :: commit();
		return true;
	}
    
   	public static function begin() {
		self :: initDB();
		self :: $dbConn->begin();
	}

	public static function commit() {
		self :: initDB();
		self :: $dbConn->commit();
	}

	public static function rollback() {
		self :: initDB();
		self :: $dbConn->rollback();
	}
    
}
?>
