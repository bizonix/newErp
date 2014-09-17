<?php
/**
*类名：B仓提货及复核
*功能：B仓提货及复核相关操作
*作者：hws
*
*/
class ScanPdaPickListBAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取配货单信息
	function act_getGroupInfo(){
		$userId 		= $_SESSION['userId'];
		$shipOrderGroup = $_POST['order_group'];
		$group_sql      = GroupRouteBModel::getOrderGroupB("*","where shipOrderGroup='$shipOrderGroup'");
		if(empty($group_sql)){
			self::$errCode = "003";
			self::$errMsg  = "该提货单不存在，请重新输入!";
			return false;
		}else{
			$scan_sql = GroupDistributionBModel::getGroupDistListB("shipOrderGroup","where shipOrderGroup='$shipOrderGroup'");
			if(empty($scan_sql)){
				$string = "";
				$time   = time();
				foreach($group_sql as $info){
					$string .= "('".$info['shipOrderGroup']."','". $info['sku']."','". $info['id']."','". $info['skuAmount']."','". $info['shipOrderId']."','0','0','".$info['pName']."','".$userId."','".$time."'),";
				}
				$string      = trim($string,",");	

				//插入配货提货单表
				$insert_info = GroupDistributionBModel::insertGroupDistB($string);			
				if($insert_info){
					$show_info = array();
					$show_sql  = GroupDistributionBModel::getGroupSkuInfoB("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 group by a.pName order by a.groupId asc");
					
					if(!empty($show_sql)){
						$order_sku_info = $this->get_valid_order($show_sql[0]['shipOrderGroup'],$show_sql[0]['sku'],$show_sql[0]['pName']);	
						if($order_sku_info){
							$show_info['group_id']       = $show_sql[0]['groupId'];
							$show_info['sku']            = $show_sql[0]['sku'];
							$show_info['sku_amount']     = $show_sql[0]['skuAmount'];
							$show_info['goods_location'] = $show_sql[0]['pName'];
						}
					}
					if(empty($show_info)){
						self::$errCode = "003";
						self::$errMsg  = "该提货单不在等提货状态，请确认!";
						return false;
					}else{
						self::$errMsg  = "请扫描该配货提货单下的料号!";
						return $show_info;
					}										
				}else{
					self::$errCode = "003";
					self::$errMsg  = "订单料号初始化出错，请重试";
					return false;
				}
			}else{
				$iscan = GroupDistributionBModel::getGroupDistListB("*","where shipOrderGroup='$shipOrderGroup' and status=0");
				if(empty($iscan)){
					self::$errCode = "003";
					self::$errMsg  = "该单已扫描配货完成，请扫描其他单!";
					return false;
				}else{
					//更新配货人
					$data = array(
						'userID' => $userId
					);
					GroupDistributionBModel::update($data,"and shipOrderGroup='$shipOrderGroup' and status=0");
					
					//检查是否有出库过
					$isout = GroupDistributionBModel::getGroupDistListB("*","where shipOrderGroup='$shipOrderGroup' and status=1");
					
					$show_info = array();
					$show_sql  = GroupDistributionBModel::getGroupSkuInfoB("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 group by a.pName order by a.groupId asc");					
					
					if(!empty($show_sql)){
						$order_sku_info = $this->get_valid_order($show_sql[0]['shipOrderGroup'],$show_sql[0]['sku'],$show_sql[0]['pName']);	
						if($order_sku_info){
							$show_info['group_id']       = $show_sql[0]['groupId'];
							$show_info['sku']            = $show_sql[0]['sku'];
							$show_info['sku_amount']     = $show_sql[0]['skuAmount'];
							$show_info['goods_location'] = $show_sql[0]['pName'];
						}
					}		
					
					if(empty($show_info)){
						if(empty($isout)){
							self::$errCode = "003";
							self::$errMsg  = "该提货单不在待提货状态，请确认!";
							return false;
						}else{
							self::$errCode = "003";
							self::$errMsg  = "该单已扫描配货完成，请扫描其他单!";
							return false;
						}					
					}else{
						self::$errMsg  = "请扫描该配货提货单下的料号!";
						return $show_info;
					}	
				}
			}
		}
	}
	
	//验证sku
	function act_checkSku(){
		$shipOrderGroup = $_POST['order_group'];
		$sku 		    = trim($_POST['sku']);
		$now_sku 	    = trim($_POST['now_sku']);
		$sku       		= get_goodsSn($sku);
		$now_sku   		= get_goodsSn($now_sku);
		if($sku!=$now_sku){
			self::$errCode = "003";
			self::$errMsg  = "所扫描料号与当前料号不符，请确认!";
			return $sku;
		}
		$sku_info = GroupDistributionBModel::getGroupDistListB("*","where shipOrderGroup='$shipOrderGroup' and sku='$sku' and status=0");
		if(empty($sku_info)){
			self::$errCode = "004";
			self::$errMsg  = "该料号已扫描出库，请扫描其他料号!";
			return false;
		}else{
			self::$errMsg  = "请输入该料号实际出库配货数量!";
			return $sku;
		}	
	}
	
	//验证sku数量
	function act_checkSkuNum(){
		$userId         = $_SESSION['userId'];
		$show_mes 		= array();
		$shipOrderGroup = $_POST['order_group'];
		$sku 		    = trim($_POST['sku']);
		//$sku         = getGoodsSn2($sku);
		$sku_num 	    = $_POST['sku_num'];
		$group_id 	    = $_POST['now_group_id'];
		$now_pname 	    = $_POST['now_pname'];

		$sku_info 		= GroupDistributionBModel::getGroupDistListB("*","where shipOrderGroup='$shipOrderGroup' and sku='$sku' and pName='$now_pname' and status=0");	
		$sku_onhand 	= GroupDistributionBModel::getSkuPositionStock("and c.sku='$sku' and b.pName='$now_pname' and a.storeId=2");		

		if(!is_numeric($sku_num) || $sku_num==0){
			self::$errCode = "003";
			self::$errMsg  = "出库数量必须为正整数，请确认!";
			return false;
		}
		
		if($sku_num>$sku_onhand[0]['nums']){
			self::$errCode = "003";
			self::$errMsg  = "出库数量不能大于系统库存，请确认!";
			return false;
		}

		if($sku_num>$sku_info[0]['skuAmount']){
			self::$errCode = "003";
			self::$errMsg  = "出库数量不能大于料号数量，请确认!";
			return false;
		}

		$i_data = array(
			'status' => 1,
			'amount' => $sku_num
		);
		$inser_info  = GroupDistributionBModel::update($i_data,"and shipOrderGroup='$shipOrderGroup' and sku='$sku' and pName='$now_pname'");
		if($inser_info){
			$skuinfo = whShelfModel::selectSku(" where sku = '{$sku}'");
			$position_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='$now_pname' and storeId=2");
			$positionId    = $position_info[0]['id'];
			$paraArr = array(
				'ordersn' 	 => $shipOrderGroup,
				'sku'     	 => $sku,
				'amount'  	 => $sku_num,
				'purchaseId' => $skuinfo['purchaseId'],
				'ioType'	 => 1,
				'ioTypeId'   => 2,
				'userId'	 => $userId,
				'reason'	 => '提货单配货出库',
				'positionId' => $positionId,
				'storeId'    => 2
			);

			$WhIoRecordsAct = new WhIoRecordsAct();
			$tt = $WhIoRecordsAct->act_addIoRecoresForWh($paraArr);     //出库记录

			$now_shipOrderId_info = GroupDistributionBModel::getGroupDistListB("shipOrderId","where shipOrderGroup='$shipOrderGroup' and sku='$sku' and pName='$now_pname' and status=1");
			$shipOrderId_arr      = array();
			$shipOrderId_arr 	  = explode(',',$now_shipOrderId_info[0]['shipOrderId']);
			foreach($shipOrderId_arr as $info){
				$complete_sql = GroupDistributionBModel::getGroupDistListB("*","where shipOrderGroup='$shipOrderGroup' and FIND_IN_SET({$info},shipOrderId) and status=0");
				if(empty($complete_sql)){
					//更新订单到待配货状态	
					GroupDistributionBModel::updateShipOrder(array('orderStatus'=>402),"and id='{$info}' and orderStatus=407");
					//WhPushModel::pushOrderStatus($info,'STATESHIPPED_BEPICKING',$_SESSION['userId'],time());        //状态推送
				}
			}	
		}
		
		$status_sql = GroupDistributionBModel::getGroupDistListB("*","where shipOrderGroup='$shipOrderGroup' and status=0");
		if(empty($status_sql)){
			self::$errCode = 1;
			self::$errMsg  = "提货单出库完成，请扫描下一提货单1!";
			return true;
		}else{
			$show_info = array();
			$show_sql  = GroupDistributionBModel::getGroupSkuInfoB("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 and a.groupId>'$group_id' group by a.pName order by a.groupId asc");
			if(!empty($show_sql)){
				$order_sku_info = $this->get_valid_order($show_sql[0]['shipOrderGroup'],$show_sql[0]['sku'],$show_sql[0]['pName']);	
				if($order_sku_info){
					$show_info['group_id']       = $show_sql[0]['groupId'];
					$show_info['sku']            = $show_sql[0]['sku'];
					$show_info['sku_amount']     = $show_sql[0]['skuAmount'];
					$show_info['goods_location'] = $show_sql[0]['pName'];
				}
			}			

			if(empty($show_info)){
				$show_info2 = array();
				$show_sql  = GroupDistributionBModel::getGroupSkuInfoB("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 and a.groupId<'$group_id' group by a.pName order by a.groupId asc");
				if(!empty($show_sql)){
					$order_sku_info = $this->get_valid_order($show_sql[0]['shipOrderGroup'],$show_sql[0]['sku'],$show_sql[0]['pName']);	
					if($order_sku_info){
						$show_info2['group_id']       = $show_sql[0]['groupId'];
						$show_info2['sku']            = $show_sql[0]['sku'];
						$show_info2['sku_amount']     = $show_sql[0]['skuAmount'];
						$show_info2['goods_location'] = $show_sql[0]['pName'];
					}
				}else{
					self::$errCode = 1;
					self::$errMsg  = "提货单出库完成，请扫描下一提货单2!";
					return true;
				}

				if(!empty($show_info2)){
					$show_mes = $show_info2;
				}else{
					self::$errCode = 1;
					self::$errMsg  = "提货单出库完成，请扫描下一提货单3!";
					return true;
				}
			}else{
				$show_mes = $show_info;
			}
					
			self::$errMsg  = "出库成功，请扫描下一个料号!";
			return $show_mes;
		}
	}
	
	//下一料号
	function act_nextSku(){
		$show_mes 		= array();
		$shipOrderGroup = $_POST['order_group'];
		$group_id 	    = $_POST['now_group_id'];	
		$now_sku 	    = $_POST['now_sku'];
		$now_pname 	    = $_POST['now_pname'];
		
		$show_info = array();
		$show_sql  = GroupDistributionBModel::getGroupSkuInfoB("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 and a.groupId>'$group_id' order by a.groupId asc");
		if(!empty($show_sql)){
			$order_sku_info = $this->get_valid_order($show_sql[0]['shipOrderGroup'],$show_sql[0]['sku'],$show_sql[0]['pName']);	
			if($order_sku_info){
				$show_info['group_id']       = $show_sql[0]['groupId'];
				$show_info['sku']            = $show_sql[0]['sku'];
				$show_info['sku_amount']     = $show_sql[0]['skuAmount'];
				$show_info['goods_location'] = $show_sql[0]['pName'];
			}
		}			

		if(empty($show_info)){
			$show_info2 = array();			
			$show_sql  = GroupDistributionBModel::getGroupSkuInfoB("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 and a.groupId<'$group_id' order by a.groupId asc");
			if(!empty($show_sql)){
				$order_sku_info = $this->get_valid_order($show_sql[0]['shipOrderGroup'],$show_sql[0]['sku'],$show_sql[0]['pName']);	
				if($order_sku_info){
					$show_info2['group_id']       = $show_sql[0]['groupId'];
					$show_info2['sku']            = $show_sql[0]['sku'];
					$show_info2['sku_amount']     = $show_sql[0]['skuAmount'];
					$show_info2['goods_location'] = $show_sql[0]['pName'];
				}
			}else{
				self::$errCode = "003";
				self::$errMsg  = "该料号是最后一个了!";
				return false;
			}

			if(!empty($show_info2)){
				$show_mes = $show_info2;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "该料号是最后一个了!";
				return false;
			}
		}else{
			$show_mes = $show_info;
		}
	
		self::$errMsg  = "请扫描该配货提货单下的料号!";
		return $show_mes;
	}
	
	//获取提货单里面料号有效订单信息(参数：提货单号、料号、仓位)
	function get_valid_order($order_group,$sku,$pName){
		$goup_sql = GroupDistributionBModel::getGroupDistListB("*","where shipOrderGroup='$order_group' and sku='$sku' and pName='$pName' and status=0");		
		foreach($goup_sql as $group){
			$infos = GroupDistributionBModel::getShipOrder("orderStatus","where id in ({$group['shipOrderId']})");
			foreach($infos as $info){
				if($info['orderStatus']!=407){
					return false;
					break;
				}
			}
		}
		return true;
	}

	//插入扫描表(参数:订单号,提货单号,料号,料号数量,扫描人)
	function inser_scan_record_by_sku($orderid,$sku,$sku_num,$userid){
		$data = array();
		$time = time();
		$sku_info   = OmAvailableModel::getTNameList("wh_shipping_orderdetail","*","where shipOrderId='$orderid' and sku='$sku' order by combineSku desc");
		foreach($sku_info as $info){		
			$scan_exist = OrderPickingRecordsModel::getPickingRecords("*","where shipOrderId='$orderid' and sku='$sku' and shipOrderdetailId='{$info['id']}' and is_delete=0");
			if(empty($scan_exist)){
				$data = array(
					'shipOrderId' => $orderid,
					'shipOrderdetailId' => $info['id'],
					'sku'         => $sku,
					'amount'      => $info['amount'],
					'totalNums'   => $info['amount'],
					'scanTime'    => $time,
					'scanUserId'  => $userid,
					'isScan'      => 1
				);
				OrderPickingRecordsModel::insertRow($data);
			}else{
				$data = array(
					'amount'      => $info['amount'],
					'scanTime'    => $time,
					'scanUserId'  => $userid,
					'isScan'      => 1
				);
				OrderPickingRecordsModel::update($data,"and shipOrderId='$orderid' and sku='$sku' and shipOrderdetailId='{$info['id']}' and is_delete=0");
			}
		}
	}
}


?>