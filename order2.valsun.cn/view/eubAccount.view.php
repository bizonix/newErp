<?php
/**
 * eub授权设置
 * @author yxd 2014/07/23
 * 
 */
class EubAccountView extends BaseView{
	public function __construct() {
		parent::__construct();
	}
	
	public function view_eubset(){
		$navlist     = array (
				array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
				array ('url' => 'index.php?mod=Account&act=index', 'title' => '账号管理'),
				array ('url' => '#', 'title' => 'EUB授权设置')
		);
		$eubInfo    = A('EubAccount')->act_getEubList();
		$eubInfo    = $eubInfo[0];
	    $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('Account') );//account下设置EUB
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('Account'));//account下设置EUB
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '平台管理');
		$this->smarty->assign('eubInfo',$eubInfo);
		$this->smarty->display('eubset.htm');
	}
	
	public function view_saveEub(){
		if(A('EubAccount')->act_saveEub()){
			$this->success(get_promptmsg(200, '保存成功'), 'index.php?mod=Account&act=index&rc=reset');
		}else{
			$errorinfo    = A('Account')->act_getErrorMsg();
			$msg          = empty($errorinfo) ? get_promptmsg(10110,"更新EUB") : implode('<br>', $errorinfo);
			$this->error($msg, 'index.php?mod=Account&act=index');
		}
	}
}
?>