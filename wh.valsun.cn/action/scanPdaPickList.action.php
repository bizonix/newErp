<?php
/**
*类名：配货清单出库
*功能：处理配货清单出库相关操作
*作者：hws
*
*/
class ScanPdaPickListAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取配货单信息
	function act_getGroupInfo(){
		$userId 		= $_SESSION['userId'];
		$shipOrderGroup = $_POST['order_group'];
		$group_sql      = GroupRouteModel::getOrderGroup("*","where shipOrderGroup='$shipOrderGroup'");
		if(empty($group_sql)){
			self::$errCode = "003";
			self::$errMsg  = "该配货清单号不存在，请重新输入!";
			return false;
		}else{
			$scan_sql = GroupDistributionModel::getGroupDistList("shipOrderGroup","where shipOrderGroup='$shipOrderGroup'");
			if(empty($scan_sql)){
				$string = "";
				$time   = strtotime(date('Y-m-d H:i:s'));
				foreach($group_sql as $info){
					$string .= "('".$info['shipOrderGroup']."','". $info['sku']."','". $info['id']."','". $info['skuAmount']."','". $info['shipOrderId']."','0','0','".$info['carNumber']."','".$info['pName']."','".$userId."','".$time."'),";
				}
				$string      = trim($string,",");	

				//插入配货清单表
				$insert_info = GroupDistributionModel::insertGroupDist($string);			
				if($insert_info){
					$show_info = array();
					$show_sql  = GroupDistributionModel::getGroupSkuInfo("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 group by a.pName order by a.groupId asc");
					
					if(!empty($show_sql)){
						foreach($show_sql as $show){
							$order_sku_info = $this->get_valid_order($show['shipOrderGroup'],$show['sku'],$show['pName']);							
							if($order_sku_info['sku_amount']!=0){
								$show_info['group_id']       = $show['groupId'];
								$show_info['sku']            = $show['sku'];
								$show_info['sku_amount']     = $order_sku_info['sku_amount'];
								$show_info['goods_location'] = $show['pName'];
								break;
							}
						}
					}
					if(empty($show_info)){
						self::$errCode = "003";
						self::$errMsg  = "该清单不在等待配货状态，请确认!";
						return false;
					}else{
						self::$errMsg  = "请扫描该配货清单下的料号!";
						return $show_info;
					}										
				}else{
					self::$errCode = "003";
					self::$errMsg  = "订单料号初始化出错，请重试";
					return false;
				}
			}else{
				$iscan = GroupDistributionModel::getGroupDistList("*","where shipOrderGroup='$shipOrderGroup' and status=0");
				if(empty($iscan)){
					self::$errCode = "003";
					self::$errMsg  = "该清单已扫描配货完成，请扫描其他清单!";
					return false;
				}else{
					//更新配货人
					$data = array(
						'userID' => $userId
					);
					GroupDistributionModel::update($data,"and shipOrderGroup='$shipOrderGroup' and status=0");
					
					//检查是否有出库过
					$isout = GroupDistributionModel::getGroupDistList("*","where shipOrderGroup='$shipOrderGroup' and status=1");
					
					$show_info = array();
					$show_sql  = GroupDistributionModel::getGroupSkuInfo("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 group by a.pName order by a.groupId asc");					
					
					if(!empty($show_sql)){
						foreach($show_sql as $show){
							$order_sku_info = $this->get_valid_order($show['shipOrderGroup'],$show['sku'],$show['pName']);							
							if($order_sku_info['sku_amount']!=0){
								$show_info['group_id']       = $show['groupId'];
								$show_info['sku']            = $show['sku'];
								$show_info['sku_amount']     = $order_sku_info['sku_amount'];
								$show_info['goods_location'] = $show['pName'];
								break;
							}
						}
					}			
					
					if(empty($show_info)){
						if(empty($isout)){
							self::$errCode = "003";
							self::$errMsg  = "该清单不在等待配货状态，请确认!";
							return false;
						}else{
							self::$errCode = "003";
							self::$errMsg  = "该清单已扫描配货完成，请扫描其他清单!";
							return false;
						}					
					}else{
						self::$errMsg  = "请扫描该配货清单下的料号!";
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
		$sku_info = GroupDistributionModel::getGroupDistList("*","where shipOrderGroup='$shipOrderGroup' and sku='$sku' and status=0");
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
		$bool = false;             //标志是否有摒弃订单
		$shipOrderGroup = $_POST['order_group'];
		$sku 		    = trim($_POST['sku']);
		//$sku         = getGoodsSn2($sku);
		$sku_num 	    = $_POST['sku_num'];
		$group_id 	    = $_POST['now_group_id'];
		$now_pname 	    = $_POST['now_pname'];

		$sku_info 		= GroupDistributionModel::getGroupDistList("*","where shipOrderGroup='$shipOrderGroup' and sku='$sku' and pName='$now_pname' and status=0");	
		$sku_onhand 	= GroupDistributionModel::getSkuPositionStock("and c.sku='$sku' and b.pName='$now_pname' and a.storeId=1");		

		$order_sku_info = $this->get_valid_order($sku_info[0]['shipOrderGroup'],$sku_info[0]['sku'],$sku_info[0]['pName']);
		
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
		
		if($sku_num>$order_sku_info['sku_amount']){
			self::$errCode = "003";
			self::$errMsg  = "出库数量不能大于料号数量，请确认!";
			return false;
		}
	
		//订单摒弃
		if($sku_num<$order_sku_info['sku_amount']){
			$rem_order = array();
			$rem_order_car = array();
			$rem_order_num = array();
			$now_num = 0;
			$tmp_num = 0;
			$orders = '';
			//$differ_num = $order_sku_info['sku_amount']-$sku_num;
			foreach($sku_info as $info){
				$orders .= $info['shipOrderId'].",";
			}
			$orders = "(".trim($orders,",").")";
			$abandon_orders = GroupDistributionModel::getShipOrderPay("a.createdTime,b.shipOrderId,b.skuAmount,b.carNumber","where a.id in {$orders} and a.orderStatus=402 and b.pName='$now_pname' order by a.createdTime asc");

			foreach($abandon_orders as $order){
				$tmp_num = $now_num+$order['skuAmount'];
				if($tmp_num<=$sku_num){
					$rem_order[] 	 = $order['shipOrderId'];
					$rem_order_car[] = $order['carNumber'];
					$rem_order_num[] = $order['skuAmount'];
					$now_num = $tmp_num;
				}
			}

			$bool = true;
		}
		
		$car_info = array();
		if($bool){
			foreach($rem_order_car as $key=>$r_order_car){
				$car_info[]=array(
					'car' => $r_order_car,
					'num' => $rem_order_num[$key]
				);
			}
			$submit_orders = implode(',',$rem_order);
			$submit_nums   = implode(',',$rem_order_num);
		}else{
			foreach($order_sku_info['car_number'] as $key=>$r_order_car){
				$car_info[]=array(
					'car' => $r_order_car,
					'num' => $order_sku_info['sku_number'][$key]
				);
			}
			$submit_orders = implode(',',$order_sku_info['orders']);
			$submit_nums   = implode(',',$order_sku_info['sku_number']);
		}

		$res = array();
		$res['res_car_info']  = $car_info;
		$res['submit_orders'] = $submit_orders;
		$res['submit_nums']   = $submit_nums;
		return $res;
		
	}
	
	//数据提交
	function  act_submitInfo(){
		$userId 		= $_SESSION['userId'];
		$show_mes 		= array();
		$shipOrderGroup = $_POST['order_group'];
		$sku 		    = trim($_POST['sku']);
		$sku            = get_goodsSn($sku);
		$submit_orders  = trim($_POST['submit_orders'],',');
		$submit_nums    = trim($_POST['submit_nums'],',');
		$group_id 	    = $_POST['now_group_id'];
		$now_sku 	    = trim($_POST['now_sku']);
		$now_sku        = get_goodsSn($now_sku);
		$now_pname 	    = $_POST['now_pname'];
		$orders_arr = explode(',',$submit_orders);
		$nums_arr 	= explode(',',$submit_nums);
		if($sku!=$now_sku){
			$sku = $now_sku;
		}
		if($sku!='undefined'){
			foreach($orders_arr as $key=>$order){
				$i_data = array(
					'status' => 1,
					'amount' => $nums_arr[$key]
				);
				$inser_info  = GroupDistributionModel::update($i_data,"and shipOrderGroup='$shipOrderGroup' and sku='$sku' and shipOrderId='$order'");
				if($inser_info){
					//outskunums2($sku,$nums_arr[$key],$order);         //出库记录
					$position_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='$now_pname' and storeId=1");
					$positionId    = $position_info[0]['id'];
					$skuinfo       = whShelfModel::selectSku(" where sku = '{$sku}'");
					
					$paraArr = array(
						'ordersn' 	 => $order,
						'sku'     	 => $sku,
						'amount'  	 => $nums_arr[$key],
						'purchaseId' => $skuinfo['purchaseId'],
						'ioType'	 => 1,
						'ioTypeId'   => 2,
						'userId'	 => $userId,
						'reason'	 => '清单配货出库',
						'positionId' => $positionId
					);
					$WhIoRecordsAct = new WhIoRecordsAct();
					$WhIoRecordsAct->act_addIoRecoresForWh($paraArr);     //出库记录
					
					$this->inser_scan_record_by_sku($order,$sku,$nums_arr[$key],$userId);   //插入扫描表
					
					$complete_sql = GroupDistributionModel::getGroupDistList("*","where shipOrderGroup='$shipOrderGroup' and shipOrderId='$order' and status=0");
					if(empty($complete_sql)){
						//更新订单到复核状态	
						GroupDistributionModel::updateShipOrder(array('orderStatus'=>403),"and id='$order' and orderStatus=402");
						WhPushModel::pushOrderStatus($order,'STATESHIPPED_PENDREVIEW',$_SESSION['userId'],time());        //状态推送
					}
				}

			}
		}		
		
		$status_sql = GroupDistributionModel::getGroupDistList("*","where shipOrderGroup='$shipOrderGroup' and status=0");
		if(empty($status_sql)){
			self::$errCode = 1;
			self::$errMsg  = "配货清单出库完成，请扫描下一清单1!";
			return true;
		}else{
			$show_info = array();
			$show_sql  = GroupDistributionModel::getGroupSkuInfo("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 and a.groupId>'$group_id' group by a.pName order by a.groupId asc");
			if(!empty($show_sql)){
				foreach($show_sql as $show){
					$order_sku_info = $this->get_valid_order($show['shipOrderGroup'],$show['sku'],$show['pName']);							
					if($order_sku_info['sku_amount']!=0){
						$show_info['group_id']       = $show['groupId'];
						$show_info['sku']            = $show['sku'];
						$show_info['sku_amount']     = $order_sku_info['sku_amount'];
						$show_info['goods_location'] = $show['pName'];
						break;
					}
				}
			}			

			if(empty($show_info)){
				$show_info2 = array();
				$show_sql  = GroupDistributionModel::getGroupSkuInfo("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 and a.groupId<'$group_id' group by a.pName order by a.groupId asc");
				if(!empty($show_sql)){
					foreach($show_sql as $show){
						$order_sku_info = $this->get_valid_order($show['shipOrderGroup'],$show['sku'],$show['pName']);							
						if($order_sku_info['sku_amount']!=0){
							$show_info2['group_id']       = $show['groupId'];
							$show_info2['sku']            = $show['sku'];
							$show_info2['sku_amount']     = $order_sku_info['sku_amount'];
							$show_info2['goods_location'] = $show['pName'];
							break;
						}
					}
				}else{
					self::$errCode = 1;
					self::$errMsg  = "配货清单出库完成，请扫描下一清单2!";
					return true;
				}

				if(!empty($show_info2)){
					$show_mes = $show_info2;
				}else{
					self::$errCode = 1;
					self::$errMsg  = "配货清单出库完成，请扫描下一清单3!";
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
		$show_sql  = GroupDistributionModel::getGroupSkuInfo("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 and a.groupId>'$group_id' and a.pName!='$now_pname' and a.sku!='$now_sku' group by a.pName order by a.groupId asc");
		if(!empty($show_sql)){
			foreach($show_sql as $show){
				$order_sku_info = $this->get_valid_order($show['shipOrderGroup'],$show['sku'],$show['pName']);							
				if($order_sku_info['sku_amount']!=0){
					$show_info['group_id']       = $show['groupId'];
					$show_info['sku']            = $show['sku'];
					$show_info['sku_amount']     = $order_sku_info['sku_amount'];
					$show_info['goods_location'] = $show['pName'];
					break;
				}
			}
		}			

		if(empty($show_info)){
			$show_info2 = array();			
			$show_sql  = GroupDistributionModel::getGroupSkuInfo("and a.shipOrderGroup='$shipOrderGroup' and a.status=0 and a.groupId<'$group_id' and a.pName!='$now_pname' and a.sku!='$now_sku' group by a.pName order by a.groupId asc");
			if(!empty($show_sql)){
				foreach($show_sql as $show){
					$order_sku_info = $this->get_valid_order($show['shipOrderGroup'],$show['sku'],$show['pName']);							
					if($order_sku_info['sku_amount']!=0){
						$show_info2['group_id']       = $show['groupId'];
						$show_info2['sku']            = $show['sku'];
						$show_info2['sku_amount']     = $order_sku_info['sku_amount'];
						$show_info2['goods_location'] = $show['pName'];
						break;
					}
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
	
		self::$errMsg  = "请扫描该配货清单下的料号!";
		return $show_mes;
	}
	
	//获取清单里面料号有效订单信息(参数：清单号、料号)
	function get_valid_order($order_group,$sku,$pName){
		$order_arr  = array();
		$car_arr    = array();
		$amount_arr = array();
		$sku_amount = 0;
		$valid_order_info = array();
		
		$goup_sql = GroupDistributionModel::getGroupDistList("*","where shipOrderGroup='$order_group' and sku='$sku' and pName='$pName' and status=0");
		
		foreach($goup_sql as $group){
			$info = GroupDistributionModel::getShipOrder("orderStatus","where id='{$group['shipOrderId']}'");
			if($info && $info[0]['orderStatus']==402){
				$sku_amount  += $group['skuAmount'];
				$order_arr[]  = $group['shipOrderId'];
				$car_arr[]    = $group['carNumber'];
				$amount_arr[] = $group['skuAmount'];
			}
		}
		
		$valid_order_info['orders']     = $order_arr;
		$valid_order_info['sku_amount'] = $sku_amount;
		$valid_order_info['car_number'] = $car_arr;
		$valid_order_info['sku_number'] = $amount_arr;

		return $valid_order_info;
	}

	//插入扫描表(参数:订单号,清单号,料号,料号数量,扫描人)
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