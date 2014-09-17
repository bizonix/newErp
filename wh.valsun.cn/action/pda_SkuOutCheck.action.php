<?php
/**
 * Pda_SkuOutCheckAct
 * 调拨清单出库复核
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Pda_SkuOutCheckAct extends Auth{
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
		  if($group_sql[0]['status'] != 102){
            self::$errCode = "003";
			self::$errMsg  = "该调拨单不在出库复核状态!";
			return false;
		  }
          $scan_sql = WhGoodsAssignModel::getDetail($group_sql[0]['id'], ' and a.scanUid !=0 and a.checkUid = 0');
          if(empty($scan_sql)){
            if($group_sql[0]['status'] == 102){
                $where     = array('id'=>$group_sql[0]['id']);
                $status    = array('status'=>103, 'statusTime'=>time());   
                WhGoodsAssignModel::updateAssignListStatus($where, $status);
            }
            self::$errCode = "003";
			self::$errMsg  = "该调拨单出库复核已完成，请扫描其他清单!";
			return false;
          }else{
            self::$errMsg  = "请扫描该配货清单下的料号!";
			return array('group_id'=>$group_sql[0]['id']);
          }
		}
	}
	
	//验证sku
	function act_checkSku(){
		$goodsAssignId  = $_POST['order_group'];
		$sku 		    = trim($_POST['sku']);
		$sku       		= get_goodsSn($sku);

		$sku_info = WhGoodsAssignModel::getDetail( $goodsAssignId ," and a.sku='$sku' and a.scanUid != 0");
		if(empty($sku_info)){
			self::$errCode = "004";
			self::$errMsg  = "该调拨单无此出库复核料号!";
			return FALSE;
		}else{
            if($sku_info['assignNum'] <= $sku_info['outCheckNum']){
                self::$errCode = "004";
    			self::$errMsg  = "该料号已完成出库复核!";
    			return FALSE;
            }
            self::$errCode      = 0;
            self::$errMsg       = '请输入接收数量!';
            $res['sku']         = $sku_info['sku'];
            $res['sku_amount']   = $sku_info['assignNum'];
            $res['check_num']  = $sku_info['outCheckNum'];
            return $res;
		}	
	}
	
	//验证sku数量
	function act_checkSkuNum(){
		$bool = false;             //标志是否有摒弃订单
		$assignNUmber = $_POST['order_group'];
		$sku 		  = trim($_POST['sku']);
		$sku          = get_goodsSn($sku);
		$sku_num 	  = intval($_POST['sku_num']);
		$goodsAssignId= intval($_POST['now_group_id']);
		
		$sku_info = WhGoodsAssignModel::getDetail( $goodsAssignId ," and a.sku='$sku' and a.scanUid != 0");
		if(empty($sku_info)){
			self::$errCode = "001";
			self::$errMsg  = "该调拨单无此出库复核料号!";
			return FALSE;
		}else{
            if($sku_info['assignNum'] <= $sku_info['outCheckNum']){
                self::$errCode = "002";
    			self::$errMsg  = "该料号已完成出库复核!";
    			return FALSE;
            }
	        
            $outCheckNum    =  $sku_info['outCheckNum']+$sku_num;  //系统累计出库复核数量
    		if($outCheckNum > $sku_info['assignNum']){
    			self::$errCode = "003";
    			self::$errMsg  = "总复核数量不能大于配货数量，请确认!";
    			return false;
    		}
            
            //print_r($sku_info);exit;
            $where          =   array('goodsAssignId'=>$goodsAssignId, 'sku'=>$sku); //生成where条件
            $update         =   array('outCheckNum'=>$outCheckNum);
            if($outCheckNum >= $sku_info['assignNum']){
                $update['checkTime']    =   time();
                $update['checkUid']     =   $_SESSION['userId'];
            }
            $arr            =   array(
                                'sku'       => $sku,
                                'sku_amount'=> $sku_info['assignNum'],
                                'check_num' => $outCheckNum
                            );
                            
            $info           =   WhGoodsAssignModel::updateAssignDetail($where, $update);
            if(!$info){
                self::$errCode = "004";
    			self::$errMsg  = "更新出库明细复核数量失败!";
    			return $arr;
            }
            if(isset($update['checkUid'])){
                $assignList     =   WhGoodsAssignModel::getDetail($goodsAssignId, ' and a.checkUid=0 and a.scanUid !=0');
                if(empty($assignList)){  //该调拨单下所有料号复核完毕
                    $where      =   array('id'=>$goodsAssignId);
                    $update     =   array('statusTime'=>time(), 'status'=>103);
                    $info       =   WhGoodsAssignModel::updateAssignListStatus($where, $update);
                    if($info){
                        self::$errCode = "006";
            			self::$errMsg  = "该调拨单出库复核完毕!";
            			return $arr;
                    }else{
                        self::$errCode = "005";
            			self::$errMsg  = "调拨单更新失败!";
            			return FALSE;
                    }
                }else{
                    self::$errCode = "0";
        			self::$errMsg  = "该料号复核完毕!";
        			return $arr;
                }
            }else{
                self::$errCode = "0";
    			self::$errMsg  = "该料号已复核，请录入下一料号!";
    			return $arr;         
            }
       }	
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