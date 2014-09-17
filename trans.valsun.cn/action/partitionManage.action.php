<?php
class partitionManageAct extends Auth{
	static $errCode = 0;
	static $errMsg = "";
	function act_addNewPartition(){
	    
		$channelId = isset($_POST['channelId'])?$_POST['channelId']:"";
		$partitionName = isset($_POST['partitionName'])?$_POST['partitionName']:"";
		$countries = isset($_POST['countries'])?$_POST['countries']:"";
		$returnAddress = isset($_POST['returnAddress'])?$_POST['returnAddress']:"";
		$enable = isset($_POST['enable'])?$_POST['enable']:"";
		//$carrierId = addNewCarrierModel::insertCarrier($transnamec,$transnamee,$weightmin,$weightmax,$timecount);
		//addNewCarrierModel::insertChannels($carrierId,$transnamec);
		//$platformAct = new platformAct;

		//$carrierId = $trans[0];
		
		$where = "(channelId,partitionName,countries,returnAddress,enable,createdTime,is_delete) values ({$channelId},'{$partitionName}','{$countries}','{$returnAddress}',{$enable},".time().",0)";
		partitionManageModel::insertPartition($where);
			
	
		
		//addNewCarrierMOdel::insertTransName($carrierToPlatform);
		
		
	}
	function act_modifyPartition(){

		$channelId = isset($_POST['channelId'])?$_POST['channelId']:"";
		$partitionName = isset($_POST['partitionName'])?$_POST['partitionName']:"";
		$couttries = isset($_POST['couttries'])?$_POST['couttries']:"";
		$returnAddress = isset($_POST['returnAddress'])?$_POST['returnAddress']:"";
		$enable = isset($_POST['enable'])?$_POST['enable']:"";
		$id = isset($_POST['id'])?$_POST['id']:"";
		//$carrierId = addNewCarrierModel::insertCarrier($transnamec,$transnamee,$weightmin,$weightmax,$timecount);
		//addNewCarrierModel::insertChannels($carrierId,$transnamec);
		//$platformAct = new platformAct;

		//$carrierId = $trans[0];
		
		partitionManageModel::updatePartition($channelId,$partitionName,$countries,$returnAddress,$enable,$id);
			
	
		
		//addNewCarrierMOdel::insertTransName($carrierToPlatform);
		
		
	}
	function act_channelsShow(){
	    
		$channels = channelsManageModel::channelsShow();
		return $channels;
	}
	function act_channelShowById($where){
		$channels = channelsManageModel::channelShowByWhere($where);
		return $channels;
	}
    function act_transById($id){
		$trans = channelsManageModel::transById($id);
		return $trans;
	}
}
?>