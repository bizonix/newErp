<?php
/**
 * 类名：User
 * 功能：读取用户信息
 * 版本：2013-08-08
 * 作者：林正祥
 * 配置文件增加设置：
 * TABLE_USER_ONLINE 	在线用户表
 * TABLE_USER_SESSION 	用户登录日志
 * TABLE_USER_INFO 		用户信息表
 * modify by lzx, date 20140604
*/

class UserModel extends CommonModel{
	
	private static $table_power_user;    	//用户管理表
	private static $table_power_global_user; //统一用户管理表
	private static $table_power_session; 	//会话管理表
	private static $table_power_online;		//在线用户表管理表
	private static $table_job_info;			//岗位表
	private static $table_dept_info;		//部门表
	private static $table_company_info;		//公司表
    private static $table_competence = "om_user_competence";		//用户细颗粒权限表
    private static $sysName 	= 'Ordermanage';
    private static $sysToken 	= 'eccd25ddf4cddea9c46cf77fb6d78fa4';
	static $_instance;
	private $is_count = false;
	
	public function __construct(){
		parent::__construct();
		self::$table_power_user 	= C('TABLE_USER_INFO');
		self::$table_power_global_user 	= C('TABLE_GLOBAL_USER_INFO');
		self::$table_power_session 	= C('TABLE_USER_SESSION');
		self::$table_power_online  	= C('TABLE_USER_ONLINE');
		self::$table_job_info 		= C('TABLE_JOB_INFO');
		self::$table_dept_info 		= C('TABLE_DEPT_INFO');
		self::$table_company_info 	= C('TABLE_COMPANY_INFO');
	}

	private static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
	public function count(){
		$this->is_count = true;
		return $this;
	}
    
    public function getUserOnlineCount(){
    	
    }
    
    public function updateUserOnlineCount(){
    	
    }
    
    public function getUserInfo($filed, $where){
    	self::initDB();
    	$sql = 'SELECT '.$filed.' FROM '.self::$table_power_user.' AS a 
				LEFT JOIN '.self::$table_job_info.' AS b ON a.user_job=b.job_id
				LEFT JOIN '.self::$table_dept_info.' AS c ON a.user_dept=c.dept_id 
				LEFT JOIN '.self::$table_company_info.' AS d ON a.user_company=d.company_id
    			'.$where.' LIMIT 1';
    	$query = self::$dbConn->query($sql);
		
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		self::$errCode = 0;
		self::$errMsg  = "[{$sql}]";		
		return self::$dbConn->fetch_array($query);
    }
	
	/*
	*方法功能：系统用户数据
	*/
	public function getUserLists($filed, $where, $order='', $limit=''){
		
		self::initDB();
		$sql = 'SELECT '.$filed.' FROM '.self::$table_power_user.' AS a 
				LEFT JOIN '.self::$table_power_global_user.' AS e ON a.user_name=e.global_user_login_name
				LEFT JOIN '.self::$table_job_info.' AS b ON a.user_job=b.job_id
				LEFT JOIN '.self::$table_dept_info.' AS c ON a.user_dept=c.dept_id 
				LEFT JOIN '.self::$table_company_info.' AS d ON a.user_company=d.company_id
				'.$where.' '.$order.' '.$limit;
		$query = self::$dbConn->query($sql);
		
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		self::$errCode = 0;
		self::$errMsg  = "[{$sql}]";
		
		if ($this->is_count===true){
			$this->is_count = false;
			return self::$dbConn->num_rows($query);
		}
		
		return self::$dbConn->fetch_array_all($query);		
	}
	
	/*
	*方法功能：统一用户数据
	*/
	public function getGlobalUserLists($filed, $where, $order='', $limit=''){
		
		self::initDB();
		$sql = 'SELECT '.$filed.' FROM '.self::$table_power_global_user.' AS a 
				LEFT JOIN '.self::$table_job_info.' AS b ON a.global_user_job=b.job_id
				LEFT JOIN '.self::$table_dept_info.' AS c ON a.global_user_dept=c.dept_id 
				LEFT JOIN '.self::$table_company_info.' AS d ON a.global_user_company=d.company_id
				'.$where.' '.$order.' '.$limit;
		$query = self::$dbConn->query($sql);
		
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		self::$errCode = 0;
		self::$errMsg  = "[{$sql}]";
		
		if ($this->is_count===true){
			$this->is_count = false;
			return self::$dbConn->num_rows($query);
		}
		
		return self::$dbConn->fetch_array_all($query);		
	}
	
	/**
	 * UserModel::userLogin()
	 * 用户登录走开放系统
	 * add by 管拥军 2013-08-21
	 * modify by lzx 2014-06-04
	 * modify by yxd 2014-07-06 add user_power
	 * @return  bool
	 */
    public function userLogin($username, $password){
    	$loginfo                  = M('InterfacePower')->userLogin(addslashes($username), addslashes($password));
    	$usertoken                = $loginfo['userToken'];           //add by yxd
    	$loginfo['user_power']    = M('InterfacePower')->getUserPower($usertoken);  //add by yxd 
    	if (empty($loginfo)){
    		self::$errMsg = M('InterfacePower')->getErrorMsg();
    		return false;
    	}
		return $loginfo;
	}
	
	/**
	 * UserModel::userInsert()
	 * 新增用户走开放系统
	 * add by 管拥军 2013-08-22
	 * @return  bool
	 */
    public function userInsert($newInfo){
		$return = M('InterfacePower')->userInsert($newInfo);
    	if (empty($return)){
    		self::$errMsg = M('InterfacePower')->getErrorMsg();
    		return false;
    	}
		return $return;
	}
	
	/**
	 * UserModel::userUpdate()
	 * 修改用户走开放系统
	 * add by 管拥军 2013-08-22
	 * @return  bool
	 */
    public static function userUpdate($newInfo, $userToken){
		$return = M('InterfacePower')->userUpdate($newInfo, $userToken);
    	if (empty($return)){
    		self::$errMsg = M('InterfacePower')->getErrorMsg();
    		return false;
    	}
		return $return;
	}
	
	/**
	 * UserModel::userDelete()
	 * 删除用户走开放系统
	 * add by 管拥军 2013-08-23
	 * @return  bool
	 */
    public function userDelete($userToken){
		$return = M('InterfacePower')->userDelete($userToken);
    	if (empty($return)){
    		self::$errMsg = M('InterfacePower')->getErrorMsg();
    		return false;
    	}
		return $return;
	}
	
	/**
	 * UserModel::getAllUser()
	 * 获取系统所有用户走开放系统
	 * add by 管拥军 2013-08-26
	 * @return  json string
	 */
    public static function getAllUser(){
		require_once WEB_PATH."api/include/functions.php";
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.user.getAllUserInfo.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
			/* API应用级输入参数 End*/
		);
		$allUserInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		return $allUserInfo;		
	}
}
?>