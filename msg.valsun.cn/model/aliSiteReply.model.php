<?php
/*
 * 站内信回复
 */

class AliSiteReplyModel {
    public static $errMsg   = '';
    public static $errCode  = 0;
    private $dbconn         = null;
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn   = $dbConn;
    }
    
    /*
     * 添加新的回复数据
     */
    public function addNewReplyData($data){
        $content    = mysql_real_escape_string($data['content']);
        $time       = time();
        $sql        = "
                    insert into msg_alisitereply (replyuser, content, replytime, relationid) values ('$data[replyuser]', 
                    '$content', $time,'$data[relationid]')
                ";
        $this->dbconn->query($sql);
    }
    
    /*
     * 生成统计数据 按回复人统计 
     */
    public function statistics($where){
        $sql        = "select replyuser, count(replyuser)  as num from msg_alisitereply where 1 $where";//echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
}
