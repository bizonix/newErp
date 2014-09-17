<?php
/**
*类名：pda及流水线查询管理
*功能：pda及流水线查询管理信息
*作者：hws
* add 陈先钰 2014-9-9
*
*/
class PdaManagementAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取配货信息
	function act_getPickingInfo($startdate,$enddate,$user){
		$startdate = strtotime($startdate);
		$enddate   = strtotime($enddate);
        $where = 'where';
        if($user !=0 ){
            $where .= " scanUserId = '{$user}' and ";
        }
		$where 	   .= " (scanTime between $startdate and $enddate) and is_delete=0 order by scanTime";
		$list	   = OmAvailableModel::getTNameList("wh_order_picking_records","*",$where);
		
		$pda_scan_record = array();
		foreach($list as $line){
			$pda_scan_record[$line['shipOrderId']][] = $line;
		}
		$is_scan = array();
		$no_scan = array();
		foreach($pda_scan_record as $key => $value){
			$scan = true;
			foreach($value as $v){
				if(!$v['isScan']){
					$scan = false;
					break;
				}
			}
			if($scan){
				array_push($is_scan, $key);
			}else{
				array_push($no_scan, $key);	
			}
		}
		$rate = round(count($is_scan)/(($enddate-$startdate)/3600), 1);
		return '已经配完 '.count($is_scan).' 单,未配完 '.count($no_scan).' 单,配货速度 '.$rate.'个/时 。<br>未配完单号:'.join(', ', $no_scan);
		
	}
	
	//获取复核信息
	function act_getReviewInfo($startdate,$enddate,$user){
		$startdate = strtotime($startdate);
		$enddate   = strtotime($enddate);
          $where = 'where';
        if($user !=0 ){
            $where .= " scanUserId = '{$user}' and ";
        }
		$where 	   .= " (scanTime between $startdate and $enddate) and isScan=1 and is_delete=0 order by scanTime";
		$list	   = OmAvailableModel::getTNameList("wh_order_review_records","count(*) as num",$where);
		
		$rate = round($list[0]['num'] / (($enddate-$startdate)/3600), 1);
		return '该员工:已经复核扫描完 '.$list[0]['num'].' 单,复核扫描速度 '.$rate.'个/时 。';
	
	}
	
	//获取包装信息
	function act_getPackageInfo($startdate,$enddate,$user){
		$startdate = strtotime($startdate);
		$enddate   = strtotime($enddate); 
        $where = 'where';
        if($user !=0 ){
            $where .= " scanUserId = '{$user}' and ";
        }
		$where 	   .= " (scanTime between $startdate and $enddate) and is_delete=0 order by scanTime";
		$list	   = OmAvailableModel::getTNameList("wh_order_package_records","*",$where);
		$pda_scan_record = array();
		foreach($list as $line){
			$pda_scan_record[$line['shipOrderId']][] = $line;
		}
		$is_scan = array();
		$no_scan = array();
		foreach($pda_scan_record as $key => $value){
			$scan = true;
			foreach($value as $v){
				if(!$v['isScan']){
					$scan = false;
					break;
				}
			}
			if($scan){
				array_push($is_scan, $key);
			}else{
				array_push($no_scan, $key);	
			}
		}
		$rate = round(count($is_scan)/(($enddate-$startdate)/3600), 1);
		return '已经包装扫描完 '.count($is_scan).' 单,配货速度 '.$rate.'个/时 。';
	}
	
	//获取称重扫描信息
	function act_getWeighInfo($startdate,$enddate,$user){
		$startdate = strtotime($startdate);
		$enddate   = strtotime($enddate);
	   $where = 'where';
        if($user !=0 ){
            $where .= " scanUserId = '{$user}' and ";
        }
		$where 	   .= " (scanTime between $startdate and $enddate) and isScan=1 and is_delete=0 order by scanTime";
		$list	   = OmAvailableModel::getTNameList("wh_order_weigh_records","count(*) as num",$where);
		
		$rate = round($list[0]['num'] / (($enddate-$startdate)/3600), 1);
		return '该员工:已经称重扫描完 '.$list['num'].' 单,称重扫描速度 '.$rate.'个/时 。';
	}
	
	//获取清单信息
	function act_getGroupInfo($groupid){
		$where 	   = "where shipOrderGroup='$groupid' order by groupId";
		$list	   = OmAvailableModel::getTNameList("wh_shipping_order_group_distribution","*",$where);
		if(empty($list)){
			return "配货清单号{$groupid}没有配货记录!";
		}else{
			$str = '';
			foreach($list as $value){
				$scan_status = ($value['status'] == 0) ? '未配货' : '已配货';
				$usermodel = UserModel::getInstance();
				//配货人
				$iqc_user = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$value['userID']}",'','');
				$op_name = $iqc_user[0]['global_user_name'];		
				$str .= "配货清单{$groupid} 发货单{$value['shipOrderId']} 的料号 {$value['sku']} {$scan_status} 配货人< {$op_name} > 已配货 {$value['amount']}  还需配货 ".($value['skuAmount']-$value['amount'])." PDA扫描时间:".($value['scanTime'] ? date('Y-m-d H:i:s', $value['scanTime']) : ' 无 ');
				$str .= "   <br>";
			}
			return $str;
		}
	}
	
	//获取订单配货信息
	function act_searchPickingInfo($orderid){
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN|LX)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$orderid) ){
		}else if( preg_match($p_trackno_eub,$orderid) ){
			$is_eub_package_type=true;
		}else{
			return "订单号{$orderid}没有配货记录!订单号不存在,请确认!";	exit;		
		}
		if($is_eub_package_type===true){
			$ordercheck = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$orderid' and a.is_delete=0");
		}else{
			$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$orderid'");
		}
		if(empty($ordercheck)){
			return "订单号{$orderid}没有配货记录!订单号不存在,请确认!";	exit;	
		}else{
			$orderid = $ordercheck[0]['id'];
		}

		$where  = "where shipOrderId='$orderid' and is_delete=0";
		$list	= OmAvailableModel::getTNameList("wh_order_picking_records","*",$where);
		$eosr_arrlist = array();
		foreach($list as $row){
			$eosr_arrlist[] = $row['shipOrderdetailId'];
		}
		//$skuinfos = get_realskunum($orderid);
		$skuinfos = OmAvailableModel::getTNameList("wh_shipping_orderdetail","*","where shipOrderId='$orderid'");
		foreach($skuinfos as $info){
			$order_detail = array();
			if(!in_array($info['id'], $eosr_arrlist)){
				$order_detail['shipOrderId'] = $info['shipOrderId'];
				$order_detail['shipOrderdetailId'] = $info['id'];
				$order_detail['sku'] 		 = $info['sku'];
				$order_detail['pName'] 		 = $info['pName'];
				$order_detail['totalNums'] 	 = $info['amount'];
				$order_detail['isScan'] 	 = 0;
				$order_detail['is_delete'] 	 = 0;
				$field =  ' SET '.array2sql($order_detail);
				OmAvailableModel::addTNameRow("wh_order_picking_records",$field);
			}
		}
		$where  = "where shipOrderId='$orderid'";
		$p_list	= OmAvailableModel::getTNameList("wh_order_picking_records","*",$where);
		$show = '';
		foreach($p_list as $value){
			$scan_status = ($value['isScan'] == 0) ? '未配货' : '已配货';
			if(empty($value['scanUserId'])){
				$op_name = '无';
			}else{
				//配货人
				$usermodel = UserModel::getInstance();
				$iqc_user  = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$value['scanUserId']}",'','');
				$op_name   = $iqc_user[0]['global_user_name'];
			}
			$op_amount   = empty($value['amount']) ? 0 : $value['amount'];
			if($value['is_delete']==1){
				$show .= "<font style='color:red;'>配货记录已删除：订单{$orderid} 的料号 {$value['sku']} 仓位{$value['pName']} {$scan_status} 配货人 {$op_name}， ";
				$show .= " 已配货 {$op_amount} 还需配货 ".($value['totalNums']-$op_amount);
				$show .= " PDA扫描时间:".($value['scanTime'] ? date('Y-m-d H:i:s', $value['scanTime']) : ' 无 ')."</font>";
			}else{
				$show .= "订单{$orderid} 的料号 {$value['sku']} 仓位{$value['pName']} {$scan_status} 配货人 {$op_name}， ";
				$show .= " 已配货 {$op_amount} 还需配货 ".($value['totalNums']-$op_amount);
				$show .= " PDA扫描时间:".($value['scanTime'] ? date('Y-m-d H:i:s', $value['scanTime']) : ' 无 ');
			}
			/*					
			if($value['isScan']==1 && $value['is_delete']==0){
				$show .= "<input class='del' type='button' name='deletebutton' orderid='{$orderid}' sku='{$value['sku']}' pname='{$value['pName']}'  value='删除订单的配货记录,回滚库存' />";
			}*/
			$show .= "<br>";
		}
		return $show;
	}
	
	//获取订单复核信息
	function act_searchReviewInfo($orderid){
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN|LX)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$orderid) ){
		}else if( preg_match($p_trackno_eub,$orderid) ){
			$is_eub_package_type=true;
		}else{
			return "订单号不存在,请确认!";	exit;		
		}
		if($is_eub_package_type===true){
			$ordercheck = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$orderid' and a.is_delete=0");
		}else{
			$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$orderid'");
		}
		if(empty($ordercheck)){
			return "订单号不存在,请确认!";	exit;	
		}else{
			$orderid = $ordercheck[0]['id'];
		}

		$where  = "where shipOrderId='$orderid' and is_delete=0 ";
		$list	= OmAvailableModel::getTNameList("wh_order_review_records","*",$where);
		if(empty($list)){
			return "订单号{$orderid}没有复核扫描记录!";
		}else{
			$scan_status = '已复核扫描。';
			foreach($list as $l){
				if($l['isScan']==0){
					$scan_status = '已扫描,未复核。';
					break;
				}
			}

			$usermodel = UserModel::getInstance();
			$iqc_user  = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$list[0]['scanUserId']}",'','');
			$op_name   = $iqc_user[0]['global_user_name'];
			return "订单{$orderid} $scan_status 复核扫描人 {$op_name} 复核扫描时间:".($list[0]['scanTime'] ? date('Y-m-d H:i:s', $list[0]['scanTime']) : ' 无 ')."<br>";
		}
	}
	
	//获取订单包装信息
	function act_searchPackageInfo($orderid){
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN|LX)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$orderid) ){
		}else if( preg_match($p_trackno_eub,$orderid) ){
			$is_eub_package_type=true;
		}else{
			return "订单号不存在,请确认!";	exit;		
		}
		if($is_eub_package_type===true){
			$ordercheck = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$orderid' and a.is_delete=0");
		}else{
			$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$orderid'");
		}
		if(empty($ordercheck)){
			return "订单号不存在,请确认!";	exit;	
		}else{
			$orderid = $ordercheck[0]['id'];
		}

		$where  = "where shipOrderId='$orderid' and is_delete=0 and isScan=1 order by scanTime";
		$list	= OmAvailableModel::getTNameList("wh_order_package_records","*",$where);
		if(empty($list)){
			return "订单号{$orderid}没有包装记录!";
		}else{
			$scan_status = ($list[0]['isScan'] == 0) ? '未包装' : '已包装';
			$usermodel = UserModel::getInstance();
			$iqc_user  = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$list[0]['scanUserId']}",'','');
			$op_name   = $iqc_user[0]['global_user_name'];
			return  "订单{$orderid} $scan_status 包装人 {$op_name} 包装时间:".($list[0]['scanTime'] ? date('Y-m-d H:i:s', $list[0]['scanTime']) : ' 无 ')."<br>";
		}
	}
	
	//获取订单称重信息
	function act_searchWeighInfo($orderid){
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN|LX)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$orderid) ){
		}else if( preg_match($p_trackno_eub,$orderid) ){
			$is_eub_package_type=true;
		}else{
			return "订单号不存在,请确认!";	exit;		
		}
		if($is_eub_package_type===true){
			$ordercheck = ShippingOrderModel::getShippingOrderInfo("b.*","where a.tracknumber='$orderid' and a.is_delete=0");
		}else{
			$ordercheck = ShippingOrderModel::getShippingOrder("*","where id='$orderid'");
		}
		if(empty($ordercheck)){
			return "订单号不存在,请确认!";	exit;	
		}else{
			$orderid = $ordercheck[0]['id'];
		}

		$where  = "where shipOrderId='$orderid' and is_delete=0 order by scanTime";
		$list	= OmAvailableModel::getTNameList("wh_order_weigh_records","*",$where);
		if(empty($list)){
			return "订单号{$orderid}没有称重扫描记录!";
		}else{
			$scan_status = '已称重扫描。';
			$usermodel = UserModel::getInstance();
			$iqc_user  = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$list[0]['scanUserId']}",'','');
			$op_name   = $iqc_user[0]['global_user_name'];
			return  "订单{$orderid} $scan_status 称重扫描人 {$op_name} 称重扫描时间:".($list[0]['scanTime'] ? date('Y-m-d H:i:s', $list[0]['scanTime']) : ' 无 ')."<br>";
		}
	}
	
	//获取订单分区信息
	function act_searchPartionInfo($orderid){
		$name   = "wh_order_partion_records as a left join wh_order_partion_print as b on a.packageId=b.id";
		$where  = "where a.shipOrderId='$orderid' and a.is_delete=0";
		$list	= OmAvailableModel::getTNameList($name,"a.*,b.totalWeight,b.totalNum,b.status",$where);
		if(empty($list)){
			return "订单号{$orderid}没有分区扫描记录!";
		}else{
			$show = '';
			foreach($list as $value){
				$pack_status = ($value['status'] == 1) ? '已打包' : '未打包';
				if(empty($value['scanUserId'])){
					$op_name = '无';
				}else{
					//配货人
					$usermodel = UserModel::getInstance();
					$iqc_user  = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$value['scanUserId']}",'','');
					$op_name   = $iqc_user[0]['global_user_name'];
				}
				$scantime = $value['scanTime'] ? date('Y-m-d H:i:s', $value['scanTime']) : ' 无 ';
				$weight   = round_num($value['totalWeight']/1000,3);
				$show .= "订单号{$orderid} &nbsp;&nbsp; 扫描用户 > {$op_name} &nbsp;&nbsp; 所属包裹> {$value['packageId']}  &nbsp;&nbsp; 所属分区 > {$value['partionId']} &nbsp;&nbsp; 包裹打包状态 > {$pack_status} &nbsp;&nbsp; 包裹总重 > {$weight}&nbsp;&nbsp; 包裹总数量 > {$value['totalNum']} &nbsp;&nbsp; 订单扫描日期 > {$scantime}<br>";
			}
			return  $show;
		}
	}
	
	//删除配货记录
	function act_removeRollback(){
		$userId      = $_SESSION['userId'];
		$time  		 = time();
		$shipOrderId = $_POST['order'];
		$sku   	     = $_POST['sku'];
		$pName   	 = $_POST['pname'];

		$scan_record = OmAvailableModel::getTNameList("wh_order_picking_records","*","where shipOrderId='$shipOrderId' and sku='$sku' and pName='$pName' and is_delete=0 ");
		if(empty($scan_record)){
			self :: $errCode = "401";
			self :: $errMsg  = "配货单号 {$shipOrderId} 其中料号 {$sku} 在仓位 {$pName } 的配货记录不存在！";
			return false;
		}else{
			OmAvailableModel::begin();
			$amount = $scan_record[0]['amount'];//配货数量
			$sql = "update ebay_order_scan_record set is_show = 1,canceltime='{$mctime}' where ebay_id = '{$ebay_id}' and sku = '{$ebay_sku}' and is_show = 0 ";
			$update_record = OmAvailableModel::updateTNameRow("wh_order_picking_records","set cancelUserId='$userId',cancelTime='$time',is_delete=1","where shipOrderId='$shipOrderId' and sku='$sku' and pName='$pName' and is_delete=0");
			if(!$update_record){
				self :: $errCode = "402";
				self :: $errMsg  = "配货单删除配货记录失败";
				return false;
			}
			
			$sku_info 	   = OmAvailableModel::getTNameList("pc_goods","id","where sku='$sku' and is_delete=0");
			$position_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='$pName' and storeId=1");
			if(!$position_info || !$sku_info){
				self :: $errCode = "403";
				self :: $errMsg  = "配货单删除配货记录失败，找不到对应仓位id或者skuid";
				OmAvailableModel::rollback();
				return false;
			}
			
			$update_product_position = OmAvailableModel::updateTNameRow("wh_product_position_relation","set nums=nums+'$amount'","where pId={$sku_info[0]['id']} and positionId={$position_info[0]['id']}");
			if(!$update_product_position){
				self :: $errCode = "404";
				self :: $errMsg  = "配货单删除配货记录失败,更新仓位数量出错";
				OmAvailableModel::rollback();
				return false;
			}
			
			$update_sku_location  = OmAvailableModel::updateTNameRow("wh_sku_location","set actualStock=actualStock+'$amount'","where sku='$sku' and storeId=1");
			if(!$update_sku_location){
				self :: $errCode = "405";
				self :: $errMsg  = "配货单删除配货记录失败,更新库存数量出错";
				OmAvailableModel::rollback();
				return false;
			}
			
			self :: $errMsg  = "配货单删除配货记录成功";
			OmAvailableModel::commit();
			return false;
		}
	}
    //分拣记录查询
     /**
      * PdaManagementAct::act_searchSortingInfo()
      * @author cxy
      * @param mixed $orderid
      * @return
      */
     function act_searchSortingInfo($orderid){
        if(empty($orderid)){
            return '请输入分拣的配货单号';
        }
        $orderid     = trim($orderid);
       	$scan_record = OmAvailableModel::getTNameList("wh_wave_info","*","where id='$orderid'  and is_delete=0 ");
        if(empty($scan_record)){            
            return '输入分拣的配货单号不存在';
        }
       
        $waveStatus = $scan_record[0]['waveStatus'];
        if($waveStatus == WAVE_WAITING_GET_GOODS){
            $status   = '待配货';
        }else if($waveStatus == WAVE_PROCESS_GET_GOODS){
           $status    = '配货中';
        }else{
             $status  = '配货完成';
        }
        if($scan_record[0]['waveType'] == 1){
           $detail =  WhWaveShippingRelationModel::select_not_scanning($orderid);          
    //   echo '<pre>';  print_r($detail);     echo '<pre>';exit;
           if($detail){
            //分拣人
                $usermodel  = UserModel::getInstance();
            	$iqc_user   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$detail[0]['pickUserId']}");
				$op_name    = $iqc_user[0]['global_user_name'];
                $time       = empty($detail[0]['pickTime'])?"": date('Y-m-d H:i:s',$detail[0]['pickTime']);
                $show       = '配货单为'. $orderid.',属于人工分拣单发货单, '.'发货单为'.$detail[0]['shipOrderId'].',该配货单状态为'.$status.', 人工分拣操作人为'.$op_name.', 操作时间为'.$time.'<br />';              
                $result     = OmAvailableModel::getTNameList("wh_wave_pick_record","*","where shipOrderId='{$detail[0]['shipOrderId']}'  and is_delete=0 ");
                foreach($result as $list){
                    if($list['pickStatus']== 0){
                        $status_SKU  = '未分拣完成'; 
                    }else if($list['pickStatus']== 1){
                         $status_SKU = '分拣完成';  
                    }else{
                        $status_SKU  = '手动完结分拣完成';  
                    }
                    $dataTime =empty($list['pickTime'])?'':  date('Y-m-d H:i:s',$list['pickTime']);
                    $show    .= 'sku为'.$list['sku'].',该SKU需要分拣的数量为'.$list['skuAmount'].',状态为'.$status_SKU.',分拣时间为'.$dataTime.'<br />';
                }
               return $show ;
               
           }else{
            return '该配货单的相关信息不存在';
           }
        }else if($scan_record[0]['waveType'] == 2){
            $result = OmAvailableModel::getTNameList("wh_wave_pick_record","*","where waveId='$orderid'  and is_delete=0 ");
            $show   = '该配货是属于单SKU配货单，状态为'.$status;
            foreach($result as $value){
                $dataTimes =empty($value['pickTime'])?'':  date('Y-m-d H:i:s',$value['pickTime']);
                $show     .='发货单为'.$value['shipOrderId'].'sku为'.$value['sku'].',该SKU需要分拣的数量为'.$value['skuAmount'].',分拣人为'.$value['pickUserId'].',分拣时间为'.$dataTimes.'<br/>';
            }
            return $show;
        }else{
             $result = OmAvailableModel::getTNameList("wh_wave_pick_record","*","where waveId='$orderid'  and is_delete=0 ");
            $show    = '该配货是属于多SKU配货单，状态为'.$status;
            foreach($result as $value){
                if($value['pickStatus']== 0){
                    $status_SKU  = '未分拣完成'; 
                }else if($value['pickStatus']== 1){
                     $status_SKU = '分拣完成';  
                }else{
                    $status_SKU  = '手动完结分拣完成';  
                }
                $dataTime =empty($value['pickTime'])?'':  date('Y-m-d H:i:s',$value['pickTime']);
                $show    .='发货单为'.$value['shipOrderId'].',sku为'.$value['sku'].',该SKU需要分拣的数量为'.$value['skuAmount'].',已经分拣的数量为'.$value['amount'].'状态为'.$status_SKU.',分拣人为'.$value['pickUserId'].',分拣时间为'.$dataTime.'<br />';
            }
            return $show;
            
        }             
    }
    /**
     * PdaManagementAct::act_searchLoading_express()
     * 装车扫描记录
     * @param mixed $orderid
     * @return
     */
    function act_searchLoading_express($orderid){
        if(empty($orderid)){
            return '请输入装车扫描的快递发货单号/口袋编号';
        }
        $orderid = trim($orderid);        
        $result  = OmAvailableModel::getTNameList("wh_wave_order_loading","*","where packageId='$orderid'  and is_delete=0 ");
        if(empty($result)){
            return '输入装车扫描的快递发货单号/口袋编号不存在';
        }
        if($result[0]['isExpress']==2){
            $usermodel  = UserModel::getInstance();
        	$iqc_user   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$result[0]['userId']}");
			$op_name    = $iqc_user[0]['global_user_name'];
            $time       = date('Y-m-d H:i:s',$value['scantime']);
            $show       = '该'.$orderid.'为小包的口袋编号，扫描人是'.$op_name.',扫描时间为'.$time;
        }else{
             $show      = '该'.$orderid.'为快递号,';
             foreach($result as $value){
                 $usermodel  = UserModel::getInstance();
            	 $iqc_user   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$value['userId']}");
			 	 $op_name    = $iqc_user[0]['global_user_name'];
                 $times      = empty($value['scantime'])?'':  date('Y-m-d H:i:s',$value['scantime']);
                 $show       .= '扫描人是'.$op_name.',扫描时间为'.$times.',跟踪号为'.$value['tracking'].'<br />';            
            }
        }     
        return $show;
    }
	/**
	 * PdaManagementAct::act_searchReview()
     * 分区复核记录查询
	 * @author cxy
	 * @param mixed $orderid发货单号
	 * @return
	 */
	function act_searchReview($orderid){
	   if(empty($orderid)){
            return '请输入需要分区复核的发货单号';
        }
        $orderid = trim($orderid); 
        $result  = OmAvailableModel::getTNameList("wh_wave_order_partion_scan_review","*","where shipOrderId='$orderid'  and errorPartion is null ");
        if(!$result){
            return '该发货单号没有分区复核信息';
        }
        $usermodel  = UserModel::getInstance();
        foreach($result as $value){           
            $iqc_user   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$value['userId']}");
            $op_name    = $iqc_user[0]['global_user_name'];
            $time       = empty($value['scantime'])?'':  date('Y-m-d H:i:s',$value['scantime']);
            $show       ='发货单为：'.$value['shipOrderId'].',在'.$value['partion'].'分区,分区复核人是'.$op_name.',复核时间是'.$time.'<br />';
        }
        return $show;
	}
    /**
     * 发货组复核记录
     * PdaManagementAct::act_searchGroupReview()
     * @author cxy
     * @param mixed $orderid口袋编号
     * @return
     */
    function act_searchGroupReview($orderid){
         if(empty($orderid)){
            return '请输入需要发货组复核的口袋编号';
        }
        $orderid = trim($orderid); 
        $result  = OmAvailableModel::getTNameList("wh_wave_order_partion_shipping_review","*","where packageId='$orderid'  and is_error =0 ");
        if(!$result){
            return '该口袋编号没有发货组复核信息';
        }
         $show      = $orderid.'口袋编号下的发货组复核信息为：<br />';
        $usermodel  = UserModel::getInstance();
        foreach($result as $value){
            $iqc_user   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$value['userId']}");
            $op_name    = $iqc_user[0]['global_user_name'];
            $time       =  empty($value['scantime'])?'': date('Y-m-d H:i:s',$value['scantime']);
            $ship_id    = $value['orders']==0 ? '':$value['orders'];
            $show      .='随机扫描发货单为：'.$ship_id.',复核人是'.$op_name.',复核时间是'.$time.'<br />';
        }
        return $show;
    }
    
    //查询配货单配货记录   
    function act_search_scan_record($orderid){
         if(empty($orderid)){
            return '请输入配货单';
        }
        $orderid = trim($orderid); 
        $usermodel  = UserModel::getInstance();
        $result  = OmAvailableModel::getTNameList(" wh_wave_info ","*","where id = '$orderid' and is_delete = 0");
        if(empty($result)){
             return '输入的配货单不存在';
        }
        if($result[0]['waveStatus']== WAVE_WAITING_GET_GOODS){
             $status = '待配货';
        }else if($result[0]['waveStatus']== WAVE_PROCESS_GET_GOODS){
             $status = '配货中';
        }else{
             $status = '配货完成';
        }
        if($result[0]['waveType']== 1){
             $waveType = '单发货单';
        }else if($result[0]['waveType']== 2){
             $waveType = '单SKU';
        }else{
             $waveType = '多SKU多发货单';
        }
        $waveZones  =   array(1=>'同区域', 2=>'同楼层跨区域', 3=>'跨楼层');
        $createTime    =  empty($result[0]['createTime'])?'': date('Y-m-d H:i:s',$result[0]['createTime']);//配货单生成时间
        $scanTime      =  empty($result[0]['scanTime'])?'': date('Y-m-d H:i:s',$result[0]['scanTime']);//配货单配货扫描时间
        $iqc_user      = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$result[0]['createUserId']}");
        $op_name       = $iqc_user[0]['global_user_name'];
        $show          = $orderid.'配货单的配货信息为<br />';
        $show         .= '包含的楼层有'.$result[0]['storey'].',配货起始区域为'.$result[0]['startArea'].',配货单的区域分类是:'.$waveZones[$result[0]['waveZone']].',配货单状态为'.$status.',配货单类型为:'.$waveType.',配货单生成时间为'.$createTime.',扫描时间为'.$scanTime.',打印楼层为'.$result[0]['printStorey'].'<br />';
        $result_sacn   = OmAvailableModel::getTNameList("wh_wave_scan_record ","*","where waveId = '$orderid' and is_delete = 0"); 
        foreach($result_sacn as $value){  
            $iqc_user      = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$value['scanUserId']}");
            $op_name       = $iqc_user[0]['global_user_name'];
            $time_pick     = empty($value['scantime'])?'': date('Y-m-d H:i:s',$value['scantime']);//配货单下的料号扫描时间
            
            $scanStatus    = $value['scanStatus']== 0 ? '未配货完':'已配货完成';
            $show         .= 'SKU为'.$value['sku'].',该SKU的配货状态为'.$scanStatus.',需配货数为'.$value['skuAmount'].',已配货数量为'.$value['amount'].',仓位名为:'.$value['pName'].',该料号所在楼层为'.$value['storey'].',区域为'.$value['area'].',配货人是'.$op_name.',扫描时间为'.$time_pick.'<br/>';            
       }
       return $show;
    }
    //查询包裹所有的订单信息
    function act_searchOrderToPackage($orderid){
        if(empty($orderid)){
            return '请输入口袋编号';
        }
        $orderid = trim($orderid); 
        //分区的包裹信息
        $result       = OmAvailableModel::getTNameList("wh_order_partion_print a left join wh_wave_order_loading b on a.id=b.packageId","a.*,b.scantime","where a.id='$orderid'  and b.is_delete =0 and b.isExpress = 2");
       //分区复核包裹信息
        $review       = OmAvailableModel::getTNameList("wh_wave_order_partion_scan_review ","*","where packageId='$orderid'  and errorPartion is null ");
   //分区的订单明细
        $order_detail = OmAvailableModel::getTNameList("wh_order_partion_records","*","where packageId ='$orderid' and is_delete = 0 ");      
        $review_count = count($review);//复核订单总数测试空间的41197有错误的62291这个是测试
        $package_all  = count($order_detail);//分区的订单号总数   
        $time        = $result[0]['scantime']?date('Y-m-d H:i:s',$result[0]['scantime']):'还没有进行装车扫描';
        $status      = $result[0]['status']==0?'未打包':'已打包';
        $totalWeight = round($result[0]['totalWeight']/1000,3);
        $show        = '包裹编号：'.$orderid.',包裹总重：'.$totalWeight.'KG,是在'.$result[0]['partion'].'分区，'.$status.',包裹订单分区数量是:'.$package_all.',分区复核数量是：'.$review_count.',发货时间是：'.$time.'<br />';
        
   //   echo '<pre>';
    //    print_r($review);       
        $arr_package     = array();//分区的订单号
        $arr_review      = array();//复核分区的订单号
         foreach($order_detail as $value){
              $arr_package[$value['shipOrderId']] = $value;
         }
         foreach($review as $value){
             $arr_review[$value['shipOrderId']] = $value;
         }
        //  print_r($arr_package);
      //        print_r($arr_review);
       //  echo $review_count.'--'.$package_all;exit;
         //当复核数量和分区的数量不相等的时候
         if($review_count !=$package_all){
            $show .='<span style ="color:red;">以下是有差异的订单号：<br />';
            if($review_count < $package_all){
                $diff_keys  = array_diff_key($arr_package,$arr_review);
                foreach($diff_keys as $ks=>$val){
                    $times = empty($val['scantime']) ? '':date('Y-m-d H:i:s',$val['scanTime']);
                    $show .= '订单号为'.$val['shipOrderId'].',分区是'.$val['partion'].'，打包时间是'.$times.'<br />';
                }
            }else{
                $diff_keyss  = array_diff_key($arr_review,$arr_package);
                 foreach($diff_keyss as $kk=>$v){
                    $time_review = empty($v['scantime']) ? '':date('Y-m-d H:i:s',$v['scantime']);
                     $show      .='订单号为'.$v['shipOrderId'].',分区是'.$v['partion'].'，分区复核时间是'.$time_review.'<br />';              
                 }  
            }
            $show .='</span>';
         }      
         $show .='以下是正常的订单号:<br />';
         foreach($order_detail as $value){
             $time   = empty($value['scanTime'])?'':date('Y-m-d H:i:s',$value['scanTime']);
             $show .='发货单号为:'.$value['shipOrderId'].',分区是:'.$value['partion'].',扫描时间:'.$time.'<br />';
         }
        return $show;
        
    }
    //查询发货单下的所有操作信息
    function act_search_order_pick($orderid){
        if(empty($orderid)){
            return '请输入发货单号';
        }
        $orderid = trim($orderid);
        $result  = OmAvailableModel::getTNameList("wh_wave_pick_record ","*","where shipOrderId ='$orderid'  and is_delete =0");
        if(empty($result)){
            $show = '没有找到该发货单的分拣信息';
            return $show;
        }
      //  $shipping_relation = OmAvailableModel::getTNameList("wh_wave_shipping_relation ","*","where shipOrderId ='$orderid'  and is_delete =0");
        $show = '发货单号为'.$orderid.'的分拣信息为<br />';
        $waveId_array= array();
        foreach($result as $val){
            if(in_array($val['waveId'],$waveId_array)){
                continue;
            }else{                
               $waveId_array[] = $val['waveId'];
            }
        }
        $waveId =implode(',',$waveId_array);
        $usermodel  = UserModel::getInstance();
        $iqc_user   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$result[0]['pickUserId']}");
        $op_name    = $iqc_user[0]['global_user_name'];
        foreach($result as $value){
            if($value['pickStatus']== 0){
                $status_SKU  = '未分拣完成'; 
            }else if($value['pickStatus']== 1){
                 $status_SKU = '分拣完成';  
            }else{
                $status_SKU  = '手动完结分拣完成';  
            }
            $dataTime = empty($value['pickTime']) ? '': date('Y-m-d H:i:s',$value['pickTime']);
            $show .='配货单为'.$waveId.',sku料号为'.$value['sku'].',需要分拣的料号数量是'.$value['skuAmount'].'已经分拣的数量为'.$value['amount'].',分拣状态为'.$status_SKU.',分拣人是'.$op_name.',分拣时间为'.$dataTime.'<br />';
        }  
        return   $show;
         
    }
}


?>