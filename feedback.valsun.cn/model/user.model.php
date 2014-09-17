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
    private static $sysName 	= 'Feedback';
    private static $sysToken 	= '282b6540be2fe3c48c55f5748a321aeb';
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
	
	/*
	*方法功能：统一用户数据
	*/
	public function getGlobalUserId($name){
		
		self::initDB();
		//$sql = 'SELECT a.global_user_id FROM '.self::$table_power_global_user.' AS a where a.global_user_company='. C('AUTH_COMPANY_ID').' and( a.global_user_login_name = "'.$name.'" or a.global_user_name = "'.$name.'") ';
		$sql = "SELECT a.global_user_id FROM ".self::$table_power_global_user." AS a where a.global_user_login_name = '".$name."' or a.global_user_name = '".$name."'";
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
	*方法功能：根据ID获取统一用户信息
	*/
	public function getUsernameById($userId, $type = 0){
		self::initDB();
		$sql = 'SELECT a.global_user_login_name, a.global_user_name FROM '.C('TABLE_GLOBAL_USER_INFO').' AS a where a.global_user_id = "'.$userId.'" ';
		$query = self::$dbConn->query($sql);
		
		if (!$query){
			self::$errCode = '1803';
			self::$errMsg  = "[{$sql}] is error";
			return false;
		}
		self::$errCode = 0;
		self::$errMsg  = "[{$sql}]";
		
		$userInfo = self::$dbConn->fetch_array_all($query);
		if($type == 0){
			return $userInfo[0]['global_user_name'];
		}else if($type == 1){
			return $userInfo[0]['global_user_login_name'];
		}
		return $userInfo;	
	}
	
	/**
	 * UserModel::userLogin()
	 * 用户登录走开放系统
	 * add by 管拥军 2013-08-21
	 * @return  bool
	 */
    public static function userLogin($username, $password, $companyId=1){
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.user.login.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'user_name' => $username,
				   'pwd'    => $password,
				'com_id'    => $companyId, 
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken

			/* API应用级输入参数 End*/
		);
		//print_r($paramArr); echo "<br>";
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
		$_SESSION['userCnName'] = $loginInfo->userCnName;
		return "ok";
	}
	
	/**
	 * UserModel::userInsert()
	 * 新增用户走开放系统
	 * add by 管拥军 2013-08-22
	 * @return  bool
	 */
    public static function userInsert($newInfo){
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$newInfo	= json_encode($newInfo);
		$newInfo	= base64_encode($newInfo);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.purchase.addUser.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'addApiUser',
				'newInfo' => $newInfo,  
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
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
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$newInfo	= json_encode($newInfo);
		$newInfo	= base64_encode($newInfo);
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.purchase.updateUserInfo.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'action'  => 'updateUserInfo',
				'newInfo' => $newInfo,  
				'userToken' => $userToken,  
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
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
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'power.user.deleteApiUser.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => 'purchase',
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'userToken' => $userToken,  
                'sysName' => self::$sysName,
                'sysToken' => self::$sysToken
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
		require_once WEB_PATH."html/api/include/opensys_functions.php";
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