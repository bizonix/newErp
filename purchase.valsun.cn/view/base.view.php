<?php
//view 层基类
if(!isset($_SESSION)){
	session_start();          
}
class BaseView{
	protected $page = 1;
	public $smarty	=	null;	//smarty
	public static $_username	= "";	
	public static $_userid		= 0; 
	public static $_companyid	= 0; 
	public static $_systemid	= 0; 
	
	public function __construct(){
		$mod	= trim($_GET['mod']);
		$act	= trim($_GET['act']);	
		if (C('IS_AUTH_ON')===true){
        	if (!AuthUser::checkLogin($mod, $act)){
				if(!$_SESSION[C("USER_AUTH_ID")]){
					echo '<script language="javascript"> 
					        self.location="index.php?mod=public&act=login";
					   </script>';
				}elseif($_SESSION[C("USER_AUTH_ID")]) {
					$res 		= AuthUser::fetchAuth();
					$user_mod 	= "";
					$user_act 	= "";
					//print_r($res);
					//exit;
					while (list($key, $val) = each($res)) {
						$user_mod	= $key;
						$user_act	= $val[0];
						break;
					}
					/*
					if (empty($user_mod) || empty($user_act)) {
							echo '<script language="javascript"> 
						        alert("亲,您尚未分配权限,请联系系统管理员分配！"); 
						        self.location="index.php?mod=public&act=logout";
								</script>';
					} else {
							echo '<script language="javascript"> 
						        alert("亲,您尚未有此权限,系统自动跳转到您有权限的页面！"); 
						        self.location="index.php?";
								</script>';
					}
					 */
				}else {
					echo '<script language="javascript"> 
					        alert("亲,您还没有登录哦！"); 
					        self.location="index.php?mod=public&act=login";
					   </script>';
				}
        	}
        }
		self::$_username	= isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
		self::$_userid		= isset($_SESSION[C("USER_AUTH_ID")]) ? $_SESSION[C("USER_AUTH_ID")] : 0;
		self::$_companyid	= isset($_SESSION['companyId']) ? $_SESSION['companyId'] : 0;
		self::$_systemid	= C('AUTH_SYSTEM_ID');
		//初始化smarty
		require(WEB_PATH.'lib/template/smarty/Smarty.class.php');
		$this->smarty = new Smarty;
		$this->smarty->template_dir = WEB_PATH.'html/template/';
		$this->smarty->compile_dir 	= WEB_PATH.'smarty/templates_c/';
		$this->smarty->config_dir 	= WEB_PATH.'smarty/configs/';
		$this->smarty->cache_dir 	= WEB_PATH.'smarty/cache/';
		$this->smarty->debugging 	= false;
		$this->smarty->caching 		= false;
		$this->smarty->cache_lifetime = 120;
		
		//初始化提交过来的变量（post and get）
		if (isset($_GET)){
			foreach ($_GET AS $gk=>$gv){
				$this->smarty->assign('g_'.$gk, $gv);
			}
		}
		if (isset($_POST)){
			foreach ($_POST AS $pk=>$pv){
				$this->smarty->assign('p_'.$pk, $pv);
			}
		}
        $this->smarty->assign('mod',$mod);//模块权限
        $this->smarty->assign('act',$act);//操作权限
        $this->smarty->assign('_username',self::$_username);
        $this->smarty->assign('_userid',self::$_userid);
		if(isset($_SESSION["userCnName"])){
			$this->smarty->assign('userCnName',$_SESSION["userCnName"]);
		}
		//初始化当前页码
		$this->page = isset($_GET['page'])&&intval($_GET['page'])>0 ? intval($_GET['page']) : 1;
		$this->smarty->assign("page", $this->page);
	}
}


?>
