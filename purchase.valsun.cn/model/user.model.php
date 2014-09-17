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
*/
if(!isset($_SESSION)){
    session_start();     
}
class UserModel{
	
	private static $dbConn;
	private static $table_power_user;    	//用户管理表
	private static $table_power_global_user; //统一用户管理表
	private static $table_power_session; 	//会话管理表
	private static $table_power_online;		//在线用户表管理表
	private static $table_job_info;			//岗位表
	private static $table_dept_info;		//部门表
	private static $table_company_info;		//公司表
	static $errCode	  = 0;
	static $errMsg	  = '';
	static $_instance;
	private $is_count = false;

	public function __construct(){
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
				LEFT JOIN '.self::$table_job_info.' AS b ON a.user_job=b.job_id
				LEFT JOIN '.self::$table_dept_info.' AS c ON a.user_dept=c.dept_id 
				LEFT JOIN '.self::$table_company_info.' AS d ON a.user_company=d.company_id
				LEFT JOIN '.self::$table_power_global_user.' AS e ON a.user_company = e.global_user_company AND a.user_name = e.global_user_login_name
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
	 * @return  bool
	 */
    public static function userLogin($username, $password, $company){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.user.login.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'user_name' => $username,  
				   'pwd'    => $password, 
				'com_id'    => $company, 
                'sysName' 	=> C('AUTH_SYSNAME'),
                'sysToken' 	=> C('AUTH_SYSTOKEN')

			/* API应用级输入参数 End*/
		);
		$loginInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$loginInfo	= json_decode($loginInfo);
		if(isset($loginInfo->errCode)) {
			echo $loginInfo->errMsg;
			self::$errCode	= $loginInfo->errCode;
			self::$errMsg	= $loginInfo->errMsg;
			return false;
		}
		$_SESSION['userToken'] 	= $loginInfo->userToken;
		$_SESSION['sysUserId'] 	= $loginInfo->globalUserId;//统一用户系统ID
		$_SESSION['userId'] 	= $loginInfo->userId;//分系统用户ID
		$_SESSION['userName'] 	= $loginInfo->userName;
		$_SESSION['companyId'] 	= $loginInfo->company;
		$_SESSION['userCnName'] = $loginInfo->userCnName; //中文名
		setcookie("userCnName", $loginInfo->userCnName, time()+24*3600);

		//颗粒化权限 写进 session

		global $dbConn;
		$sql = "SELECT * FROM  `ph_purchases_access` where user_id={$_SESSION['sysUserId']}";
		$sql = $dbConn->execute($sql);
		$accessInfo = $dbConn->fetch_one($sql);
		if(!empty($accessInfo)){
			$_SESSION['power_access'] = $accessInfo['access'];  
			$_SESSION['access_id'] = $accessInfo['power_ids'].",{$_SESSION['sysUserId']}";
		}else{
			$_SESSION['access_id'] = $_SESSION['sysUserId'];
		}
		return "ok";
	}
	
	/**
	 * UserModel::userInsert()
	 * 新增用户走开放系统
	 * add by 管拥军 2013-08-22
	 * @return  bool
	 */
    public static function userInsert($newInfo){
		$newInfo	= json_encode($newInfo);
		$newInfo	= base64_encode($newInfo);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.purchase.addUser.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'addApiUser',
				'newInfo' => $newInfo,  
                'sysName' => C('AUTH_SYSNAME'),
                'sysToken' => C('AUTH_SYSTOKEN')
			/* API应用级输入参数 End*/
		);
		$addUserInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$addUserInfo	= json_decode($addUserInfo,true);
		if($addUserInfo['userId']){
			return "ok";
		}else {
			echo $addUserInfo['errMsg'];
			return false;
		}
	}
	
	/**
	 * UserModel::userUpdate()
	 * 修改用户走开放系统
	 * add by 管拥军 2013-08-22
	 * @return  bool
	 */
    public static function userUpdate($newInfo,$userToken){
		$newInfo	= json_encode($newInfo);
		$newInfo	= base64_encode($newInfo);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.purchase.updateUserInfo.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'updateUserInfo',
				'newInfo' => $newInfo,  
				'userToken' => $userToken,  
                'sysName' => C('AUTH_SYSNAME'),
                'sysToken' => C('AUTH_SYSTOKEN')
			/* API应用级输入参数 End*/
		);
		$updateUserInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$updateUserInfo	= json_decode($updateUserInfo,true);
		if($updateUserInfo['errCode']=='0'){
			return "ok";
		}else {
			echo $updateUserInfo['errMsg'];
			return false;
		}
	}
	
	/**
	 * UserModel::userDelete()
	 * 删除用户走开放系统
	 * add by 管拥军 2013-08-23
	 * @return  bool
	 */
    public static function userDelete($userToken){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.user.deleteApiUser.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'userToken' => $userToken,  
                'sysName' => C('AUTH_SYSNAME'),
                'sysToken' => C('AUTH_SYSTOKEN')
			/* API应用级输入参数 End*/
		);
		$deleteUserInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		$deleteUserInfo	= json_decode($deleteUserInfo,true);
		if($deleteUserInfo['errCode']=='0'){
			return "ok";
		}else {
			echo $deleteUserInfo['errMsg'];
			return false;
		}
	}
	
	/**
	 * UserModel::getAllUser()
	 * 获取系统所有用户走开放系统
	 * add by 管拥军 2013-08-26
	 * @return  json string
	 */
    public static function getAllUser(){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.user.getAllUserInfo.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
                'sysName' => C('AUTH_SYSNAME'),
                'sysToken' => C('AUTH_SYSTOKEN')
			/* API应用级输入参数 End*/
		);
		$allUserInfo	= callOpenSystem($paramArr);
		unset($paramArr);
		return $allUserInfo;		
	}
}
?>
