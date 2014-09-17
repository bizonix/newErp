<?php
/**
 * Pda_whEndAssignAct
 * 调拨清单出库复核
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Pda_whEndAssignAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	//获取配货单信息
	function act_getGroupInfo(){
		$userId 		= $_SESSION['userId'];
		$shipOrderGroup = $_POST['order_group'];
		$group_sql      = WhGoodsAssignModel::getOrderGroup("*", array('assignNumber'=>$shipOrderGroup));
        //var_dump($group_sql);exit;
		if(empty($group_sql)){
			self::$errCode = "001";
			self::$errMsg  = "该调拨单号不存在，请重新输入!";
			return false;
		}
        if($group_sql[0]['status'] != 106){
            self::$errCode = "002";
    		self::$errMsg  = "调拨单只有在接收复核后才可完结!";
    		return false;
   	    }
        
        $orderIds          = WhGoodsAssignModel::getAssignOrderIds($group_sql[0]['id']);
        if(!$orderIds){
            self::$errCode = "003";
    		self::$errMsg  = "该调拨单下没有关联的B仓订单!";
    		return false;
        }
        $ids    =   array();
        foreach($orderIds as $id){
            $ids[]  =   $id['orderId'];
        }
        TransactionBaseModel :: begin();
        //更新调拨单状态
        $info       =   WhGoodsAssignModel::updateAssignListStatus(array('id'=>$group_sql[0]['id']), array('status'=>107));
        if(!$info){
            self::$errCode = "004";
			self::$errMsg  = "更新调拨单状态失败!";
            TransactionBaseModel :: rollback();
			return false;
        }
        
        $ids        =   implode(',', $ids);
        $info       =   CommonModel::updateOrderStatus($ids, 745);
        if($info['errCode'] != 200){
            self::$errCode = "004";
			self::$errMsg  = "同步旧ERP订单状态失败!";
            TransactionBaseModel :: rollback();
			return false;
        }
        self::$errCode = "0";
		self::$errMsg  = "调拨单完结成功!";
        TransactionBaseModel :: commit();
		return TRUE;
	}
}


?>