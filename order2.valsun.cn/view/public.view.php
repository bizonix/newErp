<?php

class PublicView extends BaseView {
	
	public function view_login() {
		if(!isset($_SESSION['sysUserId'])){
			$this->smarty->display('login.htm');
		}else {
			if(isset($_COOKIE['now_url']) && $_COOKIE['now_url']){
		      	header("Location: {$_COOKIE['now_url']}");
    			exit;
		    }else{
		     	header("Location: ".C('USER_GO_URL'));
    			exit;
		    }
		}
	}
	
	public function view_userLogin(){
		$data = array();
		$loginfo = A('User')->act_userLogin();
		if (!empty($loginfo)){
			$prompt = array(200=>"登陆成功");
			$_SESSION['userToken'] 	   = $loginfo['userToken'];
			$_SESSION['sysUserId'] 	   = $loginfo['globalUserId'];		//统一用户系统ID
			$_SESSION['userId'] 	   = $loginfo['userId'];				//分系统用户ID
			$_SESSION['userName'] 	   = $loginfo['userName'];
			$_SESSION['companyId'] 	   = $loginfo['company'];
			$_SESSION['userCnName']    = $loginfo['userCnName'];
			$_SESSION['menul1']         = $loginfo['menul1']; 
			$_SESSION['menul2']         = $loginfo['menul2'];
			$_SESSION['menul3']         = $loginfo['menul3'];
		}else{
			$prompt = A('User')->act_getErrorMsg();
		}
		$this->ajaxReturn(array(), $prompt);
	}
	
	public function view_logout(){
		session_destroy();
		$this->success('退出成功', "index.php?mod=public&act=login");
	}	
}
?>