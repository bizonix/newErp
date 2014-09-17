<?php
/**
 * 名称：
 * 功能：基层类，加载smarty的基本设置,登陆判断
 * 版本：
 * 日期：
 * 作者：
 */
if(!isset($_SESSION)) {
	session_start();
}

class BaseView {
	public $smarty				=	null;
	protected $page 			=	1;
	public static $_username	=	"";
	public static $_userid		=	0;
	public static $_companyid	=	0;
	public static $_systemid	=	0;

	public function __construct() {
		$mod	= trim($_GET['mod']);
		$act	= trim($_GET['act']);
		if(C('IS_AUTH_ON') === true) {
        	if(!AuthUser::checkLogin($mod, $act)) {
				if(!$_SESSION[C("USER_AUTH_ID")]) {
					echo '<script language="javascript">
						  	self.location="index.php?mod=public&act=login";
						  </script>';
				} elseif($_SESSION[C("USER_AUTH_ID")]) {
					if(!empty($_GET['callback'])) {
						$callback = $_GET['callback'];
						exit($callback.'({"errCode":"176", "errMsg":"亲,您尚未有此权限"})');
						return false;
					}
					echo '<script language="javascript">
					        alert("亲,您尚未有此权限！");
					        history.back();
					      </script>';
				} else {
					if(!empty($_GET['callback'])) {
						$callback = $_GET['callback'];
						exit($callback.'({"errCode":"043", "errMsg":"亲,您还没有登录哦！"})');
						return false;
					}
					echo '<script language="javascript">
					        alert("亲,您还没有登录哦！");
					        self.location="index.php?mod=public&act=login";
					   	  </script>';
				}
        		exit;
        	}
        }

		self::$_username	= isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
		self::$_userid		= isset($_SESSION[C("USER_AUTH_ID")]) ? $_SESSION[C("USER_AUTH_ID")] : 0;
		self::$_companyid	= isset($_SESSION['companyId']) ? $_SESSION['companyId'] : 0;
		self::$_systemid	= C('AUTH_SYSTEM_ID');

		require(WEB_PATH.'lib/template/smarty/Smarty.class.php');
		$this->smarty 					= new Smarty;
		$this->smarty->template_dir 	= WEB_PATH.'html/template/';
		$this->smarty->compile_dir 		= WEB_PATH.'smarty/templates_c/';
		$this->smarty->config_dir 		= WEB_PATH.'smarty/configs/';
		$this->smarty->cache_dir 		= WEB_PATH.'smarty/cache/';
		$this->smarty->debugging 		= false;
		$this->smarty->caching 			= false;
		$this->smarty->cache_lifetime 	= 120;

		//初始化提交过来的变量（post and get）
		if(isset($_GET)) {
			foreach($_GET AS $gk=>$gv) {
				$this->smarty->assign('g_'.$gk, $gv);
			}
		}
		if(isset($_POST)) {
			foreach($_POST AS $pk=>$pv) {
				$this->smarty->assign('p_'.$pk, $pv);
			}
		}

        $this->smarty->assign('mod', $mod);							//模块权限
        $this->smarty->assign('act', $act);							//操作权限
        $this->smarty->assign('_username', self::$_username);
        $this->smarty->assign('_userid', self::$_userid);

		//初始化当前页码
		$this->page = isset($_GET['page'])&&intval($_GET['page'])>0 ? intval($_GET['page']) : 1;
		$this->smarty->assign("page", $this->page);
	}
}
?>