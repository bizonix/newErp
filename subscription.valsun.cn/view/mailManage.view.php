<?php
/*
 * 管理订阅邮件列表以及权限
 */
class MailManageView extends BaseView {
	/*
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
	}
	/*
	 * 查看全部邮件及其订阅权限view
	 */
	function view_showMailPower() {
		$pagesize			= 5;
		$where			    .= " ";
		$show				= new MailManageModel();
		$page_mount			= $show->pageSubject($where);
		$page_obj 			= new Page($page_mount, $pagesize);
		$resultInfo         = $show->showSameListPower($page_obj->limit);
		if ($page_mount > $pagesize) {       //分页
			$pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $page_obj->fpage(array(0, 2, 3));
		}
		/* 获取全部系统 */
		$getsystem			= new MailShowModel();
		$getSystem			= $getsystem->getAllSystem();
		$title				= '查看邮件订阅权限';
		$navTitle			= '邮件权限';
		$gl_cho				= 'setup cho';
		$dy_cho				= 'Subscription';
		$url				= 'index.php?mod=MailManage&act=showMailPower';
		$powerInfo			= array();
		$status				= array();
		foreach($resultInfo as $value) {
			foreach($value as $var) {
				$powerInfo[] = $var;
			}
		}
		foreach($powerInfo as $key=>$item) {
			if($powerInfo[$key]['list_id'] == $powerInfo[$key-1]['list_id']) {
				$powerInfo[$key]['list_name'] = '';
				if($powerInfo[$key]['company_id'] == $powerInfo[$key-1]['company_id']) {
					$powerInfo[$key]['company_name'] = '';
				}
				if($powerInfo[$key]['dept_id'] == $powerInfo[$key-1]['dept_id']) {
					$powerInfo[$key]['dept_name'] = '';
				}
				if($powerInfo[$key]['system_id'] == $powerInfo[$key-1]['system_id']) {
					$powerInfo[$key]['system_name'] = '';
				}
			}
			if($powerInfo[$key]['list_name'] == '') {
				$powerInfo[$key]['status'] = 0;
			} else {
				$powerInfo[$key]['status'] = 1;
			}
		}
		$this->smarty->assign("page_str", $pagestr);
		$this->smarty->assign("title", $title);
		$this->smarty->assign("navTitle", $navTitle);
		$this->smarty->assign("gl_cho", $gl_cho);
		$this->smarty->assign("dy_cho", $dy_cho);
		$this->smarty->assign("url", $url);
		$this->smarty->assign("powerInfo", $powerInfo);
		$this->smarty->assign("status", $status);
		$this->smarty->assign("getSystem", $getSystem);
		$this->smarty->display("powerShow.html");
	}
	
	/*
	 * 根据条件搜索显示邮件权限
	*/
	function view_getMailPowerByConditions() {
		$where			    .= ' ';
		$and				.= ' ';
		$title				= '查看邮件订阅权限';
		$navTitle			= '邮件权限';
		$gl_cho				= 'setup cho';
		$dy_cho				= 'Subscription';
		$systemId			= $_POST['system'];
		$mailName			= isset($_POST['mailName']) ? trim($_POST['mailName']) : '';
		if (!empty($systemId) && $systemId != 'default') {        //是否指定keywords
			$and 			.= " AND `mail_list`.`list_system_id` = '$systemId'";
		}
		if (!empty($mailName)) {        //是否指定keywords
			$and 			.= " AND `mail_list`.`list_name` LIKE '%$mailName%'";
		}
		$getPower			= new MailManageModel();
		/* 获取全部系统 */
		$getData			= new MailShowModel();
		$getSystem			= $getData->getAllSystem();
		$resultInfo         = $getPower->showMailPower($where, $and);
		$status				= array();
		foreach($resultInfo as $key=>$item) {
			if($resultInfo[$key]['list_id'] == $resultInfo[$key-1]['list_id']) {
				$resultInfo[$key]['list_name'] = '';
				if($resultInfo[$key]['company_id'] == $resultInfo[$key-1]['company_id']) {
					$resultInfo[$key]['company_name'] = '';
					if($resultInfo[$key]['dept_id'] == $resultInfo[$key-1]['dept_id']) {
						$resultInfo[$key]['dept_name'] = '';
					}
				}
				if($resultInfo[$key]['system_id'] == $resultInfo[$key-1]['system_id']) {
					$resultInfo[$key]['system_name'] = '';
				}
			}
			if($resultInfo[$key]['list_name'] == '') {
				$resultInfo[$key]['status'] = 0;
			} else {
				$resultInfo[$key]['status'] = 1;
			}
		}
		if(empty($resultInfo)) {
			$status		= 0;
		}else{
			$status		= 1;
		}
		$this->smarty->assign("title", $title);
		$this->smarty->assign("navTitle", $navTitle);
		$this->smarty->assign("gl_cho", $gl_cho);
		$this->smarty->assign("dy_cho", $dy_cho);
		$this->smarty->assign("resultInfo", $resultInfo);
		$this->smarty->assign("getSystem", $getSystem);
		$this->smarty->assign("status", $status);
		$this->smarty->display('powerShow.html');
	}
	
	/*
	 * 显示修改邮件内容页面View
	 */
	function view_modifyPower() {
		$getMailPower		= array();
		$getResult			= array();
		$getDept			= array();
		$getJob				= array();
		$system				= array();
		$gl_cho				= 'setup cho';
		$dy_cho				= 'Subscription';
		$title				= '编辑邮件权限';
		$navTitle			= '编辑邮件权限';
		//修改权限页面设置id自增变量
		$addCompany			= 1000;
		$addDept			= 1000;
		$addJob				= 1000;
		$delete				= 1000;
		$remove				= 1000;
		$addId				= 1000;
		$jobList			= 1000;
		$list_id			= addslashes($_GET['list_id']);
		$where				= " ";
		$and				= "AND `mail_list`.`list_id`='$list_id' AND `mail_power`.`power_list_id`='$list_id'";
		$show				= new MailManageModel();
		$showMail			= $show->showMailPower($where, $and);
		$showCompany		= $show->showCompany();
		/* 获取全部系统 */
		$getSys				= new MailShowModel();
		$getSystem			= $getSys->getAllSystem();
		foreach ($showMail as $key=>$item) {
			$mailName		= $item['list_name'];
			$mailDescript	= $item['list_description'];
			$mailEnglish	= $item['list_english_id'];
			if($showMail[$key]['company_id'] == $showMail[$key-1]['company_id']) {
				if($showMail[$key]['dept_id'] == $showMail[$key-1]['dept_id']) {
					if($showMail[$key]['list_system_id'] == $showMail[$key-1]['list_system_id']) {
						$getMailPower[$item['company_id'].$item['dept_id']]['company_id'] = $item['company_id'];
						$getMailPower[$item['company_id'].$item['dept_id']]['dept_id'] = $item['dept_id'];
						$getMailPower[$item['company_id'].$item['dept_id']]['list_system_id'] = $item['list_system_id'];
					} 
				}else {
						$getMailPower[$item['company_id'].$item['dept_id']]['company_id'] = $item['company_id'];
						$getMailPower[$item['company_id'].$item['dept_id']]['dept_id'] = $item['dept_id'];
						$getMailPower[$item['company_id'].$item['dept_id']]['list_system_id'] = $item['list_system_id'];
				}
			} else {
				$getMailPower[$item['company_id'].$item['dept_id']]['company_id'] = $item['company_id'];
				$getMailPower[$item['company_id'].$item['dept_id']]['dept_id'] = $item['dept_id'];
				$getMailPower[$item['company_id'].$item['dept_id']]['list_system_id'] = $item['list_system_id'];
			}
		}
		foreach ($showMail as $key=>$item) {
			foreach ($getMailPower as $keyvar=>$itemvar) {
				if($item['company_id'].$item['dept_id'] == $keyvar) {
					$getMailPower[$item['company_id'].$item['dept_id']]['job_id'][] = $item['job_id'];
				}
			}
			if($showMail[$key]['list_system_id'] != $showMail[$key-1]['list_system_id']) {
				$system[]						= $showMail[$key]['list_system_id'];
				$system[]						= $showMail[$key]['system_name'];
			}
		}
		foreach ($getMailPower as $keyget=>$itemget) {
			$getDept[$itemget['company_id']]	= $show->showDept($itemget['company_id']);
			$getJob[$itemget['dept_id']]		= $show->showJob($itemget['company_id'], $itemget['dept_id']);
		}
		$this->smarty->assign("mailName", $mailName);
		$this->smarty->assign("mailDescript", $mailDescript);
		$this->smarty->assign("mailEnglish", $mailEnglish);
		$this->smarty->assign("showMail", $showMail);
		$this->smarty->assign("gl_cho", $gl_cho);
		$this->smarty->assign("dy_cho", $dy_cho);
		$this->smarty->assign("title", $title);
		$this->smarty->assign("navTitle", $navTitle);
		$this->smarty->assign("showCompany", $showCompany);
		$this->smarty->assign("getMailPower", $getMailPower);
		$this->smarty->assign("system", $system);
		$this->smarty->assign("getSystem", $getSystem);
		$this->smarty->assign("getDept", $getDept);
		$this->smarty->assign("getJob", $getJob);
		$this->smarty->assign("addVar", $addCompany);
		$this->smarty->assign("addDept", $addDept);
		$this->smarty->assign("addJob", $addJob);
		$this->smarty->assign("delete", $delete);
		$this->smarty->assign("remove", $remove);
		$this->smarty->assign("addId", $addId);
		$this->smarty->assign("jobList", $jobList);
		$this->smarty->assign("list_id", $list_id);
		$this->smarty->display("modifyPower.html");
	}
	
	/*
	 * 查看邮件权限详情View
	 */
	function view_checkPower() {
		$pagesize			= 10;
		$gl_cho				= 'setup cho';
		$dy_cho				= 'Subscription';
		$title				= '查看邮件订阅权限详情';
		$navTitle			= '邮件权限详情';
		$list_id			= addslashes($_GET['list_id']);
		$where			    .= " ";
		$check				= new MailManageModel();
		$page_mount			= $check->mailDetail($where, $list_id);
		$page_obj 			= new Page($page_mount, $pagesize);
		$checkPower         = $check->checkPower($page_obj->limit, $list_id);
		if ($page_mount > $pagesize) {       //分页
			$pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$pagestr =  $page_obj->fpage(array(0, 2, 3));
		}
		$var				= 0;
		foreach($checkPower as $keyvar=>$itemvar) {
			if($checkPower[$keyvar]['power_list_id'] == $checkPower[$keyvar - 1]['power_list_id']) {
				if($var % 10 != 0) {
					$checkPower[$keyvar]['list_name'] = '';
				}
				if($checkPower[$keyvar]['company_id'] == $checkPower[$keyvar - 1]['company_id']) {
					if($var % 10 != 0) {
						$checkPower[$keyvar]['company_name'] = '';
					}
				}
				if($checkPower[$keyvar]['dept_id'] == $checkPower[$keyvar - 1]['dept_id']) {
					if($var % 10 != 0) {
						$checkPower[$keyvar]['dept_name'] = '';
					}
				}
				if($checkPower[$keyvar]['system_id'] == $checkPower[$keyvar - 1]['system_id']) {
					if($var % 10 != 0) {
						$checkPower[$keyvar]['system_name'] = '';
					}
				}
			}
			$var++;
			if($checkPower[$keyvar]['list_name'] == '') {
				$checkPower[$keyvar]['status'] = 0;
			} else {
				$checkPower[$keyvar]['status'] = 1;
			}
		}
		$this->smarty->assign("page_str", $pagestr);
		$this->smarty->assign("gl_cho", $gl_cho);
		$this->smarty->assign("dy_cho", $dy_cho);
		$this->smarty->assign("title", $title);
		$this->smarty->assign("navTitle", $navTitle);
		$this->smarty->assign("checkPower", $checkPower);
		$this->smarty->assign("list_id", $list_id);
		$this->smarty->display("checkPower.html");
	}
	
	/*
	 * 提交修改邮件权限内容View
	 */
	function view_modifyMailPower() {
		$split_job			= array();
		$list_name			= isset($_POST['mail_name']) ? trim($_POST['mail_name']) : '';
		$modtime			= time();
		$list_description	= isset($_POST['mail_descript']) ? trim($_POST['mail_descript']) : '';
		$list_english_id	= isset($_POST['mail_english']) ? trim($_POST['mail_english']) : '';
		$list_name			= htmlentities($list_name);
		$list_name			= addslashes($list_name);
		$list_description	= htmlentities($list_description);
		$list_description	= addslashes($list_description);
		$list_english_id	= htmlentities($list_english_id);
		$list_english_id	= addslashes($list_english_id);
		$jobInfo			= $_POST['jobs'];
		$systemInfo			= $_POST['system'];
		$list_id			= addslashes($_GET['list_id']);
		$addMail			= new MailManageModel();
		$addMailList		= $addMail->updateMail($list_id, $list_name, $modtime, $list_description, $systemInfo);
		$deletePower		= $addMail->deletePower($list_id);
		foreach($jobInfo as $value) {
			$split_job[]		= explode("_", $value);
		}
		foreach($split_job as $key=>$value) {
			$addMailChange		= $addMail->addMailChange($list_id, $split_job[$key][0], $split_job[$key][1], $split_job[$key][2], $modtime);
		}
		echo '<script>alert("操作成功！");</script>';
		echo '<script>location.href="index.php?mod=MailManage&act=showMailPower"</script>';
	}
	
	/*
	 * 增加邮件页面
	 */
	function view_showMailList() {
		$title				= '新增邮件';
		$navTitle			= '新增邮件';
		$gl_cho				= 'setup cho';
		$dy_cho				= 'Subscription';
		$show				= new MailManageModel();
		$showCompany		= $show->showCompany();
		// 获取所有系统 
		$showSystem			= new MailShowModel();
		$getSystem			= $showSystem->getAllSystem();
		$this->smarty->assign("title", $title);
		$this->smarty->assign("navTitle", $navTitle);
		$this->smarty->assign("gl_cho", $gl_cho);
		$this->smarty->assign("dy_cho", $dy_cho);
		$this->smarty->assign("showCompany", $showCompany);
		$this->smarty->assign("getSystem", $getSystem);
		$this->smarty->display("addMail.html");
	}
	
	/*
	 * 查询数据库邮件英文ID
	 */
	function view_checkEnglishId() {
		$check				= new MailManageModel();
		$checkEnglish		= $check->checkEnglishId();
		if($checkEnglish != NULL){
			$checkEnglishId		= json_encode($checkEnglish);
			echo $checkEnglishId;
		}else{
			return true;
		}
	}
	
	/*
	 * 显示公司
	 */
	function view_showCompany() {
		$show				= new MailManageModel();
		$showCompany		= $show->showCompany();
		$showCom			= json_encode($showCompany);
		echo $showCom;
	}
	
	/*
	 * 显示部门
	 */
	function view_showDept() {
		$company_id			= addslashes($_GET['company_id']);
		$show				= new MailManageModel();
		$showDept			= $show->showDept($company_id);
		$showComDept		= json_encode($showDept);
		echo $showComDept;
	}
	
	/*
	 * 显示岗位
	 */
	function view_showJob() {
		$company_id			= addslashes($_GET['company_id']);
		$dept_id			= addslashes($_GET['dept_id']);
		$show				= new MailManageModel();
		$showJob			= $show->showJob($company_id, $dept_id);
		$showDeptJob		= json_encode($showJob);
		echo $showDeptJob;
	}
	
	/*
	 * 添加邮件分类及权限View
	 */
	function view_addMailList() {
		$split_job			= array();
		$list_name			= isset($_POST['mail_name']) ? trim($_POST['mail_name']) : '';
		$modtime			= time();
		$list_description	= isset($_POST['mail_descript']) ? trim($_POST['mail_descript']) : '';
		$list_english_id	= isset($_POST['mail_english']) ? trim($_POST['mail_english']) : '';
		$list_name			= htmlentities($list_name);
		$list_name			= addslashes($list_name);
		$list_description	= htmlentities($list_description);
		$list_description	= addslashes($list_description);
		$list_english_id	= htmlentities($list_english_id);
		$list_english_id	= addslashes($list_english_id);
		$jobInfo			= $_POST['jobs'];
		$systemInfo			= $_POST['system'];
		foreach($jobInfo as $value) {
			$split_job[]		= explode("_", $value);
		}
		$addMail			= new MailManageModel();
		$addMailList		= $addMail->addMailList($list_name, $modtime, $list_description, $list_english_id, $systemInfo);
		foreach($split_job as $key=>$value) {
			$addMailPower		= $addMail->addMailPower($addMailList, $split_job[$key][0], $split_job[$key][1], $split_job[$key][2], $modtime);
		}
		if($addMailPower) {
			echo '<script>alert("操作成功！");</script>';
			echo '<script>location.href="index.php?mod=MailManage&act=showMailPower"</script>';
		}else{
			echo '<script>alert("操作失败！");</script>';
			header("Location: index.php?mod=MailManage&act=showMailList");
		}
	}
		
	/*
	 * 删除邮件View
	 */
	function view_deleteMail() {
		$list_id			= addslashes($_GET['list_id']);
		$delete				= new MailManageModel();
		$deletePower		= $delete->deleteMail($list_id);
	}
	
	/*
	 * 删除已订阅邮件用户View
	 */
	function view_deleteUser() {
		$list_id			= addslashes($_GET['list_id']);
		$user_id			= addslashes($_GET['user_id']);
		$delete				= new MailShowModel();
		$deleteUser			= $delete->cancelMailList($list_id, $user_id);
	}
	
	/*
	 * 管理员手动增加订阅用户页面
	 */
	function view_addUser() {
		$title				= '新增订阅用户';
		$navTitle			= '新增订阅用户';
		$gl_cho				= 'setup cho';
		$dy_cho				= 'Subscription';
		$list_id			= addslashes($_GET['list_id']);
		$where				= " ";
		$and				= "AND `mail_list`.`list_id`='$list_id' AND `mail_power`.`power_list_id`='$list_id'";
		$show				= new MailManageModel();
		//显示邮件信息
		$showMail			= $show->showMailPower($where, $and);
		//获取公司列表
		$showCompany		= $show->showCompany();
		// 获取所有系统
		$showSystem			= new MailShowModel();
		$getSystem			= $showSystem->getAllSystem();
		foreach ($showMail as $key=>$item) {
			$mailName		= $item['list_name'];
			$mailDescript	= $item['list_description'];
			$mailEnglish	= $item['list_english_id'];
			$mailSystem		= $item['system_name'];
		}
		$this->smarty->assign("title", $title);
		$this->smarty->assign("navTitle", $navTitle);
		$this->smarty->assign("gl_cho", $gl_cho);
		$this->smarty->assign("dy_cho", $dy_cho);
		$this->smarty->assign("showMail", $showMail);
		$this->smarty->assign("showCompany", $showCompany);
		$this->smarty->assign("getSystem", $getSystem);
		$this->smarty->assign("mailName", $mailName);
		$this->smarty->assign("mailDescript", $mailDescript);
		$this->smarty->assign("mailEnglish", $mailEnglish);
		$this->smarty->assign("mailSystem", $mailSystem);
		$this->smarty->assign("list_id", $list_id);
		$this->smarty->display("addMailUser.html");
	}
	
	/*
	 * 根据岗位显示人员View
	 */
	function view_showUsers() {
		$company			= addslashes($_GET['company']);
		$dept				= addslashes($_GET['dept']);
		$job				= addslashes($_GET['job']);
		$show				= new MailManageModel();
		$showUser			= $show->showUser($company, $dept, $job);
		$showUserInfo		= json_encode($showUser);
		echo $showUserInfo;
	}
	
	/*
	 * 检查新增订阅用户是否有权限
	 */
	function view_checkUserPower() {
		$checkData			= array();
		$list_id			= addslashes($_GET['list_id']);
		$company			= addslashes($_GET['company']);
		$dept				= addslashes($_GET['dept']);
		$job				= addslashes($_GET['job']);
		$check				= new MailManageModel();
		$checkUser			= $check->checkUserPower($list_id, $company, $dept, $job);
		if($checkUser == null) {
			$checkData['status']	= 0;
		}else{
			$checkData['status']	= 1;
		}
		$checkUserInfo		= json_encode($checkData);
		echo $checkUserInfo;
	}
	
	/*
	 * 提交新增订阅用户
	 */
	function view_addMailUser() {
		$data				= array();
		$checkData			= array();
		$list_id			= $_GET['list_id'];
		$user_id			= $_SESSION['globaluserid'];
		$users				= isset($_POST['users']) ? $_POST['users'] : '';
		$modtime			= time();
		if(empty($users)) {
			echo '<script>alert("订阅人未设置！");</script>';
			echo '<script>location.href="index.php?mod=MailManage&act=addUser&list_id='.$list_id.'"</script>';
			return;
		}else{
			foreach($users as $value) {
				$data[]			= explode("_", $value);
			}
		}
		$addUser			= new MailShowModel();
		$check				= new MailManageModel();
		foreach($data as $keyvar=>$itemvar) {
			$checkData		= $check->checkUserPower($list_id, $data[$keyvar][0], $data[$keyvar][1], $data[$keyvar][2]);
		}
		foreach($data as $key=>$item) {
			if(!empty($checkData)) {
				$addMailPower	= $addUser->addMailList($data[$key][3], $list_id, $modtime);
			}else{
				echo '<script>alert("请先设置权限！");</script>';
				echo '<script>location.href="index.php?mod=MailManage&act=checkPower&list_id='.$list_id.'"</script>';
				return;
			}
		}
		if($addMailPower) {
			echo '<script>alert("添加成功！");</script>';
			echo '<script>location.href="index.php?mod=MailManage&act=checkPower&list_id='.$list_id.'"</script>';
		}else{
			echo '<script>alert("添加失败！");</script>';
			echo '<script>location.href="index.php?mod=MailManage&act=checkPower&list_id='.$list_id.'"</script>';
		}
	}
	
	/*
	 * 新增订阅用户时可选择同时新增该岗位权限
	 */
	function view_addJobPower() {
		$addData			= array();
		$power_list_id		= $_GET['list_id'];
		$company			= addslashes($_GET['company']);
		$dept				= addslashes($_GET['dept']);
		$job				= addslashes($_GET['job']);
		$modtime			= time();
		$add				= new MailManageModel();
		$addPower			= $add->addMailPower($power_list_id, $company, $dept, $job, $modtime);
		/* 判断是否添加成功 */
		if($addPower == null) {
			$addData['status']	= 0;
		}else{
			$addData['status']	= 1;
		}
		$addUserInfo		= json_encode($addData);
		echo $addUserInfo;
	}
}