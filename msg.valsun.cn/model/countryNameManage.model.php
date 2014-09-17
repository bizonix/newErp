<?php
/*
 * 国家名称管理
 */
class CountryNameManageModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbconn         = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn  ;
        $this->dbconn   = $dbConn;
    }
    
    /*
     * 根据国家简称获取国家全称
     */
    public function getRealCountryNameWithCountryCode($code){
        $code   = mysql_real_escape_string($code);
        $sql    = 'select * from msg_country where abbreviation='."'$code'";
        return $this->dbconn->fetch_first($sql);
    }
}





