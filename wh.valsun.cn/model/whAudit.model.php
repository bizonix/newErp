<?php

/*
 * 审核流程Model
 * ADD BY zqt 2013.8.29
 */
class WhAuditModel {
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
	 *添加指定表记录
	 */
	public static function addTNameRow($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
        //echo $sql.'<br>';
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
        echo $sql.'<br>';
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
	 *根据ordersn查找records表中记录集
	 */
	public static function getAuditRecordsByOrdersn($ordersn) {
		self :: initDB();
		$sql = "SELECT * FROM wh_audit_records WHERE ordersn='$ordersn' ORDER BY id DESC LIMIT 1";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回记录集
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据invoiceTypeId,storeId查找List表中已经开启的最小的auditLevel
	 */
	public static function getMinALevelByIS($invoiceTypeId, $storeId) {
		self :: initDB();
		$sql = "SELECT auditLevel FROM wh_audit_relation_list WHERE is_enable=1 AND invoiceTypeId='$invoiceTypeId' AND storeId='$storeId' GROUP BY auditLevel ORDER BY auditLevel LIMIT 1";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['auditLevel']; //成功， 返回唯一一条数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

     /**
	 *根据invoiceTpyeId,storeId,auditLevel,auditorId查找List表中唯一开启的存在的一条记录id
	 */
	public static function getALIdByAA($invoiceTypeId, $storeId, $auditLevel, $auditorId) {
		self :: initDB();
		$sql = "SELECT id FROM wh_audit_relation_list WHERE is_enable=1 AND invoiceTypeId='$invoiceTypeId' AND storeId='$storeId' AND auditLevel='$auditLevel' AND auditorId='$auditorId'";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['id']; //成功， 返回唯一一条数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

     /**
	 *根据auditRelationIds查找List表中开启状态下最大的auditLevel
	 */
	public static function getMaxLevelByAIds($auditRelationIds) {
		self :: initDB();
		$sql = "SELECT auditLevel FROM wh_audit_relation_list WHERE is_enable=1 AND id in($auditRelationIds) ORDER BY auditLevel DESC LIMIT 1";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['auditLevel']; //成功， 返回唯一一条数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据invoiceTypeId,storeId和当前auditLevel查找List表中开启状态下下一个auditLevel的值
	 */
	public static function getNextLevelByISL($invoiceTypeId, $storeId, $auditLevel) {
		self :: initDB();
		$sql = "SELECT auditLevel FROM wh_audit_relation_list WHERE is_enable=1 AND invoiceTypeId='$invoiceTypeId' AND storeId='$storeId' AND auditLevel>'$auditLevel' GROUP BY auditLevel ORDER BY auditLevel LIMIT 1";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['auditLevel']; //成功， 返回唯一一条数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据invoiceTypeId,storeId和当前auditLevel查找List表中开启状态下对应的审核人Id信息
	 */
	public static function getAuditorIdsByISL($invoiceTypeId, $storeId, $auditLevel) {
		self :: initDB();
		$sql = "SELECT auditorId FROM wh_audit_relation_list WHERE is_enable=1 AND invoiceTypeId='$invoiceTypeId' AND storeId='$storeId' AND auditLevel='$auditLevel'";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据invoiceTypeId取得invoiceType表中对应的ioTypeId，及ioType
	 */
	public static function getIoTypeById($invoiceTypeId) {
		self :: initDB();
		$sql = "SELECT ioType,ioTypeId FROM wh_invoice_type WHERE id='$invoiceTypeId'";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]; //成功， 返回数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据ordersn取得其id
	 */
	public static function getIdByOrdersn($ordersn) {
		self :: initDB();
		$sql = "SELECT id FROM wh_iostore WHERE ordersn='$ordersn'";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['id']; //成功， 返回数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}



   	/**
	 *根据ordersn取得下次审核人Id的array
	 */
	public static function getNextAuditorIdForWh($ordersn) {
		//审核人id
		$auditorIdArr = array (); //定义一个数组用来存放下一级对ordersn审核人Id
		if (empty ($ordersn)) {
			return $auditorIdArr;
		}
		//根据ordersn取出对应的invoiceTypeId,storeId
		$tName = 'wh_iostore';
		$select = 'invoiceTypeId,storeId';
		$where = "WHERE is_delete=0 AND ordersn='$ordersn'";
		$whIostoreList = WhAuditModel :: getTNameList($tName, $select, $where);
		if (empty ($whIostoreList)) { //ioStore表中不存在ordersn这条记录
			return $auditorIdArr;
		}
		$invoiceTypeId = $whIostoreList[0]['invoiceTypeId']; //出入库单据类型
		$storeId = $whIostoreList[0]['storeId']; //仓库id

		$whARByOrdersn = WhAuditModel :: getAuditRecordsByOrdersn($ordersn); //根据ordersn在records表中查找出最大id，其最大id所在的auditrelationId所关联的auditlevel就是该ordersn已经存在的最大审核级别记录
		if (empty ($whARByOrdersn)) { //如果$whARByOrdersn为空，则表示records表中没有该ordersn的记录，
			$minALevel = WhAuditModel :: getMinALevelByIS($invoiceTypeId, $storeId); //取出list表中invoiceTypeId,storeId下已经开启的最小的auditLevel
			if (empty ($minALevel)) { //如果$minALevel为空，表示list表中不存在开启的$invoiceTypeId，$storeId的记录
				return $auditorIdArr;
			}
			$whAuditorIdsList = WhAuditModel :: getAuditorIdsByISL($invoiceTypeId, $storeId, $minALevel); //取得invoiceTypeId，storeId下和本次auditorId匹配的最小level的list中的对应的auditorId列表
			if (!empty ($whAuditorIdsList)) { //如果不为空
				foreach ($whAuditorIdsList as $value) {
					$auditorIdArr[] = $value['auditorId'];
				}
			}
			return $auditorIdArr;
		} else { //如果$whARByOrdersn不为空，则表示records表中有该ordersn的记录，此时需要找出记录中最大的auditlevel
			$whARAidsArray = array (); //定义一个数组存放$whARByOrdersn中的auditRelationId
			foreach ($whARByOrdersn as $value) {
				$whARAidsArray[] = $value['auditRelationId'];
			}
			$whARAidsString = implode(',', $whARAidsArray); //将$whARAidsArray转化成id1,id2,id3形式的字符串
			$maxALevel = WhAuditModel :: getMaxLevelByAIds($whARAidsString); //查找出$whARAidsString中auditLevel最大的
			if (empty ($maxALevel)) { //如果为空，表示$whARAidsString中在list中都没有已经开启的记录，可能是全被人禁用了
				return $auditorIdArr;
			}
			$whNextALevel = WhAuditModel :: getNextLevelByISL($invoiceTypeId, $storeId, $maxALevel); //根据当前最大的auditLevel取得下一个auditLevel的值
			if (empty ($whNextALevel)) { //如果$whNextALevel为空，表示当前record表中最大的审核等级已经是list中最大的审核等级,不存在下一级审核了
				return $auditorIdArr;
			}
			$whAuditorIdsList = WhAuditModel :: getAuditorIdsByISL($invoiceTypeId, $storeId, $whNextALevel); //取得invoiceTypeId，storeId下和本次auditorLevel匹配的auditorId
			if (!empty ($whAuditorIdsList)) { //如果不为空
				foreach ($whAuditorIdsList as $value) {
					$auditorIdArr[] = $value['auditorId'];
				}
			}
			return $auditorIdArr;
		}
	}

}
?>
