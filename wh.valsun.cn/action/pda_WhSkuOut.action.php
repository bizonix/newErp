<?php
/**
 * ScanPdaPickListAct
 * 调拨配货出库
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Pda_WhSkuOutAct extends Auth{
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
		  if($group_sql[0]['status'] != 101){
            self::$errCode = "004";
			self::$errMsg  = "该调拨单不在待配货状态!";
			return false;
		  }
          $scan_sql = WhGoodsAssignModel::getDetail($group_sql[0]['id'], ' and a.is_delete=0');
          //var_dump($scan_sql);exit;
          if(empty($scan_sql)){
            self::$errCode = "005";
			self::$errMsg  = "该调拨单下没有需配货料号!";
			return false;
          }else{
            $show_info['group_id']       = $group_sql[0]['id'];
			$show_info['sku']            = $scan_sql['sku'];	
			$show_info['goods_location'] = $scan_sql['pName'];
            $show_info['sku_amount']     = $scan_sql['num'];
            self::$errCode  = 0;
            self::$errMsg   = "请扫描该配货清单下的料号!";
			return $show_info;
          }
		}
	}
	
	//验证sku
	function act_checkSku(){
		$goodsAssignId  = $_POST['order_group'];
		$sku 		    = trim($_POST['sku']);
		//$now_sku 	    = trim($_POST['now_sku']);
		$sku       		= get_goodsSn($sku);
        //print_r($sku);exit;        
		//$now_sku   		= get_goodsSn($now_sku);
//		if($sku!=$now_sku){
//			self::$errCode = "003";
//			self::$errMsg  = "所扫描料号与当前料号不符，请确认!";
//			return $sku;
//		}
		$sku_info = WhGoodsAssignModel::getDetail( $goodsAssignId, " and a.checkUid=0 and a.sku='$sku'");
		if(empty($sku_info)){
			self::$errCode = "004";
			self::$errMsg  = "该调拨单无此料号!";
			return false;
		}
        
        if( $sku_info['assignNum'] >= $sku_info['num']){
			self::$errCode = "005";
			self::$errMsg  = "料号配货数量不能大于原始需求数量!";
			return false;
		}
        //暂时取消仓位判断
        /*if(empty($sku_info['pName'])){
			self::$errCode = "006";
			self::$errMsg  = "该料号无对应仓位!";
			return false;
		}*/
        self::$errMsg  = "请输入该料号实际出库配货数量!";
        $arr   =    array(
                            'sku'       =>  $sku_info['sku'],
                            'assignNum' =>  $sku_info['assignNum'],
                            'num'       =>  $sku_info['num'],
                            'pName'     =>  $sku_info['pName']
                        );
		return $arr;	
	}
	
	//验证sku数量
	function act_checkSkuNum(){
		$bool         = false;             //标志是否有摒弃订单
		$assignNUmber = $_POST['order_group'];
		$sku 		  = trim($_POST['sku']);
        $sku          = get_goodsSn($sku);
        $skuinfo      = whShelfModel::selectSku(" where sku = '{$sku}'"); //获取sku信息
        //print_r($skuinfo);exit;
        //var_dump($skuinfo);exit;
		//$sku         = getGoodsSn2($sku);
		$sku_num 	    = $_POST['sku_num'];
		$assignId 	    = $_POST['now_group_id'];
		$now_pname 	    = $_POST['now_pname'];
		$sku_info 		= WhGoodsAssignModel::getDetail( $assignId," and a.sku='$sku' and a.checkUid=0");  //调拨单明细中的sku信息
		//$sku_onhand 	= GroupDistributionModel::getSkuPositionStock("and c.sku='$sku' and b.pName='$now_pname'");		
		//$order_sku_info = $this->get_valid_order($sku_info[0]['shipOrderGroup'],$sku_info[0]['sku'],$sku_info[0]['pName']);
		
        $now_num        =   $sku_num + $sku_info['assignNum']; //已配货数加上本次配货数
		if(!is_numeric($sku_num) || $now_num < 0){
			self::$errCode = "007";
			self::$errMsg  = "出库数量必须为正整数，请确认!";
			return false;
		}
        
        if($sku_info['num'] < $now_num){
			self::$errCode = "008";
			self::$errMsg  = "配货数量不能大于原始需求数量!";
			return false;
		}
        //print_r($sku_info);exit;
        $erp_onhand     = CommonModel::getErpSkuInfo($sku);  //获取老ERP料号库存信息
        if($erp_onhand['errocode'] != 200){
            self::$errCode = "014";
			self::$errMsg  = "拉取老ERP库存信息失败，请稍后再试!";
			return false;
        }
        //根据转出仓库获取库存
		$goods_count    = $sku_info['storeId'] == 1 ? $erp_onhand['data']['goods_count'] : $erp_onhand['data']['second_count'] ;
		if($sku_num > $goods_count){
			self::$errCode = "009";
			self::$errMsg  = "配货数量不能大于旧ERP系统库存[$goods_count]，请确认!";
			return false;
		}
        
        $uid        =   $_SESSION['userId'];
        $scanTime   =   time();
        $where      =   array('goodsAssignId'=>$assignId, 'sku'=>$sku); //拼接where条件
        $update     =   array('scanUid'=>$uid, 'scanTime'=>$scanTime, 'assignNum'=>"assignNum+$sku_num");  //拼接更新字段
        TransactionBaseModel :: begin();
        $info       =   WhGoodsAssignModel::updateAssignDetail($where, $update);
        if($info){
            $where  =   array(
                            'sku'       =>  $sku,
                            'storeId'   =>  $sku_info['storeId']
                            );
            $update =   array(
                            'actualStock'=> "actualStock - $sku_num",
                            'assignStock'=> "assignStock + $sku_num"
                        );
            $info   =   WhGoodsAssignModel::updateSkuLocation($where, $update); //更新wh_sku_location的调拨库存和总库存
            if(!$info){
                TransactionBaseModel :: rollback();
                self::$errCode = "010";
    			self::$errMsg  = "更新总库存失败!";
    			return false;
            }
            $where  =   array(
                            'pId'       =>  $skuinfo['id'],
                            'positionId'=>  $sku_info['positionId'],
                            );
            $update =   array(
                            'nums'=> "nums - $sku_num"
                        );
            $info   =   WhGoodsAssignModel::updateProdcutPosition($where, $update); //更新wh_product_position_relation的仓位库存
            if(!$info){
                TransactionBaseModel :: rollback();
                self::$errCode = "011";
    			self::$errMsg  = "更新仓位库存失败!";
    			return false;
            }
            
            /**** 插入出库记录 *****/
    		$paraArr = array(
    			'sku'     	 => $sku,
    			'amount'  	 => $sku_num,
    			'positionId' => $sku_info['positionId'],
    			'purchaseId' => $skuinfo['purchaseId'],
    			'ioType'	 => 1,
    			'ioTypeId'   => 34,
    			'userId'	 => $_SESSION['userId'],
    			'reason'	 => '调拨出库'
    		);
    		$record = CommonModel::addIoRecores($paraArr);     //出库记录
            
            /** 暂时取消同步老ERP库存**/
            $info   =   CommonModel::updateIoRecord($sku, $sku_num, 2, '仓库调拨', $_SESSION['userCnName'], $sku_info['pName']);
            //var_dump($info);exit;
            if($info['errCode'] != 200){
                TransactionBaseModel :: rollback();
                self::$errCode = "012";
    			self::$errMsg  = "同步旧ERP库存失败!";
    			return false;
            }
            TransactionBaseModel :: commit();
            self::$errCode = "0";
            self::$errMsg  = "配货成功，请扫描下一个料号!";
            $arr['sku']    = $sku;
            $arr['num']    = $sku_info['num'];
            $arr['assignNum']   =   $sku_info['assignNum'] + $sku_num;
            return $arr;
        }else{
       	    self::$errCode = "013";
			self::$errMsg  = "更新料号状态失败!";
			return false;  
        }
		
	}
	
	//调拨配货完结
	function  act_endAssignList(){
	   $assignNumber      =   $_POST['group'] ? $_POST['group'] : 0;
       //$goodsAssignId      =  intval($goodsAssignId);
       if(!$assignNumber){
            self::$errCode = "014";
			self::$errMsg  = "请输入调拨单号!";
			return false;
       }
       
       $assingInfo      =   WhGoodsAssignModel::getOrderGroup('id, status', array('assignNumber'=>$assignNumber));
       if(empty($assingInfo)){
            self::$errCode = "014";
			self::$errMsg  = "该调拨单不存在!";
			return false;
       }
       
       if($assingInfo[0]['status'] != 101){
            self::$errCode = "015";
			self::$errMsg  = "该调拨单不是待配货状态!";
			return FALSE;
       }
       
       	$where      =   array('id'=>$assingInfo[0]['id']);
        $update     =   array('status'=>102, 'statusTime'=>time());
        $info       =   WhGoodsAssignModel::updateAssignListStatus($where, $update);
        if(!$info){
            self::$errCode = "016";
			self::$errMsg  = "更新调拨单状态失败!";
			return FALSE;
        }else{
            self::$errCode = "0";
			self::$errMsg  = "该调拨单已完成配货，请输入下一调拨单号!";
			return TRUE;
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