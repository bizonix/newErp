<?php
/*
 * 消息回复队列
 */
class replyMessageQueueModel {
    private $dbconn = null;
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数 
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn=$dbConn ;
    }
    
    /*
     * 删除一条回复队列信息
     * $id 主键id值
     */
    public function delAQueueRecords($id){
        $sql = 'delete from msg_replyqueue where id='.$id;
        $qer = $this->dbconn->query($sql);  //执行结果
        $enum = $this->dbconn->affected_rows(); //影响行数
        if ($qer && ($enum > 0)) {  //执行成功并且影响行数大于0
        	return  TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 当回复发送失败时将该记录的计数器加一
     * $id 指定计数器的id
     */
    public function plusCountById($id){
        $sql = 'update msg_replyqueue set trytimes = trytimes+1 where id='.$id;
        return $this->dbconn->query($sql);
    }
}

