<?php
/**
 * @author  yxd
 * @copyright 2014/06/12
 * @version 1.0
 * @access public
 */
class paypalEmailView extends BaseView{
	
	public function view_index(){
		F('order');
		$navlist     = array (
				array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
				array ('url' => '#', 'title' => 'paypal邮箱管理'),
		);
		$OA              = A('paypalEmail');
		$perpage 	     = $OA->act_getPerpage();
		$pCount    = $OA->act_getpaypalEmailCount();
		$pageclass 	     = new Page($pCount, $perpage, '', 'CN');
		$pageformat      = $pCount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('paypalEmail') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('paypalEmail'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', 'paypal付款邮箱');
		$this->smarty->assign('paypalEmailLists', A('PaypalEmail')->act_getPaypalEmailList()); //循环列表
		$this->smarty->assign('accountAll',A('Account')->act_getAccountAll());//账户id和name
		/* var_dump(A('PaypalEmail')->act_getPaypalEmailList());exit; */
		$this->smarty->assign('show_page', $pageclass->fpage($pageformat));
		$this->smarty->display("paypalEmailindex.htm");
	}
	
	public function view_add(){
		$navlist = array (//面包屑
			array (
				'url' => 'index.php?mod=Platform&act=getOmPlatformList',
				'title' => '系统设置'
			),
			array (
				'url' => 'index.php?mod=paypalEmail&act=index',
				'title' => 'paypal邮箱管理'
			),
            array (
				'url' => '',
				'title' => '添加paypal付款邮箱'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '添加paypal付款邮箱');
        $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('paypalEmail') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('paypalEmail'));
		$this->smarty->assign('accountAll',A('Account')->act_getAccountAll());//账户id和name
		$this->smarty->display("paypalEmailadd.htm");
	}
	
	public function view_insert(){
		$this->ajaxReturn(A('paypalEmail')->act_insert(), A('paypalEmail')->act_getErrorMsg());
		/* if(!A('paypalEmail')->act_insert()){
			$errorinfo    = A('paypalEmail')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=paypalEmail&act=index');
		}else {
			$this->success(get_promptmsg(200, '添加成功'), 'index.php?mod=paypalEmail&act=index&rc=reset');
		} */
	}
	
	public function view_delete(){
		$this->ajaxReturn(A('paypalEmail')->act_delete(), A('paypalEmail')->act_getErrorMsg());
		/*  if(!A('paypalEmail')->act_delete()){
			$errorinfo    = A('paypalEmail')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=paypalEmail&act=index');
		}else {
			$this->success(get_promptmsg(200, '删除成功'), 'index.php?mod=paypalEmail&act=index&rc=reset');
		}  */
	}
	public function view_edit(){
		//设置修改页面上指定字段的值
		$navlist = array (//面包屑
				array (
						'url' => 'index.php?mod=Platform&act=index',
						'title' => '系统设置'
				),
				array (
						'url' => 'index.php?mod=paypalEmail&act=index',
						'title' => 'paypal邮箱管理'
				),
				array (
						'url' => '',
						'title' => '修改paypal邮箱信息'
				)
		);
		$paypalEmailAll    = A('paypalEmail')->act_getPaypalEmialById();
		
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '修改账号');
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('paypalEmail') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('paypalEmail'));
		$this->smarty->assign("paypalEmailAll",$paypalEmailAll);
		$this->smarty->assign('accountAll',A('Account')->act_getAccountAll());//账户id和name
		$this->smarty->display("paypalEmailedit.htm");
	}
	
	public function view_update(){
		if(!A('paypalEmail')->act_update()){
			$errorinfo    = A('paypalEmail')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=paypalEmail&act=index');
		}else {
			$this->success(get_promptmsg(200, '修改'), 'index.php?mod=paypalEmail&act=index&rc=reset');
		}
	}
}
?>