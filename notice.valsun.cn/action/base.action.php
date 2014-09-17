<?php
/**
 * 功能：验证用户模块权限action
 * 作者：张志强
 * 时间：2014/08/20
 */
class BaseAction {
	public function __construct() {
		session_start();
		$mod	= trim($_GET['mod']);
		$act	= trim($_GET['act']);
		if(C('IS_AUTH_ON') === true) {
			if(!AuthUser::checkLogin($mod, $act)){
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
	}
}
?>