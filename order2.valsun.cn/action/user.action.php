<?php
/*
 * 类名：User
 * 功能：读取用户信息
 * 版本：2013-08-08
 * 作者：林正祥
 * 修改：linzhengxiang @ 20140522
 */

class UserAct extends CheckAct{

	static $debug	  = false;

	static $_instance;
	private $is_count = false;

	public function __construct(){
		self::$debug = C('IS_DEBUG');
	}

	/**
	 * 外接系统获取单个用户信息
	 * @param unknown_type $userid
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

   /**
	 * 获取登陆人可见权限人员记录
	 * @return array()
	 * @author zqt
	 */
	public function act_getUserLists($condition=array(), $sort='', $limit=''){
		$userList = M('InterfacePower')->getUserList(get_userid(), $page=1, $num=500);
        //var_dump($userList);
        return $userList;
	}

    /**
	 * 修改指定用户的密码
	 * @param userId POST过来的被修改人id
	 * @param pwd 新密码
	 * @return array()
	 * @author zqt
	 */
	public function act_updateUserPsw(){
	    $userId2 = $_POST['userId'];//被修改人密码
        $pwd = $_POST['pwd'];//密码
		$data = M('InterfacePower')->userUpdatePsw(get_userid(), $userId2, $pwd);
        if(empty($data)){
            self::$errMsg[10052] = get_promptmsg(10052);
        }else{
            self::$errMsg[$data['errCode']] = $data['errMsg'];
        }
        return $data;
	}

    /**
	 * 获取指定uid用户的信息
	 * @param uid GET传过来的uid
	 * @return array()
	 * @author zqt
	 */
	public function act_getUserInfoById(){

		$userInfo = M('InterfacePower')->getUserInfo($_GET['uid']);
        print_r($userInfo);
        return $userInfo;
	}

	private function _checkReturnData($data, $errreturn){
		if ($data===false){
			self::$errCode = UserModel::$errCode;
			self::$errMsg  = UserModel::$errMsg;
			return $errreturn;
		}elseif (empty($data)){
			self::$errCode = 5806;
			self::$errMsg  = 'There is no data!';
			/*if (self::$debug===true){
				self::$errMsg .= 'The SQL is '.UserModel::$errMsg;
			}*/
			return $errreturn;
		}else {
			self::$errCode = 1;
			self::$errMsg  = 'success';
			return $data;
		}
	}

	/**
	 * 用户登录act
	 * @return bool
	 * @author lzx 
	 * modify by yxd 2014-07-05
	 */
	public function act_userLogin(){
		if(!isset($_POST['username']) || trim($_POST['username']) == ''){
			self::$errMsg[10030] = get_promptmsg(10030);
			return false;
		}
		if(!isset($_POST['password']) || trim($_POST['password']) == ''){
			self::$errMsg[10031] = get_promptmsg(10031);
			return false;
		}
        $loginfo        = M('User')->userLogin($_POST['username'], $_POST['password']);
        
        /*###############################  新增获取导航权限代码#################################### */
        
        $user_power     = $loginfo['user_power'];//用户权限数组格式
        $menul1         = array();//一级菜单
        $menul2         = array();//二级菜单
        $menul3         = array();//三级菜单 
        $user_power       = M('topmenu')->getTopmenuLists(array('is_delete'=>array('$e'=>0)),1,500);//测试用所有权限
        foreach($user_power as $key){
       // foreach($user_power as $key=>$value){//权限和导航树组装
        	$menuInfo    = M('topmenu')->getMenuByModel($key['model']);
        	$position    = $menuInfo[0]['position'];
        	$sort        = $menuInfo[0]['sort'];
        	$pid         = $menuInfo[0]['pid'];
        	if($position==2){
        		$menul2[$pid.$sort]      = $menuInfo[0];//二级导航数组
        		$topMenuInfo        = M('topmenu')->getModelBypid($pid);//通过二级导航找一级导航
        		$topmodel           = $topMenuInfo[0]['model'];
        		$topposition        = $topMenuInfo[0]['position'];
        		$topsort            = $topMenuInfo[0]['sort'];
        		$toppid             = $topMenuInfo[0]['pid'];
        		if($topposition==1)
               $menul1[$topsort]    = $topMenuInfo[0];//一级导航数组
        	}
        	if($position==3){
        		$menul3[$sort]      = $menuInfo[0];
        	}
        }
        foreach($user_power as $key){
        	$menuInfo    = M('topmenu')->getMenuByModel($key['model']);
        	$position    = $menuInfo[0]['position'];
        	$sort        = $menuInfo[0]['sort'];
        	$pid         = $menuInfo[0]['pid'];
        	if($position==1){
        		if(!array_key_exists($sort, $menul1))
        		      $menul1[$sort]      = $menuInfo[0];
        	}
        }
        
        $Kmenu    = array();
        ksort($menul1);//导航排序
        ksort($menul2);
        ksort($menul3);
        $loginfo['menul1']    = $menul1;
        $loginfo['menul2']    = $menul2;
        $loginfo['menul3']    = $menul3;
		/*########################## end 新增获取导航权限代码#############################*/
        if (empty($loginfo)){
        	self::$errMsg = M('User')->getErrorMsg();
        	return false;
        }
        return $loginfo;
    }

	/**
	 * UserAct::act_insert()
	 * 新增用户act
	 * @return bool
	 */
	public function act_insert(){
		if(!isset($_POST['username']) || trim($_POST['username']) == ''){
			self::$errMsg[10035] = get_promptmsg(10035);
			return false;
		}
		if(!isset($_POST['password']) || trim($_POST['password']) == ''){
			self::$errMsg[10036] = get_promptmsg(10036);
			return false;
		}
		$username		= addslashes($_POST['username']);
		$password		= addslashes($_POST['password']);
		$jobno 	 		= isset($_POST['jobno']) ? addslashes(trim($_POST['jobno'])) :'';
		$phone	 		= isset($_POST['phone']) ? addslashes(trim($_POST['phone'])) : '';
		$email	 		= isset($_POST['email']) ? addslashes(trim($_POST['email'])) : '';
		$userdept 		= intval($_POST['userdept']);
		$independence 	= intval($_POST['independence']);
		$stat			= intval($_POST['stat']);
		$userjob 		= explode("|",$_POST['userjob']);
		$grantDate		= post_check(trim($_POST['grantDate']));
		$effectiveDate	= intval($_POST['effectiveDate']);
		$power			= get_usercompanyid();
		$newInfo		= array(
							'userName'           => $username,//用户名，类型 string，必须项
							'pwd'                => $password,//密码，类型 string，必须项
							'jobNo'              => $jobno,//工号,类型string(10)，可选项
							'email'              => $email,//Email,类型string(80)，可选项
							'phone'              => $phone,//联系电话,类型string(20)，可选项
							'menuPower'          => '',//菜单权限，json格式string类型，可以为空
							'status'             => $stat,//状态，值为'1'表示状态有效，值为'0'表示状态无效，必须项
							'independence'       => $independence,//权限类型，值为'1'表示独立权限，值为'0'表示共享权限，必须项
							'power'              => $power,//操作权限，json格式string类型，必须项
							'jobPower'           => intval($userjob[0]),//所属的岗位权限编号，类型int(8)，必须项
							'tokenGrantDate'     => $grantDate,//用户token的授权日期，类型date()，必须项
							'TokenEffectiveDate' => $effectiveDate,//用户token的有效天数，类型int(10)，必须项
							'company' 			 => '1',//公司，类型int(10)，必须项
							'dept' 				 => $userdept,//部门，类型int(10)(10)，必须项
							'job' 				 => intval($userjob[1]),//岗位编号，类型int(10)，必须项
						);
        $result			= M('User')->userInsert($newInfo);
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
		$groupname	= ActionModel::actionGroupList("12");//读取系统的actiongroup列表
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
        $result		= M('User')->userUpdate($newInfo,$userToken);
		return $result;
    }

	/**
	 * UserAct::act_delete()
	 * 删除用户act
	 * @return bool
	 */
	public function act_delete(){
		$userid		= intval($_POST['userid']);
		if(empty($userid)){
			self::$errMsg[10015] = get_promptmsg(10015);
			return false;
		}
		return M('User')->userDelete(get_usertokenbyid($userid));
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
			echo json_encode($result[$i]['power']),"<br/>";
		}
		print_r($result);
		exit;
	}
}
?>