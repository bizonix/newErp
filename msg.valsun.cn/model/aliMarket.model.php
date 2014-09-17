<?php
class AliMarketModel {
	private $dbconn = NULL;
	public function __construct(){
		global $dbConn;
		$this->dbconn   = $dbConn;
	}
	public function insertEDMData($data){
		extract($data);
		$sql		= "SELECT `id` FROM `msg_aliMarket` WHERE `seller_id`='$seller_id' AND 
						`customer_s` = '$customer_s' AND `gmail`='$gmail' AND `shopnum`='$shopnum' ";
		$selRes			= $this->dbconn->fetch_first($sql);
		if(!empty($selRes)){
			return 'failure';
		}
		$sql = "INSERT INTO `msg_aliMarket` 
				VALUES (NULL, '$seller_id', '$customer_s', '$gmail', '$shopnum', '$pushtime', NULL, 0, NULL, 0)";
		try{
			$result		= $this->dbconn->query($sql);
		} catch (Exception $e){
			echo $this->dbconn->error();
		}
		if($result) {
			return 'success';
		}else{
			return 'failure';
		}
	}
	public function getOrderinfo($seller_id){
		$conn  =  new mysqli("192.168.200.158","cerp","123456","cerp");
		if(mysqli_connect_error()){
			die("连接失败".mysqli_connect_error());
		}
		$twoMonthTime    = time() - 60 * 24 * 60 * 60;
		$sql            = "SELECT  ebay_userid, ebay_usermail, COUNT( ebay_userid ) AS num
		FROM ebay_order where ebay_account='$seller_id' and ebay_addtime > '$twoMonthTime' and ebay_usermail <> '' and ebay_status='2'
		GROUP BY ebay_userid ORDER BY num DESC";
		//echo $sql;exit;
		if($res = $conn->query($sql)){
		while($row	=	$res->fetch_assoc()){
			$arr[]	=	$row;
		}
    		return $arr;
    	} else {
    		$conn->close();
    		die("查询失败！");
    	}
	}
	public  function getAllEDMData(){
		$sql 	= 'select * from msg_aliMarket ';
		$result = $this->dbconn->fetch_array_assoc($sql);
		return $result;
	}
	public  function getSentMail($buyer,$seller,$buyermail,$sellermail){
		$sql 	= "select id from msg_sendOK_EDM where buyer = '$buyer' and seller ='$seller' 
					and buyermail = '$buyermail' and sellermail = '$sellermail'";
		
		try{
			$result = $this->dbconn->fetch_first($sql);
			
			if(!empty($result)){   //如果已经发送过邮件
				echo "已经存储过了\n";
				return FALSE;
			} else {
				echo "未存储\n";
				return TRUE;
			}
		} catch (Exception $e){
			echo "发送失败3\n".$this->dbconn->error();
			$counter = 10;
			while(!($conn = new mysqli("localhost","root","123456","valsun_msg"))){
				if(!$counter--){
					echo "放弃重连\n";
					return FALSE;
				}
				echo("连接失败\n");
			}
			if($res=$conn->query($sql)){
				echo "查询成功！"."\n";
				if($res->num_rows){
					return FALSE;
				} else {
					return TRUE;
				}
			} else {
				return FALSE;
			}
		}
		
		
	}
	public  function insertOKMail($seller,$buyer,$sellermail,$buyermail,$pushtime,$text){
		$sql 	= "insert into msg_sendOK_EDM values (NULL,'$seller','$buyer','$sellermail','$buyermail','$pushtime','','$text') ";
		echo $sql;
		try{
			$result = $this->dbconn->query($sql);
		} catch (Exception $e){
		echo "发送失败3\n".$this->dbconn->error();
			$counter = 10;
			//连接失败，就进行10次重新连接
			while(!($conn = new mysqli("localhost","root","123456","valsun_msg"))){
				if(!$counter--){
					echo "放弃重连\n";
					return FALSE;
				}
				echo("连接失败\n");
			}
			if($res=$conn->query($sql)){
				echo "插入成功！"."\n";
			} else {
				return FALSE;
			}
		}
		
		return $result;
	}
}
?>