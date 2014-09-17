<?php
/*
 * message 操作类 
 */
class AmazonMessageTemplateModel {
    private $dbconn = NULL;
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 获得全部的模板列表
     */
    public function getAllTemplateList($where){
        $sql = "select * from msg_amazonmessagetemplate where is_delete=0 $where";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     *添加模板
     *$data array 数据 
     *$userid int 所属用户id
     */
    public function addTemplate($data, $userid=0){
        if ($data['iscommon'] == 1) {   //如果是公用 则所属人id为空
        	$userid = 0;
        }
        //公用常用模板
        if($data['iscommon'] == 1&&$data['ispublic'] == 1){
        	$data['type']='1,2';
        }
        //私人常用模板
        if($data['iscommon'] == 1&&$data['ispublic'] == 0){
        	$data['type']='0,1';
        }
        $sql = "
            insert into msg_amazonmessagetemplate values (null, '$data[title]', '$data[content]','$data[topic]', 1, 
            '$userid', '$data[type]', '0',0)
            ";
        echo $sql;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 获得指定id的模板信息
     * $tid int 模板id
     */
    public function getTplInfoById($tid){
        $sql = 'select * from msg_amazonmessagetemplate where id='.$tid;
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 更新模板信息
     * $data array 更新数据
     * $tid int 模板id
     */
    public function updateTplInfo($data, $tid, $userid){
    	//print_r($data);
    	//私人不常用模板
    	if($data['iscommon'] == 0&&$data['ispublic'] == 0){
    		$data['type']=0;//'0,0'
    	}
    	//私人常用模板
    	if($data['iscommon'] == 1&&$data['ispublic'] == 0){
    		$data['type']=1;//'0,1'
    	}
    	//公用不常用模板
    	if($data['iscommon'] == 0&&$data['ispublic'] == 1){
    		$data['type']=2;//'0,2'
    	}
    	//公用常用模板
    	if($data['iscommon'] == 1&&$data['ispublic'] == 1){
    		$data['type']=3;//'1,2'
    	}
        $sql = "
            update msg_amazonmessagetemplate set name='$data[title]', content='$data[content]', ownerid='$userid', 
            type='$data[type]',subject='$data[topic]', ordersn='$data[ordersn]' where id=$tid";
            //echo $sql;exit;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 删除模板信息
     * $tid int 模板id
     */
    public function delTplById($tid){
        $sql = "update msg_amazonmessagetemplate set is_delete=1 where id='$tid'";
        return $this->dbconn->query($sql);
    }
    
    /*
     * 获得模板列表信息
     * $userid int 所属用户
     * $field array 所需的字段名
     */
    public function getTplList($userid, $field=array(), $platform=1){
        $sql_field = '*';
        if (!empty($field)) {
        	$sql_field = implode(', ', $field);
        }
        $sql = 'select '.$sql_field.' from msg_amazonmessagetemplate where (ownerid='.$userid.' or '.
            ' type = 2 or type = 3) and is_delete=0 and platform='.$platform;
            // echo $sql;exit;
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 获得系统中模板总数
     */
    public function getAllMessageNumber($where=''){
        $sql = "select count(*) as num from msg_amazonmessagetemplate where is_delete=0 $where";
        //echo $sql;exit;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
}
