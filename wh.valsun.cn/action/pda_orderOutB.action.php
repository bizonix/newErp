<?php
/**
 * Pda_orderOut
 * @package 订单出库
 * @author Gary
 * @copyright 2014
 * @access public
 */
class Pda_orderOutBAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	//获取配货单信息
	function act_getOrderInfo(){
		$userId       =   $_SESSION['userId'];
        $userCnName   =   $_SESSION['userCnName'];
		$orderId      =   trim($_POST['orderId']);
        $log_file     =   'pda_orderOutB/'.date('Y-m-d').'.txt';
        $date         =   date('Y-m-d-H');
        if(!$orderId){
            self::$errCode  =   400;
            self::$errMsg   =   '请输入订单号!';
            return FALSE;
        }
        $search_field   =   preg_match("/^[A-Z]/", $userId) ? 'tracknumber' : 'orderId'; //判断是跟踪号还是订单号
        $res        =   Pda_orderOutBModel::selectOrderRecord(array($search_field=>$orderId));
        if(empty($res)){ //该订单已有记录
            $orderInfo  =   CommonModel::getErpOrderInfoB($orderId, $userCnName);
			$log_info       =   sprintf("订单号：%s, 时间：%s, 提示信息:%s, 信息记录: %s \r\n", $orderId, $date, self::$errMsg, 
                                        is_array($orderInfo) ? json_encode($orderInfo) : $orderInfo);
                write_log($log_file, $log_info);

            if( $orderInfo['errCode'] != 200 ){
                self::$errCode  =   401;
                self::$errMsg   =   isset($orderInfo['errMsg']) ? $orderInfo['errMsg'] : '拉取订单信息失败!';
                $log_info       =   sprintf("订单号：%s, 时间：%s, 提示信息:%s, 信息记录: %s \r\n", $orderId, $date, self::$errMsg, 
                                        is_array($orderInfo) ? json_encode($orderInfo) : $orderInfo);
                write_log($log_file, $log_info);
                return FALSE;
            }
			
            $orderId    =   $orderInfo['orderId'];
            TransactionBaseModel::begin();
            foreach($orderInfo['detail'] as $val){
                $insert['orderId']      =   $orderInfo['orderId'];
                $insert['tracknumber']  =   $orderInfo['tracknumber'];
                $insert['sku']          =   $val['sku'];
                $insert['amount']       =   $val['ebay_amount'];
                //$positionInfo           =   whShelfModel::selectPositionInfo('id', array('pName'=>$val['goods_location']));
                //$insert['positionId']   =   $positionInfo['id'];
                $insert['pName']        =   $val['goods_location'];
                $info   =   Pda_orderOutBModel::insertOrderRecord($insert);
                if($info === FALSE){
                    self::$errCode  =   401;
                    self::$errMsg   =   '插入订单详情失败!';
                    $log_info       =   sprintf("订单号：%s, 时间：%s, 提示信息:%s, 信息记录: %s \r\n", $orderId, $date, self::$errMsg, 
                                            is_array($insert) ? json_encode($insert) : $insert);
                    TransactionBaseModel::rollback();
                    return FALSE;
                }
                //$sku_arr[]  =   array('sku'=>$val['sku'], 'amount'=>$val['ebay_amount']);
            }
            TransactionBaseModel::commit();
        }else{
           $orderId =   $res[0]['orderId'];
        }
        self::$errCode  =   200;
        self::$errMsg   =   '请扫描该订单下料号!';
        return array('orderId'=>$orderId);
	}
    
    //验证sku
	function act_checkSku(){
		$orderId      = intval(trim($_POST['orderId']));
		$sku 		  = trim($_POST['sku']);
		$sku       	  = get_goodsSn($sku);
        if( !$orderId || !$sku){
            self::$errCode = "001";
			self::$errMsg  = "请扫描订单号或料号!!";
			return false;
        }
        
        $where    = array('orderId'=>$orderId, 'sku'=>$sku);
		$sku_info = Pda_orderOutBModel::selectOrderRecord($where);
		if(empty($sku_info)){
			self::$errCode = "002";
			self::$errMsg  = "该订单无此料号!";
			return false;
		}
        //print_r($sku_info);exit;
        if($sku_info[0]['amount'] <= $sku_info[0]['scanNum']){
            self::$errCode = "003";
			self::$errMsg  = "该料号已配货完毕!";
			return false;
        }
        $need_scan     = $sku_info[0]['amount'] - $sku_info[0]['scanNum'];
        self::$errMsg  = "该料号还需配货【{$need_scan}】!";
        $arr   =    array(
                            'sku'       =>  $sku_info[0]['sku'],
                            'assignNum' =>  $sku_info[0]['scanNum'],
                            'num'       =>  $sku_info[0]['amount'],
                            'pName'     =>  $sku_info[0]['pName']
                        );
		return $arr;	
	}
	
	//验证sku数量
	function act_checkSkuNum(){
		$orderId  =   intval(trim($_POST['orderId']));
		$sku      =   trim($_POST['sku']);
        $sku      =   get_goodsSn($sku);
        $sku_num  =   intval(trim($_POST['sku_num'])) ; 
        $log_file     =   'pda_orderOutB/'.date('Y-m-d').'.txt';
        $date         =   date('Y-m-d-H');
        
        if(!$orderId){
            self::$errCode = "001";
			self::$errMsg  = "请扫描订单id!";
			return FALSE;
        }
        
        if(!$sku){
            self::$errCode = "002";
			self::$errMsg  = "请扫描料号!";
			return FALSE;
        }
        
        if(!$sku_num){
            self::$errCode = "003";
			self::$errMsg  = "请输入料号配货数!";
			return FALSE;
        }
        
        $skuinfo  = whShelfModel::selectSku(" where sku = '{$sku}'"); //获取sku信息
		$where    = array('orderId'=>$orderId, 'sku'=>$sku);
		$sku_info = Pda_orderOutBModel::selectOrderRecord($where); //获取订单下该料号的信息
        if(empty($sku_info)){
            self::$errCode = "004";
			self::$errMsg  = "该订单没有此料号!";
			return FALSE;
        }
        $sku_info       =   $sku_info[0];
        
		$sku_onhand 	=   GroupDistributionModel::getSkuPositionStock("and c.sku='$sku' and a.storeId = 2");  //查看该料号的B仓库存		
	
        $now_num        =   $sku_num + $sku_info['scanNum']; //已配货数加上本次配货数
		if(!is_numeric($sku_num) || $now_num <= 0){
			self::$errCode = "005";
			self::$errMsg  = "配货数量必须大于0，请确认!";
			return FALSE;
		}
        
        if($sku_info['amount'] < $now_num){
			self::$errCode = "006";
			self::$errMsg  = "配货数量不能大于订单数!";
			return false;
		}
		
		if($sku_num > $sku_onhand[0]['nums']){
			self::$errCode = "007";
			self::$errMsg  = "配货数不能大于系统库存，请确认!";
			return false;
		}
        
        $uid        =   $_SESSION['userId'];
        $scanTime   =   time();
        $where      =   array('orderId'=>$orderId, 'sku'=>$sku); //拼接where条件
        $update     =   array('scanUser'=>$uid, 'scanTime'=>$scanTime, 'scanNum'=>$now_num);  //拼接更新字段
        TransactionBaseModel :: begin();
        $info       =   Pda_orderOutBModel::updateOrderRecord($where, $update);
        if($info){
            $where  =   array(
                            'sku'       =>  $sku,
                            'storeId'   =>  2
                            );
            $update =   array(
                            'actualStock'=> "actualStock - $sku_num"
                        );
            $info   =   WhGoodsAssignModel::updateSkuLocation($where, $update); //更新wh_sku_location的调拨库存和总库存
            if(!$info){
                TransactionBaseModel :: rollback();
                self::$errCode = "010";
    			self::$errMsg  = "更新总库存失败!";
    			return false;
            }
            
            $positionInfo   =   whShelfModel::selectPositionInfo('id', array('pName'=>$sku_info['pName'])); //仓位信息
            $where  =   array(
                            'pId'       =>  $skuinfo['id'],
                            'positionId'=>  $positionInfo['id'],
                            'storeId'   =>  2
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
    			'positionId' => $positionInfo['id'],
    			'purchaseId' => $skuinfo['purchaseId'],
    			'ioType'	 => 1,
    			'ioTypeId'   => 6,
    			'userId'	 => $_SESSION['userId'],
    			'reason'	 => 'B仓订单配货出库',
                'ordersn'    => $orderId
    		);
    		$record = CommonModel::addIoRecores($paraArr);     //出库记录
            
            /** 同步老ERP订单配货记录**/
            $is_scan    =   $sku_info['amount'] == $now_num ? 1 : 0;
            $info   =   CommonModel::updateOrderScanRecord($orderId, $sku, $sku_num, $sku_info['amount'], $is_scan, $_SESSION['userCnName']);
            if($info['errCode'] != 200){
                TransactionBaseModel :: rollback();
                self::$errCode = "012";
    			self::$errMsg  = "更新ERP配货记录失败!";
                $log_info       =   sprintf("订单号：%s, 时间：%s, 提示信息:%s, 信息记录: %s \r\n", $orderId, $date, self::$errMsg, 
                                        is_array($info) ? json_encode($info) : $info);
                write_log($log_file, $log_info);
    			return false;
            }
            TransactionBaseModel :: commit();
            $where  =  array('orderId'=>$orderId, 'scanUser'=>0); 
	        $info   =  Pda_orderOutBModel::selectOrderRecord($where); //查看该订单下是否还有未配货料号
            if(empty($info)){
                self::$errCode = "200";
                self::$errMsg  = "该订单已配货完成, 请扫描下一订单号!";
                return TRUE;
            }else{
                self::$errCode = "0";
                self::$errMsg  = "配货成功，请扫描下一个料号!";
                $arr['sku']    = $sku;
                $arr['num']    = $sku_info['amount'];
                $arr['assignNum']   =   $now_num;
                return $arr;
            }
            
        }else{
       	    self::$errCode = "013";
			self::$errMsg  = "更新料号状态失败!";
			return false;  
        }
		
	}   
}
?>