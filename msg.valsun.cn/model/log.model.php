<?php
/*
 * 日志记录
 */
class LogModel
{
    private $dbconn = null;    
    public static $errMsg = '';
    public static $errCode = 0;

    /*
     * 构造函数
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }

    /*
     * 记录日志   Message同步日志
     */
    public function addlogsmessage($log_name,$log_operationtime,$log_orderid,$log_notes,$log_ebay_account,$start,$end,$type){
        $nowtime=date("Y-m-d H:i:s");
        $log_sql		= "insert into msg_logmessage(log_name,log_operationtime,log_orderid,log_notes,currentime,
        log_ebay_account,starttime,endtime,type)
        values('$log_name','$log_operationtime','$log_orderid','$log_notes','$nowtime',
        '$log_ebay_account','$start','$end','$type')";
        $this->dbconn->query($log_sql);
    }
}

