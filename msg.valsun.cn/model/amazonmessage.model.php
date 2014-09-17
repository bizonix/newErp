<?php
/*
 * message 操作类 
 */
class amazonmessageModel {
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
        $sql = 'select count(*) as num from msg_amazonmessage where classid = '.$categoryid.$status_str;
        $row = $this->dbconn->fetch_first($sql);
        $num = $row['num'];
        return $num;
    }
    
    /*
     * 根据条件统计总数 Amazon
     * $where sql条件语句
     */
    public function getCountNumberByConditions($where){
        $sql = 'select count(*) as num from msg_amazonmessage where 1 '.$where;//echo $sql;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    
    
    /*
     * 根据条件获取列表  Amazon
     */
    public function getAmazonMessageListByConditions($where){
        $sql = 'select *  from msg_amazonmessage where 1 '.$where;
        // echo $sql;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }

       
 /*
     * 计算某个amazon message分类下的数量
     * $categoryid 分类id $status 完成状态 默认不指定
     */
    public function getAmazonNumber($categoryid, $status=NULL){
        $status_str = '';
        if ($status !== NULL) {
            $idar = implode(', ', $status);
            $status_str = ' and status in ('.$idar.' )';
        }
        $sql = 'select count(*) as num from msg_amazonmessage where is_delete=0 and classid = '.$categoryid.$status_str;
       // echo $sql;
        $row = $this->dbconn->fetch_first($sql);
        $num = $row['num'];
        return $num;
    }
    
    /*
     * 根据条件统计总数 amazon
     * $where sql条件语句
     */
    public function getAmazonCountNumberByConditions($where){
        $sql = 'select count(*) as num from msg_amazonmessage where 1 '.$where;//echo $sql;
       // echo $sql;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    

    /*
     * 获取一组message内容 
     * $ids array messageid数组
     */
    public function getMessageInfo($ids, $isdelete = 0){
        $sql_id = implode(',', $ids);
        $sql    = "select * from msg_amazonmessage where id in ($sql_id) and is_delete=$isdelete order by recievetimestamp";
       
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
      
    /*
     * 将一组message移动到指定的分类下面中
     * $ids messageid数组
     * $catid 分类id
     */
    public function moveMessagesToSpecifiedCategory($id, $catid){
        $idsql = implode(', ', $id);
        $sql = "update msg_amazonmessage set classid=$catid where id in ( $idsql )";
        return $this->dbconn->query($sql);
    }
    
    
    public function insertMessages($msginfo){
    	extract($msginfo);
    	$filedarr = array_keys($msginfo);
    	$valarr   = array_values($msginfo);
    	$filedstr = implode(',', $filedarr);
    	foreach ($valarr as &$val){
    		$val = "'$val'";
    	}
    	$valstr   = implode(',', $valarr);
    	$sql = "insert into msg_amazonmessage($filedstr) values ($valstr)";
    	//echo $sql;
    	return $this->dbconn->query($sql);
    }
    
    public function getMessageId($msgid){
    	$sql = "select message_id from msg_amazonmessage where id = '$msgid' ";
    	$res = $this->dbconn->fetch_first($sql);
    	return $res;
    }
    
    public function getMsgId($msgid){
    	$sql = "select message_id from msg_amazonmessage where message_id = '$msgid' ";
    	echo $sql;
    	$res = $this->dbconn->fetch_first($sql);
    	return $res;
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
        } else {
        	$msgdata = array('errCode'=>10000, 'errMsg'=>'邮件已经存储!');
        	echo json_encode($msgdata);
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
     * 更新message表中数据  Amazon message
    */
    public function updateMessageData($field, $where){
    	//print_r($field);
    	$upsql  = formateSqlUpdate($field);
    	
    	$sql    = 'update msg_amazonmessage set '.$upsql.$where;
    	  //echo $sql;
    	return $this->dbconn->query($sql);
    }

    
    /*
     * 插入回复消息到消息队列
     */
    public function insertToqueue($data){
        $sql = "insert into msg_amazonreplyqueue values (null, '$data[msgid]', '$data[param]', 
               '$data[retext]', '$data[catid]', '$data[type]', '$data[account]',0)";
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
    public function updateMessageStatus($msgids, $st){
        $idsql  = implode(', ', $msgids);
        if ($operator !== FALSE) {
        	$opsql = ", replyuser_id='$operator'";
        }
        if ($time !== FALSE) {
        	$timesql = ", replytime=$time";
        }
        //echo $st;exit;
        $sql    = "update msg_amazonmessage set status = $st where id in ($idsql)";
        try{
        	return $this->dbconn->query($sql);
        } catch (Exception $e) {
        	var_dump($this->dbconn->error());
        	echo '出错了!执行B计划'."\n";
        	$conn  =  new mysqli("localhost","valsun_msg_235","4vlk1b^_ptc8","ebay_valsun_msg");
			if(mysqli_connect_error()){
				die("连接失败");
			}
			if($res=$conn->query($sql)){
				echo "B计划执行成功！"."\n";
			} else {
				die("B计划宣告成功！我们即将坠入黑暗！");
			}
		}
    
       
    }
    
    /*
     * 修改标记
     * $mark 0,1 标记
     * $msgid message id
     */
    public function updateMessageMark($msgid,$mark){
        $sql = 'update msg_amazonmessage set messagelevel='.$mark.' where id='.$msgid;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 跟messageid获取message表中的一行信息
     */
    public function getMessageInfoByMessageId($messageid){
        $sql = "select * from msg_amazonmessage where message_id=$messageid and is_delete=0";
        return $this->dbconn->fetch_first($sql);
    }
    
        
   
    
    /*
     * 批量重新回复message
     */
    public function reReplyMessage_amazon($ids){
        $idsql  = implode(',', $ids);
        $sql    = 'select * from msg_amazonmessage where id in ('.$idsql.') and status=1 and is_delete =0';
        $result = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
//         print_r($result);exit;
        $failid     = array();
        foreach ($result as $val ){
            $sql    = "select id from msg_amazonreplyqueue where messageid=$val[id] order by id DESC limit 1";
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
     * 插入回复邮件的附件地址
    */
    public function insertAttachPath($id,$attach){
    	mysql_escape_string($attach);
    	$sql = "update  msg_amazonmessage set send_attachpath='$attach' where id= $id";
    	try{
    		$result = $this->dbconn->query($sql);
    		return 'success';
    	} catch (Exception $e){
    		echo $sql;
    		echo "插入附件地址出错";
    	}
    	
    	
    }
    
    /*
     * 获取某个邮箱最后插入的邮件发送时间
    */
    public function getLastSendTime($gmail){
    	$gmail      = mysql_escape_string($gmail);
    	$sql        = "select sendtime from msg_amazonmessage where recieveid = '$gmail' order by sendtime desc limit 1";
    	$result 	= $this->dbconn->fetch_first($sql);
		if($result){
			return $result['sendtime'];
		} else {
			return false;
		}
    	 
    }
    
    /** 
    * 临时增加的添加账户的方法 
    * tags 
    * @param unknowtype 
    * @return return_type 
    * @author xuzhaoyang 
    * @date 2014-6-28上午9:39:52 
    * @version v1.0.0 
    */
    public function addAccountToGlobal(){
    	$sql =<<<EOF
    			INSERT INTO `power_global_user` (`global_user_id`, `global_user_login_name`, `global_user_name`, `global_user_pwd`, `global_user_status`, `global_user_system`, `global_user_unite_pwd`, `global_user_is_delete`, `global_user_job_no`, `global_user_email`, `global_user_phone`, `global_user_company`, `global_user_dept`, `global_user_job`, `global_user_sex`, `global_user_graduate_school`, `global_user_register_time`, `global_user_remark`, `global_user_address`, `global_user_birthday`, `global_user_native_place`, `global_user_qq`, `global_user_weixin`, `global_user_major`, `global_user_education`, `global_user_degree`, `global_user_is_marry`, `global_user_blood_type`, `global_user_entry_time`, `global_user_dimission_time`, `global_user_id_number`, `global_user_id_address`, `global_user_emergency_contact`, `global_user_emergency_contact_phone`, `global_user_photo_url`, `global_user_job_path`, `global_user_independence`) VALUES
(912, 'zhuhongdan@sailvan.com', '朱红丹', 'ef5a0442ad42e0935d607171df1218f5', 1, '["22","14","7","18","10"]', 1, 0, '01295', 'zhuhongdan@sailvan.com', '', 1, 95, 300, 1, '', 1405063528, '', '', '0000-00-00', '', '', '', '', '', '', 0, '', 1405008000, 0, '360424199102141767', '', '', '', '', '299-301-300', 0)
EOF;
    	try{
    		$this->dbconn->query($sql);
    	} catch (Exception $e){
    		return FALSE;
    	}
    }
    
    /** 
    * 临时增加的添加账户到分系统的方法
    * tags 
    * @param unknowtype 
    * @return return_type 
    * @author xuzhaoyang 
    * @date 2014-6-28上午10:34:59 
    * @version v1.0.0 
    */
    public function addAccountToLocal(){
    	$sql =<<<EOF
    			INSERT INTO `power_user` (`user_id`, `user_name`, `user_pwd`, `user_menu_power`, `user_status`, `user_system_id`, `user_independence`, `user_jobpower`, `user_power`, `user_token`, `user_token_grant_date`, `user_token_effective_date`, `user_isdelete`, `user_register_time`, `user_job_no`, `user_email`, `user_phone`, `user_lastUpdateTime`, `user_company`, `user_job`, `user_dept`, `user_job_path`) VALUES
(4644, 'zhuhongdan@sailvan.com', 'ef5a0442ad42e0935d607171df1218f5', 'null', '1', 14, '0', 916, '{"listingSendEmail":["ajaxInsertEbayAccount","ajaxInsertItemId","index"],"localPower":["alarmDataSubmit","alarmSetting","mailPushSettingList"],"messagefilter":["ajaxChangeMessagesCategory","getMessageListByConditions","markLocalStatus","markMessage"],"messageReply":["ajaxGetTpl","getMessageBody","getOderInfo","getShippingInfo","markAsRead","replyMessage","replyMessageForm","reReplyMessage"],"messageTemplate":["ajaxDelTemplate","editTemplateForm","showTemplateList","tplDataSubmit"],"msgCategory":["categoryList"],"user":["index"]}', '91959771bbf60149f6175a6c73db6c18', 1405008000, 3650, 0, '1405063527', '01295', 'zhuhongdan@sailvan.com', '', 1405063528, 1, 300, 95, '299-301-300')
EOF;
    	try{
    		$this->dbconn->query($sql);
    		return TRUE;
    	} catch (Exception $e){
    		return FALSE;
    	}
    	
    }
    
    
    public function turnState(){
    	$sql ="update msg_amazon_gmailaccount set password = 'ZWdvZGlyZWN0ODg4IUAjZ28=' where gmail = 'egodirect888@gmail.com'";
    	try{
    		$this->dbconn->query($sql);
    		return TRUE;
    	} catch (Exception $e){
    		return FALSE;
    	}
    	 
    }
    
}
