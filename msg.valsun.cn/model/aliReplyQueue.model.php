<?php
/*
 * 速卖通队列处理类
 */
class AliReplyQueueModel{
    public static $errMsg;
    public static $errCode;
    private $dbconn;
    
    /*
     * 构造函数
     */
    public function __construct()
    {
        global $dbConn;
        $this->dbconn   = $dbConn;
    }
    
    /*
     * 根据id获得队列表中的一条数据
     */
    public function getQueueRow($id){
        $sql    = 'select * from msg_alimsgqueue where id='.$id;
        return $this->dbconn->fetch_first($sql);
    }
}
 
