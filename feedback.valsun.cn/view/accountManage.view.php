<?php
class AccountManageView extends BaseView{
	public function view_accountList(){
		$accountManage    = new AccountManageAct();
		$userList		  = $accountManage->act_userList();
		$accountList      = $accountManage->act_accountList();
		$this->smarty->assign("userList",$userList);
		$this->smarty->assign("accountList",$accountList);
		$this->smarty->display("accountManage.htm");
	}
	public function view_userPowerSearch(){
		$username	      = $_GET['username']?post_check($_GET['username']):"";
		$accountManage    = new AccountManageAct();
		if(!empty($username)){
			$useraccount         = $accountManage->act_userPowerSearch("$username");
			$useraccountArr      = explode(",", $useraccount);
		}else{
			$useraccount       = "";
			$useraccountArr    = "";
		}
		$userList         = $accountManage->act_userList();
		$accountList      = $accountManage->act_accountList();
		$this->smarty->assign("userList",$userList);
		$this->smarty->assign("useraccount",$useraccount);
		$this->smarty->assign("useraccountArr",$useraccountArr);
		$this->smarty->assign("accountList",$accountList);
		$this->smarty->display("accountManage.htm");
	}
	public function view_userPowerSave(){
		$username	      = $_POST['username']?post_check($_POST['username']):"";
		$power            = $_POST['power']?post_check($_POST['power']):"";
		$accountManage    = new AccountManageAct();
		if(!empty($username)&&!empty($power)){
			$res    = $accountManage->act_userPowerSave();
			echo $res;
		}else{
			echo false;
		}
		exit;
	}
}
?>