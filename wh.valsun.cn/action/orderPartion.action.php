<?php
/*
*分区扫描
*@author by heminghua
*/
class orderPartionAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	/*
     * 
     */
	public function act_orderPartion(){
		$orderid = isset($_POST['orderid'])?trim($_POST['orderid']):"";
		if(!is_numeric($orderid)){
			$tracknumber = $orderid;
			$info = orderWeighingModel::selectOrderId($tracknumber);
			if(!$info){
				self::$errCode = 501;
				self::$errMsg = "此跟踪号不存在！";
				return false;
			}
			$orderid = $info[0]['shipOrderId'];
			
		}
		$where = "where id={$orderid}";
		$order = orderPartionModel::selectOrder($where);
		if(!$order){
			self::$errCode = 601;
			self::$errMsg = "此发货单不存在！";
			return false;
		}
		if(!is_numeric($orderid)){
			$orderid = $order[0]['id'];
		}		
		$msg = orderPartionModel::selectPartionRecord($orderid);
		if($msg){
			self::$errCode = 603;
			self::$errMsg = "此发货单已扫描！";
			return false;
		}
		if($order[0]['orderStatus'] !=406){
			self::$errCode = 602;
			self::$errMsg = "此发货单不在待分区！";
			return false;
		}
		$shipping = CommonModel::getShipingNameById($order[0]['transportId']);
		if(!in_array($shipping,array('中国邮政平邮','中国邮政挂号','EUB','Global Mail','香港小包平邮','香港小包挂号','德国邮政'))){
			self::$errCode = 604;
			self::$errMsg = "此发货单不是小包！";
			return false;
		}
		$partion = $shipping;
		$platformName = CommonModel::getPlatformInfo($order[0]['platformId']);
		if($shipping=='Global Mail'){
			if($platformName=='亚马逊'){
				$partion = "非德国Global Mail";
			}elseif($platformName=='海外销售平台'){
				if($order[0]['countryName']=='Deutschland'){
				   $partion = "Global Mail";
				}else{
				   $partion = "非德国Global Mail";
				}
			 
			}
		}elseif($shipping=='中国邮政平邮'){
			$partion = printLabelModel::showPartionScan($orderid,$order[0]['accountId'],$shipping,$order[0]['countryName']);
		}elseif($shipping=='中国邮政挂号'){
			$partion = printLabelModel::showPartionScan($orderid,$order[0]['accountId'],$shipping,$order[0]['countryName']);
		}

		/*
		$lists = $memc_obj->get_extral('trans_system_carrierinfo');
		//print_r($lists);
		foreach($lists as $list){
			foreach($list as $value){
			    
				if($record[0]['channelId']==$value['channelId']){
					
					$countries = $value['countries'];
					$country_arr = explode("','",$countries);
					$country_arr[0] = str_replace("'","",$country_arr[0]);
					$country_arr[count($country_arr)-1] = str_replace("'","",$country_arr[count($country_arr)-1]);
					if(in_array($record[0]['countryName'],$country_arr)){
						
						$partionId = $value['id'];
					}
				}
			}
		}*/
		
		TransactionBaseModel :: begin();
		$weight = orderPartionModel::selectWeight($orderid);
		if(!$weight){
			self::$errCode = 605;
			self::$errMsg = "此发货单无重量！";
			return false;
		}
		$userId = $_SESSION['userId'];
		$result = orderPartionModel::insertRecord($orderid,$partion,$weight,$userId);
		if(!result){
			self::$errCode = 606;
			self::$errMsg = "插入分区记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		$ret = orderPartionModel::updateOrderRecords($orderid,$userId);
		if(!ret){
			self::$errCode = 607;
			self::$errMsg = "更新操作记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		$ostatus = orderPartionModel::updateOrderStatus($orderid);
		if(!ostatus){
			self::$errCode = 608;
			self::$errMsg = "更新发货单状态失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		$arr['orderid'] = $orderid;
		$arr['partion'] = urlencode($partion);
		TransactionBaseModel :: commit();
		return $arr;
	}
	/*
     * 
     */
	public function act_orderPartionPack(){
		$partion   = isset($_POST['partion'])?trim($_POST['partion']):"";
		$packageId = isset($_POST['packageid'])?trim($_POST['packageid']):"";
		$userId    = $_SESSION['userId'];
		$msg = orderPartionModel::selectPartionPack($packageId);
		if($msg[0]['partion']!=$partion){
			self::$errCode = 608;
			self::$errMsg = "选择的分区与扫描口袋的分区不匹配！";
			return false;
		}
		TransactionBaseModel :: begin();
		$where = " where partion='{$partion}' and scanUserId={$userId} and packageid is null";
		$data = orderPartionModel::selectData($where);
		if($data[0]['totalNum']==0){
			self::$errCode = 611;
			self::$errMsg = "已打包！";
			return false;
		}
		$result1 = orderPartionModel::updatePartionRecord($partion,$userId,$packageId);
		if(!$result1){
			self::$errCode = 609;
			self::$errMsg = "更新分区记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		$result2 = orderPartionModel::updatePartionPack($packageId,$data[0]['totalNum'],$data[0]['totalWeight'],$userId);
		if(!$result1){
			self::$errCode = 610;
			self::$errMsg = "更新口袋记录失败！";
			TransactionBaseModel :: rollback();
			return false;
		}
		TransactionBaseModel :: commit();
		return true;
	} 
	
	/**
	 * 分渠道操作
	 * @author czq
	 */
	public function act_checkChannel(){
		if(!$_SESSION['userId']){
			$result = array(
					'status' 	=> 0,
					'msg' 		=> '请先登录系统'
			);
			echo json_encode($result);exit;
		}
		$shipOrderId = trim($_REQUEST['shipOrderId']);
		$shipOrder = WhShippingOrderModel::find($shipOrderId);
		if(empty($shipOrder)){
			$result = array(
					'status' 	=> 'A00',
					'msg' 		=> '发货单信息不存在'
			);
			echo json_encode($result);exit;
		}
		if($shipOrder['orderStatus'] != PKS_WDISTRICT){
			$result = array(
					'status' 	=> 'A00',
					'msg' 		=> '发货单非待分区状态，不能分区'
			);
			echo json_encode($result);exit;
		}
		if(empty($shipOrder['channelId'])){
			$result = array(
					'status'	=> 'A00',
					'msg'		=> '发货单在申请运输方式中，请稍后分区',
			);
			echo json_encode($result);
			exit;
		}
		$partition = WhChannelPartitionModel::getChannelPartition($shipOrder['channelId']);
		if($partition){
			$vo = WhOrderChannelpartRecordsModel::find("shipOrderId='".$shipOrderId."'");
			$partition = $partition[0];
			$partition['code'] = 'A'.substr('0'.$partition['partition'], -2);
			if(!$vo){
				$data = array(
						'shipOrderId' 	=> $shipOrderId,
						'partitionId' 	=> $partition['id'],
						'scanUserId' 	=> $_SESSION['userId'],
						'scanTime' 		=> time(),
						'is_delete' 	=> 0,
				);
				WhOrderChannelpartRecordsModel::insert($data);
				$result = array(
						'status' 	=> $partition['code'],
						'partition'	=> $partition['partition'],
						'msg' 		=> $partition['title']
				);
			}else{
				$result = array(
						'status' 	=> $partition['code'],
						'partition'	=> $partition['partition'],
						'msg' 		=> '包裹已分拣，渠道为: '.$partition['title']
				);
			}
		}else{
			$result = array(
					'status' 	=> 'A00',
					'partition'	=> '',
					'msg' 		=> '当前运输方式未建立分渠道，请联系销售人员'
			);
		}
		echo json_encode($result);exit;
	}
	
	/**
	 * 分区操作
	 * @author czq
	 */
	public function act_checkPartion(){
		if(!$_SESSION['userId']){
			$result = array(
					'status' 	=> 0,
					'msg' 		=> '请先登录系统'
			);
			echo json_encode($result);exit;
		}
		$shipOrderId = trim($_REQUEST['shipOrderId']);
		$shipOrder = WhShippingOrderModel::find($shipOrderId);
		if(empty($shipOrder)){
			$result = array(
					'status' 	=> 'A00',
					'msg' 		=> '发货单信息不存在'
			);
			echo json_encode($result);exit;
		}
		if($shipOrder['orderStatus'] != PKS_WDISTRICT){
			$result = array(
					'status' 	=> 'A00',
					'msg' 		=> '发货单非待分区状态，不能分区'
			);
			echo json_encode($result);exit;
		}
		if(empty($shipOrder['channelId'])){
			$result = array(
					'status'	=> 'A00',
					'msg'		=> '发货单在申请运输方式中，请稍后分区',
			);
			echo json_encode($result);
			exit;
		}
		$partition = WhTransportPartitionModel::getPartition($shipOrder['channelId'], $shipOrder['countryName']);
		if($partition){
			$vo = WhOrderPartionRecordsModel::find("shipOrderId='".$shipOrderId."'");
			if(!$vo){
				$data = array(
						'shipOrderId' 	=> $shipOrderId,
						'packageId' 	=> 0,
						'partitionId' 	=> $partition['id'],
						'weight' 		=> $shipOrder['orderWeight'],
						'partion' 		=> $partition['title'],
						'scanUserId' 	=> $_SESSION['userId'],
						'scanTime' 		=> time(),
						'modifyTime' 	=> 0,
						'note' 			=> '',
						'is_delete' 	=> 0,
						'storeId' 		=> 0,
				);
				WhOrderPartionRecordsModel::insert($data);
				$result = array(
						'status' 	=> $partition['code'],
						'partition'	=> $partition['partition'],
						'msg' 		=> $partition['title']
				);
			}else if($vo['packageId']){
				$result = array(
						'status' 	=> 'A00',
						'partition'	=> $partition['partition'],
						'msg' 		=> '已打包，不能再分区'
				);
			}else{
				$result = array(
						'status' 	=> $partition['code'],
						'partition'	=> $partition['partition'],
						'msg' 		=> '包裹已分拣，分区为: '.$partition['title']
				);
			}
		}else{
			$result = array(
					'status' 	=> 'A00',
					'partition'	=> '',
					'msg' 		=> '当前国家未分区，请联系销售人员'
			);
		}
		echo json_encode($result);exit;
	}
}
?>