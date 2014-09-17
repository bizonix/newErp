<?php
/*
 * B仓提货单打印
 *add by :hws
 */
class PrintOrderBView extends BaseView{
	//操作页面
    public function view_printOptimal(){
		$list = isset($_GET['list'])?post_check($_GET['list']):'';
		$this->smarty->assign('list',$list);	
		$navlist = array(array('url'=>'','title'=>'首页'),              //面包屑数据
						array('url'=>'index.php?mod=orderWaitforPrint&act=printList','title'=>'打印发货单'),
                        array('url'=>'index.php?mod=PrintOrderB&act=printOptimal','title'=>'B仓提货单打印'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', 'B仓提货单打印');
		$this->smarty->assign('secnev', 1);
		$toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);

        $secondlevel = '22';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
	
		$this->smarty->display('printOrderB.htm');
    }
	
}