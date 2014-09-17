<?php
/**
 * 运输方式管理
 * @add by yxd ,date 2014/07/08
 */
class PlatformToCarrierView extends BaseView{
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
						'title' => '运输方式管理'
				)
		);
		/* $OA            	   = ;
		$perpage 	       = $OA->act_getPerpage();
		$PlatformToCarriercount    = $OA->act_getPlatformToCarrierCount();
		$pageclass 	       = new Page($PlatformToCarriercount, $perpage, '', 'CN');
		$pageformat        = $PlatformToCarriercount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4); */
		//传递系统配置等(页数,链接等)参数
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '平台黑名单');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('PlatformToCarrier') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('PlatformToCarrier'));
		$this->smarty->assign('platfromList', A('Platform')->act_getPlatformLists());
		$this->smarty->assign('platformToCarrier', A('PlatformToCarrier')->act_getPlatformToCarrier()); //循环列表
		$this->smarty->display("platformToCarrier.htm");
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
						'url'   => 'index.php?mod=PlatformToCarrier&act=index',
						'title' => '运输方式管理'
				),
				array(
						'url'   => '',
						'title' => '修改运输方式信息'
		        )
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '修改运输方式管理信息');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('PlatformToCarrier') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('PlatformToCarrier'));
		$this->smarty->assign('platfromList', A('Platform')->act_getPlatformLists());
		$this->smarty->assign('carrierList',A('PlatformToCarrier')->act_getCarrierFromApi(2));
		$this->smarty->assign('carrierListk',A('PlatformToCarrier')->act_getCarrierFromApi(0));
		$this->smarty->assign('carrierListnk',A('PlatformToCarrier')->act_getCarrierFromApi(1));
		$this->smarty->assign('carrier',A('PlatformToCarrier')->act_getPlatformToCarrierByid());
		$this->smarty->display("platformToCarrieredit.htm");
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
						'url'   => 'index.php?mod=PlatformToCarrier&act=index',
						'title' => '运输方式管理'
				),
				array(
						'url'   => '',
						'title' => '添加平台运输方式'
				)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '平台运输方式');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('PlatformToCarrier') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('PlatformToCarrier'));
		$this->smarty->assign('platfromList', A('Platform')->act_getPlatformLists());
		$this->smarty->assign('carrierList',A('PlatformToCarrier')->act_getCarrierFromApi(2));
		$this->smarty->assign('carrierListk',A('PlatformToCarrier')->act_getCarrierFromApi(0));
		$this->smarty->assign('carrierListnk',A('PlatformToCarrier')->act_getCarrierFromApi(1));
		$this->smarty->display("platformToCarrieradd.htm");
	}
	/**
	 * 更新数据
	 */
	public function view_update(){
		if(!A('PlatformToCarrier')->act_update()){
			$errorinfo    =  A('PlatformToCarrier')->act_getErrorMsg();
			$msg          = empty($errorinfo) ?get_promptmsg(10052):implode('<br>', $errorinfo);
			$this->error($msg,"index.php?mod=PlatformToCarrier&act=index");
		}else{
			$this->success(get_promptmsg(200,"更新成功"),"index.php?mod=PlatformToCarrier&act=index&rc=reset");
		}
	}
	/**
	 * 插入数据
	 */
	public function view_insert(){
		if(!A('PlatformToCarrier')->act_insert()){
			$errorinfo    =  A('PlatformToCarrier')->act_getErrorMsg();
			$msg 		  =  empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=PlatformToCarrier&act=add');
			}else {
				$this->success(get_promptmsg(200, '添加'), 'index.php?mod=PlatformToCarrier&act=index&rc=reset');
			}
	}
	/**
	 * 删除数据
	 */
	public function view_delete(){
		if(!A('PlatformToCarrier')->act_delete()){
			$errorinfo    =  A('PlatformToCarrier')->act_getErrorMsg();
			$msg 		  =  empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=PlatformToCarrier&act=index');
		}else {
			$this->success(get_promptmsg(200, '删除成功'), 'index.php?mod=PlatformToCarrier&act=index&rc=reset');
		}
	}
	/**
	 * ajax请求
	 * 通过平台id获取account信息
	 * ajax返回
	 */
	public function view_getAccountByPId(){
		$this->ajaxReturn(A('PlatformToCarrier')->act_getAccountByPId(), A('PlatformToCarrier')->act_getErrorMsg());
	}
	
	public function view_checkExit(){
		$this->ajaxReturn(A('PlatformToCarrier')->act_checkExit(), A('PlatformToCarrier')->act_getErrorMsg());
	}
}
?>