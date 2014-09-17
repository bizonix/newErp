<?php
include_once	WEB_PATH."model/user.model.php";

if(!isset($_SESSION)){
    session_start();
}
/**
 * 名称：UserAct
 * 功能： 查询通迅录
 * 版本：V 1.0
 * 日期：2013/10/09
 * 作者： Ren da hai
 * */
class UserAct {

	static $errCode	  =	0;
	static $errMsg    =	"";
	static $debug	  = false;
	static $_instance;
	private $is_count = false;

    static $sysName ;
    static $sysToken  =null;//鉴权里的notice
    static $userToken = '8d83ddeb94e34a9b37cfdbfc4d1de208';//wenxiaobin token
    static $token     = 'f8d9aa94589f2e8fe1b5f82ba1eaa16b';//opensystem里的notice token

	public function __construct(){
		self::$debug = C('IS_DEBUG');
		self::$sysName = C('OPEN_SYS_USER');
	}

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
    public function act_getUserById($userid) {
    	$userid = intval($userid);
    	if($userid === 0) {
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
    	//$filed = `user_id` as uid,`user_name` as userName,`user_job_no` as jobNo,`user_email` as email,`user_phone` as phone,`user_menu_power` as menuPower,`user_system_id` AS system_id,`user_status` as status,`user_independence` as independence,`user_power` as power,`user_company` as company,`user_job` as job,`user_job_path` as job_path,`user_dept` as dept,`user_jobpower` as jobPower,`user_token` as token,`user_token_grant_date` as tokenGrantDate,`user_token_effective_date` as TokenEffectiveDate ,`user_lastUpdateTime` as lastUpdateTime ';
    	$filed = ' a.*,b.job_name,c.dept_name,d.company_name';
    	$where = !empty($condition)&&is_array($condition) ? ' WHERE '.implode(' AND ', $condition).' ' : '';

    	if($this->is_count === true) {
    		$this->is_count = false;
    		$usercount 		= $usermodel->count()->getUserLists($filed, $where);
    		return $this->_checkReturnData($usercount, 0);
    	}
    	$orderby 	= empty($orderby) ? '' : " ORDER BY {$sort} ";
    	$userlists 	= $usermodel->getUserLists($filed, $where, $orderby, $limit);
    	return $this->_checkReturnData($userlists, array());
    }


    private function _checkReturnData($data, $errreturn) {
    	if($data === false) {
    		self::$errCode = UserModel::$errCode;
    		self::$errMsg  = UserModel::$errMsg;
    		return $errreturn;
    	} elseif(empty($data)) {
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
 /*    	if(!isset($_POST['company']) || trim($_POST['company']) == ''){
    		exit("公司为空!");
    	} */
    	$username 		= post_check(trim($_POST['username']));
    	$password 		= post_check(trim($_POST['password']));
     	//$company 		= post_check(trim($_POST['company']));
    	$UserModel 		= UserModel::getInstance();
    	$result			= $UserModel->userLogin($username, $password);
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
    public function act_update() {
    	if(!isset($_POST['username']) || trim($_POST['username']) == '') {
    		exit("用户名为空!");
    	}
    	$dataArr		= $_POST;
    	$power			= array();
    	$username		= post_check(trim($_POST['username']));
    	$password		= post_check(trim($_POST['password']));
    	$jobno 	 		= isset($_POST['jobno']) ? post_check(trim($_POST['jobno'])) :'';
    	$phone	 		= isset($_POST['phone']) ? post_check(trim($_POST['phone'])) : '';
    	$email	 		= isset($_POST['email']) ? post_check(trim($_POST['email'])) : '';
    	$independence 	= intval($_POST['user_independence']);
    	$stat			= intval($_POST['user_status']);
    	$userjob 		= explode("|",$_POST['userjob']);
    	$userdept 		= intval($_POST['userdept']);
    	$grantDate		= post_check(trim($_POST['grantDate']));
    	$effectiveDate	= intval($_POST['effectiveDate']);
    	$userToken		= post_check(trim($_POST['usertoken']));

    	$usersingle 	= UserModel::getInstance();													//获取当前用户信息
    	$filed 			= ' a.*,b.job_name,c.dept_name,d.company_name';
    	$where 			= " WHERE a.user_id='{$_SESSION[C('USER_AUTH_ID')]}' ";
    	$userinfo		= $usersingle->getUserInfo($filed, $where);
    	$groupname		= ActionModel::actionGroupList("8");										//读取系统的actiongroup列表
    	foreach($groupname as $v) {
    		if(is_array($_POST["{$v}"]) && isset($_POST["{$v}"])) {
    			array_push($power,"\"{$v}\":".json_encode($_POST["{$v}"]));
    		} else {
    			array_push($power,"\"{$v}\":[]");
    		}
    	}

    	$power		= implode(",", $power);
    	$power		= "{".$power."}";
    	$newInfo	= array(
		    			'userName'           => $username,											//用户名，类型 string，必须项
		    			'pwd'                => $password,											//密码，类型 string，必须项
		    			'jobNo'              => $jobno,												//工号,类型string(10)，可选项
		    			'email'              => $email,												//Email,类型string(80)，可选项
		    			'phone'              => $phone,												//联系电话,类型string(20)，可选项
		    			'menuPower'          => '["31"]',											//菜单权限，json格式string类型，可以为空
		    			'status'             => $stat,												//状态，值为'1'表示状态有效，值为'0'表示状态无效，必须项
		    			'independence'       => $independence,										//权限类型，值为'1'表示独立权限，值为'0'表示共享权限，必须项
		    			'power'              => $power,												//操作权限，json格式string类型，必须项
		    			'jobPower'           => intval($userjob[0]),								//所属的岗位权限编号，类型int(8)，必须项
		    			'tokenGrantDate'     => $grantDate,											//用户token的授权日期，类型date()，必须项
		    			'TokenEffectiveDate' => $effectiveDate,										//用户token的有效天数，类型int(10)，必须项
		    			'company' 			 => '1',												//公司，类型int(10)，必须项
		    			'dept' 				 => $userdept,											//部门，类型int(10)(10)，必须项
		    			'job' 				 => intval($userjob[1]),								//岗位编号，类型int(10)，必须项
		    			//'jobPath' 			 => $userjob[2],									//岗位路径，可选项
    	);

    	if($userToken == $userinfo['user_token']) {    												//如果当前用户是自己就不修改权限
    		unset($newInfo['power']);
    	}
    	$result	= UserModel::userUpdate($newInfo, $userToken);
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

    public static function getConf(){
    		self::$sysToken = trim(C('AUTH_SYSTOKEN'));
    }
    public function act_getUserInfo($userToken) {
        $paramArr = array(
			//API系统级输入参数
            'method'   => 'power.user.getUserInfo.get',  //API名称
            'format'   => 'json',  //返回格式
            'v'        => '1.0',   //API版本号
            'username' => 'notice',//系统名称
			//API应用级输入参数
           	'userToken'  => $userToken,
            'sysName'	 => self::$sysName,
            'sysToken'	 => self::$sysToken,
		);
		$userInfo = callOpenSystem($paramArr);
		$userInfo = json_decode($userInfo);
		if(isset($userInfo->errCode)) {
			self::$errCode	= $userInfo->errCode;
			self::$errMsg	= $userInfo->errMsg;
			return self::$errMsg;
		}
        return $userInfo;
    }
    //add by wxb
    function act_getAllUserInfo(){
    	$queryConditions = array();
    	$userInfo = Auth::getApiGlobalUser($queryConditions);
        self::saveUserDB($userInfo);//更新到数据库

    	$userInfo = json_decode($userInfo,true);
    	if(isset($userInfo->errCode)) {
    		self::$errCode	= $userInfo->errCode;
    		self::$errMsg	= $userInfo->errMsg;
    		return self::$errMsg;
    	}
    	return $userInfo;
    }
    function saveUserData($info) {
        $info = trim($info);
        $data = '<?php $jsondata = '."'".$info."'; ?>";
        $fp = fopen(WEB_PATH."html/api/include/userData.json.php", "w+");//追加写入
        if($fp) {
            $flag=fwrite($fp, $data);
            if(!$flag) {
                echo "写入文件失败<br>";
            }
        } else {
            echo "打开文件失败";
        }
        fclose($fp);
    }

    //replace single user
    function saveUserDB($info) {
       	$userList = json_decode(trim($info), true);
// 		echo "<pre>";
//        	print_r($userList);exit;
        foreach($userList as $key => $user) {
            $data = array(
                'id'             => $user['userId'],
                'userName'      => $user['userName'],
            	'loginName'	 => $user['loginName'],
                'phone'          => $user['phone'],
                'email'          => $user['email'],
                'userStatus'         => $user['userStatus'],
            );
            $result = UserModel::replaceRow($data);

            if($result) {
                echo "Data {$key} success: id={$user['userId']} <br/>";
            } else {
                echo "Data {$key} failed: id={$user['userId']}  username={$user['userName']} <br/>";
            }
        }
    }

   	public static function act_getPage($where, $perNum, $pa="", $lang='CN') {
		$total	= UserModel::getUserList($where, $limit='');					//返回整个通讯录列表
        $page	= new Page(count($total), $perNum, $pa, $lang);					//创建页面对象
		$list	= UserModel::getUserList($where, $page->limit);					//返回单页面
		$fpage	= $page->fpage();
		return array($list, $fpage, count($total));								//返回分页列表，分页信息和查询总数
	}


   /**
    * 功能：自动生成邮箱地址
    * @author wxb
    * 日期：2013/10/31
    * */
    function act_createEmailField(){
    	$table ="power_global_user" ;
    	$filed = "global_user_id";
    	$where  = "  global_user_is_delete = 0 AND global_user_status = 1  ";
    	$ret = NoticeApiModel::selectOneTable($table, $filed, $where);
    	if(!$ret){
    		exit('search user fail');
    	}
    	$err = array();
    	$rightCount = 0;
    		foreach($ret as $val){
    			$where = " global_user_status = 1 AND global_user_is_delete = 0 AND  global_user_id = '{$val['global_user_id']}' ";
    			$loginName = NoticeApiModel::selectOneTable($table, 'global_user_login_name', $where);
    			if(!$loginName[0]['global_user_login_name']){
    				$err[]=$val['global_user_id'];
    				continue;
    			}
    			$loginName = $loginName[0]['global_user_login_name'];
    			$email = $loginName."@sailvan.com";
    			$res = UserModel::createEmailField ($email,$where);
    			if(!$res){
    				$err[]=$val['global_user_id'];
    			}else{
    				$rightCount++;
    			}
    		}
    	var_dump( count($ret),$rightCount,$err);
    }

   /**
    * 功能：展示所有用户
    * @author wxb
    * 日期：2013/11/21
    * */
    public function showNameList() {
    	$res 			= UserModel::showNameList();
    	self::$errCode 	= UserModel::$errCode;
    	self::$errMsg 	= UserModel::$errMsg;
    	return $res;
    }
}

?>