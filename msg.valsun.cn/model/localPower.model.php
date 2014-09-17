<?php
/*
 * 本地权限表
 */
class LocalPowerModel{
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
     * 修改权限         针对 ebay
     * 先判断是否有记录 有记录则更新 没记录则插入
     */
    public function updatePower($userid, $power){
        $pwoerstr   = mysql_real_escape_string($power);
        $se_sql = 'select userid from msg_localpower where userid='.$userid;
        $re = $this->dbconn->fetch_first($se_sql);
        if (empty($re)) {   //没有数据  则插入新数据
        	$in_sql = "insert into msg_localpower values (null, $userid, '$pwoerstr')";
        	return $this->dbconn->query($in_sql);
        } else {
            $up_sql = "update msg_localpower set power='$pwoerstr' where userid=$userid";
            return $this->dbconn->query($up_sql);
        }
    }
    
    /*
     * 修改权限         针对速卖通
     * 先判断是否有记录 有记录则更新 没记录则插入
     */
    public function updatePowerAli($userid, $power){
        $pwoerstr   = mysql_real_escape_string($power);
        $se_sql = 'select userid from msg_localpowerali where userid='.$userid;
        $re = $this->dbconn->fetch_first($se_sql);
        if (empty($re)) {   //没有数据  则插入新数据
            $in_sql = "insert into msg_localpowerali values (null, $userid, '$pwoerstr')";
            return $this->dbconn->query($in_sql);
        } else {
            $up_sql = "update msg_localpowerali set power='$pwoerstr' where userid=$userid";
            return $this->dbconn->query($up_sql);
        }
    }
    
    /*
     * 查看本地权限表是否存在某个用户记录  eaby
     */
    public function getUserInfo($userid){
        $sql    = 'select * from msg_localpower where userid='.$userid;
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 查看本地权限表是否存在某个用户记录  eaby
     */
    public function getUserInfoAli($userid){
        $sql    = 'select * from msg_localpowerali where userid='.$userid; 
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 获得某个用户有权限访问的文件夹id 以数组形式      针对ebay
     * $userid      分系统用户id
     */
    public function getEbayPowerlist($userid){
        $info = $this->getUserInfo($userid);
        if (!empty($info)) {
            $uns    = unserialize($info['power']);
        	if (empty($uns)) {
        		return array('field'=>array());
        	} else {
        	    return $uns;
        	}
        } else {
            return array('field'=>array());
        }
    }
    
    /*
     * 获得某个用户的速卖通文件夹权限
     * 针对速卖通
     */
    public function getAliPowerlist($userid){
        $info = $this->getUserInfoAli($userid);
        if (!empty($info)) {
            $uns    = unserialize($info['power']);
            if (empty($uns)) {
                return array('field'=>array());
            } else {
                return $uns;
            }
        } else {
            return array('field'=>array());
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
      $sql      = "select ebay_account from msg_messagecategory where id in ($sql_id)";
      $row      = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
      $returndata   = array();
      foreach ($row as $r){
          $returndata[]     = $r['ebay_account'];
      }
      return $returndata;
  }
}

?>