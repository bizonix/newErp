<?php
/**
*类名:FeedbackAPIAct
*说明:对接feedback系统业务逻辑类
*author:王民伟
*date:2014-02-28
**/
class FeedbackAPIAct{
	static $errCode = 0;
	static $errMsg  = "";
	public function act_updOrderErpFeedBack(){
		$comUserId = $_GET['userid'];//买家ID
		$itemId    = $_GET['itemid'];//Ebay产品编码
		$tranId    = $_GET['tranid'];//交易号
		$comType   = $_GET['comtype'];//平价类型
		$rtnData   = FeedbackAPIModel::updFeedBack($comUserId, $itemId, $tranId, $comType);
		return $rtnData;
    }
}
?>	