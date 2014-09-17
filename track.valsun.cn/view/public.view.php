<?php

class PublicView extends BaseView {
	
	//登录渲染
	function view_login() {
		if(!isset($_SESSION['userName'])) {
			$error	= isset($_GET['msg']) ? check_html($_GET['msg']) : "";
			$this->smarty->assign('error',$error); 
			$this->smarty->display('login.htm');
		} else {
			$ref	= empty($_GET['ref']) ? "" : rawurldecode($_GET['ref']);
			if (empty($ref)) {
				redirect_to(C('USER_GO_URL'));
			} else {
				redirect_to($ref);
			}
		}
	}
	
	//用户登录
	function view_userLogin(){
		$result	= UserAct::act_userLogin();
		echo trim($result);
	}
	
	//用户退出
	function view_logout(){
		session_destroy();
		$error	= isset($_GET['msg']) ? "&msg=".rawurldecode($_GET['msg']) : "";
		redirect_to("index.php?mod=public&act=login{$error}");
	}	
}
?>