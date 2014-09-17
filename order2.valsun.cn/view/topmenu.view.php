<?php
/**
 * 
 * 导航管理
 * @author yxd 2014/7/3
 *
 */
class TopmenuView extends BaseView {
	
	public function __construct() {
    	parent::__construct();
    }
    
    /**
     * 导航管理首页渲染
     */
    public function view_index(){
    	//面包屑
    	$navlist = array (
    			array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
    			array ('url' => 'index.php?mod=topmenu&act=index', 'title' => '导航管理'),
    	);
    	$OA = A('Topmenu');
    	$perpage 	     = $OA->act_getPerpage();
    	$topmenucount    = $OA->act_getTopmenuCount();
    	$pageclass 	     = new Page($topmenucount, $perpage, '', 'CN');
    	$pageformat      = $topmenucount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('topmenu'));
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('topmenu'));
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '导航管理');
    	$this->smarty->assign('TopmenuList', A('Topmenu')->act_getTopmenuLists()); //循环列表
    	$this->smarty->assign('show_page', $pageclass->fpage($pageformat));
    	$this->smarty->display("topmenuindex.htm");
    }
    
    /**
     * 导航管理添加页面渲染
     */
    public function view_add(){
    	$navlist = array (//面包屑
    			array (
    					'url' => 'index.php?mod=Platform&act=index',
    					'title' => '系统设置'
    			),
    			array (
    					'url' => 'index.php?mod=Topmenu&act=index',
    					'title' => '导航管理'
    			),
    			array (
    					'url' => '',
    					'title' => '添加导航'
    			)
    	);
    	$Topmenu       = A('Topmenu')->act_getmenutree();//二级和操作级导航
    	$top1           = A('Topmenu')->act_getmenuTop1();//一级导航
    	$top2           = A('Topmenu')->act_getmenuTop2();//二级导航
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '添加导航信息');
        $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('topmenu'));
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('topmenu'));
    	$this->smarty->assign('Topmenu',$Topmenu);
    	$this->smarty->assign('top1',$top1);
    	$this->smarty->assign('top2',$top2);
    	$this->smarty->display("topmenuadd.htm");
    }
    
    public function view_insert(){
    	if(!A('Topmenu')->act_insert()){
    		$errorinfo    = A('Topmenu')->act_getErrorMsg();
    		$msg          = empty($errorinfo) ? get_promptmsg(10100,"添加导航条") : implode('<br>', $errorinfo);
    		$this->error($msg, 'index.php?mod=Topmenu&act=index');
    	}else {
			$this->success(get_promptmsg(200, '添加导航条'), 'index.php?mod=Topmenu&act=index&rc=reset');
		}
    }
    
    public function view_delete(){
    	if (!A('Topmenu')->act_delete()){
    		$errorinfo    =  A('Topmenu')->act_getErrorMsg();
    		$msg 		  =  empty($errorinfo) ? get_promptmsg(10100,"删除导航条") : implode('<br>', $errorinfo);
    		$this->error($msg, 'index.php?mod=Topmenu&act=index');
    	}else {
    		$this->success(get_promptmsg(200, '删除成功'), 'index.php?mod=Topmenu&act=index&rc=reset');
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
    					'url' => 'index.php?mod=Topmenu&act=index',
    					'title' => '导航管理'
    			),
    			array (
    					'url' => '',
    					'title' => '修改导航信息'
    			)
    	);
    	$TopmenuList    = A('Topmenu')->act_getTopmenuByid();
    	$Topmenu        = A('Topmenu')->act_getmenutree();//二级和操作级导航
    	$top1           = A('Topmenu')->act_getmenuTop1();//一级导航
    	$top2           = A('Topmenu')->act_getmenuTop2();//二级导航
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '修改平台信息');
    	$this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('topmenu'));
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('topmenu'));
    	$this->smarty->assign('TopmenuList',$TopmenuList);
    	$this->smarty->assign('TopmenuAll',$Topmenu);
    	$this->smarty->assign('top1',$top1);
    	$this->smarty->assign('top2',$top2);
    	$this->smarty->display("topmenuedit.htm");
    }
    
    /**
     * 执行修改
     */
    public function view_update(){
    	if(!A('Topmenu')->act_update()){
    		$errorinfo    = A('Topmenu')->act_getErrorMsg();
    		$msg 		  =  empty($errorinfo) ? get_promptmsg(10100,"修改导航条") : implode('<br>', $errorinfo);
    	    $this->error($msg, 'index.php?mod=Topmenu&act=index');
    	}else {
    		$this->success(get_promptmsg(200, '修改成功'), 'index.php?mod=Topmenu&act=index&rc=reset');
    	}
    }
}
?>