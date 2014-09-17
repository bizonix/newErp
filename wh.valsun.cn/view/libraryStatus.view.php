<?php
/*
 * 流程状态信息页面管理
 * add by:Herman.Xi
 * 2013-08-31 13:04:00
 */
class LibraryStatusView extends BaseView{  
	//流程状态信息页面
    public function view_libraryStatusList(){
		$reason    = array();
		$statusGroupId = isset($_GET['statusGroupId'])?$_GET['statusGroupId']:'';

		$LibraryStatusAct = new LibraryStatusAct();
		$where  = 'where storeId=1 ';
		
		if($statusGroupId){
			$where .= "and groupId= '{$statusGroupId}' ";
		}
		$total = $LibraryStatusAct->act_getLibraryStatusNum($where);
		$num      = 20;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		
		$LibraryStatusInfo = $LibraryStatusAct->act_getLibraryStatusList('*',$where);
		$LibraryStatusGroupInfo = $LibraryStatusAct->act_getLibraryStatusGroupList("");
		
		if(!empty($_GET['page'])){
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num)){
				$n=1;
			}else{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num){
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else{
			$show_page = $page->fpage(array(0,2,3));
		}
		$this->smarty->assign('show_page',$show_page);

		$this->smarty->assign('LibraryStatusInfo', $LibraryStatusInfo);
		$this->smarty->assign('LibraryStatusGroupInfo', $LibraryStatusGroupInfo);
		
		$this->smarty->assign('statusGroupId', $statusGroupId);
		$navlist = array(array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓库设置'),         //面包屑数据
                        array('url'=>'index.php?mod=LibraryStatus&act=libraryStatusList','title'=>'状态管理')
                );
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = "08";   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '仓库状态码列表');
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('libraryStatusShow.htm');
    }
	
	//流程状态信息页面
    public function view_libraryStatusGroupList(){
		$reason    = array();

		$LibraryStatusAct = new LibraryStatusAct();
		$where  = ' and storeId=1 ';
		
		$total = $LibraryStatusAct->act_getLibraryStatusGroupNum($where);
		$num      = 10;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		
		$LibraryStatusGroupInfo = $LibraryStatusAct->act_getLibraryStatusAllGroup($where);
		if(!empty($_GET['page'])){
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num)){
				$n=1;
			}else{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num){
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else{
			$show_page = $page->fpage(array(0,2,3));
		}
		$this->smarty->assign('show_page',$show_page);

		$this->smarty->assign('LibraryStatusGroupInfo', $LibraryStatusGroupInfo);
		
		$navlist = array(
		                array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓库设置'),         //面包屑数据
                        array('url'=>'index.php?mod=LibraryStatus&act=libraryStatusList','title'=>'仓库状态码'),
						array('url'=>'index.php?mod=LibraryStatus&act=libraryStatusGroupList','title'=>'仓库状态码分组列表')
                   );
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = "08";   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '仓库状态码分组列表');
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('libraryStatusGroupShow.htm');
    }
}