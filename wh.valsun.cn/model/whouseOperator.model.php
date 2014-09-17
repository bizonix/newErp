<?php

/** 
 * @author 处理仓库操作人员信息的类
 * 
 */
class WhouseOperatorModel
{
    public static $errCode = 0 ;
    public static $errMsg = '' ;
    private $dbconn = null;
    
    
    /**
     * 构造函数
     */
    function __construct (){
    	global $dbConn;
    	$this->dbconn = $dbConn;
    }
    
    /*
     * 获取包装员信息列表
     */
    public function getPackingUserList() {
    	/*
    	 * 处理包装员信息
    	 */
		$usermodel = UserModel::getInstance();
		$iqc_user  = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job in (127,167)",'','');
		return $iqc_user;
	}
}

?>