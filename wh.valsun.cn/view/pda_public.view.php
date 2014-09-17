<?php

class Pda_publicView extends Pda_commonView {
	
	function view_pda_login() {
		if(!isset($_SESSION['userName'])){
			$toptitle = "PDA登陆页面";
			$this->smarty->assign('toptitle', $toptitle);
			//$this->smarty->assign('curusername', $_SESSION['userCnName']);
        	$this->smarty->assign('action', $toptitle);
			$this->smarty->template_dir = WEB_PATH.'pda/html/';
			$this->smarty->display("pda_login.htm");
		}else {
			redirect_to("index.php?mod=pda_index&act=pda_index0");
		}
		

	}

	/*function view_pda_userLogin(){
		$result	= UserAct::act_userLogin();
	
		$this->smarty->display("pda_login.htm");
	}
	*/
	function view_pda_logout(){
		session_destroy();
		redirect_to("index.php?mod=pda_public&act=pda_login");
	}	
}
?>