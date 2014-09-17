<?php
/**
* 订单流程管理
*add by:yxd
*/
class StatusMenuView extends BaseView{
	/**
	 * 
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}
	
	public function view_index(){
		//面包屑
		f('order');
    	$navlist = array (
    			array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
    			array ('url' => '#', 'title' => '订单流程管理'),
    	);
    	$statusMenuList = M('StatusMenu')->getStatusMenuIndentList();
    	
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('StatusMenu') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('StatusMenu'));
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '订单流程管理');
    	$this->smarty->assign('statusMenuList', $statusMenuList); //循环列表
    	$this->smarty->assign('groupList', A('StatusMenu')->act_getMenuGroupList($statusMenuList));//状态名称数组
    	$this->smarty->display("statusMenuindex.htm");
	}
	
	public function view_edit(){
		$navlist = array (
				array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
				array ('url' => '#', 'title' => '订单流程管理'),
		);
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('StatusMenu') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('StatusMenu'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '订单流程管理');
		$this->smarty->assign('menu', A('StatusMenu')->act_getStatusMenu()); //循环列表
		$this->smarty->assign('group', A('StatusMenu')->act_getMenuGroup());
		$this->smarty->display("statusMenuedit.htm");
	}
	
	public function view_add(){
		$navlist = array (
				array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
				array ('url' => '#', 'title' => '订单流程管理'),
		);
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('StatusMenu') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('StatusMenu'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '订单流程管理');
		$this->smarty->assign('group', A('StatusMenu')->act_getMenuGroup());
		$this->smarty->display("statusMenuadd.htm");
	}
	
	public function view_update(){
		if(!A('StatusMenu')->act_update()){
			$errorinfo    = A('StatusMenu')->act_getErrorMsg();
    		$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
    		$this->error($msg, 'index.php?mod=StatusMenu&act=index');
		}else{
			$this->success(get_promptmsg(200,"修改成功"),"index.php?mod=StatusMenu&act=index&rc=reset");
		}
	}
	
	public function view_insert(){
		if(!A('StatusMenu')->act_insert()){
			$errorinfo    = A('StatusMenu')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=StatusMenu&act=index');
		}else{
			$this->success(get_promptmsg(200,"新增成功"),"index.php?mod=StatusMenu&act=index&rc=reset");
		}
	}
	
	public function view_delete(){
		$this->ajaxReturn(A('StatusMenu')->act_delete(), A('StatusMenu')->act_getErrorMsg());
	}
}
?>