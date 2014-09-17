<?php
class channelsManageAct extends Auth{
	static $errCode = 0;
	static $errMsg = "";

	function act_addNewChannels(){
	    
		
		$channelname = isset($_POST['channelname'])?$_POST['channelname']:"";
		$transname = isset($_POST['transname'])?$_POST['transname']:"";
		$channelAlias = isset($_POST['channelAlias'])?$_POST['channelAlias']:"";
		$discount = isset($_POST['discount'])?$_POST['discount']:"";
		$enable = isset($_POST['enable'])?$_POST['enable']:"";

		$trans = channelsManageModel::transnametoid($transname);
		//print_r($trans);
		$carrierId = $trans['id'];
			
		channelsManageModel::insertchannelsall($carrierId,$channelname,$channelAlias,$discount,$enable);
			
	
		//header("Location:index.php?mod=channelsManage&act=channels&carrierId=3");
		
	}
	function act_modifyChannels(){
	    
		$channelname = isset($_POST['channelname'])?$_POST['channelname']:"";
		//$transname = isset($_POST['transname'])?$_POST['transname']:"";
		$channelAlias = isset($_POST['channelAlias'])?$_POST['channelAlias']:"";
		$discount = isset($_POST['discount'])?$_POST['discount']:"";
		$enable = isset($_POST['enable'])?$_POST['enable']:"";
		$id = isset($_POST['id'])?$_POST['id']:"";

		//$trans = channelsManageModel::transnametoid($transname);
		//$carrierId = $trans[0];
			
		$msg= channelsManageModel::modifychannel($id,$channelname,$channelAlias,$discount,$enable);
		
		
		
	}
	
	
	function act_channelsShow(){
	    
		$channels = channelsManageModel::channelsShow();
		return $channels;
	}
	function act_channelShowById($id){
		$channels = channelsManageModel::channelShowById($id);
		return $channels;
	}
    function act_transById($id){
		$trans = channelsManageModel::transById($id);
		return $trans;
	}
}
?>