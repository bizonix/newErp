<?php


/*
 * 出入库记录action
 * ADD BY zqt 2013.8.23
 */
class WhIoRecordsAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 取得指定iorecords记录
	 */
	function act_getTNameList($tName, $set, $where) { //表名，SET，WHERE
		$list = WhIoRecordsModel :: getTNameList($tName, $set, $where);
		if (is_array($list)) {
			return $list;
		} else {
			self :: $errCode = WhIoRecordsModel :: $errCode;
			self :: $errMsg = WhIoRecordsModel :: $errMsg;
			return false;
		}
	}

	function act_getTNameCount($tName, $where) {
		$ret = WhIoRecordsModel :: getTNameCount($tName, $where);
		if ($ret !== false) {
			return $ret;
		} else {
			self :: $errCode = WhIoRecordsModel :: $errCode;
			self :: $errMsg = WhIoRecordsModel :: $errMsg;
			return false;
		}
	}

	//仓库内部调用action
	//添加出入库记录
	function act_addIoRecoresForWh($paraArr) { //参数数组，具体键如下
		try {
			TransactionBaseModel :: begin();
			if (empty ($paraArr)) {
				self :: $errCode = '0101';
				self :: $errMsg = 'empty paraArr';
				throw new Exception('');
			}
			if (!is_array($paraArr)) {
				self :: $errCode = '0201';
				self :: $errMsg = 'is not array';
				throw new Exception('');
			}
			$ordersn = $paraArr['ordersn']; //发货单号或者是单据的ordersn
			$sku = $paraArr['sku']; //sku
			$amount = $paraArr['amount']; //数量
			$purchaseId = $paraArr['purchaseId']; //采购员id
			$ioType = $paraArr['ioType']; //出/入库，1为出库，2为入库
			$ioTypeId = $paraArr['ioTypeId']; //出入库类型id，即出入库类型表中对应的id
			$userId = $paraArr['userId']; //添加人id
			$reason = isset ($paraArr['reason']) ? $paraArr['reason'] : ''; //原因
			$storeId = isset ($paraArr['storeId']) ? intval($paraArr['storeId']) : 1;//仓库，默认为1
			$positionId = isset ($paraArr['positionId']) ? $paraArr['positionId'] : 0; //仓位id
			
			$createdTime = time();
			$tName = 'wh_iorecords';
			if (empty ($ordersn)) {
				self :: $errCode = '0301';
				self :: $errMsg = 'empty ordersn';
				throw new Exception('');
			}
			if (empty ($sku)) {
				self :: $errCode = '0401';
				self :: $errMsg = 'sku';
				throw new Exception('');
			}
			if (empty ($amount)) {
				self :: $errCode = '0501';
				self :: $errMsg = 'empty amount';
				throw new Exception('');
			}
			if (empty ($purchaseId)) {
				self :: $errCode = '0601';
				self :: $errMsg = 'empty purchaseId';
				throw new Exception('');
			}
			if (empty ($ioType)) {
				self :: $errCode = '0701';
				self :: $errMsg = '';
				throw new Exception('');
			}
			if (empty ($ioTypeId)) {
				self :: $errCode = '0801';
				self :: $errMsg = 'empty ioTypeId';
				throw new Exception('');
			}
			if (empty ($userId)) {
				self :: $errCode = '0901';
				self :: $errMsg = 'empty userId';
				throw new Exception('');
			}
			if (empty ($storeId)) {
				self :: $errCode = '1001';
				self :: $errMsg = 'emptyOrError storeId';
				throw new Exception('');
			}
			if ($ioType != 1 && $ioType != 2) {
				self :: $errCode = '1101';
				self :: $errMsg = 'error ioType';
				throw new Exception('');
			}
			$set = "SET ordersn='$ordersn',sku='$sku',amount='$amount',positionId='$positionId',purchaseId='$purchaseId',ioType='$ioType',ioTypeId='$ioTypeId',userId='$userId',reason='$reason',createdTime='$createdTime',storeId='$storeId'";
			$ret = WhIoRecordsModel :: addTNameRow($tName, $set); //添加入库记录
			if ($ret) {
				$tName = 'wh_sku_location';
				if ($ioType == 1) {
					$actualStock = (-1) * $amount;
				}
				$set = "SET actualStock=actualStock+$actualStock";
				$where = "WHERE sku='$sku' AND storeId='$storeId'";
				$affectRow = WhIoRecordsModel :: updateTNameRow($tName, $set, $where);//总库存变化
				if ($affectRow) {
					if(!empty($positionId)){
						$sku_info = OmAvailableModel::getTNameList("pc_goods","id","where sku='$sku' and is_delete=0");
						$skuId    = $sku_info[0]['id'];
						
						$pName = 'wh_product_position_relation';
						if ($ioType == 1) {
							$nums = (-1) * $amount;
						}
						$set = "SET nums=nums+$nums";
						$where = "WHERE pId='$skuId' AND positionId='$positionId' AND is_delete=0";
						$affectRow = WhIoRecordsModel :: updateTNameRow($pName, $set, $where);//仓位库存变化
						if ($affectRow) {
							self :: $errCode = '200';
							self :: $errMsg = 'success';
							TransactionBaseModel :: commit();
							TransactionBaseModel :: autoCommit();
							return 1;
						} else {
							self :: $errCode = '1201'; //库存加减错误
							self :: $errMsg = 'addAmount error';
							throw new Exception('');
						}
					}else{
						self :: $errCode = '200';
						self :: $errMsg = 'success';
						TransactionBaseModel :: commit();
						TransactionBaseModel :: autoCommit();
						return 1;
					}
				} else {
					self :: $errCode = '1201'; //库存加减错误
					self :: $errMsg = 'addAmount error';
					throw new Exception('');
				}
				

			} else {
				self :: $errCode = '1301'; //添加记录错误
				self :: $errMsg = 'addRecords error';
				throw new Exception('');
			}
		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			TransactionBaseModel :: autoCommit();
			return 0;
		}
	}

	//外部调用api
	//添加出入库记录
	function act_addIoRecores() {
		$paraArr = isset ($_GET['paraArr']) ? $_GET['paraArr'] : ''; //参数base64,json数组，具体键如下,
		try {
			TransactionBaseModel :: begin();
			if (empty ($paraArr)) {
				self :: $errCode = '0101';
				self :: $errMsg = 'empty paraArr';
				throw new Exception('');
			}
			$paraArr = json_decode(base64_decode($paraArr), true); //对base64及json解码
			if (!is_array($paraArr)) {
				self :: $errCode = '0201';
				self :: $errMsg = 'is not array';
				throw new Exception('');
			}
			$ordersn = $paraArr['ordersn']; //发货单号或者是单据的ordersn
			$sku = $paraArr['sku']; //sku
			$amount = $paraArr['amount']; //数量
			$purchaseId = $paraArr['purchaseId']; //采购员id
			$ioType = $paraArr['ioType']; //出/入库，1为出库，2为入库
			$ioTypeId = $paraArr['ioTypeId']; //出入库类型id，即出入库类型表中对应的id
			$userId = $paraArr['userId']; //添加人id
			$reason = isset ($paraArr['reason']) ? $paraArr['reason'] : ''; //原因
			$storeId = isset ($paraArr['storeId']) ? intval($paraArr['storeId']) : 1;
			$createdTime = time();
			$tName = 'wh_iorecords';
			if (empty ($ordersn)) {
				self :: $errCode = '0301';
				self :: $errMsg = 'empty ordersn';
				throw new Exception('');
			}
			if (empty ($sku)) {
				self :: $errCode = '0401';
				self :: $errMsg = 'sku';
				throw new Exception('');
			}
			if (empty ($amount)) {
				self :: $errCode = '0501';
				self :: $errMsg = 'empty amount';
				throw new Exception('');
			}
			if (empty ($purchaseId)) {
				self :: $errCode = '0601';
				self :: $errMsg = 'empty purchaseId';
				throw new Exception('');
			}
			if (empty ($ioType)) {
				self :: $errCode = '0701';
				self :: $errMsg = '';
				throw new Exception('');
			}
			if (empty ($ioTypeId)) {
				self :: $errCode = '0801';
				self :: $errMsg = 'empty ioTypeId';
				throw new Exception('');
			}
			if (empty ($userId)) {
				self :: $errCode = '0901';
				self :: $errMsg = 'empty userId';
				throw new Exception('');
			}
			if (empty ($storeId)) {
				self :: $errCode = '1001';
				self :: $errMsg = 'emptyOrError storeId';
				throw new Exception('');
			}
			if ($ioType != 1 && $ioType != 2) {
				self :: $errCode = '1101';
				self :: $errMsg = 'error ioType';
				throw new Exception('');
			}
			$set = "SET ordersn='$ordersn',sku='$sku',amount='$amount',purchaseId='$purchaseId',ioType='$ioType',ioTypeId='$ioTypeId',userId='$userId',reason='$reason',createdTime='$createTime',storeId='$storeId'";
			$ret = WhIoRecordsModel :: addTNameRow($tName, $set); //添加入库记录
			if ($ret) {
				$tName = 'wh_sku_location';
				if ($ioType == 1) {
					$amount = (-1) * $amount;
				}
				$set = "SET actualStock=actualStock+$amount";
				$where = "WHERE sku='$sku' AND storeId='$storeId'";
				$affectRow = WhIoRecordsModel :: updateTNameRow($tName, $set, $where);
				if ($affectRow) {
					self :: $errCode = '200';
					self :: $errMsg = 'success';
					TransactionBaseModel :: commit();
					TransactionBaseModel :: autoCommit();
					return 1;
				} else {
					self :: $errCode = '1201'; //库存加减错误
					self :: $errMsg = 'addAmount error';
					throw new Exception('');
				}

			} else {
				self :: $errCode = '1301'; //添加记录错误
				self :: $errMsg = 'addRecords error';
				throw new Exception('');
			}
		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			TransactionBaseModel :: autoCommit();
			return 0;
		}

	}
    
    
    //仓库内部调用action
	//添加出入库记录
	function act_addOneIoRecoresForWh($paraArr) { //参数数组，具体键如下
			if (empty ($paraArr)) {
				self :: $errCode = '0101';
				self :: $errMsg = 'empty paraArr';
				throw new Exception('');
			}
			if (!is_array($paraArr)) {
				self :: $errCode = '0201';
				self :: $errMsg = 'is not array';
				throw new Exception('');
			}
			$ordersn = $paraArr['ordersn']; //发货单号或者是单据的ordersn
			$sku = $paraArr['sku']; //sku
			$amount = $paraArr['amount']; //数量
			$purchaseId = $paraArr['purchaseId']; //采购员id
			$ioType = $paraArr['ioType']; //出/入库，1为出库，2为入库
			$ioTypeId = $paraArr['ioTypeId']; //出入库类型id，即出入库类型表中对应的id
			$userId = $paraArr['userId']; //添加人id
			$reason = isset ($paraArr['reason']) ? $paraArr['reason'] : ''; //原因
			$storeId = isset ($paraArr['storeId']) ? intval($paraArr['storeId']) : 1;//仓库，默认为1
			$createdTime = time();
			
			if (empty ($ordersn)) {
				self :: $errCode = '0301';
				self :: $errMsg = 'empty ordersn';
				throw new Exception('');
			}
			if (empty ($sku)) {
				self :: $errCode = '0401';
				self :: $errMsg = 'sku';
				throw new Exception('');
			}
			if (empty ($amount)) {
				self :: $errCode = '0501';
				self :: $errMsg = 'empty amount';
				throw new Exception('');
			}
			if (empty ($purchaseId)) {
				self :: $errCode = '0601';
				self :: $errMsg = 'empty purchaseId';
				throw new Exception('');
			}
			if (empty ($ioType)) {
				self :: $errCode = '0701';
				self :: $errMsg = '';
				throw new Exception('');
			}
			if (empty ($ioTypeId)) {
				self :: $errCode = '0801';
				self :: $errMsg = 'empty ioTypeId';
				throw new Exception('');
			}
			if (empty ($userId)) {
				self :: $errCode = '0901';
				self :: $errMsg = 'empty userId';
				throw new Exception('');
			}
			if (empty ($storeId)) {
				self :: $errCode = '1001';
				self :: $errMsg = 'emptyOrError storeId';
				throw new Exception('');
			}
			if ($ioType != 1 && $ioType != 2) {
				self :: $errCode = '1101';
				self :: $errMsg = 'error ioType';
				throw new Exception('');
			}
            $tName = 'wh_iorecords';
			$set = "SET ordersn='$ordersn',sku='$sku',amount='$amount',purchaseId='$purchaseId',ioType='$ioType',ioTypeId='$ioTypeId',userId='$userId',reason='$reason',createdTime='$createdTime',storeId='$storeId'";
			$ret = WhIoRecordsModel :: addTNameRow($tName, $set); //添加入库记录
			if ($ret) {
				self :: $errCode = '200';
				self :: $errMsg = 'success';
				return 1;
			} else {
				self :: $errCode = '404'; //添加记录错误
				self :: $errMsg = 'addRecords error';
				throw new Exception('');
			}
	}
    
    function act_updateActualStock($paraArr){
        $sku 		= $paraArr['sku']; //sku
		$pId		= $paraArr['pId']; //skuId
		$positionId = $paraArr['positionId']; //仓位ID
	    $amount	    = $paraArr['amount']; //数量
        $ioType	    = $paraArr['ioType']; //出/入库，1为出库，2为入库
        $storeId    = isset ($paraArr['storeId']) ? intval($paraArr['storeId']) : 1;//仓库，默认为1
        
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
        if ($ioType != 1 && $ioType != 2) {
				self :: $errCode = '1101';
				self :: $errMsg = 'error ioType';
			return 0;
		}
        
        $tName = 'wh_sku_location';
		if ($ioType == 1) {
			$amount = (-1) * $amount;
		}else{
			$amount = "+".$amount;
		}
		$set = "SET actualStock=actualStock".$amount;
		$where = "WHERE sku='$sku' AND storeId='$storeId'";
		$affectRow = WhIoRecordsModel :: updateTNameRow($tName, $set, $where);	
		if ($affectRow) {
			$tName2     = 'wh_product_position_relation';
			$set2       = "SET nums=nums".$amount;
			$where2     = "WHERE pId='$pId' AND positionId='$positionId' LIMIT 1";
			$affectRow2 = WhIoRecordsModel :: updateTNameRow($tName2, $set2, $where2);			
			self :: $errCode = '200';
			self :: $errMsg = 'success';
			return 1;
		} else {
			self :: $errCode = '404'; //库存加减错误
			self :: $errMsg = 'error';
			return 0;
		}
    }
    
	/*
     * 出入库报表导出
     */
	public function act_export(){
		$ioType     = intval($_GET['ioType']);
		$id         = isset ($_GET['id']) ? post_check($_GET['id']) : '';
		$ordersn    = isset ($_GET['ordersn']) ? post_check($_GET['ordersn']) : '';
		$ioTypeId   = isset ($_GET['ioTypeId']) ? post_check($_GET['ioTypeId']) : '';
		$sku        = isset ($_GET['sku']) ? post_check($_GET['sku']) : '';
		$purchaseId = isset ($_GET['purchaseId']) ? post_check($_GET['purchaseId']) : '';
		$userId     = isset ($_GET['userId']) ? post_check($_GET['userId']) : '';
		$cStartTime = isset ($_GET['cStartTime']) ? post_check($_GET['cStartTime']) : '';
		$cEndTime   = isset ($_GET['cEndTime']) ? post_check($_GET['cEndTime']) : '';
		
		if(empty($id)&&empty($ordersn)&&empty($ioTypeId)&&empty($sku)&&empty($purchaseId)&&empty($userId)&&empty($cStartTime)&&empty($cEndTime)){
			echo "请选择导出条件";exit;
		}
		
		$where = "WHERE ioType='$ioType' ";
		if (!empty ($id)) {
			$where .= "AND id='$id' ";
		}
		if (!empty ($ordersn)) {
			$where .= "AND ordersn='$ordersn' ";
		}
		if (!empty ($ioTypeId)) {
			$where .= "AND ioTypeId='$ioTypeId' ";
		}
		if (!empty ($sku)) {
			$where .= "AND sku='$sku' ";
		}
		if (!empty ($purchaseId)) {
			$purchaseId = getUserIdByName($purchaseId);
			$where .= "AND purchaseId='$purchaseId' ";
		}
		if (!empty ($userId)) {
			$userId = getUserIdByName($userId);
			$where .= "AND userId='$userId' ";
		}
		if (!empty ($cStartTime)) {
			$startTime = strtotime($cStartTime.'00:00:00');
			$where .= "AND createdTime >='$startTime' ";
		}
		if (!empty ($cEndTime)) {
			$endTime = strtotime($cEndTime.'23:59:59');
			$where .= "AND createdTime <='$endTime' ";
		}
		$lists = WhIoRecordsModel :: getTNameList('wh_iorecords', '*', $where);
		
		if($ioType==1){
			$excel  = new ExportDataExcel('browser', "out_warehouse.".date('Y-m-d').".xls"); 
		}else{
			$excel  = new ExportDataExcel('browser', "in_warehouse.".date('Y-m-d').".xls"); 
		}
		
		$excel->initialize();
		$tharr = array("日期","料号","仓位","数量","类型","申请人","订单号","备注");
		$excel->addRow($tharr);
		
		foreach($lists as $list){			
			$time	  = date('Y/m/d',$list['createdTime']);
			$sku      = $list['sku'];
			$position_info = whShelfModel::selectPosition("where id={$list['positionId']}");
			$pName	  = $position_info[0]['pName'];
			$num 	  = $list['amount'];
			$ioTypeName = WhIoStoreModel :: getIoTypeNameById($list['ioTypeId']);
			$user     = getUserNameById($list['userId']);
			$ordersn  = $list['ordersn'];
			$reason   = $list['reason'];
			
			$tdarr	  = array($time,$sku,$pName,$num,$ioTypeName,$user,$ordersn,$reason);
			$excel->addRow($tdarr);	
		}
	
		$excel->finalize();
		exit;
	}
}
?>
