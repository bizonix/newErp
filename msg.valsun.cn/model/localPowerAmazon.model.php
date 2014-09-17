<?php
/*
 * 本地权限表
 */
class LocalPowerAmazonModel{
    private $dbconn = NULL;
    public static $errCode  = 0;
    public static $errMsg   = '';
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn   = $dbConn;
    }
    
    /* 
     * 修改权限         针对amazon
     * 先判断是否有记录 有记录则更新 没记录则插入
     */
    public function updatePower($userid, $power){
    	$pwoerstr = '';
    	if(!empty($power)){
    		if(substr_count($power, '-1')){
    			$pwoerstr   = mysql_real_escape_string($power);
    		} else {
    			$pwoerstr   = mysql_real_escape_string($power).',-1';
    		}
    	} 
        $se_sql 	= 'select * from msg_localpoweramazon where userid='.$userid;
        $re 		= $this->dbconn->fetch_first($se_sql);
        if(empty($re)) {   //没有数据  则插入新数据
        	$in_sql = "insert into msg_localpoweramazon values (null, $userid, '$pwoerstr')";
        	return $this->dbconn->query($in_sql);
        } else{
            $up_sql = "update msg_localpoweramazon set power='$pwoerstr' where userid=$userid";
            //echo $up_sql;exit;
        }
       // echo $up_sql;exit;
        return $this->dbconn->query($up_sql);
    }
    
    
    /*
     * 新增分类时修改权限         针对amazon
    * 
    */
    public function updatePowerByAddClass($userid, $power){
    	$pwoerstr   = mysql_real_escape_string($power);
    	$se_sql 	= 'select * from msg_localpoweramazon where userid='.$userid;
    	$re 		= $this->dbconn->fetch_first($se_sql);
    	if(empty($re['power'])){
    		$up_sql = "update msg_localpoweramazon set power=concat(power,'$pwoerstr') where userid=$userid";
    	}else{
    		$up_sql = "update msg_localpoweramazon set power=concat(power,',$pwoerstr') where userid=$userid";
    	}
    		
    	//echo $up_sql;exit;
    	return $this->dbconn->query($up_sql);
    }
    
    
    /*
     * 查看本地权限表是否存在某个用户记录  amazon
     * 该userid是在power_user中的id
     */
    public function getUserInfo($userid){
        $sql    = 'select * from msg_localpoweramazon where userid='.$userid;
        return $this->dbconn->fetch_first($sql);
    }
    
    public function getAllUserInfo(){
    	$sql    = 'select * from msg_localpoweramazon' ;
    	return $this->dbconn->fetch_array_assoc($sql);
    }    
    /*
     * 获得某个用户有权限访问的文件夹id 以数组形式      针对ebay
     * $userid      分系统用户id
     */
    public function getAmazonPowerlist($userid){
        $info = $this->getUserInfo($userid);
        if (!empty($info)) {
        	$rtnpowerlist=$info['power'];
        	return $rtnpowerlist;
        } else {
            return '';
        }
    }
    
    
    /*
     * 根据用户名获得用户id
     */
   public function getUserIdByLoginName($name, $company=1) {
       $sql     = "select global_user_id, global_user_name from power_global_user where global_user_login_name='$name' and global_user_company=$company";
       return $this->dbconn->fetch_first($sql);
   }
   
   /*
    * 根据用户名获得分系统userid
    * $username 用户登陆名       $systemid   分系统id
    */
  public function getUserIdByNname($username, $systemid=14, $companyid=1) {
      $sql  = "select user_id from power_user where user_name='$username' and user_system_id=$systemid and user_company=$companyid";
      return $this->dbconn->fetch_first($sql);
  }
  
  /*
   * 根据文件夹权限列表 获取相应的账号列表
   */
  public function getAccountListByCatList($catList){
      if (empty($catList)) {
          return array();
      }
      $sql_id   = implode(',', $catList);
      $sql      = "select amazon_account from msg_amazonmessagecategory where id in ($sql_id)";
      $row      = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
      $returndata   = array();
      foreach ($row as $r){
          $returndata[]     = $r['amazon_account'];
      }
      return $returndata;
  }
  
  public function addAmazonAccount($info){
  	extract($info);
  	$sql="insert into msg_amazon_gmailaccount values (null,'$account','$gmail','$site','$password',0)";
  	echo $sql;
  	if($res=$this->dbconn->query($sql)){
  		echo '添加成功';
  	} else {
  		echo '添加失败';
  	}
  }
  
  public function getAccountInfoByGmail($gmail){
  	$sql="select id from msg_amazon_gmailaccount where gmail = '$gmail' ";
  	echo $sql;
  	if($res=$this->dbconn->query($sql) ){
  		if(mysql_numrows($res)>0){
  			return FALSE;
  		} else {
  			return TRUE;
  		}
  	} 
  }
}

?>