<?php
/*
 * 用户登录
 */
class LoginAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";

	public function __construct(){	

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

		$_SESSION['userId']        = $loginInfo['userId']; 
		$_SESSION['userToken']     = $loginInfo['userToken'];       
		$_SESSION['lastLoginTime'] = $loginInfo['lastLoginTime'];
		$_SESSION['userName'] 	   = $username;
       /*
	   $where  = "and `username` = '$username'";
        $result = UserModel::getUserInfo($where);
		if(!empty($result)){
			$where1 = "and `username` = '$username'";
			$data = array(
				'userPowerId' => $loginInfo['userId']
			);
			UserModel::update($data,$where1);
		}else{
			$data = array(
				'userPowerId' => $loginInfo['userId'],
				'userName'    => $username,
			);
			UserModel::insertRow($data);
		}
		//存储缓存数据
		//UserCacheModel::userInfoCache($loginInfo['userToken'] ,$loginInfo['userId']);
		*/
		UserCacheModel::userInfoCache($loginInfo['userToken']);
		//UserCacheModel::goodsInfosCache("*", "sku='001'");
		return array('url' => 'index.php?mod=iqc&act=iqcList');
       
	}
	
	//退出登录
	public function act_logout()
	{
		UserModel::logout();
	}

		
}
?>