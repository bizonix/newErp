<?php

if(!isset($_SESSION)){
    session_start();
}

include_once WEB_PATH.'action/user.action.php';
include_once WEB_PATH.'action/notice.action.php';

/**
 * 名称：NoticeView
 * 功能：查询消息纪录 视图层
 * 版本：V 1.0
 * 日期：2013/10/09
 * 作者： Ren da hai
 * */
class NoticeView extends BaseView {

    public function view_index() {
    }

    /**
     * 本类的公共方法
     * 功能：获取用用户列表变量并分配给视图
     */
    protected function view_comm() {
    	$allName = UserAct::showNameList();
    	$this->smarty->assign('allName', $allName);
    }

    /**
     *邮件发送记录
     */
    public function view_emailNoticeList() {
   	    $this->view_lang();
		$this->smarty->assign("title", "邮件发送记录");

        $where		= '';
        $isAdmin 	= $_SESSION['isAdmin'];								//管理员判断
        if($isAdmin == '0') {
             $where = " and `from_name` = '{$_SESSION['cnName']}' ";
        }

        $from_name 	= post_check($_GET['sender']);
        $to_name   	= post_check($_GET['receiver']);
        if($from_name != '') {
            $where .= " AND (`from_name` like '%{$from_name}%'  OR `from_login_name` like '%{$from_name}%')";
        }
        if($to_name != '') {
            $where .= " AND (`to_name`  like  '%{$to_name}%' OR `to_login_name`  like  '%{$to_name}%' )";
        }

        if(!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
			$starTime		= strtotime(trim($_GET['start_date'])." 00:00:00 ");
			$endTime		= strtotime(trim($_GET['end_date'])." 23:59:59 ");
			if($starTime > $endTime) {
				$temp 		= $starTime;
				$starTime 	= $endTime;
				$endTime 	= $temp;
			}
			$where .= " AND (addtime>{$starTime} OR addtime = {$starTime} ) AND (addtime < {$endTime} OR addtime = {$endTime})";
        }

        $perNum		= 20;
        $list		= NoticeAct::act_getEmailsPage($where, $perNum, "");

        self::view_comm();
        $this->smarty->assign("emailNoticelist", $list[0]);								//消息数据
	    $this->smarty->assign("pageIndex", $list[1]);									//导航条
       	$this->smarty->assign("searchResults", $list[2]);								//总数
		$this->smarty->display('emailNoticeList.htm');
    }

    /**
     * 邮件接收记录
     */
    public function view_emailNoticeList_receive() {
    	$this->view_lang();
    	$this->smarty->assign("title", "邮件接受记录");

    	$where		= '';
    	$isAdmin	= $_SESSION['isAdmin'];
    	if($isAdmin == '0') {
    		$where 	= " AND `to_name` = '{$_SESSION['cnName']}' ";
    	}
    	$from_name 	= post_check($_GET['sender']);
    	$to_name   	= post_check($_GET['receiver']);
    	if($from_name != '') {
    		$where .= " AND (`from_name` like '%{$from_name}%' OR `from_login_name` like '%{$from_name}%' )";
    	}
    	if($to_name != '') {
    		$where .= " AND (`to_name`  like  '%{$to_name}%' OR `to_login_name`  like  '%{$to_name}%') ";
    	}
    	if(!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    		$starTime		= strtotime(trim($_GET['start_date'])." 00:00:00 ");
    		$endTime		= strtotime(trim($_GET['end_date'])." 23:59:59 ");
    		if($starTime > $endTime) {
    			$temp		= $starTime;
    			$starTime	= $endTime;
    			$endTime	= $temp;
    		}
    		$where .= " AND (addtime>{$starTime} OR addtime = {$starTime} ) AND (addtime < {$endTime} OR addtime = {$endTime})";
    	}

    	$perNum		= 20;
    	$list		= NoticeAct::act_getEmailsPage($where, $perNum, "");
    	self::view_comm();
    	$this->smarty->assign("emailNoticelist", $list[0]);			//消息数据
    	$this->smarty->assign("pageIndex", $list[1]);				//导航条
    	$this->smarty->assign("searchResults", $list[2]);			//总数
    	$this->smarty->display('emailNoticeList.htm');
    }

    /**
     *短信发送记录
     */
    public function view_smsNoticeList() {
   	    $this->view_lang();
		$this->smarty->assign("title", "短信发送记录");

        $where 		= '';
        $isAdmin 	= $_SESSION['isAdmin'];
        if($isAdmin == '0') {
             $where = " and `from_name` = '{$_SESSION['cnName']}' ";
        }

        $from_name 	= post_check($_GET['sender']);
        $to_name   	= post_check($_GET['receiver']);
        if($from_name != '') {
            $where .= " AND ( `from_name` like '%{$from_name}%'  OR  `from_login_name` like '%{$from_name}%' )";
        }
        if($to_name != '') {
            $where .= " AND (`to_name` like '%{$to_name}%'  OR `to_login_name` like '%{$to_name}%' )";
        }

        if(!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
        	$starTime 		= strtotime(trim($_GET['start_date'])." 00:00:00 ");
        	$endTime 		= strtotime(trim($_GET['end_date'])." 23:59:59 ");
        	if($starTime > $endTime) {
        		$temp 		= $starTime;
        		$starTime 	= $endTime;
        		$endTime 	= $temp;
        	}
        	$where .= " AND (addtime>{$starTime} OR addtime = {$starTime} ) AND (addtime < {$endTime} OR addtime = {$endTime})";
        }

        $perNum		= 20;
        $list 		= NoticeAct::act_getSMSPage($where, $perNum, "");
       	self::view_comm();
		//var_dump($list[0]);
        $this->smarty->assign("smsNoticelist", $list[0]);					//消息数据
	    $this->smarty->assign("pageIndex", $list[1]);						//导航条
       	$this->smarty->assign("searchResults", $list[2]);					//总数
 		$this->smarty->display('smsNoticeList.htm');
    }

    /**
     * 短信接收记录
     */
    public function view_smsNoticeList_receive() {
    	$this->view_lang();
    	$this->smarty->assign("title", "短信接受记录");

    	$where 		= '';
    	$isAdmin 	= $_SESSION['isAdmin'];
    	if($isAdmin == '0') {
    		$where 	= " AND `to_name` = '{$_SESSION['cnName']}' ";
    	}

    	$from_name 	= post_check($_GET['sender']);
    	$to_name   	= post_check($_GET['receiver']);
    	if($from_name != '') {
    		$where .= " AND (`from_name` like '%{$from_name}%'  OR `from_login_name` like '%{$from_name}%' )";
    	}
    	if($to_name != '') {
    		$where .= " AND (`to_name` like '%{$to_name}%'  OR `to_login_name` like '%{$to_name}%' )";
    	}

    	if(!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    		$starTime 		= strtotime(trim($_GET['start_date'])." 00:00:00 ");
    		$endTime 		= strtotime(trim($_GET['end_date'])." 23:59:59 ");
    		if($starTime > $endTime) {
    			$temp 		= $starTime;
    			$starTime 	= $endTime;
    			$endTime 	= $temp;
    		}
    		$where .= " AND (addtime>{$starTime} OR addtime = {$starTime} ) AND (addtime < {$endTime} OR addtime = {$endTime})";
    	}

    	$perNum	= 20;
    	$list 	= NoticeAct::act_getSMSPage($where, $perNum, "");
    	self::view_comm();
    	$this->smarty->assign("smsNoticelist", $list[0]);				//消息数据
    	$this->smarty->assign("pageIndex", $list[1]);					//导航条
    	$this->smarty->assign("searchResults", $list[2]);				//总数
    	$this->smarty->display('smsNoticeList.htm');
    }

    private function view_lang() {
        $this->smarty->assign("sender", "发件人");
        $this->smarty->assign('receiver', '收件人');
   		$this->smarty->assign("state", '发送状态');
        $this->smarty->assign("contents", '内容');
    	$this->smarty->assign("time", '时间');
        $this->smarty->assign("title", "用户登录");
        $this->smarty->assign('search_word', '搜索联系人');
   		$this->smarty->assign("search_button", '搜索');
    	$this->smarty->assign("username", '姓名');
        $this->smarty->assign("phone", '手机');
        $this->smarty->assign("email", '邮箱');
        $this->smarty->assign("delete", '删除');
        $this->smarty->assign("deleting", '删除中...');
        $this->smarty->assign("delete_confirm", '确认要删除吗？');
        $this->smarty->assign("search_empty", '结果为空！');
        $this->smarty->assign("operat_failedMsg", '操作失败，错误信息:');
        $this->smarty->assign("operat_success", '操作成功！');
        $this->smarty->assign("select_item", '请选择要操作的项！');
        $this->smarty->assign("input_seach_condition", '请输入发件人或收件人！');
        $this->smarty->assign("logou_url", "index.php?mod=login&act=logout");
        $this->smarty->assign("lang_logOut_key", "退出");
     }
}
?>