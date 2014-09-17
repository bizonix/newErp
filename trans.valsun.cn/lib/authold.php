<?php
/**
*类名：Auth
*功能：与鉴权系统交互
*作者：冯赛明
*版本：V1.2
*最后修改时间：2013-7-17
*/

class Auth{
	//private static $actionURL= 'http://localhost/html/api/json.php';//本地环境
	//private static $actionURL  = 'http://power.valsun.cn/api/json.php';//192.168.200.122正式环境
	private static $actionURL  = 'http://dev.power.valsun.cn/api/json.php';//192.168.200.222开发环境
	private static $systemName = 'Transportsys';//接口系统名称  类型：string
	private static $token      = '7b199966daac30778e9c1b6a08605b1f';//  类型：string
	public static $errCode	   = "0";
	public static $errMsg	   = "";
        public static $access;
	
	public function __construct()
	{
	}
	
        static public function setAccess($access){
		if(!empty($access) && is_array($access)){
			self::$access	=	$access;
		}
		return;
	}
        
	/*
	*功能：1、用户远程登录
	*参数为:用户名、密码
	*/
	public static function login($user_name='',$pwd='')
	{   
		$params='mod=LoginAct&act=login&userName='.$user_name.'&pwd='.$pwd;	
		$data=self::http(self::$actionURL,$params);
		if(!$data)
		{
			self::$errCode='0001';
			self::$errMsg ='Login error';
			self::showError();
			return false;
		}
		return $data;		
	}
	
	/*
	*功能：2、通过用户的token获取其操作权限
	*/
	public static function	getAccess($userToken='')
	{
		$data=Auth::getUserInfo($userToken);
		$result=json_decode($data,true);
		if(!empty($result['power']))
		{
			return $result['power'];
		}
		self::$errCode='0002';
		self::$errMsg ='Get access error';
		self::showError();
		return false;
	}	
	
	/*
	*功能：3、获取某个用户信息
	*/
	public static function getUserInfo($userToken='')
	{
		$param='mod=UserAct&act=getUserInfo&userToken='.$userToken;		
		$data=self::http(self::$actionURL,$param);
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode='0003';
			self::$errMsg ='Get user info error';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;			
		}
		return $data;
	}	
	
	/*
	*功能：4、获取你的系统所有用户信息
	*/
	public static function getAllUserInfo()
	{
		$param='mod=UserAct&act=getAllUserInfo';		
		$data=self::http(self::$actionURL,$param);
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode='0004';
			self::$errMsg ='Get all user info error';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;
		}
		return $data;
	}
	
	/*
	*功能：5、修改用户信息
	*参数：新的用户信息、用户token
	*/
	public static function updateUserInfo($newInfo,$userToken)
	{
		$param='mod=UserAct&act=updateUserInfo&newInfo='.$newInfo.'&userToken='.$userToken;		
		$data=self::http(self::$actionURL,$param);
		$rt=json_decode($data,true);
		if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;		
		}
		else if(!$data)
		{   
			self::$errCode = '0005';
			self::$errMsg  = 'Update user info error';
			self::showError();
			return false;			
		}
		return json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
	}	
	
	/*
	*功能：6、新增用户信息
	*参数：新的用户信息
	*/
	public static function addApiUser($newInfo)
	{
		$param='mod=UserAct&act=addApiUser&newInfo='.$newInfo;		
		$data=self::http(self::$actionURL,$param); 
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode='0006';
			self::$errMsg ='Add api user';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;			
		}
		return $data;
	}	
	
	/*
	*功能：7、删除用户信息
	*参数：要被删除的用户的token
	*/
	public static function deleteApiUser($userToken)
	{
		$param='mod=UserAct&act=deleteApiUser&userToken='.$userToken;		
		$data=self::http(self::$actionURL,$param); 
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode = '0007';
			self::$errMsg  = 'Delete api user error';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;			
		}
		return json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
	}	
	
	/*
	*功能：8、获取系统所有菜单信息
	*/
	public static function getApiMenus()
	{
		$param='mod=MenuAct&act=getApiMenus';		
		$data=self::http(self::$actionURL,$param);
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode='0008';
			self::$errMsg ='Get api menus error';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;
		}
		return $data;
	}
	
	/*
	*功能：9、修改系统某个菜单信息
	*参数：新的菜单信息、要修改的菜单编号
	*/
	public static function updateApiMenus($newMenus,$menuId)//json格式string类型、int类型
	{
		$param='mod=MenuAct&act=updateApiMenus&newMenus='.$newMenus.'&menuId='.$menuId;		
		$data=self::http(self::$actionURL,$param); 
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode = '0010';
			self::$errMsg  = 'Update api menus error';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;
		}
		return $data;
	}
	
	/*
	*功能：10、获取部门信息
	*/
	public static function getApiDept()
	{
		$param='mod=DeptAct&act=getApiDept';		
		$data=self::http(self::$actionURL,$param); 
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode = '0011';
			self::$errMsg  = 'Get api dept error';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;
		}		
		return $data;
	}
	
	/*
	* 功能：11、获取岗位信息
	*/
	public static function getApiJob()
	{
		$param='mod=JobAct&act=getApiJob';		
		$data=self::http(self::$actionURL,$param); 
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode = '0012';
			self::$errMsg  = 'Get api job error';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;
		}		
		return $data;
	}	
	
	/*
	*功能：12、获取公司信息
	*/
	public static function getApiCompany()
	{
		$param='mod=CompanyAct&act=getApiCompany';		
		$data=self::http(self::$actionURL,$param); 
		$rt=json_decode($data,true);
		if(!$data)
		{
			self::$errCode = '0013';
			self::$errMsg  = 'Get api company error';
			self::showError();
			return false;
		}
		else if(!empty($rt['errCode']) and $rt['errCode']>'0')
		{   
			self::$errCode = $rt['errCode'];
			self::$errMsg  = $rt['errMsg'];
			self::showError();
			return false;
		}		
		return $data;
	}	
	
	public function __call($name,$params)
	{
		echo '<br/>你调用的方法'.$name.'不存在<br/>';
	}
	
	public static function __callStatic($name,$params)
	{
		echo '<br/>你调用的方法'.$name.'不存在<br/>';
	}
	
	public function __get($var)
	{
		echo '<br/>你调用的属性'.$var.'不存在<br/>';
	}
	
	/*
	*方法功能：对参数实行md5加密
	*/
	public static function http($url,$urlPost)
	{
		$urlPost.='&systemName='.self::$systemName;//加上系统名称
		$key=md5('json.php?'.$urlPost.self::$token);//对参数加密
	    $urlPost.='&key='.$key;
		return(self::curl($url,$urlPost));		
	}
	
	/*
	*方法功能：远程传输数据
	*/
	public static function curl($url,$urlPost)
	{   
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,$url);//设置你要抓取的URL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);//设置CURL参数，要求结果保存到字符串还是输出到屏幕上
		curl_setopt($curl,CURLOPT_POST,1);//设置为POST提交
		curl_setopt($curl,CURLOPT_POSTFIELDS,$urlPost);//提交的参数
		$data=curl_exec($curl);//运行CURL，请求网页
		curl_close($curl);
		if($data)
		{
			return $data;
		}
		return false;			
	}	
	
	/*
	*功能：检查登录的用户是否拥有某个操作权限
	*参数：类名称、方法名称
	*/
	public static function	checkAccess($mod,$act)//string，string
	{	
		if(self::checkLogin())//判断用户是否登录
		{
			$userToken=$_SESSION['userToken'];
			$data=Auth::getAccess($userToken);
			$data=json_decode($data,true); 
			if(isset($data[$mod]))//判断用户传值过来的操作组名(也既是类名)是否存在
			{
				if(in_array($act,$data[$mod]))//判断用户的操作是否存在
				{
					return true;
				} else {
					self::$errCode = "0012";
					self::$errMsg  = "No power to access: ".$mod."->".$act;
				} 
			}else
			{
				self::$errCode = "0013";
				self::$errMsg  = "No ActionGroup ".$mod."->".$act;
			}
		}	
		return false;		
	}	
	
	/*
	*功能：判断用户是否登录，或者登录是否失效
	*/
	public static function checkLogin()
	{	
		if(!isset($_SESSION['userToken']))
		{
			self::$errCode = "0014";
			self::$errMsg  = "Please login first";
			self::showError();
			return false;
		}	
		return true;		
	}
	
	/*
	*功能：输出错误
	*/
	public static function showError(){
		exit (json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg)));
	}
	
	/*
	*用户登出
	*/
	public static function loginOut()
	{
		session_destroy();
	}
}
?>