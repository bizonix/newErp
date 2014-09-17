<?php
/*
 * 获得本地用message用户相关信息
 */
class GetLoacalUserModel{
    private $dbconn         = NULL;
    public static $errCode  = 0;
    public static $errMsg   = '';
    
    /*
     * 构造函数数
     */
    public function __construct(){
        global $dbConn ;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 获取message用户列表 根据部门id情况 和系统id
     * $sysId   系统id
     * $depId   部门id
     */
    public function getAllMessageUserList($sysId,$depId){
        $uerlist    = array();
        $sql        = 'select u.user_id,  u.user_name, u.user_company, j.job_name from power_user as u left join power_job as j on u.user_job=j.job_id where u.user_system_id='.$sysId." and user_dept=$depId".' and user_isdelete=0';
        
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 获取用户信息 根据分系统id
     */
    public function getUserInfoById($id){
        $sql = 'select * from power_user where user_id='.$id;
        return  $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 根据系统级id获得用户信息
     */
    public function getUserInfoBySysId($sysid){
        $sql = 'select * from power_global_user where global_user_id ='.$sysid.' and global_user_is_delete=0';
        // echo $sql;exit;
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 根据用户名和用户公司id获得全局信息
     * $name  用户名 $compay 公司id
     * $field 字段
     */
    public function getGlobalUserInfoByName($field, $name, $company){
        $field  = implode(',', $field);
        $sql    = "select $field from power_global_user where global_user_login_name='$name' and global_user_company=$company";
        //echo $sql;
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 获取一组用户的全局id和真实用户名 根据用户登录名
    */
    public function getUserId($userlist){
        $u_sql  = implode("', '", $userlist);
        $sql    = "select global_user_id, global_user_name from power_global_user where global_user_login_name in ('$u_sql') and global_user_company=1";
        $result = array();
        $qres   = mysql_query($sql);
        while ($row = mysql_fetch_assoc($qres)){
            $result[$row['global_user_name']] = $row['global_user_id'];
        }
        return $result;
    }
    
    /*
     * 获取某个部门用户的全局id和真实用户名，登录名根据部门id
    */
    public function getUserInfo($deptid){
    	$sql    = "select global_user_id, global_user_name,global_user_login_name from power_global_user where global_user_dept = $deptid and global_user_company=1 and global_user_is_delete=0";
    	$result = array();
    	$qres   = mysql_query($sql);
    	while ($row = mysql_fetch_assoc($qres)){
    		$result[$row['global_user_name']] = $row['global_user_login_name'];
    	}
    	return $result;
    }
    
    public function getRealName($loginnames){
    	$names = implode(',', $loginnames);
    	//echo $names;
    	$sql    = "select global_user_name  from power_global_user where global_user_login_name in( $names) and global_user_company=1 and global_user_is_delete=0";
    	//echo $sql;
    	$result = array();
    	$qres   = mysql_query($sql);
    	while ($row = mysql_fetch_assoc($qres)){
    		$result[$row['global_user_name']] = $row['global_user_id'];
    	}
    	return $result;
    }
    
    public function getRealNameByGlobalId($gid){
    	$sql    = "select global_user_name  from power_global_user where global_user_id =$gid and global_user_company=1 and global_user_is_delete=0";
		echo $sql;
    	$result = $this->dbconn->fetch_first($sql);
    	return $result;
    }

     /**
     * 王民伟
     * Enter description here ...
     * @param unknown_type $sysId
     * @param unknown_type $depId
     */
    public function getAllMessageUserInfo($sysId,$depId){
        $uerlist    = array();
        $sql        = 'select u.user_id,  u.user_name, u.user_company, u.user_id, j.job_name from power_user as u left join power_job as j on u.user_job=j.job_id where u.user_system_id='.$sysId.' and user_dept in '.$depId.' and user_isdelete=0';
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /**
     * 获取有效的客户职员
     */
    public function getAllMessageUserData($sysId, $depId){
        $sql    = "SELECT a.global_user_login_name AS user_name FROM power_global_user AS a JOIN power_user AS b ON a.global_user_login_name = b.user_name WHERE a.global_user_dept IN ".$depId." AND b.user_system_id = '{$sysId}' AND a.global_user_status = 1";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }

    /*
     * 根据登陆id获取用户信息
     */
    public function getUserInfoByLoginName($loginName){
        $loginName  = mysql_real_escape_string($loginName);
        $sql    = "select * from power_global_user where global_user_login_name='$loginName'";
        return $this->dbconn->fetch_first($sql);
    }
    
    
}

?>
