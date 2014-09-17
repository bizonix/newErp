<?php

class PublicView extends BaseView {
	
	function view_login() {
		if(!isset($_SESSION['userName'])){
			$error	= isset($_GET['msg']) ? $_GET['msg'] : "";
			//$res = TransOpenApiAct::act_getAuthCompanyList();//获取鉴权的公司列表
			//$this->smarty->assign('lists',$res); 
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
	
	function view_userLogin(){
		$result	= UserAct::act_userLogin();
		echo trim($result);
	}
	
	function view_logout(){
		session_destroy();
		$error	= isset($_GET['msg']) ? "&msg=".rawurldecode($_GET['msg']) : "";
		redirect_to("index.php?mod=public&act=login{$error}");
	}	
}
?>