<?php 
class FromOpenConfigView extends BaseView {
	
	public function __construct() {
    	parent::__construct();
    }
    public function view_index(){
    	//面包屑
    	$navlist = array (
    			array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
    			array ('url' => '#', 'title' => '开发接口管理'),
    	);
    	$OA = A('FromOpenConfig');
    	$perpage 	   = $OA->act_getPerpage();
    	$ordercount    = $OA->act_getFromOpenConfigCount();
    	$pageclass 	   = new Page($ordercount, $perpage, '', 'CN');
    	$pageformat    = $ordercount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('FromOpenConfig') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('FromOpenConfig'));
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '平台管理');
    	$this->smarty->assign('FromOpenConfigList', A('FromOpenConfig')->act_getFromOpenConfigLists()); //循环列表
    	$this->smarty->assign('show_page', $pageclass->fpage($pageformat));
    	$this->smarty->display("fromOpenConfig.htm");
    }
    public function view_insert(){
    	if(!A('FromOpenConfig')->act_insert()){
    		$errorinfo    = A('FromOpenConfig')->act_getErrorMsg();
    		$msg          = empty($errorinfo) ? get_promptmsg(10109) : implode('<br>', $errorinfo);
    		$this->error($msg, 'index.php?mod=FromOpenConfig&act=index');
    	}else {
			$this->success(get_promptmsg(200, '添加'), 'index.php?mod=FromOpenConfig&act=index&rc=reset');
		}
    }
    
    public function view_delformList(){
    	if (!A('FromOpenConfig')->act_delete()){
    		$errorinfo    =  A('FromOpenConfig')->act_getErrorMsg();
    		$msg 		  =  empty($errorinfo) ? get_promptmsg(10110,"删除开放接口") : implode('<br>', $errorinfo);
    		$this->error($msg, 'index.php?mod=FromOpenConfig&act=index');
    	}else {
    		$this->success(get_promptmsg(200, '删除成功'), 'index.php?mod=FromOpenConfig&act=index&rc=reset');
    	}
    }
    
    public function view_update(){
    	if(!A('FromOpenConfig')->act_update()){
    		$errorinfo    = A('FromOpenConfig')->act_getErrorMsg();
    		$msg 		  =  empty($errorinfo) ? get_promptmsg(10110,"更新开发接口") : implode('<br>', $errorinfo);
    	    $this->error($msg, 'index.php?mod=FromOpenConfig&act=index');
    	}else {
    		$this->success(get_promptmsg(200, '修改成功'), 'index.php?mod=FromOpenConfig&act=index&rc=reset');
    	}
    }
    
    /**
     * 渲染编辑页面
     */
    public function view_edit(){
    	$navlist = array (//面包屑
    			array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
    			array ('url' => 'index.php?mod=fromOpenConfig&act=index', 'title' => '开发接口管理'),
    			array ('url' => '','title' => '修改开发接口')
    	);
    	$fromOpenConfig    = A('fromOpenConfig')->act_getfromOpenConfigByid();
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '修改平台信息');
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('FromOpenConfig') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('FromOpenConfig'));
    	$this->smarty->assign('fromOpenConfig',$fromOpenConfig);
    	$this->smarty->display("fromOpenConfigedit.htm");
    }
    
}
?>