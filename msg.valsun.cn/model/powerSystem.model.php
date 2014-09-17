<?php
/*
 * 获取系统信息model
 */
class PowerSystemModel{
    private     $dbconn     = NULL;
    public static $errMsg   = '';
    public static $errCode  = 0;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn ;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 根据系统名获取相关信息
     */
    public function getSysInfoByName($sysname){
        $sql = 'select * from power_system where system_name='."'$sysname'";
        return $this->dbconn->fetch_first($sql);
    }
}

?>