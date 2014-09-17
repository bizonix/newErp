<?php
/**
 * 黑名单管理
 * 
 * @add by yxd ,date 2014/06/10
 */
class BlacklistView extends BaseView{
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}
	/**
	 * 首页面视图渲染
	 */
	public function view_index(){
		F("order");
		$navlist     	= array (//面包屑
				array (
						'url'   => 'index.php?mod=Platform&act=index',
						'title' => '系统设置'
				),
				array (
						'url'   => '',
						'title' => '平台黑名单'
				)
		);
		$OA            	   = A('Blacklist');
		$perpage 	       = $OA->act_getPerpage();
		$blacklistcount    = $OA->act_getBlacklistCount();
		$pageclass 	       = new Page($blacklistcount, $perpage, '', 'CN');
		$pageformat        = $blacklistcount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
		
		//传递系统配置等(页数,链接等)参数
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '平台黑名单');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Blacklist') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Blacklist'));
		$this->smarty->assign('show_page', $pageclass->fpage($pageformat));
		
		//传递查询出来的数据
		$this->smarty->assign('accountAll', A('Account')->act_getAccountAll());
		$this->smarty->assign('platfromIAN', A('Platform')->act_getPlatformLists());
		$this->smarty->assign('BlackList', $OA->act_getBlackList()); //循环列表
		$this->smarty->display("blacklistindex.htm");
	}
	/**
	 * 编辑页面渲染
	 */
	public function view_edit(){
		F("order");
		$navlist     	= array (//面包屑
				array (
						'url'   => 'index.php?mod=Platform&act=index',
						'title' => '系统设置'
				),
				array (
						'url'   => 'index.php?mod=blacklist&act=index',
						'title' => '平台黑名单管理'
				),
				array(
						'url'   => '',
						'title' => '修改黑名单信息'
		        )
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '修改黑名单信息');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Blacklist') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Blacklist'));
		$this->smarty->assign('platfromList', A('Platform')->act_getPlatformLists());
		$this->smarty->assign('BlackList',A('Blacklist')->act_getBlackListByid());
		$this->smarty->assign('accountAll', A('Blacklist')->act_getaccountOptionByPid());//渲染平台对应的账户
		$this->smarty->display("blacklistedit.htm");
	}
	/**
	 * 添加页面渲染
	 */
	public function view_add(){
		$navlist     	= array (//面包屑
				array (
						'url'   => 'index.php?mod=Platform&act=index',
						'title' => '系统设置'
				),
				array (
						'url'   => 'index.php?mod=blacklist&act=index',
						'title' => '平台黑名单管理'
				),
				array(
						'url'   => '',
						'title' => '添加黑名单'
				)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '平台黑名单');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Blacklist') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Blacklist'));
		$this->smarty->assign('platfromIAN', A('Platform')->act_getPlatformLists());
		$this->smarty->assign('account', A('Blacklist')->act_getAccountByPId());
		$this->smarty->display("blacklistadd.htm");
	}
	/**
	 * 更新数据
	 */
	public function view_update(){
		if(!A('Blacklist')->act_update()){
			$errorinfo    =  A('Blacklist')->act_getErrorMsg();
			$msg          = empty($errorinfo) ?get_promptmsg(10052):implode('<br>', $errorinfo);
			$this->error($msg,"index.php?mod=Blacklist&act=index");
		}else{
			$this->success(get_promptmsg(200,"更新成功"),"index.php?mod=Blacklist&act=index&rc=reset");
		}
	}
	/**
	 * 插入数据
	 */
	public function view_insert(){
		if(!A('Blacklist')->act_insert()){
			$errorinfo    =  A('Blacklist')->act_getErrorMsg();
			$msg 		  =  empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=Blacklist&act=add');
			}else {
				$this->success(get_promptmsg(200, '添加'), 'index.php?mod=Blacklist&act=add&rc=reset');
			}
	}
	/**
	 * 删除数据
	 */
	public function view_delete(){
		if(!A('Blacklist')->act_delete()){
			$errorinfo    =  A('Blacklist')->act_getErrorMsg();
			$msg 		  =  empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=Blacklist&act=index');
		}else {
			$this->success(get_promptmsg(200, '删除成功'), 'index.php?mod=Blacklist&act=index&rc=reset');
		}
	}
	/**
	 * ajax请求
	 * 通过平台id获取account信息
	 * ajax返回
	 */
	public function view_getAccountByPId(){
		$this->ajaxReturn(A('Blacklist')->act_getAccountByPId(), A('Blacklist')->act_getErrorMsg());
	}
}
?>