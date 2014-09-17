<?php

class LoginAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";

	public function __construct(){	
		@session_start();
	}
	//用户登录
    public function act_login(){
	    $errStr	  = "";
        $username = "";
        $password = "";
		
		if($_SERVER['REQUEST_METHOD'] == "POST") {
			$username = trim($_POST['username']);
			$password = trim($_POST['password']);          		
		} else {
			$username = trim($_GET['username']);
			$password = trim($_GET['password']);			
		}    

        $loginInfo	= Auth::login($username,$password);
		$loginInfo	= json_decode($loginInfo,true);

		if(isset($loginInfo['errCode'])) {
			return  array('errCode' => $loginInfo['errCode'], 'errMsg' => '用户名或者密码错误', 'data' => '');
		}
        $_SESSION['userId'] = getPersonIdByName($username);//userId存的是统一用户的id
		//$_SESSION['userId']        = $loginInfo['userId']; 
		$_SESSION['userToken']     = $loginInfo['userToken']; //userToken是分系统的token      
		$_SESSION['lastLoginTime'] = $loginInfo['lastLoginTime'];
		$_SESSION['username'] 	   = $username;
        //$where  = "and `username` = '$username'";
//        $result = UserModel::getUserInfo($where);
//		if(!empty($result)){
//			$where1 = "and `username` = '$username'";
//			$data = array(
//				'userPowerId' => $loginInfo['userId']
//			);
//			UserModel::update($data,$where1);
//		}else{
//			$data = array(
//				'userPowerId' => $loginInfo['userId'],
//				'userName'    => $username,
//			);
//			UserModel::insertRow($data);
//		}
		//存储缓存数据
		//UserCacheModel::userInfoCache($loginInfo['userToken'] ,$loginInfo['userId']);
		return array('url' => 'index.php?mod=goods&act=getGoodsList');        
       
	}
	
	//退出登录
	public function act_logout()
	{
		UserModel::logout();
	}

		
}
?>