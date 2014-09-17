<?php
/*
 * PlatformView
 * @author zqt
 * @copyright 2013
 * @version 1.0
 * @access public
 * @modify by lzx ,date 20140525
 */
class PlatformView extends BaseView {
	
	public function __construct() {
    	parent::__construct();
    }
	
	public function view_index(){
		//面包屑
		$navlist = array (
	   		array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
			array ('url' => '#', 'title' => '平台管理'),
		);
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Platform') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Platform'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '平台管理');
		$this->smarty->assign('PlatformList', A('Platform')->act_getPlatformLists()); //循环列表
		$this->smarty->display("platformIndex.htm");
	}

	public function view_insert(){
		if (!A('Platform')->act_insert()){
			$errorinfo = A('Platform')->act_getErrorMsg();
			$msg = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=platform&act=index&rc=reset');
		}else {
			$this->success(get_promptmsg(200, "添加平台"), 'index.php?mod=platform&act=index&rc=reset');
		}
	}

    public function view_update(){
   	 	if (!A('Platform')->act_update()){
			$errorinfo = A('Platform')->act_getErrorMsg();
			$msg = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=platform&act=index');
		}else {
			$this->success(get_promptmsg(200, '修改平台'), 'index.php?mod=platform&act=index&rc=reset');
		}
	}
	
	/**
	 * 渲染修改页面数据
	 */
	public function view_edit(){
		$navlist = array (//面包屑
				array (
						'url' => 'index.php?mod=Platform&act=index',
						'title' => '系统设置'
				),
				array (
						'url' => 'index.php?mod=platform&act=index',
						'title' => '平台管理'
				),
				array (
						'url' => '',
						'title' => '修改账号'
				)
		);
		$platform    = A('platform')->act_getPlatformInfoByid();
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '修改平台信息');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Platform') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Platform'));
		$this->smarty->assign('platform',$platform);
		$this->smarty->display("platformedit.htm");
	}
	
	public function view_delete(){
   	 	if (!A('Platform')->act_delete()){
			$errorinfo = A('Platform')->act_getErrorMsg();
			$msg = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=platform&act=index');
		}else {
			$this->success(get_promptmsg(200, '删除平台'), 'index.php?mod=platform&act=index&rc=reset');
		}
	}
}