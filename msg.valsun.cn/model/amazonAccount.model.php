<?php
/*
 * Amazon账户类
 */
class AmazonAccountModel {
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
    public function getAmazonAccountsGmail(){
    	$sql = "select distinct * from msg_amazon_gmailaccount where is_delete =0";
    	return $this->dbconn->fetch_array_assoc($sql);
    }
    
    public function getGmailAndPassword(){
    	$sql = "select gmail,password from msg_amazon_gmailaccount where is_delete=0" ;
    	return $this->dbconn->fetch_array_assoc($sql);
    }
    
    public function getAmazonAccountByGmail($gmail){
    	$sql = "select amazon_account from msg_amazon_gmailaccount where is_delete=0 and gmail='$gmail'";
    	return $this->dbconn->fetch_array_assoc($sql);
    }
    
    public function getAmazonPasswordByGmail($gmail){
    	$sql = "select password from msg_amazon_gmailaccount where is_delete=0 and gmail='$gmail'";
    	return $this->dbconn->fetch_array_assoc($sql);
    }
}
?>