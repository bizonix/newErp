<?php
/*
 * Amazon账户类
 */
class AmazonOrderModel {
    private $dbconn         = NULL;
    public static $errCode  = 0;
    public static $errMsg   = '';
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 获取所有亚马逊账号  以数组形式返回
     */
    public function getAmazonBuyerandSeller($ordernum,$email){
    	$conn  =  new mysqli("192.168.200.158","cerp","123456","cerp");
    	if(mysqli_connect_error()){
    		die("连接失败");
    	}
    	if(!empty($ordernum)){
    		$wheresql = "recordnumber like '$ordernum'";
    	} else{
    		$wheresql = "ebay_usermail like '$email'";
    	}
    		$sql="select ebay_userid,ebay_account from ebay_order where  $wheresql";
    		//echo $sql;
    		if($res=$conn->query($sql)){
    			$ordernum=$res->fetch_assoc();
    			$conn->close();
    			return $ordernum;
    		} else {
    			$conn->close();
    			die("查询失败！");
    		}
    	}
    
    
    
}
?>