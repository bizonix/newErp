<?php
class UserView extends BaseView {

	public function __construct() {
		parent::__construct();
	}
    
    //授权管理首页列表
	public function view_index(){
        //面包屑
    	$navlist = array (
    			array ('url' => 'index.php?mod=user&act=index', 'title' => '授权管理'),
    			array ('url' => '#', 'title' => '用户列表'),
    	);
        $userList = A('User')->act_getUserLists(get_userid());
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '授权管理');
    	$this->smarty->assign('userList', $userList?$userList:array());
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('User'));
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('User'));
    	$this->smarty->display("userindex.htm");
	}
    
    //修改密码页面
	public function view_edit(){
		//面包屑
    	$navlist = array (
    			array ('url' => 'index.php?mod=user&act=index', 'title' => '授权管理'),
                array ('url' => '#', 'title' => '用户列表'),
    			array ('url' => 'index.php?mod=user&act=edit&uid='.$id, 'title' => '编辑'),
    	);
        $userInfo = A('User')->act_getUserInfoById(get_userid());
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '编辑');
    	$this->smarty->assign('userInfo', $userInfo);
    	$this->smarty->display("userModify.htm");
	}
    
    //提交修改密码页面
	public function view_editOn(){
		$this->ajaxReturn(A('User')->act_updateUserPsw(), A('User')->act_getErrorMsg());
	}
}
?>