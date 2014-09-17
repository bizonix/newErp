<?php
/**
*类名：盘点管理
*功能：处理盘点信息
*作者：hws
*
*/
class InventoryAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	//获取申请盘点记录列表
	function act_getWaitInvList($select = '*',$where){
		$list =	WaitInventoryModel::getWaitInvList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = WaitInventoryModel::$errCode;
			self::$errMsg  = WaitInventoryModel::$errMsg;
			return false;
		}
	}
	
	//获取申请盘点记录数量
	function act_getWaitInvNum($where){
		//调用model层获取数据
		$list =	WaitInventoryModel::getWaitInvNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = WaitInventoryModel::$errCode;
			self::$errMsg  = WaitInventoryModel::$errMsg;
			return false;
		}
	}
	
	//获取盘点记录列表
	function act_getInvRecordList($select = '*',$where){
		$list =	InvRecordModel::getInvRecordList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = InvRecordModel::$errCode;
			self::$errMsg  = InvRecordModel::$errMsg;
			return false;
		}
	}
	
	//获取盘点记录数量
	function act_getInvNum($where){
		//调用model层获取数据
		$list =	InvRecordModel::getInvNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = InvRecordModel::$errCode;
			self::$errMsg  = InvRecordModel::$errMsg;
			return false;
		}
	}
	
	//提交盘点申请
	function  act_sumbAppInv(){
		$data     = array();
		$userId   = $_SESSION['userId'];
		$sku      = post_check(trim($_POST['sku']));
		$reasonId = trim($_POST['reasonId']);
		$sku_info = InvRecordModel::getSkuInfo($sku);
		$data = array(
			'sku' 	   		   => $sku,
			'applicantId' 	   => $userId,
			'applicantionTime' => time(),
			'invReasonId'	   => $reasonId,
			'invStatus'        => 0,
			'systemNums'  	   => $sku_info['actualStock'],
		);

		$insertid = WaitInventoryModel::insertRow($data);
		if($insertid){
			return true;
		}else{
			return false;
		}		
	}
	
	//提交盘点
	function  act_sumbInv(){
	    $log_file   =   'update_inventory/'.date('Y-m-d').".txt";
        $date       =   date('Y-m-d H:i:s');
		$userCnName  = $_SESSION['userCnName'];
		$data        = array();
		$invType     = 0;
		$adjustNums  = 0;
		$auditStatus = 1;
		$sku      = post_check(trim($_POST['sku']));
		$location = post_check(trim($_POST['location']));
		$invNums  = post_check(trim($_POST['invNums']));
		$reasonId = trim($_POST['reasonId']);		
		$sku = get_goodsSn($sku);
		
		$where    = " where sku = '{$sku}'";
		$skuinfo  = whShelfModel::selectSku($where);
		if(empty($skuinfo)){
			self::$errCode = 401;
			self::$errMsg  = "无该料号信息";
            $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, sql条件:%s \r\n", $sku, $sku, $date, self::$errMsg,
                                        $skuinfo, $where);
            write_log($log_file, $log_info);
			return false;
		}else{
			$skuId = $skuinfo['id'];
		}
		
		$sku_positon_list = InvRecordModel::getSkuPosition($skuId,$location);
		if(empty($sku_positon_list)){
			self::$errCode = 402;
			self::$errMsg  = "料号和仓位信息有误";
            $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                        $sku_positon_list, $skuId, $location);
            write_log($log_file, $log_info);
			return false;
		}else{
			//$sku_num  =  $sku_positon_list['nums'];
			$sku_num    =  CommonModel::getGoodsCount($sku);
            if($sku_num === FALSE){
                self::$errCode = 402;
    			self::$errMsg  = "获取旧ERP库存失败!";
                $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s \r\n", $sku, $date, self::$errMsg,
                                        $sku_num, $sku);
                write_log($log_file, $log_info);
    			return false;
            }
			$positionId =  $sku_positon_list['poid'];
		}

		//审核条件
		$condition = InvConditionModel::getInvConditionList("*","where companyId=1 and is_enable=1");

		if($invNums>$sku_num){
			$invType = 1;         //盘盈
		}else if($invNums<$sku_num){
			$invType = 2;         //盘亏
		}else{
			$invType = 0;
		}
		
		//调整数量
		$adjustNums = abs($invNums-$sku_num);
		$adjust_num = $invNums-$sku_num;
		
		//是否需审核
		if($condition){
			foreach($condition as $cond){
				if(($cond['id']==1) && ($adjustNums>=$cond['auditValues'])){
					$auditStatus = 0;
				}
				if($cond['id']==2){
					$sku_cost = InvRecordModel::getSkuCost($sku);
					if(($adjustNums*$sku_cost['goodsCost'])>=$cond['auditValues']){
						$auditStatus = 0;
					}
				}
			}
		}
		OmAvailableModel::begin();
		$data = array(
			'sku' 	      => $sku,
			'location'    => $location,
			'reasonId'    => $reasonId,
			'auditStatus' => $auditStatus,
			'adjustNums'  => $adjustNums,
			'systemNums'  => $sku_num,
			'invNums' 	  => $invNums,
			'invType' 	  => $invType,
			'invPeople'   => $_SESSION['userId'],
			'invTime'     => time(),
		);

		$insertid = InvRecordModel::insertRow($data);
		if(!$insertid){
			self::$errCode = "401";
			self::$errMsg  = "插入盘点记录失败";
            $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s \r\n", $sku, $date, self::$errMsg,
                                        $insertid, json_encode($data));
            write_log($log_file, $log_info);
			return false;
		}
		
		$ioType = 2;
		$ioTypeId = 10;
		$reason = '盘点入库';
		if($auditStatus==1){
			if($invType!=0){
				$update_onhand = CommonModel::adjustInventory($sku,$adjust_num,$userCnName);
				if($update_onhand==0){
					self::$errCode = 415;
					self::$errMsg = "更新旧erp库存失败";
                    $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $sku, $date, self::$errMsg,
                                        $update_onhand, $sku, $adjust_num, $userCnName);
                    write_log($log_file, $log_info);
					OmAvailableModel :: rollback();
					return false;
				}
			}
		
			if($invType==2){
				$adjustNums = "-".$adjustNums;
				$ioType = 1;
				$ioTypeId = 11;
				$reason = '盘点出库';
			}
			$tName = 'wh_sku_location';
			$set   = "SET actualStock=actualStock+'$adjustNums'";
			$where = "WHERE sku='$sku' AND storeId=1";
			$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);//库存变化
			if($affectRow===false){
				self::$errCode = "402";
				self::$errMsg  = "更新总库存失败";
                $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $sku, $date, self::$errMsg,
                                        $affectRow, $tName, $set, $where);
                write_log($log_file, $log_info);
				OmAvailableModel::rollback();
				return false;
			}
			$pName = 'wh_product_position_relation';
			$pset   = "SET nums=nums+'$adjustNums'";
			$pwhere = "WHERE pId='$skuId' AND positionId='$positionId' AND storeId=1 AND is_delete=0";
			$paffectRow = OmAvailableModel :: updateTNameRow($pName, $pset, $pwhere);//库存变化
			if($paffectRow===false){
				self::$errCode = "403";
				self::$errMsg  = "更新库存失败";
                $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $sku, $date, self::$errMsg,
                                        $paffectRow, $pName, $pset, $pwhere);
                write_log($log_file, $log_info);
				OmAvailableModel::rollback();
				return false;
			}
			
			/**** 插入出入库记录 *****/
			if($adjustNums!=0){
				$skuinfo = whShelfModel::selectSku(" where sku = '{$sku}'");
				$paraArr = array(
					'sku'     	 => $sku,
					'amount'  	 => abs($adjustNums),
					'positionId' => $positionId,
					'purchaseId' => $skuinfo['purchaseId'],
					'ioType'	 => $ioType,
					'ioTypeId'   => $ioTypeId,
					'userId'	 => $_SESSION['userId'],
					'reason'	 => $reason,
				);
				$record = CommonModel::addIoRecores($paraArr);     //出库记录
				if(!$record){
					self::$errCode = 413;
					self::$errMsg = "插入出入库记录失败！";
                    $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s \r\n", $sku, $date, self::$errMsg,
                                        $record, json_encode($paraArr));
                    write_log($log_file, $log_info);
					TransactionBaseModel :: rollback();
					return false;
					
				}
			}
			
			//更新申请盘点表
			$waitInf = WaitInventoryModel::updateInv($sku,$_SESSION['userId']);
			if(!$waitInf){
				self::$errCode = 414;
				self::$errMsg = "更新申请盘点表失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                        $waitInf, $sku, $_SESSION['userId']);
                write_log($log_file, $log_info);
				TransactionBaseModel :: rollback();
				return false;
				
			}
		}
		self::$errMsg  = "盘点成功";
		OmAvailableModel::commit();
		return true;	
	}
	
	//盘点审核通过
	function  act_surePass(){
	    $log_file   =   'update_inventory/'.date('Y-m-d').".txt";
        $date       =   date('Y-m-d H:i:s');
		$userCnName  = $_SESSION['userCnName'];
		$data = array();
		$id   = trim($_GET['id']);
		$data = array(
			'auditStatus' => 1
		);
		$Inv_info 	   = InvRecordModel::getInvRecordList("*","where id='$id'");
		$position_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='{$Inv_info[0]['location']}'");
		$skuinfo  	   = whShelfModel::selectSku(" where sku='{$Inv_info[0]['sku']}'");
		OmAvailableModel::begin();
		$updatedata = InvRecordModel::update($data,"and id='$id'");
		if(!$updatedata){
		    self::$errMsg  = '更新盘点记录表失败!';
		    $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s \r\n", $Inv_info[0]['sku'], $date, self::$errMsg,
                                        $updatedata, json_encode($data), $id);
            write_log($log_file, $log_info);
			return false;
		}
        $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s \r\n", $Inv_info[0]['sku'], $date, '更新盘点记录成功',
                                        $updatedata, json_encode($data), $id);
        write_log($log_file, $log_info);

		$tName     = 'wh_product_position_relation';
		$set       = "SET nums='{$Inv_info[0]['invNums']}'";
		$where     = "WHERE pId='{$skuinfo['id']}' AND positionId='{$position_info[0]['id']}' AND is_delete=0 AND storeId=1";
		$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);//库存变化
		if($affectRow===false){
            self::$errMsg  = '更新仓位库存失败!';
		    $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $Inv_info[0]['sku'], $date, self::$errMsg,
                                        $affectRow, $tName, $set, $where);
            write_log($log_file, $log_info);
			OmAvailableModel::rollback();
			return false;
		}
         $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $Inv_info[0]['sku'], $date, '更新仓位库存成功',
                                        $affectRow, $tName, $set, $where);
         write_log($log_file, $log_info);
		
		$adjustNums = $Inv_info[0]['adjustNums'];
		if($Inv_info[0]['invType']==2){
			$adjustNums = "-".$adjustNums;
		}
		$tName = 'wh_sku_location';
		$set   = "SET actualStock=actualStock+'$adjustNums'";
		$where = "WHERE sku='{$Inv_info[0]['sku']}' AND storeId=1";
		$affectRow = WhIoRecordsModel :: updateTNameRow($tName, $set, $where);//库存变化
		if($affectRow===false){
		    self::$errMsg  = '更新总库存失败!';
		    $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $Inv_info[0]['sku'], $date, self::$errMsg,
                                        $affectRow, $tName, $set, $where);
            write_log($log_file, $log_info);
			OmAvailableModel::rollback();
			return false;
		}
        $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $Inv_info[0]['sku'], $date, '更新总库存成功',
                                    $affectRow, $tName, $set, $where);
        write_log($log_file, $log_info);
		
		/**** 插入出入库记录 *****/
		if($Inv_info[0]['invType']==2){
			$ioType = 1;
			$ioTypeId = 11;
			$reason = '盘点出库';
		}else{
			$ioType = 2;
			$ioTypeId = 10;
			$reason = '盘点入库';
		}
		if($adjustNums!=0){
		
			$skuinfo = whShelfModel::selectSku(" where sku = '{$Inv_info[0]['sku']}'");
			$paraArr = array(
				'sku'     	 => $Inv_info[0]['sku'],
				'amount'  	 => abs($adjustNums),
				'positionId' => $position_info[0]['id'],
				'purchaseId' => $skuinfo['purchaseId'],
				'ioType'	 => $ioType,
				'ioTypeId'   => $ioTypeId,
				'userId'	 => $_SESSION['userId'],
				'reason'	 => $reason,
			);
			$record = CommonModel::addIoRecores($paraArr);     //出库记录
			if(!$record){
                self::$errMsg  = "更新入库记录失败";
    		    $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s \r\n", $Inv_info[0]['sku'], $date, self::$errMsg,
                                            $record, json_encode($paraArr));
                write_log($log_file, $log_info);
				OmAvailableModel::rollback();
				return false;
			}
            
            $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s \r\n", $Inv_info[0]['sku'], $date, '更新入库记录成功',
                                        $record, json_encode($paraArr));
            write_log($log_file, $log_info);
			
			//更新申请盘点表
			$waitInf = WaitInventoryModel::updateInv($Inv_info[0]['sku'],$_SESSION['userId']);
			if(!$waitInf){
				self::$errCode = 414;
				self::$errMsg = "更新申请盘点表失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s \r\n", $Inv_info[0]['sku'], $date, self::$errMsg,
                                            $waitInf, $Inv_info[0]['sku'], $_SESSION['userId']);
                write_log($log_file, $log_info);
				TransactionBaseModel :: rollback();
				return false;
			}
            $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s \r\n", $Inv_info[0]['sku'], $date, '更新申请盘点表成功',
                                            $waitInf, $Inv_info[0]['sku'], $_SESSION['userId']);
            write_log($log_file, $log_info);
            
            /** 更新老ERP库存**/
            $update_onhand = CommonModel::adjustInventory($Inv_info[0]['sku'],$adjustNums,$userCnName);
			if($update_onhand['errCode'] != 200){
				self::$errCode = 415;
				self::$errMsg = "更新旧erp库存失败";
    		    $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $Inv_info[0]['sku'], $date, self::$errMsg,
                                            is_array($update_onhand) ? json_encode($update_onhand) : $update_onhand, $Inv_info[0]['sku'], $adjustNums, $userCnName);
                write_log($log_file, $log_info);
				OmAvailableModel :: rollback();
				return false;
			}
            
            $log_info      = sprintf("料号：%s, 时间：%s,提示信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $Inv_info[0]['sku'], $date, '更新旧ERP库存成功',
                                        is_array($update_onhand) ? json_encode($update_onhand) : $update_onhand, $Inv_info[0]['sku'], $adjustNums, $userCnName);
            write_log($log_file, $log_info);
            /** end**/
		}
		
		OmAvailableModel::commit();
		return true;
	}
	
	//盘点审核不通过
	function  act_sureNoPass(){
		$data = array();
		$id   = trim($_GET['id']);
		$data = array(
			'auditStatus' => 2
		);
		$updatedata = InvRecordModel::update($data,"and id='$id'");
		if($updatedata){
			return true;
		}else{
			return false;
		}		
	}
	
	//盘点审核通过
	function  act_allPass(){
		$userCnName  = $_SESSION['userCnName'];
		$id_arr  = $_POST['id'];
		$f_count = count($id_arr);
		$id      = implode(',',$id_arr);		
		$where   = "where id in(".$id.") and auditStatus=0";
		$record_list = InvRecordModel::getInvRecordList("*",$where);
		$s_count 	 = count($record_list);
		if($f_count!=$s_count){
			self::$errCode = "401";
			self::$errMsg  = "当前包含有不用审核的订单，请确认！";
			return false;
		}
		OmAvailableModel::begin();
		foreach($record_list as $record){
			$data = array();
			$id   = $record['id'];
			$data = array(
				'auditStatus' => 1
			);
			$Inv_info 	   = InvRecordModel::getInvRecordList("*","where id='$id'");
			$position_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='{$Inv_info[0]['location']}'");
			$skuinfo  	   = whShelfModel::selectSku(" where sku='{$Inv_info[0]['sku']}'");
			$updatedata    = InvRecordModel::update($data,"and id='$id'");
			if(!$updatedata){
				self::$errCode = "402";
				self::$errMsg  = "更新通过状态失败！";
				return false;
			}

			$tName     = 'wh_product_position_relation';
			$set       = "SET nums='{$Inv_info[0]['invNums']}'";
			$where     = "WHERE pId='{$skuinfo['id']}' AND positionId='{$position_info[0]['id']}' AND is_delete=0 AND storeId=1";
			$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);//库存变化
			if($affectRow===false){
				self::$errCode = "403";
				self::$errMsg  = "更新具体仓位库存失败！";
				OmAvailableModel::rollback();
				return false;
			}
			
			$adjustNums = $Inv_info[0]['adjustNums'];
			if($Inv_info[0]['invType']==2){
				$adjustNums = "-".$adjustNums;
			}
			$tName = 'wh_sku_location';
			$set   = "SET actualStock=actualStock+'$adjustNums'";
			$where = "WHERE sku='{$Inv_info[0]['sku']}' AND storeId=1";
			$affectRow = WhIoRecordsModel :: updateTNameRow($tName, $set, $where);//库存变化
			if($affectRow===false){
				self::$errCode = "404";
				self::$errMsg  = "更新总库存失败！";
				OmAvailableModel::rollback();
				return false;
			}
			
			/**** 插入出入库记录 *****/
			if($Inv_info[0]['invType']==2){
				$ioType = 1;
				$ioTypeId = 11;
				$reason = '盘点出库';
			}else{
				$ioType = 2;
				$ioTypeId = 10;
				$reason = '盘点入库';
			}
			if($adjustNums!=0){
				$update_onhand = CommonModel::adjustInventory($Inv_info[0]['sku'],$adjustNums,$userCnName);
				if($update_onhand==0){
					self::$errCode = 415;
					self::$errMsg = "更新旧erp库存失败";
					OmAvailableModel :: rollback();
					return false;
				}
			
				$skuinfo = whShelfModel::selectSku(" where sku = '{$Inv_info[0]['sku']}'");
				$paraArr = array(
					'sku'     	 => $Inv_info[0]['sku'],
					'amount'  	 => abs($adjustNums),
					'positionId' => $position_info[0]['id'],
					'purchaseId' => $skuinfo['purchaseId'],
					'ioType'	 => $ioType,
					'ioTypeId'   => $ioTypeId,
					'userId'	 => $_SESSION['userId'],
					'reason'	 => $reason,
				);
				$record = CommonModel::addIoRecores($paraArr);     //出库记录
				if(!$record){
					OmAvailableModel::rollback();
					return false;
				}
			}
			
			//更新申请盘点表
			$waitInf = WaitInventoryModel::updateInv($Inv_info[0]['sku'],$_SESSION['userId']);
			if(!$waitInf){
				self::$errCode = 414;
				self::$errMsg = "跟新申请盘点表失败！";
				TransactionBaseModel :: rollback();
				return false;
				
			}
			
		}
		OmAvailableModel::commit();
		return true;
	}
	
	//盘点审核不通过
	function  act_allNoPass(){
		$data   	 = array();
		$id_arr 	 = $_POST['id'];
		$f_count 	 = count($id_arr);
		$id          = implode(',',$id_arr);
		$where       = "where id in(".$id.") and auditStatus=0";
		$record_list = InvRecordModel::getInvRecordList("*",$where);
		$s_count 	 = count($record_list);
		if($f_count!=$s_count){
			self::$errCode = "401";
			self::$errMsg  = "当前包含有不用审核的订单，请确认！";
			return false;
		}
		
		$where  = " and id in(".$id.")";
		$data   = array(
			'auditStatus' 	=> 2
		);
		$list =	InvRecordModel::update($data,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "审核失败，请重试！";
			return false;
		}
	}
	
	//获取当前盘点原因列表
	function act_getInvReasonList($select = '*',$where){
		$list =	InvReasonModel::getInvReasonList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = InvReasonModel::$errCode;
			self::$errMsg  = InvReasonModel::$errMsg;
			return false;
		}
	}
	
	//增加/修改盘点原因
	function  act_sureAddRea(){
		$data 	    		= array();
		$id 	    		= trim($_POST['reasonId']);
		$data['reasonName'] = post_check(trim($_POST['reasonName']));
		if(empty($id)){
			$insertid = InvReasonModel::insertRow($data);
			if($insertid){
				return true;
			}else{
				return false;
			}
		}else{
			$updatedata = InvReasonModel::update($data,"and id='$id'");
			if($updatedata){
				return true;
			}else{
				return false;
			}
		}		
	}
	
	//获取盘点审核列表
	function act_getInvConditionList($select = '*',$where){
		$list =	InvConditionModel::getInvConditionList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = InvConditionModel::$errCode;
			self::$errMsg  = InvConditionModel::$errMsg;
			return false;
		}
	}
	
	//增加/修改盘点条件
	function  act_sureAddCon(){
		$data 	    		 = array();
		$id 	    		 = trim($_POST['conId']);
		$data['auditName']   = post_check(trim($_POST['auditName']));
		$data['auditValues'] = post_check(trim($_POST['auditValues']));
		$data['notes'] 		 = post_check(trim($_POST['notes']));
		$is_enable    		 = trim($_POST['is_enable']);
		if($is_enable==1){
			$data['is_enable'] = 1;
		}else{
			$data['is_enable'] = 0;
		}
		if(empty($id)){
			$insertid = InvConditionModel::insertRow($data);
			if($insertid){
				return true;
			}else{
				return false;
			}
		}else{
			$updatedata = InvConditionModel::update($data,"and id='$id'");
			if($updatedata){
				return true;
			}else{
				return false;
			}
		}		
	}
	
	//检测sku
	function  act_checkSku(){
		$sku     = post_check(trim($_POST['sku']));
		$sku 	 = get_goodsSn($sku);
		$where   = " where sku = '{$sku}'";
		$skuinfo = whShelfModel::selectSku($where);
		if(empty($skuinfo)){
			self::$errCode = 401;
			self::$errMsg  = "无该料号信息";
			return false;
		}else{
			self::$errMsg  = "请扫描仓位号";
			return $sku;
		}
	}
	
	//检测仓位
	function  act_checkSkuPositon(){
		$sku      = post_check(trim($_POST['sku']));
		$sku 	  = get_goodsSn($sku);
		$location = post_check(trim($_POST['location']));
		$where    = " where sku = '{$sku}'";
		$skuinfo  = whShelfModel::selectSku($where);
		if(empty($skuinfo)){
			self::$errCode = 401;
			self::$errMsg  = "无该料号信息";
			return false;
		}else{
			$skuId = $skuinfo['id'];
		}
		
		$sku_positon_list = InvRecordModel::getSkuPosition($skuId,$location);
		if(empty($sku_positon_list)){
			self::$errCode = 402;
			self::$errMsg  = "料号和仓位信息有误";
			return false;
		}else{
			$sku_num       =  CommonModel::getGoodsCount($sku);
			self::$errMsg  = "请输入盘点数量,选择盘点原因";
			return $sku_num;
		}
	}
	
	/*
     * 盘点报表导出
     */
	public function act_export(){
		$invPeople    = isset($_GET['invPeople'])?$_GET['invPeople']:'';
		$sku          = isset($_GET['sku'])?post_check($_GET['sku']):'';
		$invType      = isset($_GET['invType'])?$_GET['invType']:'';
		$startdate    = isset($_GET['startdate'])?post_check($_GET['startdate']):'';
		$enddate      = isset($_GET['enddate'])?post_check($_GET['enddate']):'';
        $auditStatus  = isset($_GET['auditStatus'])?post_check($_GET['auditStatus']):'';
        
		if(empty($invPeople)&&empty($sku)&&empty($invType)&&empty($startdate)&&empty($enddate)&&empty($auditStatus)){
			echo "请选择导出条件";exit;
		}
		
		$where  = 'where storeId=1 ';
		if($invPeople){
			$where .= "and invPeople ='$invPeople' ";
		}
		if($sku){
			$where .= "and sku ='$sku' ";
		}
		if($invType){
			$where .= "and invType ='$invType' ";
		}
		if($startdate){
			$starttime = strtotime($startdate);
			$where .= "and invTime >='$starttime' ";			
		}
		if($enddate){
			$endtime = strtotime($enddate);
			$where .= "and invTime <='$endtime' ";			
		}
        if($auditStatus != '' && $auditStatus != 3){
            $where  .=  "and auditStatus = $auditStatus";
        }
		$lists = InvRecordModel::getInvRecordList('*',$where);

		$excel  = new ExportDataExcel('browser', "Files_warehouse".date('Y-m-d').".xls"); 
		$excel->initialize();
		$tharr = array("日期","料号","仓位","系统数量","盘点数量","差异数量","料号单价","类型","盘点原因","盘点人","采购员","物料管理人","状态", '备注信息');
		$excel->addRow($tharr);
		
		foreach($lists as $list){
			$time 	  = date('Y-m-d',$list['invTime']);
			$sku 	  = $list['sku'];
			$location = $list['location'];
			$systemNums = $list['systemNums'];
			$sku_info   = getSkuInfoBySku($list['sku']);
			$goodsCost  = $sku_info['goodsCost'];
			$invNums  = $list['invNums'];
			if($list['invType']==1){
				$num  = $list['adjustNums'];
				$type = "盘盈";
			}elseif($list['invType']==2){
				$num  = '-'.$list['adjustNums'];
				$type = "盘亏";
			}else{
				$num  = $list['adjustNums'];
				$type = "";
			}
            $remark   = $list['remark'] ? $list['remark'] : '';
			
			$reason_info = InvReasonModel::getInvReasonList("reasonName","where id='{$list['reasonId']}'");
			$reason      = $reason_info[0]['reasonName'];	
			$invPeople   = getUserNameById($list['invPeople']);
			//$sku_info    = getSkuInfoBySku($list['sku']);
			$purchaseName = $sku_info['purchaseId'] ? getUserNameById($sku_info['purchaseId']) : '无';
			$menergeMan  = '';
			$mark        = '';
			if($list['auditStatus']==0){
				$mark    = '未审核';
			}else if($list['auditStatus']==1){
				$mark    = '通过';
			}else if($list['auditStatus']==2){
				$mark    = '拒绝';
			}
			$tdarr	  = array($time,$sku,$location,$systemNums,$invNums,$num,$goodsCost,$type,$reason,$invPeople,$purchaseName,$menergeMan,$mark, $remark);
			$excel->addRow($tdarr);	
		}
	
		$excel->finalize();
		exit;
	}
    
    /**
     * Pda_inventorySearchAct::getInfo()
     * 获取盘点信息
     * GARY
     * @return
     */
    public function act_getInfo(){
        $sku    =   trim($_POST['sku']);
        $sku    =   get_goodsSn($sku);
        if(!$sku){
            self::$errCode  =   101;
            self::$errMsg   =   '请扫描料号!';
            return  FALSE;
        }
        
        $info   =   CommonModel::getSkuInevntory($sku);
        if($info['res_code'] != 200){
            self::$errCode = 102;
            self::$errMsg  = '获取sku盘点信息失败!';
            return FALSE;
        }
        self::$errCode  =   $info['res_code'];
        self::$errMsg   =   '拉取数据成功!';
        return $info['res_data'];
    }
    
    /**
     * InventoryAct::act_editInventoryNote()
     * 修改点货备注信息
     * @return void
     */
    public function act_editInventoryNote(){
        $ids     =   array_map('trim', $_POST['ids']);
        $notes   =   array_map('trim', $_POST['notes']);
        foreach($notes as $k=>$note){
            if(!$note){
                continue;
            }
            $id =   intval($ids[$k]);
            if($id){
                InvRecordModel::update_note($id, $note);
            } 
        }
        self::$errCode = 0;
		self::$errMsg  = "更新备注成功";
		return TRUE;
   }
}


?>