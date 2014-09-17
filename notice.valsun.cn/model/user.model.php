<?php
/**
 * 名称：UserModel
 * 功能： 查询通迅录
 * 版本：v1.0
 * 日期：2013/10/0
 * 作者：Ren da hai
 * 配置文件增加设置：
 * TABLE_USER_ONLINE 	在线用户表
 * TABLE_USER_SESSION 	用户登录日志
 * TABLE_USER_INFO 		用户信息表
 * */
if (! isset ( $_SESSION )) {
	session_start ();
}
class UserModel {
	static $errCode = 0;
	static $errMsg 	= "";
	static $table 	= "power_global_user";
	private static $dbConn;
	private static $table_power_user; 					// 用户管理表
	private static $table_power_global_user; 			// 统一用户管理表
	private static $table_power_session; 				// 会话管理表
	private static $table_power_online; 				// 在线用户表管理表
	private static $table_job_info; 					// 岗位表
	private static $table_dept_info; 					// 部门表
	private static $table_company_info; 				// 公司表
	private static $sysName ;
	private static $sysToken ;
	static $_instance;
	private $is_count = false;

	public function __construct() {
		self::$table_power_user 		= C ( 'TABLE_USER_INFO' );
		self::$table_power_global_user 	= C ( 'TABLE_GLOBAL_USER_INFO' );
		self::$table_power_session 		= C ( 'TABLE_USER_SESSION' );
		self::$table_power_online 		= C ( 'TABLE_USER_ONLINE' );
		self::$table_job_info 			= C ( 'TABLE_JOB_INFO' );
		self::$table_dept_info 			= C ( 'TABLE_DEPT_INFO' );
		self::$table_company_info 		= C ( 'TABLE_COMPANY_INFO' );
		self::$sysName 					= C ('AUTH_SYSNAME');
		self::$sysToken 				= C ( 'AUTH_SYSTOKEN' );
	}

	// 单实例
	public static function getInstance() {
		if (! (self::$_instance instanceof self)) {
			self::$_instance = new self ();
		}
		return self::$_instance;
	}

	public function count() {
		$this->is_count = true;
		return $this;
	}

	public function getUserOnlineCount() {
	}

	public function updateUserOnlineCount() {
	}

	public function getUserInfo($filed, $where) {
		self::initDB ();
		$sql = 'SELECT ' . $filed . ' FROM ' . self::$table_power_user . ' AS a
				LEFT JOIN ' . self::$table_job_info . ' AS b ON a.user_job=b.job_id
				LEFT JOIN ' . self::$table_dept_info . ' AS c ON a.user_dept=c.dept_id
				LEFT JOIN ' . self::$table_company_info . ' AS d ON a.user_company=d.company_id
    			' . $where . ' LIMIT 1';
		$query = self::$dbConn->query ( $sql );

		if (! $query) {
			self::$errCode 	= '1803';
			self::$errMsg 	= "[{$sql}] is error";
			return false;
		}
		self::$errCode 	= 0;
		self::$errMsg 	= "[{$sql}]";
		return self::$dbConn->fetch_array ( $query );
	}

	/*
	 * 方法功能：系统用户数据
	 */
	public function getUserLists($filed, $where, $order = '', $limit = '') {
		self::initDB ();
		$sql = 'SELECT ' . $filed . ' FROM ' . self::$table_power_user . ' AS a
				LEFT JOIN ' . self::$table_job_info . ' AS b ON a.user_job=b.job_id
				LEFT JOIN ' . self::$table_dept_info . ' AS c ON a.user_dept=c.dept_id
				LEFT JOIN ' . self::$table_company_info . ' AS d ON a.user_company=d.company_id
				' . $where . ' ' . $order . ' ' . $limit;
		$query = self::$dbConn->query ( $sql );

		if (! $query) {
			self::$errCode = '1803';
			self::$errMsg = "[{$sql}] is error";
			return false;
		}
		self::$errCode = 0;
		self::$errMsg = "[{$sql}]";

		if ($this->is_count === true) {
			$this->is_count = false;
			return self::$dbConn->num_rows ( $query );
		}

		return self::$dbConn->fetch_array_all ( $query );
	}

	/*
	 * 方法功能：统一用户数据
	 */
	public function getGlobalUserLists($filed, $where, $order = '', $limit = '') {
		self::initDB ();
		$sql = 'SELECT ' . $filed . ' FROM ' . self::$table_power_global_user . ' AS a
			LEFT JOIN ' . self::$table_job_info . ' AS b ON a.global_user_job=b.job_id
			LEFT JOIN ' . self::$table_dept_info . ' AS c ON a.global_user_dept=c.dept_id
			LEFT JOIN ' . self::$table_company_info . ' AS d ON a.global_user_company=d.company_id
			' . $where . ' ' . $order . ' ' . $limit;
		$query = self::$dbConn->query ( $sql );

		if (! $query) {
			self::$errCode = '1803';
			self::$errMsg = "[{$sql}] is error";
			return false;
		}
		self::$errCode = 0;
		self::$errMsg = "[{$sql}]";

		if ($this->is_count === true) {
			$this->is_count = false;
			return self::$dbConn->num_rows ( $query );
		}

		return self::$dbConn->fetch_array_all ( $query );
	}

	/**
	 * UserModel::userLogin()
	 * 用户登录走开放系统
	 * add by 管拥军 2013-08-21
	 *
	 * @return bool
	 */
	public static function userLogin($username, $password) {
		$paramArr = array(
	  		/* API系统级输入参数 Start */
			'method' 	=> 'power.user.login.get', 					// API名称
			'format' 	=> 'json', 									// 返回格式
			'v'			=> '1.0', 									// API版本号
			'username' 	=> 'notice',
			/* API系统级参数 End */
			/* API应用级输入参数 Start */
			'user_name' => $username,
			'pwd' 		=> rawurldecode($password),
			'sysName' 	=> self::$sysName,
			'sysToken' 	=> self::$sysToken,
            //'com_id' 	=> $company
			/* API应用级输入参数 End*/
		);
		$loginInfo = callOpenSystem( $paramArr );
		unset( $paramArr );
		$loginInfo = json_decode( $loginInfo );
		if(isset( $loginInfo->errCode )) {
			echo $loginInfo->errMsg;
			self::$errCode 	= $loginInfo->errCode;
			self::$errMsg 	= $loginInfo->errMsg;
			return false;
		}
		if(isset( $loginInfo->error_response )) {
			echo  $loginInfo->error_response ->msg;
			self::$errCode = $loginInfo->error_response ->code;
			self::$errMsg  = $loginInfo->error_response ->msg;
			return false;
		}
		$_SESSION ['userToken'] = $loginInfo->userToken;
		$_SESSION ['sysUserId'] = $loginInfo->userId; 				// 分系统用户ID
		$_SESSION ['userId'] 	= $loginInfo->globalUserId; 		// 统一用户系统ID
		$_SESSION ['userName'] 	= $loginInfo->userName;
		$_SESSION ['companyId'] = $loginInfo->company;

		//获取中文名
		$table 	= "`power_global_user`";
		$filed 	= "global_user_name";
		$where 	= " global_user_is_delete = 0 AND global_user_status = 1 AND  global_user_login_name='{$username}' LIMIT 1";
		$ret	= NoticeApiModel::selectOneTable($table,$filed,$where);
		if($ret){
			$cnName = $ret[0]['global_user_name'];
		}
		$_SESSION['cnName']		= $cnName;
		$isAdmin 				= '0';
		$_SESSION['isAdmin']  	= $isAdmin;
		log::write( var_export($_SESSION,true),log::DEBUG);
		return "ok";
	}

	/**
	 * UserModel::userInsert()
	 * 新增用户走开放系统
	 * add by 管拥军 2013-08-22
	 *
	 * @return bool
	 */
	public static function userInsert($newInfo) {
		$newInfo 	= json_encode( $newInfo );
		$newInfo 	= base64_encode( $newInfo );
		$paramArr 	= array(
						/* API系统级输入参数 Start */
						'method' 	=> 'power.purchase.addUser.get', 				// API名称
						'format' 	=> 'json', 										// 返回格式
						'v' 		=> '1.0', 										// API版本号
						'username' 	=> 'purchase',
						 /* API系统级参数 End */
						 /* API应用级输入参数 Start */
						'action' 	=> 'addApiUser',
						'newInfo' 	=> $newInfo,
						'sysName' 	=> self::$sysName,
						'sysToken' 	=> self::$sysToken
						/* API应用级输入参数 End*/
						);
		$addUserInfo = callOpenSystem($paramArr);
		unset ($paramArr);
		$addUserInfo = json_decode($addUserInfo, true);
		if($addUserInfo['userId']) {
			return "ok";
		} else {
			echo $addUserInfo['errMsg'];
			return false;
		}
	}

	/**
	 * UserModel::userUpdate()
	 * 修改用户走开放系统
	 * add by 管拥军 2013-08-22
	 *
	 * @return bool
	 */
	public static function userUpdate($newInfo, $userToken) {
		$newInfo = json_encode($newInfo);
		$newInfo = base64_encode($newInfo);
		$paramArr = array(
						/* API系统级输入参数 Start */
						'method' 	=> 'power.purchase.updateUserInfo.get', 			// API名称
						'format' 	=> 'json', 											// 返回格式
						'v' 		=> '1.0', 											// API版本号
						'username' 	=> 'purchase',
						/* API系统级参数 End */
						/* API应用级输入参数 Start */
						'action' 	=> 'updateUserInfo',
						'newInfo' 	=> $newInfo,
						'userToken' => $userToken,
						'sysName' 	=> self::$sysName,
						'sysToken'	=> self::$sysToken
						/* API应用级输入参数 End*/
					);
		$updateUserInfo = callOpenSystem($paramArr);
		unset ($paramArr);
		$updateUserInfo = json_decode($updateUserInfo, true);
		if($updateUserInfo['errCode'] == '0') {
			return "ok";
		} else {
			echo $updateUserInfo['errMsg'];
			return false;
		}
	}

	/**
	 * UserModel::userDelete()
	 * 删除用户走开放系统
	 * add by 管拥军 2013-08-23
	 *
	 * @return bool
	 */
	public static function userDelete($userToken) {
		$paramArr = array(
					/* API系统级输入参数 Start */
					'method' 	=> 'power.user.deleteApiUser.get', // API名称
					'format' 	=> 'json', // 返回格式
					'v' 		=> '1.0', // API版本号
					'username' 	=> 'purchase',
					/* API系统级参数 End */
					/* API应用级输入参数 Start */
					'userToken' => $userToken,
					'sysName' 	=> self::$sysName,
					'sysToken' 	=> self::$sysToken
					/* API应用级输入参数 End*/
			);
		$deleteUserInfo = callOpenSystem($paramArr);
		unset($paramArr);
		$deleteUserInfo = json_decode($deleteUserInfo, true);
		if($deleteUserInfo['errCode'] == '0') {
			return "ok";
		} else {
			echo $deleteUserInfo ['errMsg'];
			return false;
		}
	}

	/**
	 * UserModel::getAllUser()
	 * 获取系统所有用户走开放系统
	 * add by 管拥军 2013-08-26
	 *
	 * @return json string
	 */
	public static function getAllUser() {
		$paramArr = array(
		   		 		/* API系统级输入参数 Start */
						'method' 	=> 'power.user.getAllUserInfo.get', 		// API名称
						'format' 	=> 'json', 									// 返回格式
						'v' 		=> '1.0', 									// API版本号
						'username' 	=> 'purchase',
						/* API系统级参数 End */
		   		 		/* API应用级输入参数 Start */
						'sysName' 	=> self::$sysName,
						'sysToken' 	=> self::$sysToken
		   		 		/* API应用级输入参数 End*/
					);
		$allUserInfo = callOpenSystem($paramArr);
		unset($paramArr);
		return $allUserInfo;
	}

	/**
	 * 功能：获取全局变量，以对数据库操作
	 *
	 * @return self::$dbConn
	 *
	 */
	public static function initDB() {
		global $dbConn;
		self::$dbConn = $dbConn;
	}

	/**
	 * 获取用户通讯录
	 * @param string $where
	 * @param string $limit
	 * @return array $ret
	 */
	public static function getUserList($where, $limit) {
		self::initDB ();
		$field 	= "global_user_login_name,global_user_name,global_user_email,global_user_phone";
		$sql 	= "SELECT {$field} FROM `" . self::$table . "` WHERE 1 " . $where . " AND `global_user_is_delete` = 0 AND global_user_status = 1  ORDER BY global_user_id DESC " . $limit;
		$query 	= self::$dbConn->query($sql);
		if($query) {
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self::$errCode = "003";
			self::$errMsg  = "Error occurred！Function=" . __FUNCTION__ . " sql= " . $sql;
			return false;
		}
	}

	public static function getData($where = "", $field = "*", $order = "", $limitStart = NULL, $limit = NULL) {
		self::initDB();
		if($limit > 0) {
			$limitStr = " LIMIT " . $limitStart . "," . $limit;
		} else {
			$limitStr = "";
		}
		$sql = "SELECT $field FROM `" . self::$table . "` WHERE 1 " . $where . $order . $limitStr;
		// echo $sql;
		$query = self::$dbConn->query($sql);
		if($query) {
			$result = self::$dbConn->fetch_array_all ($query);
			return $result;
		} else {
			self::$errCode 	= "004";
			self::$errMsg 	= "Error occurred！Function=" . __FUNCTION__ . " sql= " . $sql;
			return false;
		}
	}

	/**
	 * 插入一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function insertRow($data) {
		self::initDB();
		$ret = self::IsDataExist($data);
		if($ret > 0) {
			return - 1; // username exists
		}

		$sql = array2sql($data);
		$sql = "INSERT INTO `" . self::$table . "` SET " . $sql;
		// echo $sql;
		$query = self::$dbConn->query($sql);
		if($query) {
			$affectedrows = self::$dbConn->affected_rows();
			return $affectedrows;
		} else {
			self::$errCode 	= "005";
			self::$errMsg 	= "Error occurred！Function=" . __FUNCTION__ . " sql= " . $sql;
			return false;
		}
	}

	/**
	 * 替换一条记录
	 *
	 * @param array $data
	 * @return int $affectedrows 影响的行数
	 */
	public static function replaceRow($data) {
		self::initDB();
		$sql = array2sql($data);
		$sql = "REPLACE INTO `" . self::$table . "` SET " . $sql;
		// echo $sql; exit;
		$query = self::$dbConn->query($sql);
		if($query) {
			$affectedrows = self::$dbConn->affected_rows();
			return $affectedrows;
		} else {
			self::$errCode 	= "006";
			self::$errMsg 	= "Error occurred！Function=" . __FUNCTION__ . " sql= " . $sql;
			return false;
		}
	}

	/**
	 * 根据条件统计表的行数
	 * @para：$where
	 */
	public static function rowCount($where = "") {
		self::initDB();
		$sql 	= "select count(1) as row_count from `" . self::$table . "` where 1 " . $where;
		$query 	= self::$dbConn->query($sql);
		if($query) {
			$result = self::$dbConn->fetch_array_all($query);
			return $result;
		} else {
			self::$errCode 	= "007";
			self::$errMsg 	= "Error occurred！Function=" . __FUNCTION__ . " sql= " . $sql;
			return false;
		}
	}

	/**
	 * 更新一条记录，只支持一维数组
	 * @para $data as array
	 * @where as String
	 */
	public static function update($data, $where = "") {
		self::initDB();
		$sql = array2sql($data);
		$sql = "update `" . self::$table . "` SET " . $sql . " where 1 " . $where;
		// echo $sql;
		$query = self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode 	= "008";
			self::$errMsg 	= "Error occurred！Function=" . __FUNCTION__ . " sql= " . $sql;
			return false;
		}
	}
	public static function createEmailField($email, $where) {
		self::initDB();
		if(empty( $email )) {
			return false;
		}
		$where 	= " WHERE 1 AND  " . $where;
		$sql 	= "UPDATE  `power_global_user`  SET global_user_email = '{$email}'  {$where} ";
		$query 	= self::$dbConn->query($sql);
		if($query) {
			if (self::$dbConn->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * 功能：展示所有用户
	 * @author wxb
	 * 日期：2013/11/21
	 * */
	public static function showNameList() {
		self::initDB();
		$field	= "global_user_name as name ,global_user_id as id ";
		$sql 	= "SELECT {$field} FROM `" . self::$table . "` WHERE 1 AND `global_user_is_delete` = 0 AND global_user_status = 1  ";
		$sql .= " ORDER BY global_user_id DESC " ;

		$query 	= self::$dbConn->query($sql);
		if($query) {
			$ret = self::$dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self::$errCode 	= "003";
			self::$errMsg 	= "Error occurred！Function=" . __FUNCTION__ . " sql= " . $sql;
			return false;
		}
	}

}
?>