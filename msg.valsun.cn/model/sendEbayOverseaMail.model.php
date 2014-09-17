<?php
/*
 * 获取试图开case的买家信息并推送邮件
 */
class sendEbayOverseaMailModel {
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
	public function insertEbayOrder($tracknumber, $item_id, $buyer_id, $sendstatus, $failure_reason){
		$time           = time();
		$select			= "SELECT `id`, `item_id`
						   FROM `msg_sendebayOverseamail`
						   WHERE `item_id`='$item_id' AND `seller_id`='$tracknumber' AND `buyer_id`='$buyer_id' '";
		
		$selRes			= $this->dbconn->fetch_first($select);
		if(empty($selRes)) {
			$sql			= "INSERT INTO `msg_sendebayOverseamail` 
						   VALUES (NULL, '$tracknumber', '$item_id', '$buyer_id', $sendstatus, '$failure_reason', '$time')";
			try{
				$result		= $this->dbconn->query($sql);
			} catch (Exception $e){
				echo $this->dbconn->error();
			}
		}
		
		if($result) {
			return 'success';
		}else{
			return 'failure';
		}
	}
	
}