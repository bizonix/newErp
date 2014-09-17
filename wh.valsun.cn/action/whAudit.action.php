<?php


/*
 * 审核流程action
 * ADD BY zqt 2013.8.29
 */
class WhAuditAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 取得指定iorecords记录
	 */
	function act_getTNameList($tName, $set, $where) { //表名，SET，WHERE
		$list = WhAuditModel :: getTNameList($tName, $set, $where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = WhAuditModel :: $errCode;
			self :: $errMsg = WhAuditModel :: $errMsg;
			return false;
		}
	}

	function act_getTNameCount($tName, $where) {
		$ret = WhAuditModel :: getTNameCount($tName, $where);
		if ($ret !== false) {
			return $ret;
		} else {
			self :: $errCode = WhAuditModel :: $errCode;
			self :: $errMsg = WhAuditModel :: $errMsg;
			return false;
		}
	}

	function act_addTNameRow($tName, $set) {
		$ret = WhAuditModel :: addTNameRow($tName, $set);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = WhAuditModel :: $errCode;
			self :: $errMsg = SkuStockModel :: $errMsg;
			return false;
		}
	}

	function act_updateTNameRow($tName, $set, $where) {
		$ret = WhAuditModel :: updateTNameRow($tName, $set, $where);
		if ($ret !== FALSE) {
			return $ret;
		} else {
			self :: $errCode = WhAuditModel :: $errCode;
			self :: $errMsg = WhAuditModel :: $errMsg;
			return false;
		}
	}

	////
	//对内接口，对单据进行审核操作
	function act_auditIoStoreForWh($ordersn, $auditStatus, $auditorId) {

		//单据编码
		//审核状态，1为通过，2为不通过
		//审核人id
		$now = time(); //当前时间

		if (empty ($ordersn)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'empty ordersn';
			return 3;
		}
		if (intval($auditStatus) != 1 && intval($auditStatus) != 2) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error auditStatus';
			return 5;
		}
		if (intval($auditorId) == 0) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error auditorId';
			return 6;
		}

		//根据ordersn取出对应的invoiceTypeId,storeId
		$tName = 'wh_iostore';
		$select = 'invoiceTypeId,storeId';
		$where = "WHERE is_delete=0 AND ordersn='$ordersn'";
		$whIostoreList = WhAuditModel :: getTNameList($tName, $select, $where);
		if (empty ($whIostoreList)) { //ioStore表中不存在ordersn这条记录
			self :: $errCode = 0201;
			self :: $errMsg = 'empty whIostoreList';
			return 7;
		}
		$invoiceTypeId = $whIostoreList[0]['invoiceTypeId']; //出入库单据类型
		$storeId = $whIostoreList[0]['storeId']; //仓库id
		try {
			TransactionBaseModel :: begin();
			$nowInsertALevel = 0; //定义一个变量用来存放本次插入的审核等级

			$whARByOrdersn = WhAuditModel :: getAuditRecordsByOrdersn($ordersn); //根据ordersn在records表中查找出最大id，其最大id所在的auditrelationId所关联的auditlevel就是该ordersn已经存在的最大审核级别记录
			if (empty ($whARByOrdersn)) { //如果$whARByOrdersn为空，则表示records表中没有该ordersn的记录，
				$minALevel = WhAuditModel :: getMinALevelByIS($invoiceTypeId, $storeId); //取出list表中invoiceTypeId,storeId下已经开启的最小的auditLevel
				if (empty ($minALevel)) { //如果$minALevel为空，表示list表中不存在开启的$invoiceTypeId，$storeId的记录
					self :: $errCode = 0201;
					self :: $errMsg = 'empty minALevel';
					return 8;
				}
				$whWillInsertAid = WhAuditModel :: getALIdByAA($invoiceTypeId, $storeId, $minALevel, $auditorId); //取得invoiceTypeId，storeId下和本次auditorId匹配的最小level的list中的id
				if (empty ($whWillInsertAid)) { //如果$whWillInsertAid为空，表示该最小的level没有和auditor有对应的关系,可能是不存在这条审核类型记录或者是审核人无权限，或者是审核类型记录被禁用了
					self :: $errCode = 0201;
					self :: $errMsg = 'empty whWillInsertAid';
					return 9;
				}
				$tName = 'wh_audit_records';
				$set = "SET ordersn='$ordersn',auditRelationId='$whWillInsertAid',auditStatus='$auditStatus',auditTime='$now'";
				$affectRow = WhAuditModel :: addTNameRow($tName, $set);
				if (!$affectRow) { //插入错误或者是影响记录数为0时抛出异常
					throw new Exception('add error');
				}
				$nowInsertALevel = $minALevel; //本次插入的审核等级
			} else { //如果$whARByOrdersn不为空，则表示records表中有该ordersn的记录，此时需要找出记录中最大的auditlevel
				$whARAidsArray = array (); //定义一个数组存放$whARByOrdersn中的auditRelationId
				foreach ($whARByOrdersn as $value) {
					$whARAidsArray[] = $value['auditRelationId'];
				}
				$whARAidsString = implode(',', $whARAidsArray); //将$whARAidsArray转化成id1,id2,id3形式的字符串
				$maxALevel = WhAuditModel :: getMaxLevelByAIds($whARAidsString); //查找出$whARAidsString中auditLevel最大的
				if (empty ($maxALevel)) { //如果为空，表示$whARAidsString中在list中都没有已经开启的记录，可能是全被人禁用了
					self :: $errCode = 0201;
					self :: $errMsg = 'empty maxALevel';
					return 10;
				}
				$whNextALevel = WhAuditModel :: getNextLevelByISL($invoiceTypeId, $storeId, $maxALevel); //根据当前最大的auditLevel取得下一个auditLevel的值
				if (empty ($whNextALevel)) { //如果$whNextALevel为空，表示当前record表中最大的审核等级已经是list中最大的审核等级,不存在下一级审核了
					self :: $errCode = 0201;
					self :: $errMsg = 'empty whNextALevel';
					return 11;
				}
				$whWillInsertAid = WhAuditModel :: getALIdByAA($invoiceTypeId, $storeId, $whNextALevel, $auditorId); //查找本次要插入records表记录的auditRelationId
				if (empty ($whWillInsertAid)) { //$whWillInsertAid为空，表示$auditorId不存在该开启的这条记录中
					self :: $errCode = 0201;
					self :: $errMsg = 'empty whWillInsertAid';
					return 12;
				}
				$tName = 'wh_audit_records';
				$set = "SET ordersn='$ordersn',auditRelationId='$whWillInsertAid',auditStatus='$auditStatus',auditTime='$now'";
				$affectRow = WhAuditModel :: addTNameRow($tName, $set);
				if (!$affectRow) {
					throw new Exception('add error2');
				}
				$nowInsertALevel = $whNextALevel; //本次要插入的审核等级
			}

			if ($auditStatus == 2) { //如果本次审核结果为不通过时，反些iostore表中该ordersn的ioStatus为不通过，和endTime
				$tName = 'wh_iostore';
				$set = "SET ioStatus='3',endTime='$now',operatorId='$auditorId' ";
				$where = "WHERE ordersn='$ordersn' AND is_delete=0";
				$affectRow = WhAuditModel :: updateTNameRow($tName, $set, $where);
				if (!$affectRow) {
					throw new Exception('update error1');
				}
			} else { //如果审核为通过时，要判断本次审核是不是最后一次审核，如果是最后一次审核，则要反写ioStatus中的值
				if ($nowInsertALevel == 0) { //如果$nowInsertALevel为0，表示程序异常
					self :: $errCode = 0201;
					self :: $errMsg = 'empty $nowInsertALevel';
					return 13;
				}
				$whNextALevel = WhAuditModel :: getNextLevelByISL($invoiceTypeId, $storeId, $nowInsertALevel); //本次审核等级的下次审核等级
				if (empty ($whNextALevel)) { //如果$whNextALevel为空，表示没有下一级审核，该次审核为最后一次审核，此时要反写ioStatus等信息
					$tName = 'wh_iostore';
					if ($auditStatus == 1) {
						$ioStatus = 2;
					} else {
						$ioStatus = 3;
					}
					$set = "SET ioStatus='$ioStatus',endTime='$now',operatorId='$auditorId' ";
					$where = "WHERE ordersn='$ordersn' AND is_delete=0";
					$affectRow = WhAuditModel :: updateTNameRow($tName, $set, $where);
					if (!$affectRow) {
						throw new Exception('update error2');
					}
					if ($auditStatus == 1) { //如果是最后一级审核，并且审核通过的话，添加出入库记录及加减库存
						$ioTypeList = WhAuditModel :: getIoTypeById($invoiceTypeId); //根据id查出invoiceType表中的ioType和ioTypeId
						if (empty ($ioTypeList)) { //如果没找到对应的ioTypeId，和ioType，则抛出异常
							throw new Exception('empty ioTypeList');
						}
						$ioType = $ioTypeList['ioType'];
						$ioTypeId = $ioTypeList['ioTypeId'];
						$whIoRecordsAct = new WhIoRecordsAct(); //库方法中
						$ioType = $ioType == 0 ? 1 : 2; //在act_addIoRecoresForWh中inType为1表示出库，2表示入库
						$iostoreId = WhAuditModel :: getIdByOrdersn($ordersn); //取得ordersn所在的iostoreId
						$tName = 'wh_iostoredetail';
						$select = '*';
						$where = "WHERE is_delete=0 AND iostoreId='$iostoreId'";
						$iostoreDetailList = WhAuditModel :: getTNameList($tName, $select, $where);
						if (empty ($iostoreDetailList)) {
							throw new Exception('empty iostoreDetailList');
						}

						foreach ($iostoreDetailList as $value) {//进行出入库记录和添加库存操作
							$sku = $value['sku'];
							$amount = $value['amount'];
							$purchaseId = $value['purchaseId'];
                            $userId = $auditorId;
							if (empty ($ordersn)) {
								self :: $errCode = '0301';
								self :: $errMsg = 'empty ordersn';
								return 0;
							}
							if (empty ($sku)) {
								self :: $errCode = '0401';
								self :: $errMsg = 'sku';
								return 0;
							}
							if (empty ($amount)) {
								self :: $errCode = '0501';
								self :: $errMsg = 'empty amount';
								return 0;
							}
							if (empty ($purchaseId)) {
								self :: $errCode = '0601';
								self :: $errMsg = 'empty purchaseId';
								return 0;
							}
							if (empty ($ioType)) {
								self :: $errCode = '0701';
								self :: $errMsg = '';
								return 0;
							}
							if (empty ($ioTypeId)) {
								self :: $errCode = '0801';
								self :: $errMsg = 'empty ioTypeId';
								return 0;
							}
							if (empty ($userId)) {
								self :: $errCode = '0901';
								self :: $errMsg = 'empty userId';
								return 0;
							}
							if (empty ($storeId)) {
								self :: $errCode = '1001';
								self :: $errMsg = 'emptyOrError storeId';
								return 0;
							}
							if ($ioType != 1 && $ioType != 2) {
								self :: $errCode = '1101';
								self :: $errMsg = 'error ioType';
								return 0;
							}
							$tName = 'wh_iorecords';
							$set = "SET ordersn='$ordersn',sku='$sku',amount='$amount',purchaseId='$purchaseId',ioType='$ioType',ioTypeId='$ioTypeId',userId='$userId',reason='$reason',createdTime='$now',storeId='$storeId'";
							$ret = WhIoRecordsModel :: addTNameRow($tName, $set); //添加入库记录
							if ($ret) {
								$tName = 'wh_sku_location';
								if ($ioType == 1) {
									$amount = (-1) * $amount;
								}
								$set = "SET actualStock=actualStock+$amount";
								$where = "WHERE sku='$sku' AND storeId='$storeId'";
								$affectRow = WhIoRecordsModel :: updateTNameRow($tName, $set, $where); //库存变化
								if (!$affectRow) {
									throw new Exception('');
								}
							} else {
								throw new Exception('');
							}
						}
					}
				}
			}
			TransactionBaseModel :: commit();
			TransactionBaseModel :: autoCommit();
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return 200;
		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			TransactionBaseModel :: autoCommit();
			return 404;
		}

	}

	////
	//对外接口，对单据进行审核操作
	function act_auditIoStore() {

		$ordersn = isset ($_GET['ordersn']) ? $_GET['ordersn'] : ''; //单据编码
		$auditStatus = isset ($_GET['auditStatus']) ? $_GET['auditStatus'] : ''; //审核状态，1为通过，2为不通过
		$auditorId = isset ($_GET['auditorId']) ? $_GET['auditorId'] : ''; //审核人id
		$now = time(); //当前时间

		if (empty ($ordersn)) {
			self :: $errCode = 0201;
			self :: $errMsg = 'empty ordersn';
			return 3;
		}
		if (intval($auditStatus) != 1 && intval($auditStatus) != 2) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error auditStatus';
			return 5;
		}
		if (intval($auditorId) == 0) {
			self :: $errCode = 0201;
			self :: $errMsg = 'error auditorId';
			return 6;
		}

		//根据ordersn取出对应的invoiceTypeId,storeId
		$tName = 'wh_iostore';
		$select = 'invoiceTypeId,storeId';
		$where = "WHERE is_delete=0 AND ordersn='$ordersn'";
		$whIostoreList = WhAuditModel :: getTNameList($tName, $select, $where);
		if (empty ($whIostoreList)) { //ioStore表中不存在ordersn这条记录
			self :: $errCode = 0201;
			self :: $errMsg = 'empty whIostoreList';
			return 7;
		}
		$invoiceTypeId = $whIostoreList[0]['invoiceTypeId']; //出入库单据类型
		$storeId = $whIostoreList[0]['storeId']; //仓库id
		try {
			TransactionBaseModel :: begin();
			$nowInsertALevel = 0; //定义一个变量用来存放本次插入的审核等级

			$whARByOrdersn = WhAuditModel :: getAuditRecordsByOrdersn($ordersn); //根据ordersn在records表中查找出最大id，其最大id所在的auditrelationId所关联的auditlevel就是该ordersn已经存在的最大审核级别记录
			if (empty ($whARByOrdersn)) { //如果$whARByOrdersn为空，则表示records表中没有该ordersn的记录，
				$minALevel = WhAuditModel :: getMinALevelByIS($invoiceTypeId, $storeId); //取出list表中invoiceTypeId,storeId下已经开启的最小的auditLevel
				if (empty ($minALevel)) { //如果$minALevel为空，表示list表中不存在开启的$invoiceTypeId，$storeId的记录
					self :: $errCode = 0201;
					self :: $errMsg = 'empty minALevel';
					return 8;
				}
				$whWillInsertAid = WhAuditModel :: getALIdByAA($invoiceTypeId, $storeId, $minALevel, $auditorId); //取得invoiceTypeId，storeId下和本次auditorId匹配的最小level的list中的id
				if (empty ($whWillInsertAid)) { //如果$whWillInsertAid为空，表示该最小的level没有和auditor有对应的关系,可能是不存在这条审核类型记录或者是审核人无权限，或者是审核类型记录被禁用了
					self :: $errCode = 0201;
					self :: $errMsg = 'empty whWillInsertAid';
					return 9;
				}
				$tName = 'wh_audit_records';
				$set = "SET ordersn='$ordersn',auditRelationId='$whWillInsertAid',auditStatus='$auditStatus',auditTime='$now'";
				$affectRow = WhAuditModel :: addTNameRow($tName, $set);
				if (!$affectRow) { //插入错误或者是影响记录数为0时抛出异常
					throw new Exception('add error');
				}
				$nowInsertALevel = $minALevel; //本次插入的审核等级
			} else { //如果$whARByOrdersn不为空，则表示records表中有该ordersn的记录，此时需要找出记录中最大的auditlevel
				$whARAidsArray = array (); //定义一个数组存放$whARByOrdersn中的auditRelationId
				foreach ($whARByOrdersn as $value) {
					$whARAidsArray[] = $value['auditRelationId'];
				}
				$whARAidsString = implode(',', $whARAidsArray); //将$whARAidsArray转化成id1,id2,id3形式的字符串
				$maxALevel = WhAuditModel :: getMaxLevelByAIds($whARAidsString); //查找出$whARAidsString中auditLevel最大的
				if (empty ($maxALevel)) { //如果为空，表示$whARAidsString中在list中都没有已经开启的记录，可能是全被人禁用了
					self :: $errCode = 0201;
					self :: $errMsg = 'empty maxALevel';
					return 10;
				}
				$whNextALevel = WhAuditModel :: getNextLevelByISL($invoiceTypeId, $storeId, $maxALevel); //根据当前最大的auditLevel取得下一个auditLevel的值
				if (empty ($whNextALevel)) { //如果$whNextALevel为空，表示当前record表中最大的审核等级已经是list中最大的审核等级,不存在下一级审核了
					self :: $errCode = 0201;
					self :: $errMsg = 'empty whNextALevel';
					return 11;
				}
				$whWillInsertAid = WhAuditModel :: getALIdByAA($invoiceTypeId, $storeId, $whNextALevel, $auditorId); //查找本次要插入records表记录的auditRelationId
				if (empty ($whWillInsertAid)) { //$whWillInsertAid为空，表示$auditorId不存在该开启的这条记录中
					self :: $errCode = 0201;
					self :: $errMsg = 'empty whWillInsertAid';
					return 12;
				}
				$tName = 'wh_audit_records';
				$set = "SET ordersn='$ordersn',auditRelationId='$whWillInsertAid',auditStatus='$auditStatus',auditTime='$now'";
				$affectRow = WhAuditModel :: addTNameRow($tName, $set);
				if (!$affectRow) {
					throw new Exception('add error2');
				}
				$nowInsertALevel = $whNextALevel; //本次要插入的审核等级
			}

			if ($auditStatus == 2) { //如果本次审核结果为不通过时，反些iostore表中该ordersn的ioStatus为不通过，和endTime
				$tName = 'wh_iostore';
				$set = "SET ioStatus='3',endTime='$now',operatorId='$auditorId' ";
				$where = "WHERE ordersn='$ordersn' AND is_delete=0";
				$affectRow = WhAuditModel :: updateTNameRow($tName, $set, $where);
				if (!$affectRow) {
					throw new Exception('update error1');
				}
			} else { //如果审核为通过时，要判断本次审核是不是最后一次审核，如果是最后一次审核，则要反写ioStatus中的值
				if ($nowInsertALevel == 0) { //如果$nowInsertALevel为0，表示程序异常
					self :: $errCode = 0201;
					self :: $errMsg = 'empty $nowInsertALevel';
					return 13;
				}
				$whNextALevel = WhAuditModel :: getNextLevelByISL($invoiceTypeId, $storeId, $nowInsertALevel); //本次审核等级的下次审核等级
				if (empty ($whNextALevel)) { //如果$whNextALevel为空，表示没有下一级审核，该次审核为最后一次审核，此时要反写ioStatus等信息
					$tName = 'wh_iostore';
					if ($auditStatus == 1) {
						$ioStatus = 2;
					} else {
						$ioStatus = 3;
					}
					$set = "SET ioStatus='$ioStatus',endTime='$now',operatorId='$auditorId' ";
					$where = "WHERE ordersn='$ordersn' AND is_delete=0";
					$affectRow = WhAuditModel :: updateTNameRow($tName, $set, $where);
					if (!$affectRow) {
						throw new Exception('update error2');
					}
					if ($auditStatus == 1) { //如果是最后一级审核，并且审核通过的话，添加出入库记录及加减库存
						$ioTypeList = WhAuditModel :: getIoTypeById($invoiceTypeId); //根据id查出invoiceType表中的ioType和ioTypeId
						if (empty ($ioTypeList)) { //如果没找到对应的ioTypeId，和ioType，则抛出异常
							throw new Exception('empty ioTypeList');
						}
						$ioType = $ioTypeList['ioType'];
						$ioTypeId = $ioTypeList['ioTypeId'];
						$whIoRecordsAct = new WhIoRecordsAct(); //库方法中
						$ioType = $ioType == 0 ? 1 : 2; //在act_addIoRecoresForWh中inType为1表示出库，2表示入库
						$iostoreId = WhAuditModel :: getIdByOrdersn($ordersn); //取得ordersn所在的iostoreId
						$tName = 'wh_iostoredetail';
						$select = '*';
						$where = "WHERE is_delete=0 AND iostoreId='$iostoreId'";
						$iostoreDetailList = WhAuditModel :: getTNameList($tName, $select, $where);
						if (empty ($iostoreDetailList)) {
							throw new Exception('empty iostoreDetailList');
						}

						foreach ($iostoreDetailList as $value) {//进行出入库记录和添加库存操作
							$sku = $value['sku'];
							$amount = $value['amount'];
							$purchaseId = $value['purchaseId'];
                            $userId = $auditorId;
							if (empty ($ordersn)) {
								self :: $errCode = '0301';
								self :: $errMsg = 'empty ordersn';
								return 0;
							}
							if (empty ($sku)) {
								self :: $errCode = '0401';
								self :: $errMsg = 'sku';
								return 0;
							}
							if (empty ($amount)) {
								self :: $errCode = '0501';
								self :: $errMsg = 'empty amount';
								return 0;
							}
							if (empty ($purchaseId)) {
								self :: $errCode = '0601';
								self :: $errMsg = 'empty purchaseId';
								return 0;
							}
							if (empty ($ioType)) {
								self :: $errCode = '0701';
								self :: $errMsg = '';
								return 0;
							}
							if (empty ($ioTypeId)) {
								self :: $errCode = '0801';
								self :: $errMsg = 'empty ioTypeId';
								return 0;
							}
							if (empty ($userId)) {
								self :: $errCode = '0901';
								self :: $errMsg = 'empty userId';
								return 0;
							}
							if (empty ($storeId)) {
								self :: $errCode = '1001';
								self :: $errMsg = 'emptyOrError storeId';
								return 0;
							}
							if ($ioType != 1 && $ioType != 2) {
								self :: $errCode = '1101';
								self :: $errMsg = 'error ioType';
								return 0;
							}
							$tName = 'wh_iorecords';
							$set = "SET ordersn='$ordersn',sku='$sku',amount='$amount',purchaseId='$purchaseId',ioType='$ioType',ioTypeId='$ioTypeId',userId='$userId',reason='$reason',createdTime='$now',storeId='$storeId'";
							$ret = WhIoRecordsModel :: addTNameRow($tName, $set); //添加入库记录
							if ($ret) {
								$tName = 'wh_sku_location';
								if ($ioType == 1) {
									$amount = (-1) * $amount;
								}
								$set = "SET actualStock=actualStock+$amount";
								$where = "WHERE sku='$sku' AND storeId='$storeId'";
								$affectRow = WhIoRecordsModel :: updateTNameRow($tName, $set, $where); //库存变化
								if (!$affectRow) {
									throw new Exception('');
								}
							} else {
								throw new Exception('');
							}
						}
					}
				}
			}
			TransactionBaseModel :: commit();
			TransactionBaseModel :: autoCommit();
			self :: $errCode = 200;
			self :: $errMsg = 'success';
			return 200;
		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			TransactionBaseModel :: autoCommit();
			return 404;
		}

	}
	
	//审核人验证是否存在
	function act_auditorNameVerify() { 
		$auditorName   = trim($_POST['whData']);
		$usermodel     = UserModel::getInstance();
		$whereStr	   = "where a.global_user_name = '".$auditorName."'";         
		$auditorUserId = $usermodel->getGlobalUserLists('global_user_id',$whereStr,'','');//$auditorUserId[0]['global_user_id'];	
		if(empty($auditorUserId)){
			self :: $errCode = '4444';
			return false;
		}else{
			self :: $errCode = '200';
			return $auditorUserId;
		}
	}
	

}