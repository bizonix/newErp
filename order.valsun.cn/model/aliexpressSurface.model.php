<?php
/**
 * 标记发货日志表
 * add by wcx @20140307
 */
class AliexpressSurfaceModel{
    public static $dbConn;
    private static $_instance;
    public static $materInfo;
    public static $errCode;
    public static $errMsg;

    public static function initDB(){
        global $dbConn;
        self::$dbConn =	$dbConn;
    }

    //单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public static function showAliexpressSurfaceList($field,$where){
        self::initDB();
        $sql    =   "select $field from om_aliexpress_surface where $where ";
        //echo $sql;exit;
        $queryresult = self :: $dbConn->query($sql);
        if (empty ($queryresult)) { //更新失败
            self :: $errCode = '005';
            self :: $errMsg = '更新状态失败！';
            return FALSE;
        }
        return self::$dbConn->fetch_array_all($queryresult);
    }
	
}