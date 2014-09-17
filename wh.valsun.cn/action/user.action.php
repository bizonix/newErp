<?php
/**
 * 类名：User
 * 功能：读取用户信息
 * 版本：2013-08-08
 * 作者：林正祥
*/

class UserAct{
		
	static $errCode	  = 0;
	static $errMsg	  = '';
	static $debug	  = false;
	
	static $_instance;
	private $is_count = false;
	
	public function __construct(){
		self::$debug = C('IS_DEBUG');
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
	
	/*
	*功能：外接系统获取单个用户信息
	*/
	public function act_getUserById($userid){
		$userid = intval($userid);
		if ($userid===0){
			self::$errCode = '5806';
			self::$errMsg  = 'userid is error';
			return array();
		}
		$usersingle = UserModel::getInstance();
		$filed 		= ' a.*,b.job_name,c.dept_name,d.company_name';
		$where 		= " WHERE a.user_id='{$userid}' ";
		$userinfo	= $usersingle->getUserInfo($filed, $where);
		return $this->_checkReturnData($userinfo, array());
	}
	/**
	 * 根据用户token获取用户信息
	 */
	public function act_getUserByToken($token) {
		if (preg_match("/[a-z0-9]{32}/", $token)===false){
			self::$errCode = '5806';
			self::$errMsg  = 'token is error';
			return false;
		}
		
	}
	
	/*
	*获取某个外接系统所有用户信息
	*/
	public function act_getUserLists($condition=array(), $sort='', $limit=''){
		
		$usermodel = new UserModel();
		
		//$filed 		= ' `user_id` as uid,`user_name` as userName,`user_job_no` as jobNo,`user_email` as email,`user_phone` as phone,`user_menu_power` as menuPower,`user_system_id` AS system_id,`user_status` as status,`user_independence` as independence,`user_power` as power,`user_company` as company,`user_job` as job,`user_job_path` as job_path,`user_dept` as dept,`user_jobpower` as jobPower,`user_token` as token,`user_token_grant_date` as tokenGrantDate,`user_token_effective_date` as TokenEffectiveDate ,`user_lastUpdateTime` as lastUpdateTime ';
		$filed = ' a.*,b.job_name,c.dept_name,d.company_name,e.global_user_name,e.global_user_id';
		$where = !empty($condition)&&is_array($condition) ? ' WHERE '.implode(' AND ', $condition).' ' : '';
	
		if ($this->is_count===true){
			$this->is_count = false;
			$usercount = $usermodel->count()->getUserLists($filed, $where);
			return $this->_checkReturnData($usercount, 0);
		}
		$orderby = empty($orderby) ? '' : " ORDER BY {$sort} ";
		$userlists = $usermodel->getUserLists($filed, $where, $orderby, $limit);
		return $this->_checkReturnData($userlists, array());
	}
	
	/*
	*功能：用户退出系统
	*/	
	public function logout(){
		session_destroy();
	}
	
	private function _checkReturnData($data, $errreturn){
		if ($data===false){
			self::$errCode = UserModel::$errCode;
			self::$errMsg  = UserModel::$errMsg;
			return $errreturn;
		}elseif (empty($data)){
			self::$errCode = 5806;
			self::$errMsg  = 'There is no data!';
			if (self::$debug===true){
				self::$errMsg .= 'The SQL is '.UserModel::$errMsg;
			}
			return $errreturn;
		}else {
			self::$errCode = 1;
			self::$errMsg  = 'success';
			return $data;
		}
	}
	
	/**
	 * UserAct::act_userLogin()
	 * 用户登录act
	 * @return bool 
	 */
	public function act_userLogin(){
		if(!isset($_POST['username']) || trim($_POST['username']) == ''){
			exit("用户名为空!");
		}
		if(!isset($_POST['password']) || trim($_POST['password']) == ''){
			exit("密码为空!");
		}
		/*
		if(!isset($_POST['companyId']) || trim($_POST['companyId']) == ''){
			exit("公司为空!");
		}*/
		$username 		= post_check(trim($_POST['username']));
		$password 		= post_check(trim($_POST['password']));
		//$companyId 		= post_check(trim($_POST['companyId']));
        $result			= UserModel::userLogin($username, $password);

		//$powerAccountList    = UserCompetenceModel::showCompetenceVisibleAccount($_SESSION['userId']);   //获取权限对应账号
//		$powerPlatformList   = UserCompetenceModel::getCompetenceVisiblePlat($_SESSION['userId']);   //获取权限对应平台
//		$powershipping       = UserCompetenceModel::showCompetenceVisibleShip($_SESSION['userId']);   //获取权限对应运输方式
		//$_SESSION['platformList'] = $powerPlatformList;
//		$_SESSION['accountList']  = $powerAccountList;
//		$_SESSION['shippingList'] = $powershipping;
		return $result;
    }
	
	
	/**
	 * UserAct::act_userLogin()
	 * 用户登录act
	 * @return bool 
	 */
	public function act_pdaUserLogin(){
		if(!isset($_POST['username']) || trim($_POST['username']) == ''){
			exit("用户名为空!");
		}
		if(!isset($_POST['password']) || trim($_POST['password']) == ''){
			exit("密码为空!");
		}

		$username  = post_check(trim($_POST['username']));
		$username  = str_pad($username,5,0,STR_PAD_LEFT);
		$userInfo  = CommonModel::getLoginUserInfo($username);		
		if(empty($userInfo)){
			return "用户不存在!";
		}
		
		$password 		= post_check(trim($_POST['password']));
        $result			= UserModel::userLogin($userInfo[0]['global_user_login_name'], $password);
		return $result;
    }
	
	/**
	 * UserAct::act_insert()
	 * 新增用户act
	 * @return bool 
	 */
	public function act_insert(){
		if(!isset($_POST['username']) || trim($_POST['username']) == ''){
			exit("用户名为空!");
		}
		if(!isset($_POST['password']) || trim($_POST['password']) == ''){
			exit("密码为空!");
		}
		$username	= post_check(trim($_POST['username']));
		$password	= post_check(trim($_POST['password']));
		$jobno 	 	= isset($_POST['jobno']) ? post_check(trim($_POST['jobno'])) :'';
		$phone	 	= isset($_POST['phone']) ? post_check(trim($_POST['phone'])) : '';
		$email	 	= isset($_POST['email']) ? post_check(trim($_POST['email'])) : '';
		$userdept 	= intval($_POST['userdept']);
		$independence = intval($_POST['independence']);
		$stat		= intval($_POST['stat']);
		$userjob 	= explode("|",$_POST['userjob']);
		$grantDate		= post_check(trim($_POST['grantDate']));
		$effectiveDate	= intval($_POST['effectiveDate']);
		$power		= '{"goods":"[\"index\"]"}';
		$newInfo	= array(
						'userName'           => $username,//用户名，类型 string，必须项
						'pwd'                => $password,//密码，类型 string，必须项
						'jobNo'              => $jobno,//工号,类型string(10)，可选项
						'email'              => $email,//Email,类型string(80)，可选项
						'phone'              => $phone,//联系电话,类型string(20)，可选项
						'menuPower'          => '["31"]',//菜单权限，json格式string类型，可以为空
						'status'             => $stat,//状态，值为'1'表示状态有效，值为'0'表示状态无效，必须项
						'independence'       => $independence,//权限类型，值为'1'表示独立权限，值为'0'表示共享权限，必须项    
						'power'              => $power,//操作权限，json格式string类型，必须项
						'jobPower'           => intval($userjob[0]),//所属的岗位权限编号，类型int(8)，必须项
						'tokenGrantDate'     => $grantDate,//用户token的授权日期，类型date()，必须项
						'TokenEffectiveDate' => $effectiveDate,//用户token的有效天数，类型int(10)，必须项
						'company' 			 => '1',//公司，类型int(10)，必须项
						'dept' 				 => $userdept,//部门，类型int(10)(10)，必须项
						'job' 				 => intval($userjob[1]),//岗位编号，类型int(10)，必须项
						//'jobPath' 			 => $userjob[2],//岗位路径，可选项
					);
        $result		= UserModel::userInsert($newInfo);
		return $result;
    }
	
	/**
	 * UserAct::act_update()
	 * 修改用户act
	 * @return bool 
	 */
	public function act_update(){
		if(!isset($_POST['username']) || trim($_POST['username']) == ''){
			exit("用户名为空!");
		}
		$dataArr	= $_POST;
		$power		= array();
		$username	= post_check(trim($_POST['username']));
		$password	= post_check(trim($_POST['password']));
		$jobno 	 	= isset($_POST['jobno']) ? post_check(trim($_POST['jobno'])) :'';
		$phone	 	= isset($_POST['phone']) ? post_check(trim($_POST['phone'])) : '';
		$email	 	= isset($_POST['email']) ? post_check(trim($_POST['email'])) : '';
		$independence 	= intval($_POST['user_independence']);
		$stat		= intval($_POST['user_status']);
		$userjob 	= explode("|",$_POST['userjob']);
		$userdept 	= intval($_POST['userdept']);
		$grantDate		= post_check(trim($_POST['grantDate']));
		$effectiveDate	= intval($_POST['effectiveDate']);
		$userToken	= post_check(trim($_POST['usertoken']));
		$usersingle = UserModel::getInstance();//获取当前用户信息
		$filed 		= ' a.*,b.job_name,c.dept_name,d.company_name';
		$where 		= " WHERE a.user_id='{$_SESSION[C('USER_AUTH_ID')]}' ";
		$userinfo	= $usersingle->getUserInfo($filed, $where);
		$groupname	= ActionModel::actionGroupList("10");//读取系统的actiongroup列表
		foreach($groupname as $v){
			if(is_array($_POST["{$v}"]) && isset($_POST["{$v}"])){
				array_push($power,"\"{$v}\":".json_encode($_POST["{$v}"]));
			}else {
				array_push($power,"\"{$v}\":[]");
			}
		}
		$power		= implode(",",$power);
		$power		= "{".$power."}";
		$newInfo	= array(
						'userName'           => $username,//用户名，类型 string，必须项
						'pwd'                => $password,//密码，类型 string，必须项
						'jobNo'              => $jobno,//工号,类型string(10)，可选项
						'email'              => $email,//Email,类型string(80)，可选项
						'phone'              => $phone,//联系电话,类型string(20)，可选项
						'menuPower'          => '["31"]',//菜单权限，json格式string类型，可以为空
						'status'             => $stat,//状态，值为'1'表示状态有效，值为'0'表示状态无效，必须项
						'independence'       => $independence,//权限类型，值为'1'表示独立权限，值为'0'表示共享权限，必须项    
						'power'              => $power,//操作权限，json格式string类型，必须项
						'jobPower'           => intval($userjob[0]),//所属的岗位权限编号，类型int(8)，必须项
						'tokenGrantDate'     => $grantDate,//用户token的授权日期，类型date()，必须项
						'TokenEffectiveDate' => $effectiveDate,//用户token的有效天数，类型int(10)，必须项
						'company' 			 => '1',//公司，类型int(10)，必须项
						'dept' 				 => $userdept,//部门，类型int(10)(10)，必须项
						'job' 				 => intval($userjob[1]),//岗位编号，类型int(10)，必须项
						//'jobPath' 			 => $userjob[2],//岗位路径，可选项
					);
		//如果当前用户是自己就不修改权限
		if($userToken == $userinfo['user_token']){
			unset($newInfo['power']);
		}
        $result		= UserModel::userUpdate($newInfo,$userToken);
		return $result;
    }
	
	/**
	 * UserAct::act_delete()
	 * 删除用户act
	 * @return bool 
	 */
	public function act_delete(){
		$userid		= intval($_POST['userid']);
		if(!$userid){
			return false;
			exit;
		}
		$usersingle = UserModel::getInstance();
		$filed 		= ' a.user_token';
		$where 		= " WHERE a.user_id='{$userid}' ";
		$userinfo	= $usersingle->getUserInfo($filed, $where);
		$userToken	= $userinfo["user_token"];
		$result		= UserModel::userDelete($userToken);
		return $result;
	}
	
	/**
	 * UserAct::act_getAllUser()
	 * 获取全部用户act
	 * @return json string 
	 */
	public function act_getAllUser(){
		$result		= UserModel::getAllUser();
		$result		= json_decode($result,true);
		$count		= count($result);
		for($i=0; $i<=$count; $i++){
			//print_r($result[$i]);
			echo json_encode($result[$i]['power']),"<br/>";
		}
		//echo $result;
		print_r($result);
		exit;
	}
}
?>