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
        		if(!$_SESSION['sysUserId']){
					echo '<script language="javascript"> 
					        self.location="index.php?mod=public&act=login";
					   	  </script>';
				}elseif($_SESSION['sysUserId']) { 
                    echo '您尚未有此权限...';exit;
				}else {
                    echo '还未登陆!';exit;
				}
        		exit;
        	}
        }
		self::$_username	= isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
		self::$_userid		= isset($_SESSION['sysUserId']) ? $_SESSION['sysUserId'] : 0;
		self::$_companyid	= isset($_SESSION['companyId']) ? $_SESSION['companyId'] : 0;
		self::$_systemid	= C('AUTH_SYSTEM_ID');
		//初始化smarty
		require(WEB_PATH.'lib/template/smarty/Smarty.class.php');
		$this->smarty = new Smarty;
		$this->smarty->template_dir = WEB_PATH.'html/template/v1/';
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
		
		//初始化当前页码
		$this->page = isset($_GET['page'])&&intval($_GET['page'])>0 ? intval($_GET['page']) : 1;
		$this->smarty->assign("page", $this->page);
	}
}


?>