<?php
/**
 * pda_whAssignSkuReturn
 * 调拨清单出库复核
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Pda_whAssignSkuReturnAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	//获取配货单信息
	function act_getGroupInfo(){
		$userId 		= $_SESSION['userId'];
		$shipOrderGroup = $_POST['order_group'];
		$group_sql      = WhGoodsAssignModel::getOrderGroup("*", array('assignNumber'=>$shipOrderGroup));
        //var_dump($group_sql);exit;
		if(empty($group_sql)){
			self::$errCode = "003";
			self::$errMsg  = "该调拨单号不存在，请重新输入!";
			return false;
		}else{
		  if(in_array($group_sql[0]['status'], array(100))){
            self::$errCode = "003";
			self::$errMsg  = "该调拨单不在可退库状态!";
			return false;
		  }
            self::$errMsg  = "请扫描该要退库的料号!";
    		return array('group_id'=>$group_sql[0]['id']);
		}
	}
	
	//验证sku
	function act_checkSku(){
		$goodsAssignId  = $_POST['order_group'];
		$sku 		    = trim($_POST['sku']);
		$sku       		= get_goodsSn($sku);
        
        
        $assignStock    =   WhGoodsAssignModel::getAssignStock($sku); //获取该料号调拨库存
        if($assignStock == 0){
            self::$errCode = "004";
			self::$errMsg  = "该调拨单无调拨库存，不能退库!";
			return FALSE;
        }
        
		$sku_info = WhGoodsAssignModel::getDetail( $goodsAssignId ," and a.sku='$sku'");
		if(empty($sku_info)){
			self::$errCode = "004";
			self::$errMsg  = "该调拨单无此料号!";
			return FALSE;
		}else{
            if($sku_info['num'] <= $sku_info['inCheckNum'] && $sku_info['num'] <= $sku_info['outCheckNum']){
                self::$errCode = "004";
    			self::$errMsg  = "该料号不符合退库数量!";
    			return FALSE;
            }
            self::$errCode = "0";
			self::$errMsg  = "请输入退库数量!";
            //$arr['sku']    = $sku;
			return $sku;               
		}	
	}
	
	//验证sku数量
	function act_checkSkuNum(){
		$bool = false;             //标志是否有摒弃订单
		$assignNUmber = $_POST['order_group'];
		$sku 		  = trim($_POST['sku']);
		//$sku         = getGoodsSn2($sku);
		$sku_num 	    = $_POST['sku_num'];
		$assignId 	    = $_POST['now_group_id'];
		//$now_pname 	    = $_POST['now_pname'];
        $assignStock    =   WhGoodsAssignModel::getAssignStock($sku); //获取该料号调拨库存
        if($assignStock == 0){
            self::$errCode = "004";
			self::$errMsg  = "该调拨单无调拨库存，不能退库!";
			return FALSE;
        }
        
        if($assignStock < $sku_num){
            self::$errCode = "004";
			self::$errMsg  = "退库数量大于调拨库存，不能退库!";
			return FALSE;
        }
        
        $sku_info = WhGoodsAssignModel::getDetail( $assignId ," and a.sku='$sku'");
		if(empty($sku_info)){
			self::$errCode = "004";
			self::$errMsg  = "该调拨单无此料号!";
			return FALSE;
		}
        
        TransactionBaseModel :: begin();
        
		$where  =   array(
                        'sku'       =>  $sku,
                        'storeId'   =>  $sku_info['storeId'],
                        );
        $update =   array(
                        'actualStock'=> "actualStock + $sku_num",
                        'assignStock'=> "assignStock - $sku_num"
                    );
        $info   =   WhGoodsAssignModel::updateSkuLocation($where, $update); //更新wh_sku_location的调拨库存和总库存
        if(!$info){
            TransactionBaseModel :: rollback();
            self::$errCode = "003";
			self::$errMsg  = "更新总库存失败!";
			return false;
        }
        $where  =   array(
                        'pId'       =>  $skuinfo['id'],
                        'positionId'=>  $sku_info['positionId'],
                        );
        $update =   array(
                        'nums'=> "nums + $sku_num"
                    );
        $info   =   WhGoodsAssignModel::updateProdcutPosition($where, $update); //更新wh_product_position_relation的仓位库存
        if(!$info){
            TransactionBaseModel :: rollback();
            self::$errCode = "003";
			self::$errMsg  = "更新仓位库存失败!";
			return false;
        }
        TransactionBaseModel::commit();
        self::$errCode = "0";
        self::$errMsg  = "退库成功!";
        return true;
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