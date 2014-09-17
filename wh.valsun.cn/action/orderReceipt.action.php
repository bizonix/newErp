<?php
/*
*配货收货
*@author czq
*@date 2014.07.23
*/
class orderReceiptAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	
    /**
     * 配货收货
     * @return boolean
     * @author czq
     */
	public function act_orderPicking(){
		$zone 			= isset($_POST['zone'])?strtoupper(trim($_POST['zone'])):"";
		$invoiceNumber 	= isset($_POST['invoice'])?trim($_POST['invoice']):"";
		
		if (empty($zone)){
			self::$errCode = 502;
			self::$errMsg = "区域不能为空";
			return false;
		}
		if (empty($invoiceNumber)){
			self::$errCode = 502;
			self::$errMsg = "配货单号不能为空!";
			return false;
		}	
		/*$start = time();*/
		$waveId 			= WhWaveInfoModel::number_decode($invoiceNumber);
		/*$firstF				= strpos($zone,'F');
		$floor				= substr($zone,0,$firstF);
		$area				= substr($zone,$firstF+1);*/
		/*$end = time();
		echo $end - $start; echo "<br>";
		$start = $end;*/
		$waveReceiveInfo 	= WhWaveReceiveRecordModel::find(array('waveId'=>$waveId,'area'=>$zone));
		/*$end = time();
		echo $end - $start; echo "<br>";
		$start = $end;*/
		if(!$waveReceiveInfo){
			//检查是否已经配货完成
			self::$errCode = 502;
			self::$errMsg = "此区域无需处理该配货单！";
			return false;	
		}
		if($waveReceiveInfo['scanStatus'] == 0){
			//检查是否已经配货完成
			self::$errCode = 502;
			self::$errMsg = "此区域未配货完成！";
			return false;
		}else if($waveReceiveInfo['scanStatus'] == 2){
			//检查是否已收货，防止重复收货
			self::$errCode = 502;
			self::$errMsg = "请不要重复收货！";
			return false;
		}
		
		 
		//插入记录配货表
		$receiveData = array(
			'userId'  	 	=> $_SESSION['userId'],
			'time' 			=> time(),
			'scanStatus'	=> 2,
		);
		WhWaveReceiveRecordModel::begin();
		if(!WhWaveReceiveRecordModel::update($receiveData,array('waveId'=>$waveId,'area'=>$zone,'is_delete'=>0))){
			self::$errCode = 502;
			self::$errMsg = "更新收货失败！";
			WhWaveReceiveRecordModel::rollback();
			return false;
		}
		//查找是否已经全部收货
		$waveReceiveNum = WhWaveReceiveRecordModel::count(" waveId='{$waveId}' AND scanStatus !=2 AND is_delete=0 ");
		$statusMessage 	= ''; 
		if($waveReceiveNum == 0){
			//已经完结，更新波次为完结状态
			$waveInfoData = array(
				'waveStatus' => WAVE_FINISH_GET_GOODS, 
			);
			if(!WhWaveInfoModel::update($waveInfoData,$waveId)){
				self::$errCode = 502;
				self::$errMsg = "更新配货单完结状态失败！";
				WhWaveReceiveRecordModel::rollback();
				return false;
			}
			$statusMessage = '此配货单已经完结';
			
			//如果此波次全部已收货，那么需要把发货单的状态改为待分拣
			$shippingOrders = WhWaveShippingRelationModel::getShippingOrderIdsByWaveId($waveId);
			//更新发货单状态为待分拣
			
			foreach($shippingOrders as $shipOrder){
				if(!WhShippingOrderModel::update(array('orderStatus'=>PKS_WAITING_SORTING),$shipOrder['shipOrderId'])){
					self::$errCode 	= 502;
					self::$errMsg	= '更新发货单状态失败！';
					WhWaveReceiveRecordModel::rollback();
					return false;
				}
			}
		}
		
		WhWaveReceiveRecordModel::commit();
		self::$errCode = 200;
		self::$errMsg = "收货成功！请扫描下一个配货单 ".$statusMessage;
		return true;
	}
	
	/**
	 * 获取配货单的接下来的配货区域
	 * @return string
	 * @author czq
	 */
	public static function act_orderPickRoute(){
		$invoiceNumber 	= isset($_POST['invoice'])?trim($_POST['invoice']):"";

		if (empty($invoiceNumber)){
			self::$errCode = 502;
			self::$errMsg = "配货单号不能为空!";
			return false;
		}

		$waveId 			= WhWaveInfoModel::number_decode($invoiceNumber);
		//是否已完结
		$waveInfo 			= WhWaveInfoModel::find(array('id'=>$waveId,'is_delete'=>0),'waveStatus');
		if($waveInfo['waveStatus'] == WAVE_FINISH_GET_GOODS){
			self::$errCode = 502;
			self::$errMsg = "此配货单已经完结！";
			return false;
		}
		//只获取未配货的三个路由区域
		$waveReceiveInfo 	= WhWaveReceiveRecordModel::getNextReceiveRoute($waveId);
		if(!$waveReceiveInfo){
			self::$errCode = 502;
			self::$errMsg = "未找到收货区域！";
			return false;
		}
		$str = '未收货路由（显示部分）:';
		$areas = array();
		foreach($waveReceiveInfo as $wave){
			$areas[] = $wave['area'];
		}
		$str .= implode('=>',$areas);
		self::$errCode = 200;
		self::$errMsg = "获取收货区域成功！";
		return $str;
	}
}
?>	