<?php
/**
 * Pda_orderInCheckB
 * @package 订单出库
 * @author Gary
 * @copyright 2014
 * @access public
 */
class Pda_orderInCheckBAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	//获取配货单信息
	function act_getOrderInfo(){
		$userId       =   $_SESSION['userId'];
		$orderId      =   trim($_POST['orderId']);
        $log_file     =   'pda_orderOutB/'.date('Y-m-d').'.txt';
        $date         =   date('Y-m-d-H');
        if(!$orderId){
            self::$errCode  =   400;
            self::$errMsg   =   '请输入订单号!';
            return FALSE;
        }
        $search_field   =   preg_match("/^[A-Z]/", $userId) ? 'tracknumber' : 'orderId'; //判断是跟踪号还是订单号
        $res            =   Pda_orderOutBModel::selectOrderRecord(array($search_field=>$orderId));
        
        if(empty($res)){ //没有该订单记录
            self::$errCode  =   401;
            self::$errMsg   =   '没有该订单信息!';
            return FALSE;
        }
        self::$errCode  =   200;
        self::$errMsg   =   '请扫描该订单下料号!';
        return array('orderId'=>$res[0]['orderId']);
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
        if($sku_info[0]['scanNum'] <= $sku_info[0]['checkNum']){
            self::$errCode = "003";
			self::$errMsg  = "该料号已接收完毕!";
			return false;
        }
        $need_scan     = $sku_info[0]['scanNum'] - $sku_info[0]['checkNum'];
        self::$errMsg  = "该料号还需接收【{$need_scan}】!";
        $arr   =    array(
                            'sku'       =>  $sku_info[0]['sku'],
                            'assignNum' =>  $sku_info[0]['checkNum'],
                            'num'       =>  $sku_info[0]['scanNum']
                        );
		return $arr;	
	}
	
	//验证sku数量
	function act_checkSkuNum(){
		$orderId  =   intval(trim($_POST['orderId']));
		$sku      =   trim($_POST['sku']);
        $sku      =   get_goodsSn($sku);  //转义sku
        $sku_num  =   intval(trim($_POST['sku_num'])) ; 
        
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
			self::$errMsg  = "请输入料号接收数!";
			return FALSE;
        }
		$where    = array('orderId'=>$orderId, 'sku'=>$sku);
		$sku_info = Pda_orderOutBModel::selectOrderRecord($where); //获取订单下该料号的信息
        if(empty($sku_info)){
            self::$errCode = "004";
			self::$errMsg  = "该订单没有此料号!";
			return FALSE;
        }
        $sku_info       =   $sku_info[0];
        
        $now_num        =   $sku_num + $sku_info['checkNum']; //已接收数加上本次接收数
		if(!is_numeric($sku_num) || $now_num <= 0){
			self::$errCode = "005";
			self::$errMsg  = "接收总数必须大于0，请确认!";
			return FALSE;
		}
        
        if($sku_info['scanNum'] < $now_num){
			self::$errCode = "006";
			self::$errMsg  = "接收数不能大于订单配货数!";
			return false;
		}
        
        $uid        =   $_SESSION['userId'];
        $scanTime   =   time();
        $where      =   array('orderId'=>$orderId, 'sku'=>$sku); //拼接where条件
        $update     =   array('checkUser'=>$uid, 'checkTime'=>$scanTime, 'checkNum'=>$now_num);  //拼接更新字段
        $info       =   Pda_orderOutBModel::updateOrderRecord($where, $update);
        if($info == FALSE){
            self::$errCode = "007";
    		self::$errMsg  = "更新料号状态失败!";
    		return FALSE;  
        }
        
        $where  =  array('orderId'=>$orderId, 'checkUser'=>0); 
        $info   =  Pda_orderOutBModel::selectOrderRecord($where); //查看该订单下是否还有未接受料号
        if(empty($info)){
            self::$errCode = "200";
    		self::$errMsg  = "该订单接收完成,请扫描下一订单号!";
    		return TRUE;
        }
        self::$errCode = "0";
        self::$errMsg  = "接收成功，请扫描下一个料号!";
        $arr['sku']    = $sku;
        $arr['num']    = $sku_info['scanNum'];
        $arr['assignNum']   =   $now_num;
        return $arr;	
	}   
}
?>