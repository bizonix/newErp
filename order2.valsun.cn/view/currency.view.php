<?php
/**
* 汇率管理
*add by:yxd
*/
class CurrencyView extends BaseView{
	/**
	 * 
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}
	public function view_index(){
		//面包屑
    	$navlist = array (
    			array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
    			array ('url' => '#', 'title' => '汇率管理'),
    	);
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Currency') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Currency'));
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '汇率管理');
    	$this->smarty->assign('currencyList', A('Currency')->act_getCurrencyList()); //循环列表
    	$this->smarty->display("currencyindex.htm");
	}
	public function view_edit(){
		$navlist = array (
				array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
				array ('url' => '#', 'title' => '汇率管理'),
		);
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Currency') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Currency'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '汇率管理');
		$this->smarty->assign('currency', A('Currency')->act_getCurrency()); //循环列表
		$this->smarty->display("currencyedit.htm");
	}
	public function view_add(){
		$navlist = array (
				array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
				array ('url' => '#', 'title' => '汇率管理'),
		);
		$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Currency') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Currency'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '汇率管理');
		$this->smarty->display("currencyadd.htm");
	}
	public function view_update(){
		if(!A('Currency')->act_update()){
			$errorinfo    = A('Curecny')->act_getErrorMsg();
    		$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
    		$this->error($msg, 'index.php?mod=FromOpenConfig&act=index');
		}else{
			$this->success(get_promptmsg(200,"修改成功"),"index.php?mod=Currency&act=index&re=reset");
		}
	}
	public function view_insert(){
		if(!A('Currency')->act_insert()){
			$errorinfo    = A('Curecny')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=FromOpenConfig&act=index');
		}else{
			$this->success(get_promptmsg(200,"新增成功"),"index.php?mod=Currency&act=index&re=reset");
		}
	}
}
?>