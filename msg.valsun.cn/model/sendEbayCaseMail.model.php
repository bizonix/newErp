<?php
/*
 * 获取试图开case的买家信息并推送邮件
 */
class sendEbayCaseMailModel {
	public static $errMsg   = '';
	public static $errCode  = 0;
	private $dbconn = NULL;
	/*
	 * 构造函数
	*/
	public function __construct(){
		global $dbConn;
		$this->dbconn   = $dbConn;
	}

	//写入数据
	public function insertBuyerMsgIntoDatabase($seller_id, $item_id, $transaction_item, $transaction_date, $delay_arrive_time, $buyer_id, $buyer_try_open_time, $create_time){
		$select			= "SELECT `id`, `item_id`
						   FROM `msg_sendebaycasemail`
						   WHERE `item_id`='$item_id' AND `seller_id`='$seller_id' AND `buyer_id`='$buyer_id' AND `transaction_date`='$transaction_date' AND `delay_arrive_time`='$delay_arrive_time' AND `buyer_try_open_time`='$buyer_try_open_time'";
		$selRes			= $this->dbconn->fetch_first($select);
		if(empty($selRes)) {
			$sql			= "INSERT INTO `msg_sendebaycasemail` 
							   VALUES (NULL, '$seller_id', '$item_id', '$transaction_item', '$transaction_date', '$delay_arrive_time', '$buyer_id', '$buyer_try_open_time', NULL, 0, NULL, '$create_time')";
			$result			= $this->dbconn->query($sql);
		}
		if($result) {
			return 'success';
		}else{
			return 'failure';
		}
	}
	
	//获取买家信息数据
	public function getBuyerMsg($where) {
		$BuyerData		= array();
		$select			= "SELECT `id`, `seller_id`, `item_id`, `transaction_item`, `transaction_date`, `delay_arrive_time`, `buyer_id`, `buyer_try_open_time`, `create_time`
						   FROM `msg_sendebaycasemail` WHERE `is_send_mail`=0 limit 0,50".$where;
		$result			= $this->dbconn->query($select);
		$showResult		= $this->dbconn->fetch_array_all($result);
		if (!empty($showResult)) {
			$BuyerData = $showResult;
		}
		return $BuyerData;
	}
	
	//发送成功后更新是否已推送状态
	public function updateIsSend($status, $id) {
		if($status == 'success') {
			$stu = 1;	
		}else{
			$stu = 2;
		}
		$update			= "UPDATE `msg_sendebaycasemail` SET `is_send_mail`= '{$stu}' WHERE `id`='$id'";
		$result			= $this->dbconn->query($update);
		return $result;
	}
	
	//调用erp接口获取跟踪号信息
	/**
	 * sendEbayCaseMailModel::getTranNumFromErp()
	 * 从erp接口获取跟踪号
	 * @param string $ebay_account ebay账号
	 * @param string $ebay_userid 买家账号
	 * @return  json string
	 */
	public static function getTranNumFromErp($ebay_account, $ebay_userid) {
		include_once WEB_PATH.'lib/opensys_functions.php';
		$paramList = array(
			/* API系统级输入参数 Start */
			'method'	=> 'order.getOrderInfoByUserId',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'Message',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'type'			=> 'orderinfo',  //类型
			'buyeraccount'	=> $ebay_account,  //买家帐号
			'selleraccount'	=> $ebay_userid,  //卖家帐号
			/* API应用级输入参数 End*/
		);
		//生成签名
		$sign = createSign($paramList);
		//组织参数
		$strParam = createStrParam($paramList);
		$strParam .= 'sign='.$sign;
		//构造Url
		$urls = $url.$strParam;
		$cnt=0;	
		while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
		$data = json_decode($result,true);
		return $data;
	}
}