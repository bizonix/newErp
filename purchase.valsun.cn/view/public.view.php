<?php

class PublicView extends BaseView {
	
	function view_login() {
		if(!isset($_SESSION['userName'])){
			$api = new ApiAct();
			$res = $api->act_getAuthCompanyList();//获取鉴权的公司列表
			$this->smarty->assign('lists',$res); 
			$this->smarty->display('login.htm');
		}else {
			//echo C('USER_GO_URL'));
			redirect_to(C('USER_GO_URL'));
		}
	}
	
	function view_userLogin(){
		$user = new UserAct();
		$result	= $user->act_userLogin();
		echo trim($result);
	}
	
	function view_logout(){
		session_destroy();
		$nowUrl = urlencode($_SERVER['REQUEST_URI']);
		redirect_to("index.php?mod=public&act=login&ref={$nowUrl}");
	}	
}
?>
