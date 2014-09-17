<?php
/**
 * InCheckPdaAssignListAct
 * 调拨清接收复核
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class InCheckPdaAssignListAct extends Auth{
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
		  if($group_sql[0]['status'] != 105){
            self::$errCode = "003";
			self::$errMsg  = "该调拨单不在待接收复核状态!";
			return false;
		  }
          //$scan_sql = WhGoodsAssignModel::getDetail($group_sql[0]['id'], ' and a.scanUid !=0 and a.checkUid != 0');
//          if(empty($scan_sql)){
//            if($group_sql[0]['status'] == 105){
//                $where     = array('id'=>$group_sql[0]['id']);
//                $status    = array('status'=>106, 'updateTime'=>time());   
//                WhGoodsAssignModel::updateAssignListStatus($where, $status);
//            }
//            self::$errCode = "003";
//			self::$errMsg  = "该调拨单接收已完成，请扫描其他清单!";
//			return false;
//          }else{
            self::$errMsg  = "请扫描该调拨单下的料号!";
			return array('group_id'=>$group_sql[0]['id']);
          //}
		}
	}
	
	//验证sku
	function act_checkSku(){
		$goodsAssignId  = $_POST['order_group'];
		$sku 		    = trim($_POST['sku']);
		$sku       		= get_goodsSn($sku);

		$sku_info = WhGoodsAssignModel::getDetail( $goodsAssignId ," and a.sku='$sku' and a.checkUid != 0");
		if(empty($sku_info)){
			self::$errCode = "004";
			self::$errMsg  = "该调拨单无此料号!";
			return FALSE;
		}else{
            if($sku_info['assignNum'] <= $sku_info['inCheckNum']){
                self::$errCode = "004";
    			self::$errMsg  = "该料号已完成接收复核!";
    			return FALSE;
            }
            self::$errCode      = 0;
            self::$errMsg       = '请输入接收数量!';
            $res['sku']         = $sku_info['sku'];
            $res['sku_amount']   = $sku_info['assignNum'];
            $res['check_num']  = $sku_info['inCheckNum'];
            return $res;
			//self::$errMsg  = "请输入该料号实际出库配货数量!";
            //echo $sku_info['inCheckNum'];
            //$inCheckNum    =  $sku_info['inCheckNum']+1;  //系统累计接收复核数量
            //print_r($sku_info);exit;
		}	
	}
    
    //验证接收复核数量
    function act_checkSkuNum(){
        $assignNUmber = $_POST['order_group'];
		$sku 		  = trim($_POST['sku']);
        $sku       	  = get_goodsSn($sku);
        
		$sku_num      = intval($_POST['sku_num']);
        
        if(!is_numeric($sku_num)){
            self::$errCode  = '001';
            self::$errMsg   = '请输入正确数量!';
            return false;
        }
        
		$goodsAssignId= intval($_POST['now_group_id']);
        $skuinfo      = whShelfModel::selectSku(" where sku = '{$sku}'"); //获取sku信息
		$sku_info 	  = WhGoodsAssignModel::getDetail( $goodsAssignId," and a.sku='$sku'");  //调拨单明细中的sku信息
        
        if($sku_info['inCheckNum'] >= $sku_info['assignNum']){
            self::$errCode  = '002';
            self::$errMsg   = '该料号已接收复核完毕!';
            return false;
        }
        $inCheckNum     = $sku_info['inCheckNum'] + $sku_num;
        if($inCheckNum > $sku_info['assignNum']){
            self::$errCode  = '003';
            self::$errMsg   = '接收数不能大于配货数！';
            return false;
        }
        
        $where          =   array('goodsAssignId'=>$goodsAssignId, 'sku'=>$sku); //生成where条件
        $update         =   array('inCheckNum'=>$inCheckNum);
        //if($inCheckNum == $sku_info['assignNum']){
            $update['inCheckTime']    =   time();
            $update['inCheckUid']     =   $_SESSION['userId'];
            $checkAssignList          =   true;
        //}
        $arr            =   array(
                            'sku'       => $sku,
                            'sku_amount'=> $sku_info['assignNum'],
                            'check_num' => $inCheckNum
                        );
        
           
        $info           =   WhGoodsAssignModel::updateAssignDetail($where, $update);
        if(!$info){
            self::$errCode = "004";
			self::$errMsg  = "更新接收明细失败!";
			return false;
        }
        
        self::$errCode  =   "200";
        self::$errMsg   =   isset($checkAssignList) ? '该料号接收复核完毕' : '该料号已复核，请录入下一料号!';
        return $arr;
    }
    
    /**
     * InCheckPdaAssignListAct::act_inCheckEnd()
     * 调拨单接收复核完成变更状态 
     * @return void
     */
    function act_inCheckEnd(){
        $assignNumber       =   trim($_POST['group_id']);
        if( ! preg_match("/AN\d{8}/", $assignNumber)){
            self::$errCode  =   '001';
            self::$errMsg   =   '请不要输入非调拨单号!';
            return FALSE;
        }
        
        $group_sql      = WhGoodsAssignModel::getOrderGroup("id, status", array('assignNumber'=>$assignNumber));
        if(empty($group_sql)){
            self::$errCode  =   '002';
            self::$errMsg   =   '没有该调拨单号!';
            return FALSE;
        }
        //print_r($group_sql);exit;
        if($group_sql[0]['status'] != 105){
            self::$errCode  =   '003';
            self::$errMsg   =   '该调拨单不在接收复核状态!';
            return FALSE;
        }
        $assignId           =   $group_sql[0]['id'];
        //$assignDetail       =   WhGoodsAssignModel::getDetail($assignId, ' and inCheckNum = 0');
//        if(empty($assignDetail)){
//            
//        }
        $where  =   array('id'=>$assignId);
        $update =   array('status'=>106, 'statusTime'=>time());
        $info   =   WhGoodsAssignModel::updateAssignListStatus($where, $update);
        if($info){
            self::$errCode  =   '200';
            self::$errMsg   =   '该调拨单复核接收完成!';
            return TRUE;
        }else{
            self::$errCode  =   '004';
            self::$errMsg   =   '该调拨单复核接收完成!';
            return FALSE;
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