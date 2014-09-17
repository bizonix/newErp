<?php

class PublicView extends BaseView {
	function view_login() {
		if(!isset($_SESSION['userName'])) {						//mod by wxb 2013/11/11
			$res = NoticeApiAct::act_getAuthCompanyList();		//获取鉴权的公司列表
			$this->smarty->assign('lists',$res);
			$this->smarty->display('login.htm');
		} else {
			redirect_to(C('USER_GO_URL'));
		}
	}

	function view_userLogin() {
		$result	= UserAct::act_userLogin();
		echo trim($result);
	}

	function view_logout() {
		session_destroy();
		redirect_to("index.php?mod=public&act=login");
	}
}
?>