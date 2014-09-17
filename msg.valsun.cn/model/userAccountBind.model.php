<?php
/*
 * ebay账号绑定
 */
class UserAccountBindModel {
    public static $errCode  = '';
    public static $errMsg   = '';
    private $dbConn         = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 设置一个账号和一个用户的绑定
     */
    public function updateBindRelation($account, $userId){
        $account    = mysql_real_escape_string($account);
        $userId     = array_map('intval', $userId);
        /*
         *执行逻辑 先删除旧的 然后再批量插入新的 
         */
        $delSql     = "delete from msg_userAccountBind where account='$account'";
        $delQuey    = $this->dbConn->query($delSql);
        if (FALSE === $delQuey) {
        	self::$errMsg  = '更新失败!';
        	return FALSE;
        }
        foreach ($userId as $id){
            $insertSql  = "insert into msg_userAccountBind (account, userID) values ('$account', '$id')";
            $this->dbConn->query($insertSql);
        }
    }
    
    /*
     * 获得一个账号的授权人信息
     */
    public function getBindInfo($account){
        $account    = mysql_real_escape_string($account);
        $sql        = "select * from msg_userAccountBind where account='$account'";
        $result = $this->dbConn->fetch_array_all($this->dbConn->query($sql));
        return $result;
    }
    
    /*
     * 根据一个用户ID获得授权的账号
     */
    public function getAllowedAccount($uerID){
        $sql    = "select * from msg_userAccountBind where userID='$uerID'";//echo $sql;exit;
        $list   = $this->dbConn->fetch_array_all($this->dbConn->query($sql));
        $returnData = array();
        foreach ($list as $row){
            $returnData[]   = $row['account'];
        }
        return $returnData;
    }
    
}
