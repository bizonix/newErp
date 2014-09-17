<?php
//权限和角色验证

/******************************
 * 具体权限判断， 联系冯赛明
 */
class Auth{
	public static $access;
	public $userName;	
	public $userId;
	static $errCode	=	0;
	static $errMsg	=	"";

	public function __construct(){
		
	}

	static public function setAccess($access){
		if(!empty($access) && is_array($access)){
			self::$access	=	$access;
		}
		return;
	}
	
	/*************************************
	 *	获取用户的登录信息
	 *	return 
	 */
	public function	getLoginAttr(){
	}

	
	public function checkLogin(){
		if(empty($_SESSION['pics_userName']) || empty($_SESSION['pics_userid'])){
			self::$errCode	=	"7001";
			self::$errMsg	=	"no login";
			return false;
		}else{
			return true;
		}
	}

	public function logout(){
		//清空session
		setcookie("pics_userName", "", 0);
		setcookie("pics_userid", "", 0);
		unset($_SESSION['pics_userName']);
		unset($_SESSION['pics_userid']);

		//TODO net operation	到鉴权中心去退出
	}

	//登录验证
	public function login(){
		//TODO net operation	到鉴权中心去验证
		
		echo "xxxx";
	
	}
	
	/***************************************************
	 * 通过
	 */
	public function loginByToken($token){
		//处理逻辑待更新
		if(!empty($token)){
			$_SESSION['pics_userName']	=	"admin";
			$_SESSION['pics_userid']	=	"1";
		}
	}

	/***************************************************
	 *	检测action的操作权限
	 *	@param	string	$action	操作的action
	 *	@param	string	$operation	具体的操作
	 *	@return 允许true,	拒绝false
	 */
	public function checkAccess($action_group,$operation){
		$ret	=	$this->checkLogin();
		if(!$ret){
			//echo '您还没有登录系统，请先登录';
			return false;
		}else{
			//权限检查
			
			//2013-5-7 冯赛明 修改			
			if(in_array($action_group,$my_action['group']))
			{
				if(in_array($operation,$my_action['operation'][$action_group]))
				{
					return true;
				}
			}else{
				//echo '您无该权限';
				return false;
			}
			
			
			//if($action == "picture")return false;
			//else return true
		}
	}
}
?>