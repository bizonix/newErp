<?php
class PromptMsgView extends BaseView {
	
	public function __construct() {
    	parent::__construct();
    }
    public function view_index(){
    	//面包屑
    	$navlist = array (
    			array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
    			array ('url' => '#', 'title' => '错误提示设置'),
    	);
    	$OA = A('PromptMsg');
    	$perpage 	   = $OA->act_getPerpage();
    	$ordercount    = $OA->act_getPromptMsgCount();
    	$pageclass 	   = new Page($ordercount, $perpage, '', 'CN');
    	$pageformat    = $ordercount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('PromptMsg') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('PromptMsg'));
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '错误提示信息管理');
    	$this->smarty->assign('PromptMsgList', A('PromptMsg')->act_getPromptMsgLists()); //循环列表
    	$this->smarty->assign('show_page', $pageclass->fpage($pageformat));
    	$this->smarty->display("promptMsgIndex.htm");
    }
    public function view_insert(){
    	if(!A('PromptMsg')->act_insert()){
    		$errorinfo    = A('PromptMsg')->act_getErrorMsg();
    		$msg          = empty($errorinfo) ? get_promptmsg(10104) : implode('<br>', $errorinfo);
    		$this->error($msg, 'index.php?mod=PromptMsg&act=index');
    	}else {
			$this->success(get_promptmsg(200, '添加错误信息'), 'index.php?mod=PromptMsg&act=index&rc=reset');
		}
    }
    
    public function view_delformList(){
    	if (!A('PromptMsg')->act_delete()){
    		$errorinfo    =  A('PromptMsg')->act_getErrorMsg();
    		$msg 		  =  empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
    		$this->error($msg, 'index.php?mod=PromptMsg&act=index');
    	}else {
    		$this->success(get_promptmsg(200, '删除成功'), 'index.php?mod=PromptMsg&act=index&rc=reset');
    	}
    }
    
    /**
     * 编辑页面渲染
     */
    public function view_edit(){
    	$navlist = array (//面包屑
    			array (
    					'url' => 'index.php?mod=Platform&act=index',
    					'title' => '系统设置'
    			),
    			array (
    					'url' => 'index.php?mod=promptMsg&act=index',
    					'title' => '错误提示设置'
    			),
    			array (
    					'url' => '',
    					'title' => '修改提示信息'
    			)
    	);
    	$promptmsg    = A('promptMsg')->act_getPromptMsgByid();
    	
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '修改提示信息');
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('PromptMsg') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('PromptMsg'));
    	$this->smarty->assign('promptMsg',$promptmsg[0]);
    	$this->smarty->display("promptMsgedit.htm");
    }
    
    /**
     * 执行修改
     */
    public function view_update(){
    	if(!A('PromptMsg')->act_update()){
    		$errorinfo    = A('PromptMsg')->act_getErrorMsg();
    		$msg 		  =  empty($errorinfo) ? get_promptmsg(10103) : implode('<br>', $errorinfo);
    	    $this->error($msg, 'index.php?mod=PromptMsg&act=index');
    	}else {
    		$this->success(get_promptmsg(200, '修改成功'), 'index.php?mod=PromptMsg&act=index&rc=reset');
    	}
    }
}
?>