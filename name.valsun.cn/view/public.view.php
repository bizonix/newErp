<?php

class PublicView extends BaseView {
	
	function view_login() {
		if(!isset($_SESSION['userName'])){
			$this->smarty->display('login.htm');
		}else {
			redirect_to(C('USER_GO_URL'));
		}
	}
	
	function view_userLogin(){
		$result	= UserAct::act_userLogin();
		echo trim($result);
	}
	
	function view_logout(){
		session_destroy();
		redirect_to("index.php?mod=public&act=login");
	}	
}
?>