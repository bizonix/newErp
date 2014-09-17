<?php
/**
 * 分区管理
 * @author czq
 */
class WhTransportPartitionAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	/**
	 * 通过运输方式id获取对应的渠道
	 * @return array $channellist
	 * @author czq
	 */
    public function act_getChannel(){
    	$transportId = isset($_POST['transportId']) ? intval($_POST['transportId']) : '';
    	if(empty($transportId)){
    		$errCode = 101;
    		$errMsg	 = '未获取运输方式id';
    		return false;		
    	}
		
    	$channellist = CommonModel::getCarrierChannelByIds($transportId);  //获取国家渠道信息
    	self::$errCode = 200;
    	self::$errMsg  = '获取运输渠道成功！' ;
    	return $channellist;
	}
}

?>