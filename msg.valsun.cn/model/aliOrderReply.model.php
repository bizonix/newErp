<?php
/*
 * 订单留言回复数据msg_aliorderreply 表
 */
class AliOrderReplyModel {
    public static $errMsg   = '';
    public static $errCode  = 0;
    private $dbconn         = NULL;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn   = $dbConn;
    }
    
    /*
     * 插入回复数据 订单留言
     */
    public function insertData($data){
        $orderId    = $data['orderid'];
        $content    = $data['content'];
        $content    = mysql_real_escape_string($content);
        $replyuser  = $data['replyuser'];
        $replytime  = time();
        $sql        = "
                insert into msg_aliorderreply (orderid, content, replytime, replyuserid) values ('$orderId', '$content', '$replytime', '$replyuser')
                ";//echo $sql;exit;
        $this->dbconn->query($sql);
    }
    
    /*
     * 生成统计数据 按回复人统计 
     */
    public function statistics($where){
        $sql        = "select replyuserid, count(replyuserid)  as num from msg_aliorderreply where 1 $where";//echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
}

?>