<?php
/*
 * 速卖通账号处理类
 */
class AliAccountModel {
    public static $errMsg   = '';
    public static $errCode  = 0;
    private $dbconn         = NULL;
    private $accountList    = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn   = $dbConn;
        include WEB_PATH.'lib/ali_keys/common.php';
        $this->accountList  = $erp_user_mapping;
    }
    
    /*
     * 获取全部的速卖通账号列表
     * $sort    是否排序 desc 降序  asc 升序
     * $dis     正对什么排序  account 账号名称  name 名称
     */
    public function getAllAliAccountList($dis=null, $sort=NULL){
        $erp_user_mapping   = $this->accountList;
        switch ($sort) {
        	case 'desc':           //降序
            	$dis == 'account' ? krsort($erp_user_mapping) : arsort($erp_user_mapping);
            	break;
        	case 'asc':
        	    $dis == 'account' ? ksort($erp_user_mapping)  : asort($erp_user_mapping);
        	    break;
        	default :
        	   break;
        }
        return $erp_user_mapping;                            
    }
    
    /*
     * 判断账号是否存在
     */
    public function aliAccountExists($account){
        $erp_user_mapping   = $this->accountList;
        return array_key_exists($account, $erp_user_mapping);
    }
    
    /*
     * 根据账号id 获取账号名称
     */
    public function accountId2Name($account){
        $returnData = '';
        if (array_key_exists($account, $this->accountList)) {
        	$returnData    = $this->accountList[$account];
        }
        return $returnData;
    }
}
