<?php
/*
* 取消合并包裹功能
* @author by heminghua 
*/
class cancelCombineAct extends Auth{
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	public function act_findCombineOrder(){
		$str = isset($_POST['str'])?$_POST['str']:"";
		$id_arr = explode(",",$str);
		$id_arr = array_filter($id_arr);
		$orders = cancelCombineModel::selectCombineOrder($str);
		if(!$orders){
			self::$errCode = 401;
			self::$errMsg  = "无合并包裹关系的订单！";
			//BaseModel::rollback();
			return false;
		}
		//BaseModel::begin();
		$data = "";
		foreach($orders as $key=>$value){
			if($value['combinePackage']==1){
				$data .= "#".$value['id']."*";
				
				$sonOrders = cancelCombineModel::selectSonOrder($value['id']);
				
				foreach($sonOrders as $sonorder){
					if($key==0){
						$data .= $sonorder['split_order_id']; 
					}else{
						$data .= ",".$sonorder['split_order_id']; 
					}
				}
			}
			if($value['combinePackage']==2){
				$mainOrder = cancelCombineModel::selectMainOrder($value['id']);
				$sonOrders = cancelCombineModel::selectSonOrder($mainOrder);
				$data .= "#".$mainOrder."*";
				foreach($sonOrders as $key=>$sonorder){
					if($key==0){
						$data .= $sonorder['split_order_id']; 
					}else{
						$data .= ",".$sonorder['split_order_id']; 
					}
				}
			}
		}
		return $data;
	}
	public function act_cancelCombine(){
		//更新主从订单状态及记录
		$str = isset($_POST['str'])?$_POST['str']:"";
		$orderids = explode(",",$str);
		$userId = $_SESSION['sysUserId'];
		BaseModel::begin();
		foreach($orderids as $orderid){
			$order = cancelCombineModel::selectRecord($orderid);
			if($order[0]['combinePackage']==1){
				$msg = cancelCombineModel::updateOrder($orderid);
				if(!$msg){
					self::$errCode = 402;
					self::$errMsg  = "更新主订单失败！";
					BaseModel::rollback();
					return false;
				}
				$sonOrders = cancelCombineModel::selectSonOrder($orderid);
				
				foreach($sonOrders as $sonorder){
					$msg1 = cancelCombineModel::updateOrder($sonorder['split_order_id']);
					if(!$msg1){
						self::$errCode = 403;
						self::$errMsg  = "更新子订单失败！";
						BaseModel::rollback();
						return false;
					}
					if(in_array($sonorder['split_order_id'],$orderids)){
						unset($orderids[$sonorder['split_order_id']]);
					}
				}
				$msg2 = cancelCombineModel::updateRecords($orderid,$userId);
				if(!$msg2){
					self::$errCode = 404;
					self::$errMsg  = "更新合并包裹记录失败！";
					BaseModel::rollback();
					return false;
				}
			}
			if($order[0]['combinePackage']==2){
				$mainOrder = cancelCombineModel::selectMainOrder($order[0]['id']);
				$sonOrders = cancelCombineModel::selectSonOrder($mainOrder);
				if(count($sonOrders)==1){
					$msg3 = cancelCombineModel::updateOrder($mainorder);
					if(!$msg3){
						self::$errCode = 405;
						self::$errMsg  = "更新主订单失败！";
						BaseModel::rollback();
						return false;
					}
					if(in_array($mainOrder,$orderids)){
						unset($orderids[$mainOrder]);
					}
					$msg4 = cancelCombineModel::updateOrder($sonOrders[0]['split_order_id']);
					if(!$msg4){
						self::$errCode = 406;
						self::$errMsg  = "更新子订单失败！";
						BaseModel::rollback();
						return false;
					}

				}else{
					$msg5 = cancelCombineModel::updateOrder($orderid);
					if(!$msg5){
						self::$errCode = 407;
						self::$errMsg  = "更新子订单失败！";
						BaseModel::rollback();
						return false;
					}
				}
				$msg6 = cancelCombineModel::updateRecords($orderid,$userId,"son");
				if(!$msg6){
					self::$errCode = 408;
					self::$errMsg  = "更新合并包裹记录失败！";
					BaseModel::rollback();
					return false;
				}
			}
		}
		BaseModel::commit();
		return true;


	}
}
?>