<?php
/*
 * message 操作类 
 */
class messageModel {
    private $dbconn         = NULL;
    public static $errCode  = 0;
    public static $errMsg   = '';
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 计算某个message分类下的数量
     * $categoryid 分类id $status 完成状态 默认不指定
     */
    public function getNumber($categoryid, $status=NULL){
        $status_str = '';
        if ($status !== NULL) {
            $idar = implode(', ', $status);
        	$status_str = ' and status in ('.$idar.' )';
        }
        $sql = 'select count(*) as num from msg_message where classid = '.$categoryid.$status_str;
        $row = $this->dbconn->fetch_first($sql);
        $num = $row['num'];
        return $num;
    }
    
    /*
     * 根据条件统计总数 ebay
     * $where sql条件语句
     */
    public function getCountNumberByConditions($where){
        $sql = 'select count(*) as num from msg_message where 1 '.$where;//echo $sql;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 根据条件统计总数 速卖通 订单留言
    * $where sql条件语句
    */
    public function getCountNumberByConditions_aliOrder($where){
        $sql = 'select count(*) as num from msg_aliordermessage where 1 and role=1 '.$where;//echo $sql;exit;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 根据条件统计总数 速卖通 订单留言 需要group by 语句
    * $where sql条件语句
    */
    public function getCountNumberByConditions_aliOrder_groupby($where){
        $sql = 'select count(*) as num from (select id from msg_aliordermessage where 1 and role=1 '.$where.' ) as view';//echo $sql;exit;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 根据条件统计总数 速卖通 站内信
    * $where sql条件语句
    */
    public function getCountNumberByConditions_aliSite($where){
        $sql = 'select count(*) as num from msg_alisitemessage where 1 and role=1 '.$where; //echo $sql;exit;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 根据条件统计总数 速卖通 站内信  需要group by 语句
     * $where sql条件语句
     */
    public function getCountNumberByConditions_aliSite_groupby($where){
        $sql = 'select count(*) as num from (select id from msg_alisitemessage where 1 and role=1 '.$where.' ) as view';//echo $sql;exit;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 根据条件获取列表  ebay
     */
    public function getMessageListByConditions($where){
        $sql = 'select * from msg_message where 1 '.$where;
//         echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }

    /*
     * 根据条件获取列表  ali订单留言
     */
    public function getMessageListByConditions_aliorder($where){
        $sql = 'select * from msg_aliordermessage where 1 and role=1 '.$where;//echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 根据条件获取列表  ali订单留言 需要 group by
     */
    public function getMessageListByConditions_aliorder_groupby($where){
        $sql = 'select * , count(*) as num from msg_aliordermessage where 1 and role=1 '.$where;//echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 根据条件获取列表  速卖通 站内信
     */
    public function getMessageListByConditions_alisite($where){
        $sql = 'select * from msg_alisitemessage where 1 and role=1 '.$where;//echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 根据条件获取列表  ali站内信  需要 group by
     */
    public function getMessageListByConditions_alisite_groupby($where){
        $sql = 'select * , count(*) as num from msg_alisitemessage where 1 and role=1 '.$where;//echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 获取一组message内容 
     * $ids array messageid数组
     */
    public function getMessageInfo($ids, $isdelete = 0){
        $sql_id = implode(',', $ids);
        $sql    = "select * from msg_message where id in ($sql_id) and is_delete=$isdelete order by createtimestamp";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 获取一组message内容                        订单留言
     * $ids array messageid数组
     */
    public function getMessageInfoAliOrder($ids, $isdelete = 0){
        $sql_id = implode(',', $ids);
        $sql    = "select * from msg_aliordermessage where id in ($sql_id) and is_delete=$isdelete group by orderid order by createtimestamp";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 获取一组message内容                        站内信
     * $ids array messageid数组
     */
    public function getMessageInfoAliSite($ids, $isdelete = 0){
        $sql_id = implode(',', $ids);
        $sql    = "select * from msg_alisitemessage where id in ($sql_id) and is_delete=$isdelete group by relationId order by createtimestamp";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 将一组message移动到指定的分类下面中
     * $ids messageid数组
     * $catid 分类id
     */
    public function moveMessagesToSpecifiedCategory($id, $catid){
        $idsql = implode(', ', $id);
        $sql = "update msg_message set classid=$catid where id in ( $idsql )";
        return $this->dbconn->query($sql);
    }
    
    /*
     * 将一组message移动到指定的分类下面中            速卖通订单留言
     * $ids messageid数组
     * $catid 分类id
     */
    public function moveMessagesToSpecifiedCategory_aliorder($id, $catid){
        $idsql = implode(', ', $id);
        $sql = "update msg_aliordermessage set fieldId=$catid where id in ( $idsql )";
        // echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 将一组message移动到指定的分类下面中            速卖通 站内信
     * $ids messageid数组
     * $catid 分类id
     */
    public function moveMessagesToSpecifiedCategory_alisite($id, $catid){
        $idsql = implode(', ', $id);
        $sql = "update msg_alisitemessage set fieldId=$catid where id in ( $idsql )";
        // echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    
    /*
     * 处理message回复内容
     * $field 更新的字段内容
     * $where where条件语句
     * $msgid message表的主键id
     */
    public function insertMessageReply($msgid,  $copy, $retext, $categoryid, $account, &$newid){
        /*----- 事务处理 -----*/
        $this->dbconn->begin();
        $retext = mysql_real_escape_string($retext);
        
        /*----- 更新message表中的数据 -----*/
        $messageupdate = array(
            'replycontent'=>$retext, 
            'replyuser_id'=>$_SESSION['globaluserid'],
            'replytime'=>time(),
            'status'=>1
        );
        $msg_upresult = $this->updateMessageData($messageupdate, ' where id='.$msgid);  //更新数据到message表
        if ($msg_upresult == FALSE) {                                                   //更新失败
        	$this->dbconn->rollback();
        	$this->dbconn->query('set autocommit=1');
        	return FALSE;
        }
        /*----- 更新message表中的数据 -----*/
        
        /*----- 插入数据到队列 -----*/
        $queuedata = array(
        	'msgid'   => $msgid,
            'param'   => mysql_real_escape_string(serialize(array('iscopy'=>$copy))),
            'retext'  => $retext,
            'catid'   => $categoryid,
            'type'    => 1,
            'account' => $account
        );
        $queue = $this->insertToqueue($queuedata);                                       //插入数据到消息队列
        if ($queue == FALSE) {                                                           //更新失败
            $this->dbconn->rollback();
            $this->dbconn->query('set autocommit=1');
            return FALSE;
        }
        /*----- 插入数据到队列 -----*/
        $newid  = $this->dbconn->insert_id();
        $this->dbconn->commit();                                                           //提交事务
        $this->dbconn->query('set autocommit=1');
        return TRUE;
    }
    
    /*
     * 处理message回复内容                    速卖通订单留言
     * $field 更新的字段内容
     * $where where条件语句
     * $msgid message表的主键id
     */
    public function insertMessageReplyAliOrder($msgid,  $retext, &$newid){
        /*----- 事务处理 -----*/
        $this->dbconn->begin();
        $retext = mysql_real_escape_string($retext);
        
        /*----- 更新message表中的数据 -----*/
        $messageupdate = array(
            'responsecontent'=>$retext, 
            'replyerid'=>$_SESSION['globaluserid'],
            'responsetime'=>time(),
            'status'=>1
        );
        $msg_upresult = $this->updateMessageDataAliOrder($messageupdate, ' where id<='.$msgid.' and status=0');  //更新数据到message表
        if ($msg_upresult == FALSE) {                                                   //更新失败
            $this->dbconn->rollback();
            $this->dbconn->query('set autocommit=1');
            return FALSE;
        }
        /*----- 更新message表中的数据 -----*/
        
        /*----- 插入数据到队列 -----*/
        $queuedata = array(
            'msgid'   => $msgid,
            'type'    => 1,
        );
        $queue = $this->insertToqueueAli($queuedata);                                       //插入数据到消息队列
        if ($queue == FALSE) {                                                           //更新失败
            $this->dbconn->rollback();
            $this->dbconn->query('set autocommit=1');
            return FALSE;
        }
        /*----- 插入数据到队列 -----*/
        $newid  = $this->dbconn->insert_id();
        $this->dbconn->commit();                                                           //提交事务
        $this->dbconn->query('set autocommit=1');
        return TRUE;
    }
    
    /*
     * 处理message回复内容                    速卖通          站内信
     * $field 更新的字段内容
     * $where where条件语句
     * $msgid message表的主键id
     */
    public function insertMessageReplyAliSite($msgid,  $retext, &$newid){
        /*----- 事务处理 -----*/
        $this->dbconn->begin();
        $retext = mysql_real_escape_string($retext);
        
        /*----- 更新message表中的数据 -----*/
        $messageupdate = array(
            'replyconten'=>$retext, 
            'replyUser'=>$_SESSION['globaluserid'],
            'replytime'=>time(),
            'status'=>1
        );
        $msg_upresult = $this->updateMessageDataAliSite($messageupdate, ' where id='.$msgid);   //更新数据到message表
        if ($msg_upresult == FALSE) {                                                           //更新失败
            $this->dbconn->rollback();
            $this->dbconn->query('set autocommit=1');
            return FALSE;
        }
        /*----- 更新message表中的数据 -----*/
        
        /*----- 插入数据到队列 -----*/
        $queuedata = array(
            'msgid'   => $msgid,
            'type'    => 2,
        );
        $queue = $this->insertToqueueAli($queuedata);                                       //插入数据到消息队列
        if ($queue == FALSE) {                                                              //更新失败
            $this->dbconn->rollback();
            $this->dbconn->query('set autocommit=1');
            return FALSE;
        }
        /*----- 插入数据到队列 -----*/
        $newid  = $this->dbconn->insert_id();
        $this->dbconn->commit();                                                           //提交事务
        $this->dbconn->query('set autocommit=1');
        return TRUE;
    }
    
    /*
     * 更新message表中数据  ebay message
     */
    public function updateMessageData($field, $where){
        $upsql  = formateSqlUpdate($field);
        $sql    = 'update msg_message set '.$upsql.$where;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 更新message表中数据  速卖通 订单留言
     */
    public function updateMessageDataAliOrder($field, $where){
        $upsql  = formateSqlUpdate($field);
        $sql    = 'update msg_aliordermessage set '.$upsql.$where;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 更新message表中数据  速卖通 站内信
     */
    public function updateMessageDataAliSite($field, $where){
        $upsql  = formateSqlUpdate($field);
        $sql    = 'update msg_alisitemessage set '.$upsql.$where;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 插入回复消息到消息队列
     */
    public function insertToqueue($data){
        $sql = "insert into msg_replyqueue values (null, '$data[msgid]', '$data[param]', 
               '$data[retext]', '$data[catid]', '$data[type]', '$data[account]',0)";
        //echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 插入回复消息到消息队列      速卖通
     */
    public function insertToqueueAli($data){
        $sql = "insert into msg_alimsgqueue values (null, '$data[msgid]',  
               '$data[type]', 0, '')";
        //echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 将message标记为依据回复
     */
    public function markAaRead($msgid, $categoryid, $type , $account, &$newid){
        /*----- 事务处理 -----*/
        $this->dbconn->begin();
        
        /*----- 更新message数据 -----*/
        $messageupdate = array(
            'replyuser_id' => $_SESSION['globaluserid'],
            'replytime'    => time(),
            'status'       => 1
        );
        $msg_upresult = $this->updateMessageData($messageupdate, ' where id='.$msgid);      //更新数据到message表
        if ($msg_upresult == FALSE) {                                                       //更新失败
            $this->dbconn->rollback();
            $this->dbconn->query('set autocommit=1');
            return FALSE;
        }
        /*----- 更新message中的数据 -----*/
        
        /*----- 插入数据到队列 -----*/
        $queuedata = array(
            'msgid'  => $msgid,
            'param'  => mysql_real_escape_string(serialize(array('type'=>$type))),
            'retext' => '',
            'catid'  => $categoryid,
            'type'   => 2,
            'account'=> $account
        );
        $queue = $this->insertToqueue($queuedata);                                      //插入数据到消息队列
        if ($queue == FALSE) {                                                          //更新失败
            $this->dbconn->rollback();
            $this->dbconn->query('set autocommit=1');
            return FALSE;
        }
        /*----- 插入数据到队列 -----*/
        $newid  = $this->dbconn->insert_id();
        $this->dbconn->commit();    //提交事务
        return TRUE;
    }
    
    /*
     * 批量修改message的本地状态
     * $msgids 要修改的id数组
     * $操作人id
     */
    public function updateMessageStatus($msgids, $status, $operator=FALSE, $time=FALSE){
        $idsql  = implode(', ', $msgids);
        if ($operator !== FALSE) {
        	$opsql = ", replyuser_id='$operator'";
        }
        if ($time !== FALSE) {
        	$timesql = ", replytime=$time";
        }
        $sql    = "update msg_message set status=$status $opsql $timesql where id in ($idsql)";
        //echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 批量修改message的本地状态         速卖通 订单留言
     * $msgids 要修改的id数组
     * $操作人id
     */
    public function updateMessageStatus_aliorder($msgids, $status, $operator=FALSE, $time=FALSE){
        $idsql  = implode(', ', $msgids);
        if ($operator !== FALSE) {
            $opsql = ", replyerid='$operator'";
        }
        if ($time !== FALSE) {
            $timesql = ", responsetime=$time";
        }
        $sql    = "update msg_aliordermessage set status=$status $opsql $timesql where id in ($idsql)";
        //echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 修改标记
     * $mark 0,1 标记
     * $msgid message id
     */
    public function updateMessageMark($msgid,$mark){
        $sql = 'update msg_message set markrate='.$mark.' where id='.$msgid;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 跟messageid获取message表中的一行信息
     */
    public function getMessageInfoByMessageId($messageid){
        $sql = "select * from msg_message where message_id=$messageid and is_delete=0";
        return $this->dbconn->fetch_first($sql);
    }
    
        
    /*
     * 批量修改message的本地状态         速卖通  站内信
     * $msgids 要修改的id数组
     * $操作人id
     */
    public function updateMessageStatus_alisite($msgids, $status, $operator=FALSE, $time=FALSE){
        $idsql  = implode(', ', $msgids);
        if ($operator !== FALSE) {
            $opsql = ", replyUser='$operator'";
        }
        if ($time !== FALSE) {
            $timesql = ", replytime=$time";
        }
        $sql    = "update msg_alisitemessage set status=$status $opsql $timesql where id in ($idsql)";
        // echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 批量重新回复message
     */
    public function reReplyMessage_ebay($ids){
        $idsql  = implode(',', $ids);
        $sql    = 'select * from msg_message where id in ('.$idsql.') and status=1 and is_delete =0';
        $result = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
//         print_r($result);exit;
        include_once WEB_PATH.'lib/rabbitmq.class.php';         //消息队列类
        $mq_obj     = new RabbitMQClass(MQ_USER, MQ_PSW, MQ_VHOST, MQ_SERVER);
        $failid     = array();
        foreach ($result as $val ){
            $sql    = "select id from msg_replyqueue where messageid=$val[id] order by id DESC limit 1";
            $row = $this->dbconn->fetch_first($sql);
            if (empty($row)) {      //未找到队列数据
                $failid[] = $val['sendid'];
            } else {
                $mq_obj->queue_publish(MQ_EXCHANGE, array('id'=>$row['id']));
            }
        }
        return $failid;
    }
    
    /*
     * 获取与某个订单关联的message沟通记录
     * $orderId     订单id
     * $createTime  订单的生成时间
     */
    public function getCommunicationList($orderId){
        $sql        = "select id,role, hasread,content, sendername, createtimestamp , message_id from msg_aliordermessage where orderid='$orderId' 
         order by createtimestamp asc "; 
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 回复内容处理
     * 处理message的订单内容
     */
    public function setReplyStatus($id, $status){
        $sql    = "update msg_aliordermessage set status=$status where id<=$id";
        return $this->dbconn->query($sql);
    }
    
    /*
     * 将留言标记为已读
     */
    public function markAsRead($ids){
        $insql  = implode(',', $ids);
        $sql    = "update msg_aliordermessage set hasread=1 where id in ($insql)";
        $this->dbconn->query($sql);
    }
    
    /*
     * 将订单留言标记为已读
     */
    public function markAsReadByMsgId($ids){
        $insql  = implode(',', $ids);
        $time   = time();
        $sql    = "update msg_aliordermessage set hasread=1 , replytime=$time where message_id in ($insql)";
//         echo $sql;exit;
        $this->dbconn->query($sql);
    }
    
    /*
     * 将站内信标记为已读
    */
    public function markAsReadByMsgId_site($ids){
        $insql  = implode(',', $ids);
        $time   = time();
        $sql    = "update msg_alisitemessage set hasread=1, replytime=$time where message_id in ($insql)";//echo $sql;exit;
        $this->dbconn->query($sql);
    }
    
    /*
     * 根据messageid 记录回复人id
     */
    public function markUser($ids, $userid){
        $insql  = implode(',', $ids);
        $sql    = "update msg_aliordermessage set replyer='$userid' where message_id in ($insql)";
        $this->dbconn->query($sql);
    }
    
    /*
     * 根据messageid 记录回复人id
    */
    public function markUser_site($ids, $userid){
        $insql  = implode(',', $ids);
        $sql    = "update msg_alisitemessage set replyer='$userid' where message_id in ($insql)";
        $this->dbconn->query($sql);
    }
    
    /*
     * 根据一组站内信的relationId获得一组相关的站内信信息
     */
    public function getRlatedSiteMessage($relationid){
        $sql    = "select sendername, message_id,fileUrl, hasread,productName, productUrl, receiverid, content, orderId, orderUrl, role, createtimestamp 
                    from msg_alisitemessage where relationId =$relationid order by createtimestamp asc
                ";
//         echo $sql;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 将站内信标记为已读
     */
    public function markSiteMsgAsRead($ids){
        $insql  = implode(',', $ids);
        $sql    = "update msg_alisitemessage set hasread=1 where id in ($insql)";
        $this->dbconn->query($sql);
    }
    
    /*
     * 设置某一个messageid区间的订单留言的状态
     */
    public function setOrderMsgStatus($status, $first, $end, $orderid){
        $sql    = "update msg_aliordermessage set hasread='$status' where role=1 and message_id>='$first' and message_id<='$end' and orderid='$orderid'";
        echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 设置某一个messageid区间的订单留言的处理人
    */
    public function setOrderMsgReply($userId){
        $sql    = "update msg_aliordermessage set replyer='$userId' where role=1 and message_id>='$first' and message_id<='$end'";
        return $this->dbconn->query($sql);
    }
    
}
