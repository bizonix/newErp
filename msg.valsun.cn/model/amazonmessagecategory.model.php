<?php
/*
 *对Amazon邮件目录相关的数据库操作
 */
class amazonmessagecategoryModel
{
    private $dbconn = null;
    public static $errMsg = '';
    public static $errCode = 0;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 获取全部的正常message分类的信息
     * @return array() 数组 没有返回空数组
     */
    public function getAllCategoryInfoList($where='',$field="*"){
        $sql = "select $field from msg_amazonmessagecategory where is_delete = 0 and id<>-1 $where ";//echo $sql;exit;
        $returnval = array();
        $result = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        //print_r($result);
        if (!empty($result)) {
        	$returnval = $result;
        }
        return $returnval;
    }
    public function getCategoryInfoByCatname($catname){
    	$sql = "select id,category_name from msg_amazonmessagecategory where is_delete = 0 and category_name ='$catname'  ";
    	
    	$result=$this->dbconn->fetch_first($sql);
    	return $result;
    }
    /*
     * 新增messagecategory的内容
     * $data 数组 新增分类所需信息
     * 返回 bool值 成功 TRUE 失败 FALSE
     */
    public function addNewCategory($data){
        $name       = $data['name'];
        $rules      = $data['rules'];
        $account    = $data['account'];
        $site       = $data['site'];
        $gmail      = $data['gmail'];
        $notes      = $data['notes'];
        $platform   = $data['platform'];
        $creater    = $data['creater'];
        $createtime = $data['createtime'];
        $insertsql  = "
            insert into msg_amazonmessagecategory values (null, '$name', '$rules', '$account','$site','$gmail','$notes',0,'$createtime','$creater',0)
            ";
        //print_r($insertsql);
        $qre = $this->dbconn->query($insertsql);
        return $qre;
    }
    
    public function updatePower($userid,$cid){
    	
    }
    
    /*
     * 更新分类
     * $cid int 分类id
     * $data array 更新数据
     */
    public function updateCategoryInfo($cid, $data){
        $name    = $data['name'];
        $rules   = $data['rules'];
        $account = $data['account'];
        $site    = $data['site'];
        $gmail   = $data['gmail'];
        $notes 	 = $data['notes'];
        $updatesql = "
            update msg_amazonmessagecategory set category_name='$name', amazon_note='$notes', rules='$rules',amazon_account='$account',
            site='$site',gmail='$gmail'  where id=$cid";
            
        return $this->dbconn->query($updatesql);
    }
    
    /*
     * 获得某个messagecategory的信息 根据id
     */
    public function getCategoryInfoById($cid, $where=""){
        $sql = 'select * from msg_amazonmessagecategory where id='.$cid.$where;
        return $row = $this->dbconn->fetch_first($sql);
    }
    
    

    /*
     * 级联查询某个账户相关的Amazon站点和Gmail邮箱
    */
    public function getSiteGmailByAJAX($amazonaccount){
    	$sql       =  "select distinct site,gmail from msg_amazon_gmailaccount where amazon_account='$amazonaccount'";
    	$rtnarr    =  array();
    	$sites     =  array();
    	$mailboxes =  array();
    	$rtnarr = $this->dbconn->fetch_array_assoc($sql);
    	
    	
    	foreach ($rtnarr as $var){
    		if(!in_array($var['site'], $sites)){
    			$sites[]     = $var['site'];
    		}
    		if(!in_array($var['gmail'], $mailboxes)){
    			$mailboxes[] = $var['gmail'];
    		}
    	}
    	$rtnarr =  array($sites,$mailboxes);
    	$rtnarr =  json_encode($rtnarr);
    	echo $rtnarr;
    }
    public function getSiteGmail($amazonaccount){
    	$sql    =  "select distinct site,gmail from msg_amazon_gmailaccount where amazon_account='$amazonaccount'";
    	
    	$rtnarr =  array();
    	$rtnarr = $this->dbconn->fetch_array_assoc($sql);
    	 return $rtnarr;
    	
    }
    /*
     * 删除一个分类
     * $cid 分类id
     */
    public function delCategoryById($cid){
        $sql = 'update msg_amazonmessagecategory set is_delete=1 where id='.$cid;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 获得一组分类的信息
     * 
     */
    public function getFieldInfoByIds($idar, $where='') {
    	if(is_array($idar)){
    		$idar      =implode(',', $idar);
    	}
    	
    	$sql       = "select * from msg_amazonmessagecategory where id in ($idar) $where and is_delete=0";
    	return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    public function getAllAccount(){
    	$sql  =  "select distinct amazon_account from msg_amazon_gmailaccount";
    	return $this->dbconn->fetch_array_assoc($sql);
    }
    /*
     * 获得分类id和规则
     * 
     */
    public function getCatRules($gmail=''){
    	if(empty($gmail)){
    		$wheresql = '';
    	} else {
    		$wheresql = "gmail = '$gmail' and";
    	}
    	$sql  =  "select id,rules from msg_amazonmessagecategory where $wheresql is_delete=0 order by id ";
    	return $this->dbconn->fetch_array_assoc($sql);
    }
    
}
