<?php
/**
 * OmAccountView
 *
 * @package order.valsun.cn
 * @author  yxd
 * @copyright 2014/06/09
 * @version 1.0
 * @access public
 */
class AccountView extends BaseView{
	
	public function view_index(){
		F('order');
		$navlist     = array (
				array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
				array ('url' => '#', 'title' => '账号管理'),
		);
		$OA              = A('account');
		$perpage 	     = $OA->act_getPerpage();
		$accountcount    = $OA->act_getAccountCount();
		$pageclass 	     = new Page($accountcount, $perpage, '', 'CN');
		$pageformat      = $accountcount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Account') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Account'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '账号管理');
		$this->smarty->assign('accountList', A('account')->act_getaccountList()); //循环列表
		$this->smarty->assign('accountAll',A('account')->act_getAccountAll());//账户id和name
		$this->smarty->assign('platfromIAN',A('platform')->act_getPlatformLists());//平台id和name
		$this->smarty->assign('show_page', $pageclass->fpage($pageformat));
		$this->smarty->display("accountindex.htm");
	}
	
	public function view_add(){
		$navlist = array (//面包屑
			array (
				'url' => 'index.php?mod=Platform&act=index',
				'title' => '系统设置'
			),
			array (
				'url' => 'index.php?mod=Account&act=index',
				'title' => '平台账号'
			),
            array (
				'url' => '',
				'title' => '添加账号'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '添加账号');
       $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Account') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Account'));
		$this->smarty->assign('accountAll',A('platform')->act_getPlatformLists());//账户id和name
		$this->smarty->display("accountadd.htm");
	}
	
	public function view_insert(){
		if(!A('Account')->act_insert()){
			$errorinfo    = A('Account')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=Account&act=index');
		}else {
			$this->success(get_promptmsg(200, '添加'), 'index.php?mod=Account&act=index&rc=reset');
		}
	}
	
	public function view_delete(){
		if(!A('Account')->act_delete()){
			$errorinfo    = A('Account')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=Account&act=index');
		}else {
			$this->success(get_promptmsg(200, '删除成功'), 'index.php?mod=Account&act=index&rc=reset');
		}
	}
	public function view_edit(){
		//设置修改页面上指定字段的值
		$navlist = array (//面包屑
				array (
						'url' => 'index.php?mod=Platform&act=index',
						'title' => '系统设置'
				),
				array (
						'url' => 'index.php?mod=Account&act=index',
						'title' => '平台账号'
				),
				array (
						'url' => '',
						'title' => '修改账号'
				)
		);
		$accountAll    = A('account')->act_getAccountList();
		$accountAll    = $accountAll[0];
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '修改账号');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Account') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Account'));
		$this->smarty->assign("accountAll",$accountAll);
		$this->smarty->assign('platformAll',A('platform')->act_getPlatformLists());
		$this->smarty->display("accountedit.htm");
	}
	
	public function view_update(){
		if(!A('Account')->act_update()){
			$errorinfo    = A('Account')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=Account&act=index');
		}else {
			$this->success(get_promptmsg(200, '修改成功'), 'index.php?mod=Account&act=index&rc=reset');
		}
	}
}
?>