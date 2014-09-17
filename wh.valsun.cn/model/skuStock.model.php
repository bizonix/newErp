<?php

/*
 * 库存信息显示调用Model
 * ADD BY zqt 2013.8.14
 */
class SkuStockModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}

	/*
	 * 仓库名称管理数据查询
	 */
	public static function getSkuStockList($where) {
		self :: initDB();
		$sql = "SELECT a.id,a.sku,a.spu,a.goodsName,a.goodsWeight,a.goodsCost,a.purchaseId,a.goodsCreatedTime,a.isNew,a.goodsCategory,a.goodsStatus,b.positionId,b.nums,b.scanNums,b.storeId FROM pc_goods as a LEFT JOIN wh_product_position_relation as b ON a.id=b.pid and b.is_delete = 0 $where";
		//echo $sql.'<br>';exit;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			foreach($ret as &$r){
				$sku_position = "select a.id,a.pName,b.nums from wh_position_distribution as a left join wh_product_position_relation as b on a.id=b.positionId where b.pId={$r['id']} and b.is_delete=0";
				$query  = self :: $dbConn->query($sku_position);
				$reslut = self :: $dbConn->fetch_array_all($query);
				foreach($reslut as $key=>$res){
					if($res['id']==$r['positionId']){
						unset($reslut[$key]);
						break;
					}
				}
				$r['pinfo'] = $reslut;
			}
			return $ret;
		} else {
			self :: $errCode = 0100;
			self :: $errMsg = 'getSkuStockList';
			return false;
		}
	}

	public static function getSkuStockCount($where) {
		self :: initDB();
		$sql = "SELECT a.sku FROM pc_goods as a LEFT JOIN wh_product_position_relation as b ON a.id=b.pid $where";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			$ret = count($ret);
			return $ret;
		} else {
			self :: $errCode = 0200;
			self :: $errMsg = 'getSkuStockCount';
			return false;
		}
	}

    /**
	 *根据sku,storeId查找出对应sku_location的数据
	 */
	public static function getSkuLocationActualStock($sku,$storeId) {
		self :: initDB();
		$sql = "SELECT * FROM wh_sku_location WHERE sku='$sku' AND storeId='$storeId'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list[0]['actualStock']; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据sku,storeId查找出对应sku_location的数据
	 */
	public static function getSkuLocationArrivalInventory($sku,$storeId) {
		self :: initDB();
		$sql = "SELECT * FROM wh_sku_location WHERE sku='$sku' AND storeId='$storeId'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list[0]['arrivalInventory']; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据positionId查找出仓位名称
	 */
	public static function getPNameByPositionId($id) {
		self :: initDB();
		$sql = "SELECT pName FROM wh_position_distribution WHERE id='$id'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list[0]['pName']; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据pName和storeId查找出仓位id
	 */
	public static function getPositionIdByPName($pName, $storeId) {
		self :: initDB();
		$sql = "SELECT id FROM wh_position_distribution WHERE pName='$pName' AND storeId='$storeId'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list[0]['id']; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	/**
	 *根据pName查找出所有仓位id
	 */
	public static function getAllPositionIdByPName($pName) {
		self :: initDB();
		$sql = "SELECT id FROM wh_position_distribution WHERE pName='$pName'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *获取商品总价
	 */
	public static function getAllGoodsCost($where) {
		self :: initDB();
		$sql = "SELECT sum(a.goodsCost * b.actualStock) as totalCost FROM pc_goods a LEFT JOIN wh_sku_location b ON a.sku=b.sku ".$where;
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$list = self :: $dbConn->fetch_array_all($query);
			return $list; //成功， 返回列表数据
		} else {
			self :: $errCode = "004";
			self :: $errMsg = "查找失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}
	
	
    /*
	 *取得指定表中的指定记录
	 */
	public static function getTNameList($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
        //echo $sql.'<br>';
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

	/**
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
	 *添加指定表记录
	 */
	public static function addTNameRow($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
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
	 *修改指定表记录
	 */
	public static function updateTNameRow($tName, $set, $where) {
		self :: initDB();
		$sql = "UPDATE $tName $set $where";
        //echo $sql.'<br>';
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
	 *获取分类信息
	 */
	public static function getCategoryInfo($pid) {
		self :: initDB();
		$sql = "select * from pc_goods_category where pid={$pid}";
        //echo $sql.'<br>';
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
	 *获取分类信息
	 */
	public static function getCategoryInfoByPath($path) {
		self :: initDB();
		$sql = "select * from pc_goods_category where path='{$path}'";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['name']; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "error";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

}
?>
