<?php
class MailShowView extends BaseView {
	public static $errCode	= 0;
	public static $errMsg	= '';
	
	/*
	 * 构造函数
	*/
	public function __construct(){
		parent::__construct();
	}
	
	/*
	 * 显示用户已订阅邮件View
	 * 显示用户可订阅邮件View
	*/
	function view_showUserMail() {
		$username			= $_SESSION['userName'];
		$user_id			= $_SESSION['globaluserid'];
		$pagesize			= 7;
		$where				= " ";
		$title				= '邮件订阅系统';
		$navTitle			= '邮件订阅';
		$dy_cho				= 'cho Subscription';
		$gl_cho				= 'setup';
		$url				= 'index.php?mod=MailShow&act=showUserMail';
		$showMail			= new MailShowModel();
		/* 显示用户已订阅邮件 */
		$showUserMail		= $showMail->showUserMail($user_id);
		/* 获取全部系统 */
		$getSystem			= $showMail->getAllSystem();
		/* 分页  */
		$userMail			= $showMail->userMail($where, $user_id);
		$page_obj 			= new Page($userMail, $pagesize);
		/* 显示用户可订阅邮件并分页 */
		$showMailList         = $showMail->showMailList($user_id, $page_obj->limit);
		if ($userMail > $pagesize) {       //分页
			$pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $page_obj->fpage(array(0, 2, 3));
		}
		foreach($showMailList as $key=>$item){
			if(array_key_exists($item['power_list_id'], $showUserMail)) {
				$showMailList[$key]['issubscript']	= 1;
			}else{
				$showMailList[$key]['issubscript']	= 0;
			}
		}
		$this->smarty->assign("username", $username);
		$this->smarty->assign("title", $title);
		$this->smarty->assign("navTitle", $navTitle);
		$this->smarty->assign("dy_cho", $dy_cho);
		$this->smarty->assign("gl_cho", $gl_cho);
		$this->smarty->assign("url", $url);
		$this->smarty->assign("showUserMail", $showUserMail);
		$this->smarty->assign("showMailList", $showMailList);
		$this->smarty->assign("getSystem", $getSystem);
		$this->smarty->assign("pagestr", $pagestr);
		$this->smarty->display('index.html');
	}
	
	/*
	 * 用户添加订阅内容View
	 */
	function view_addMailList() {
		$username			= $_SESSION['userName'];
		$user_name_id		= $_SESSION['globaluserid'];
		$user_list_id		= addslashes($_GET['list_id']);
		$user_modtime		= time();
		$addMail			= new MailShowModel();
		$addMailList		= $addMail->addMailList($user_name_id, $user_list_id, $user_modtime);
		$this->smarty->display('index.html');
	}
	
	/*
	 * 用户取消订阅内容View
	 */
	function view_cancelMailList() {
		$username			= $_SESSION['userName'];
		$user_id			= $_SESSION['globaluserid'];
		$list_id			= addslashes($_GET['list_id']);
		$cancelMail			= new MailShowModel();
		$cancelMailList		= $cancelMail->cancelMailList($list_id, $user_id);
		$this->smarty->display('index.html');
	}
	
	/*
	 * 用户根据条件搜索邮件View
	 */
	function view_getUserMailByCondition() {
		$title				= '邮件订阅系统';
		$navTitle			= '邮件订阅';
		$dy_cho				= 'cho Subscription';
		$gl_cho				= 'setup';
		$user_id			= $_SESSION['globaluserid'];
		$pagesize			= 7;
		$where				= " ";
		$wheresql			= " ";
		$and				= " ";
		$systemId			= $_POST['system'];
		$mailName			= isset($_POST['mailName']) ? trim($_POST['mailName']) : '';
		if (!empty($systemId) && $systemId != 'default') {        //是否指定keywords
			$and 			.= " AND `mail_list`.`list_system_id` = '$systemId'";
		}
		if (!empty($mailName)) {        //是否指定keywords
			$and 			.= " AND `mail_list`.`list_name` LIKE '%$mailName%'";
		}
		$getMail			= new MailShowModel();
		//$getUserMail		= $getMail->getUserMailByCondition($where, $user_id);
		/* 分页  */
		$userMail			= $getMail->userMail($and, $user_id);
		$page_obj 			= new Page($userMail, $pagesize);
		/* 显示用户可订阅邮件并分页 */
		$getUserMail        = $getMail->getUserMailByCondition($and, $page_obj->limit, $user_id);
		/* 显示用户已订阅邮件 */
		$showUserMail		= $getMail->showUserMail($user_id);
		/* 获取全部系统 */
		$getSystem			= $getMail->getAllSystem();
		/* 显示用户可订阅邮件并分页 */
		$showMailList         = $getMail->showMailList($user_id, $wheresql);
		if ($showMailList > $pagesize) {       //分页
			$pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $page_obj->fpage(array(0, 2, 3));
		}
		foreach($getUserMail as $key=>$item){
			if(array_key_exists($item['power_list_id'], $showUserMail)) {
				$getUserMail[$key]['issubscript']	= 1;
			}else{
				$getUserMail[$key]['issubscript']	= 0;
			}
		}
		$this->smarty->assign("title", $title);
		$this->smarty->assign("navTitle", $navTitle);
		$this->smarty->assign("dy_cho", $dy_cho);
		$this->smarty->assign("gl_cho", $gl_cho);
		$this->smarty->assign("showUserMail", $showUserMail);
		$this->smarty->assign('getUserMail', $getUserMail);
		$this->smarty->assign("getSystem", $getSystem);
		$this->smarty->assign("page_str", $pagestr);
		$this->smarty->display('index.html');
	}
}