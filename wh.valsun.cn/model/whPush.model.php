<?php
/**
 * 类名：whPushModel
 * 功能：仓库系统推送model层
 * 版本：1.0
 * 日期：2013/11/13
 * 作者：hws
 */
class WhPushModel {
	public static $dbConn;
	//public static $prefix;
	public static $errCode	= 0;
	public static $errMsg	= "";
	
	//初始化
	public static function	initDB(){
		global $dbConn;
		
		self::$dbConn = $dbConn;
	}
		
	/**
	 * 推送信息给订单系统
	 * @return  array
	 */
    public static function listPushMessage(){
		self::initDB();
		$storeId = 1;
		$time = strtotime('-2 days');
		$rmq_config	=	C("RMQ_CONFIG");
		$rabbitMQClass= new RabbitMQClass($rmq_config['sendOrder'][1],$rmq_config['sendOrder'][2],$rmq_config['sendOrder'][4],$rmq_config['sendOrder'][0]);//队列对象
		
		$orderDataInfo = array();
		//基础信息
		$sql = "select a.orderStatus,b.originOrderId from `wh_shipping_order` as a join `wh_shipping_order_relation` as b 
				on a.id=b.shipOrderId where a.createdTime>'{$time}' and a.orderTypeId=1 and a.orderStatus!=400 and a.orderStatus!=900 and a.is_delete=0 and a.storeId={$storeId} ";
		$query	       = self::$dbConn->query($sql);
		$orderDataInfo = self::$dbConn->fetch_array_all($query);
		$exchange='send_shippingorder_exchange';
		if($rabbitMQClass->queue_publish($exchange,$orderDataInfo)){
			self::$errMsg = "推送成功！";
			return true;
		}else{
			self::$errCode = "401";
			self::$errMsg = "推送失败！";
			return false;
		}
		
		
	}	
	
	/**
	 * 仓库订单状态实时推送给订单系统
	 */
    public static function pushOrderStatus($shipOrderId,$status,$userId,$operateTime,$storeId = 1){
		self::initDB();
		$rmq_config	=	C("RMQ_CONFIG");
		$rabbitMQClass= new RabbitMQClass($rmq_config['sendOrder'][1],$rmq_config['sendOrder'][2],$rmq_config['sendOrder'][4],$rmq_config['sendOrder'][0]);//队列对象
		
		$orderDataInfo = array();
		//基础信息
	//	$sql = "select originOrderId from `wh_shipping_order_relation` where shipOrderId={$shipOrderId}";
		//$query	   = self::$dbConn->query($sql);
	//	$orderInfo = self::$dbConn->fetch_first($sql);
        $result = WhShippingOrderRelationModel::get_orderId($shipOrderId);
      //  if(is_array($result)){
            
      //  }
		$orderDataInfo = array(
			'originOrderId' => $result,
			'orderStatus'   => $status,
			'operateUserId' => $userId,
			'operateTime'   => $operateTime,
            'storeId'       => $storeId
		//	'actualWeight'  => $actualWeight,
			//'tracknumber'   => $tracknumber,
		);

		$exchange='WH_STATUS_EXCHANGE';
		if($rabbitMQClass->queue_publish($exchange,json_encode($orderDataInfo),"direct")){
			self::$errMsg = "推送成功！";
			return true;
		}else{
			self::$errCode = "401";
			self::$errMsg  = "推送失败！";
			return false;
		}
	}	
	
	/**
	 * 订单系统通过接口更改仓库系统订单废弃
	 */
    public static function orderDiscard($oidStr,$storeId){
		self::initDB();
		if(empty($oidStr) || !is_numeric($storeId)){
			self::$errCode = "401";
			self::$errMsg  = "参数有误";
			return false;
		}
		$orders   	  = trim($oidStr,',');

		$sql 		  = "select shipOrderId from wh_shipping_order_relation where originOrderId in ($orders) and storeId={$storeId} and is_delete = 0";
		$query	      = self::$dbConn->query($sql);
		$shipOrderIds = self::$dbConn->fetch_array_all($query);
		$shipOrderArr = array();
		if($shipOrderIds){
			foreach($shipOrderIds as $shipOrderId){
				$shipOrderArr[] =  $shipOrderId['shipOrderId'];
			}
			$shipOrders = implode(',',$shipOrderArr);
            $orderStatus =PKS_DONE;
            $sql ="select orderStatus from wh_shipping_order where id in ($shipOrders) and orderStatus = $orderStatus";
           	$result	    = self::$dbConn->query($sql);
            $result     = self::$dbConn->fetch_array_all($result);
            if($result){
              	self::$errCode = "404";
			    self::$errMsg  = "仓库系统对应的发货单已经发货！";
                return false;  
            }            
            WhBaseModel::begin();
			$sql        = "update wh_shipping_order set orderStatus=900 where id in($shipOrders)";
			$query	    = self::$dbConn->query($sql);
			if($query){
			    $sql1        = "update wh_shipping_order_relation set is_delete= 1 where shipOrderId in($shipOrders)";
      	        $query1	     = self::$dbConn->query($sql1);
                if(!$query1){
                    WhBaseModel :: rollback();
                   	self::$errCode = "404";
			     	self::$errMsg  = "仓库系统订单废弃失败！";
			    	return false; 
                }
                WhBaseModel::commit();
				return true;
			}else{			   
				self::$errCode = "403";
				self::$errMsg  = "仓库系统订单废弃失败！";
				return false;    
			}
		}else{
			self::$errCode = "402";
			self::$errMsg  = "找不到仓库系统对应的发货单！";
			return false;
		}
	}
	
	/**
	 * 获取异常订单
	 */
    public static function getAbOrderList(){
		self::initDB();
		$sql   = "select b.originOrderId,a.id from wh_shipping_order as a left join wh_shipping_order_relation as b on a.id=b.shipOrderId where a.orderStatus=901";
		$query = self::$dbConn->query($sql);
		if($query){
			$info  = self::$dbConn->fetch_array_all($query);
			return $info;
		}else{
			self::$errCode = "401";
			self::$errMsg  = "error";
			return false;
		}
		
	}
	
	/**
	 * 获取异常订单配货信息
	 */
    public static function getAbOrderInfo($orderId){
		self::initDB();
		//$sql   		   = "select shipOrderId from wh_shipping_order_relation where originOrderId='{$orderId}'";
		//$shipOrderInfo = self::$dbConn->fetch_first($sql);

		if(is_numeric($orderId)){
			$detail_info = self::getorderUnusualInfo($orderId);
			return $detail_info['picking'];
		}else{
			self::$errCode = "401";
			self::$errMsg  = "发货单号有误";
			return false;
		}
		
	}
	
	/**
	 * 订单系统通过接口更改仓库系统异常订单
	 */
    public static function orderUnusual($orderId,$calcWeight){
		self::initDB();
		//$sql   		   = "select shipOrderId from wh_shipping_order_relation where originOrderId='{$orderId}'";
		//$shipOrderInfo = self::$dbConn->fetch_first($sql);
		
		if(is_numeric($orderId)){
			OmAvailableModel::begin();
			$del_arr = array();
			$id_str  = '';
			$detail_info = self::getorderUnusualInfo($orderId);
			foreach($detail_info['detail'] as $detail){
				if(!empty($detail['combineSku'])){
					if($detail_info['picking'][$detail['combineSku']]==0){
						$del_arr[] = $detail['id'];
					}
				}else{
					if($detail_info['picking'][$detail['sku']]==0){
						$del_arr[] = $detail['id'];
					}
				}
			}
			if(empty($del_arr)){
				self::$errCode = "405";
				self::$errMsg  = "该订单料号库存足够,不需拆分";
				OmAvailableModel::rollback();
				return false;
			}
			$id_str = implode(',',$del_arr);
			$time = time();
			$update_record_sql = "update wh_order_picking_records set is_delete=1,cancelTime={$time} where shipOrderdetailId in($id_str)";
			$udate_query	   = self::$dbConn->query($update_record_sql);
			if($udate_query){
				$del_detail_sql = "delete from wh_shipping_orderdetail where id in($id_str) ";
				$del_query	    = self::$dbConn->query($del_detail_sql);
				if($del_query){
					$update_status_sql = "update wh_shipping_order set orderStatus=403,calcWeight='{$calcWeight}' where id={$orderId}";
					$status_query	   = self::$dbConn->query($update_status_sql);
					if($status_query){
						OmAvailableModel::commit();
						return true;
					}else{
						self::$errCode = "404";
						self::$errMsg  = "仓库系统拆分失败";
						OmAvailableModel::rollback();
						return false;
					}
				}else{
					self::$errCode = "403";
					self::$errMsg  = "仓库系统拆分失败";
					OmAvailableModel::rollback();
					return false;
				}
			}else{
				self::$errCode = "402";
				self::$errMsg  = "仓库系统拆分失败";
				return false;
			}		
		}else{
			self::$errCode = "401";
			self::$errMsg  = "发货单号有误";
			return false;
		}
	}
	
	/**
	 * 仓库点货、打标信息推送给qc系统
     * $tallyListId 点货Id集合
	 */
    public static function pushTallyingList($tallyListId){
		self::initDB();
		$rmq_config	=	C("RMQ_CONFIG");
		$rabbitMQClass= new RabbitMQClass($rmq_config['sendOrder'][1],$rmq_config['sendOrder'][2],$rmq_config['sendOrder'][4],$rmq_config['sendOrder'][0]);//队列对象
		
		$orderDataInfo = array();
		//基础信息
		$sql 		  = "select * from `wh_tallying_list` where id in({$tallyListId})";
		$query	      = self::$dbConn->query($sql);
		$tallyingInfo = self::$dbConn->fetch_array_all($query);
		foreach($tallyingInfo as &$info){
			$info['googsCode'] = get_skuGoodsCode($info['sku']);
			$info['googsName'] = getSKUName($info['sku']);
		}
		$exchange='send_tallying_list';
		if($rabbitMQClass->queue_publish($exchange,$tallyingInfo)){
			self::$errMsg = "推送成功！";
			return true;
		}else{
			self::$errCode = "401";
			self::$errMsg  = "推送失败！";
			return false;
		}
	}
	
	
	/**
	 * 订单系统通过接口更改仓库系统异常订单
	 */
    public static function getorderUnusualInfo($shipOrderId){
		self::initDB();
		$detail_info = array();
		$return_data = array();
		$d_sql       = "select * from wh_shipping_orderdetail where shipOrderId={$shipOrderId}";
		$d_query     = self::$dbConn->query($d_sql);
		$d_infos 	 = self::$dbConn->fetch_array_all($d_query);
		
		foreach($d_infos as $d_i){
			if(!empty($d_i['combineSku'])){
				$detail_info[$d_i['combineSku']][] = array(
					'id' => $d_i['id'],
				);
			}else{
				$detail_info[$d_i['sku']][] = array(
					'id' => $d_i['id'],
				);
			}
		}
		foreach($detail_info as $key=>$detail){
			foreach($detail as $d){
				$r_sql  = "select * from wh_order_picking_records where shipOrderdetailId={$d['id']} and is_delete=0 and isScan=1";
				$r_info = self::$dbConn->fetch_first($r_sql);
				if(!empty($r_info)){
					if($r_info['totalNums']!=$r_info['amount']){
						$detail_info[$key] = 0;
						break;
					}else{
						$detail_info[$key] = 1;
					}
				}else{
						$detail_info[$key] = 0;
						break;
				}
				
			}
		}
		
		$return_data['detail']  =  $d_infos;
		$return_data['picking'] =  $detail_info;
		return $return_data;
	}
	
	/**
	 * 获取订单配货信息(参数：订单系统订单号)
	 */
    public static function getOrderPickingInfo($orderId){
		self::initDB();
		$re_info = array();
		if(is_numeric($orderId)){
			$shipOrder_sql   = "select shipOrderId from wh_shipping_order_relation where originOrderId='{$orderId}'";
			$shipOrder_query = self::$dbConn->query($shipOrder_sql);
			$shipOrderInfos  = self::$dbConn->fetch_array_all($shipOrder_query);
			
			if(empty($shipOrderInfos)){
				self::$errCode = "402";
				self::$errMsg  = "仓库系统未找到对应发货单";
				return false;
			}
			
			foreach($shipOrderInfos as $orderInfo){
				$detail_info = self::getorderUnusualInfo($orderInfo['shipOrderId']);
				$re_info[$orderInfo['shipOrderId']] = $detail_info['picking'];
			}
			return $re_info;
		}else{
			self::$errCode = "401";
			self::$errMsg  = "订单号有误";
			return false;
		}
		
	}
	
	/**
	 * 推送重量，运输方式，跟踪号相关信息给订单系统
	 * @param array $orderDataInfo
	 * @return boolean
	 * @author czq
	 */
	public static function pushTransportInfo($orderDataInfo){
		$rmq_config	=	C("RMQ_CONFIG");
		$rabbitMQClass= new RabbitMQClass($rmq_config['sendOrder'][1],$rmq_config['sendOrder'][2],$rmq_config['sendOrder'][4],$rmq_config['sendOrder'][0]);//队列对象
	
		$exchange='WH_PUSH_ORDER_TRACK';
		if($rabbitMQClass->queue_publish($exchange,json_encode($orderDataInfo),'direct')){
			self::$errMsg = "推送成功！";
			return true;
		}else{
			self::$errCode = "401";
			self::$errMsg  = "推送失败！";
			return false;
		}
	}
}
?>