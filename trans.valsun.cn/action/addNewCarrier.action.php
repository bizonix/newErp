<?php
class addNewCarrierAct extends Auth{
	static $errCode = 0;
	static $errMsg = "";
	function act_addNewCarrier(){
	
		$carrierToPlatform = isset($_POST['carrierToPlatform'])?$_POST['carrierToPlatform']:"";
		$transnamec = isset($_POST['transnamec'])?$_POST['transnamec']:"";
		$transnamee = isset($_POST['transnamee'])?$_POST['transnamee']:"";
		$weightmin = isset($_POST['weightmin'])?$_POST['weightmin']:"";
		$weightmax = isset($_POST['weightmax'])?$_POST['weightmax']:"";
		$timecount = isset($_POST['timecount'])?$_POST['timecount']:"";
		$carrierId = addNewCarrierModel::insertCarrier($transnamec,$transnamee,$weightmin,$weightmax,$timecount);
		addNewCarrierModel::insertChannels($carrierId,$transnamec);
		$platformAct = new platformAct;
		
		foreach($carrierToPlatform as $value){
			$platform_info = explode("*",$value);
			$platform = $platform_info[1];
			$carrierName = $platform_info[0];
			$where  = "where platformNameCn='{$platform}'";
			$platform_arr = $platformAct::act_platformShow($where);
			$platformId=$platform_arr[0]['id'];
			
			addNewCarrierModel::insertTransName($carrierId,$carrierName,$platformId);
			
		}
		
		//addNewCarrierMOdel::insertTransName($carrierToPlatform);
		
		
	}
	function act_platformShow(){
	    $where = "";
		$paltform = platformModel::platformShow($where);
		return $platform;
	}
}
?>