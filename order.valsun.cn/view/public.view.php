<?php

class PublicView extends BaseView {
	
	function view_login() {
		if(!isset($_SESSION['userName'])){
			$companyInfo=Auth::getApiCompany();
			$companyInfo = json_decode($companyInfo,true);
			//var_dump($companyInfo); exit;
			$this->smarty->assign("companyInfo", $companyInfo);
			$this->smarty->display('login.htm');
		}else {
			if(isset($_COOKIE['now_url']) && $_COOKIE['now_url']){
		      redirect_to($_COOKIE['now_url']);
		    }else{
		      redirect_to(C('USER_GO_URL'));
		    }
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