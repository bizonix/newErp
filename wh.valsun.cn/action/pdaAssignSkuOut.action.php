<?php
/**
 * PdaAssignSkuOutAct
 * 调拨清单出库
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class PdaAssignSkuOutAct extends Auth{
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
            if($group_sql[0]['status'] != 104){
                self::$errCode = "003";
                self::$errMsg  = "该调拨单不在待出库状态!";
                return false;
   		    }
            if($group_sql[0]['status'] == 105){
                self::$errCode = "0";
    			self::$errMsg  = "该调拨单已完成出库扫描，请扫描其他清单!";
    			return false;
            }
            
            //$sku_info = WhGoodsAssignModel::getDetail( $group_sql[0]['id'] ," and a.checkUid = 0");
//    		if(!empty($sku_info)){
//    			self::$errCode = "004";
//    			self::$errMsg  = "该调拨单仍有料号未复核!";
//    			return FALSE;
//    		}else{
                $where      =   array('id'=>$group_sql[0]['id']);
                $update     =   array('status'=>105, 'statusTime'=>time());
                $sku_info   =   WhGoodsAssignModel::updateAssignListStatus($where, $update);
                if(!$sku_info){
                    self::$errCode = "004";
        			self::$errMsg  = "调拨单出库状态变更失败!";
        			return FALSE;
                }else{
                    self::$errCode = "0";
        			self::$errMsg  = "调拨清单出库成功!";
        			return TRUE;
                }
            //}
		}
	}
}


?>