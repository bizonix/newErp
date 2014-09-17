<?php
//view 层基类
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
				if(!$_SESSION['userId']){
					echo '<script language="javascript"> 
					        self.location="index.php?mod=public&act=login";
					   </script>';
				}elseif($_SESSION['userId']) {
					$AccessList = AuthUser::getAccessList();
					if(empty($AccessList)){
						echo '<script language="javascript">
					        alert("亲,您尚未设置系统权限！");
					        self.location="index.php?mod=public&act=logout";
					   		 </script>';
						//header('Location: index.php?mod=public&act=logout');
						exit;
					}else{
						$slice_AccessList = array_slice($AccessList,0,1);
						foreach($slice_AccessList as $akey => $aValue){
							$relocation = 'index.php?mod='.$akey.'&act='.$aValue[0];
						}
					echo '<script language="javascript">
					        alert("亲,您尚未有此权限！");
					        self.location="'.$relocation.'";
					   </script>';
					}
				}else {
					echo '<script language="javascript"> 
					        alert("亲,您还没有登录哦！"); 
					        self.location="index.php?mod=public&act=login";
					   </script>';
				}
        		exit;
        	}
        }
		if(!in_array($act, array('login', 'logout', 'userLogin'))){
            $now_url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; //记录当前页面url
            setcookie('now_url', $now_url, time()+86400);
            //print_r($_COOKIE['now_url']);exit;
        }
		//self::$_username	= isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
		self::$_username	= isset($_SESSION['userCnName']) ? $_SESSION['userCnName'] : "";
		self::$_userid		= isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
		self::$_companyid	= isset($_SESSION['companyId']) ? $_SESSION['companyId'] : 0;
		self::$_systemid	= '12';

		//初始化smarty
		require(WEB_PATH.'lib/template/smarty/Smarty.class.php');
		$this->smarty = new Smarty;
		$this->smarty->template_dir = WEB_PATH.'html/template/v1'.DIRECTORY_SEPARATOR;
		$this->smarty->compile_dir 	= WEB_PATH.'smarty/templates_c'.DIRECTORY_SEPARATOR;
		$this->smarty->config_dir 	= WEB_PATH.'smarty/configs'.DIRECTORY_SEPARATOR;
		$this->smarty->cache_dir 	= WEB_PATH.'smarty/cache'.DIRECTORY_SEPARATOR;
		$this->smarty->debugging 	= false;
		$this->smarty->caching 		= false;
		$this->smarty->cache_lifetime = 120;
		$this->smarty->assign('curusername',$_SESSION['userName']); //设置当前用户名
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