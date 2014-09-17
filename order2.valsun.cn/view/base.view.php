<?php
//view 层基类
class BaseView{

	protected $page 		= 1;
	protected $smarty		= '';
	protected $_username	= '';	
	protected $_userid		= 0; 
	protected $_companyid	= 0; 
	protected $_systemid	= 0;
	protected $waitSecond	= 2;
	
	public function __construct(){
		$mod	= trim($_GET['mod']);
		$act	= trim($_GET['act']);
		####################  smarty初始化 start ####################
		require(WEB_PATH.'lib/template/smarty/Smarty.class.php');
		$this->smarty = new Smarty;
		$this->smarty->template_dir = WEB_PATH.'html/template/v1'.DIRECTORY_SEPARATOR;
		$this->smarty->compile_dir 	= WEB_PATH.'smarty/templates_c'.DIRECTORY_SEPARATOR;
		$this->smarty->config_dir 	= WEB_PATH.'smarty/configs'.DIRECTORY_SEPARATOR;
		$this->smarty->cache_dir 	= WEB_PATH.'smarty/cache'.DIRECTORY_SEPARATOR;
		$this->smarty->debugging 	= false;
		$this->smarty->caching 		= false;
		$this->smarty->cache_lifetime = 120;
		####################  smarty初始化  end ####################
		
		if (C('IS_AUTH_ON')===true){
        	if (AuthUser::checkLogin($mod, $act)){
				//$this->error('请先登录', 'index.php?mod=public&act=login');
        	}
        }
        
		if(!in_array($act, array('login', 'logout', 'userLogin'))){
            $now_url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; //记录当前页面url
            setcookie('now_url', $now_url, time()+86400);
        }
        $this->_menul1      = isset($_SESSION['menul1']) ? $_SESSION['menul1'] :array();
        $this->_menul2      = isset($_SESSION['menul2']) ? $_SESSION['menul2'] :array();
        $this->_menul3      = isset($_SESSION['menul3']) ? $_SESSION['menul3'] :array();
		$this->_username	= isset($_SESSION['userCnName']) ? $_SESSION['userCnName'] : "xx";
		$this->_userid		= isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;
		$this->_companyid	= isset($_SESSION['companyId']) ? $_SESSION['companyId'] : 0;
		$this->_systemid	= '12';

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
		$this->smarty->assign('curusername',$_SESSION['userName']); //设置当前用户名
        $this->smarty->assign('mod', $mod);//模块权限
        $this->smarty->assign('act', $act);//操作权限
        $this->smarty->assign('_username', $this->_username);
		$this->smarty->assign('_userid', $this->_userid);
		$this->smarty->assign('menul1', $this->_menul1);
		$this->smarty->assign('menul2', $this->_menul2);
		$this->smarty->assign('menul3', $this->_menul3); 
		//初始化当前页码
		$this->page = isset($_GET['page'])&&intval($_GET['page'])>0 ? intval($_GET['page']) : 1;
		$this->smarty->assign("page", $this->page);
	}
	
	protected function ajaxReturn($data, $prompt){
		header('Content-Type:application/json; charset=utf-8');
		$result = array();
		foreach ($prompt AS $key=>$msg){
			$result['errCode'] = $key;
			$result['errMsg'] .= $msg.'<br>';
		}
		$result['data'] = $data;
		exit(json_encode($result));
	}
	
	/**
     * 操作错误跳转的快捷方法
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param int $waitSecond 等待时间
     * @return void
     * @author lzx
     */
	protected function error($message, $jumpUrl='', $waitSecond=3) {
        $this->dispatchJump($message, 0, $jumpUrl, $waitSecond);
    }

    /**
     * 操作成功跳转的快捷方法
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param int $waitSecond 等待时间
     * @return void
     * @author lzx
     */
    protected function success($message, $jumpUrl='', $waitSecond=3) {
        $this->dispatchJump($message, 1, $jumpUrl, $waitSecond);
    }
    
	/**
     * 无论操作是否成功都不跳转
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @return void
     * @author lzx
     */
    protected function notJump($message,$jumpUrl='') {
        $this->dispatchJump($message,2,$jumpUrl);
    }
    
	/**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     * @param string $message 提示信息
     * @param Boolean $status 状态
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @access private
     * @return void
     */
    private function dispatchJump($message,$status=1,$jumpUrl='', $waitSecond=3) {

        if(!empty($jumpUrl)) $this->smarty->assign('jumpUrl',$jumpUrl);
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        $this->smarty->assign('status',$status);   // 状态
        if($status==1) {
            $this->smarty->assign('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!isset($this->smarty->waitSecond))    $this->smarty->assign('waitSecond','3');
            // 默认操作成功自动返回操作前页面
            if(!isset($jumpUrl)) $this->smarty->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->smarty->display('success_jump.htm');
        }else if($status==1){
        	$this->smarty->assign('message',$message);													// 提示信息
            if(!isset($jumpUrl)) $this->smarty->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);				// 默认操作成功自动返回操作前页面
            $this->smarty->display('success_jump.htm');
        }else{
            $this->smarty->assign('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            $this->smarty->assign('waitSecond', $waitSecond);
            // 默认发生错误的话自动返回上页
            if(!isset($jumpUrl)) $this->smarty->assign('jumpUrl',"javascript:history.back(-1);");
            $this->smarty->display('error_jump.htm');
        }
        exit;			// 中止执行  避免出错后继续执行
    }
}
?>