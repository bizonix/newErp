<?php
/**
 * 名称类型管理 nameTypeManage.view.php
 * @author chenwei 2013.11.1
 */
class NameTypeManageView extends BaseView {
	
	 private $where = '';
	 private $table = '';

	//列表显示页面、搜索功能页面渲染
	public function view_nameTypeManageList() {
		//基础代码准备
		$NameTypeManage = new nameTypeManageAct();	
			
		//搜索操作
		$condition    = array();
		$keyWords     = "";
		if(isset($_POST) && !empty($_POST)){			
			$keyWords	  = trim($_POST['keyWords']);//填写类型名称
			if(!empty($keyWords)){
				$condition[] = "typeName = '{$keyWords}'";		
				$this->where = "WHERE ".implode(" and ",$condition)." ORDER BY addTime DESC ";		
			}else{
				$this->where = " ORDER BY addTime DESC ";
			}
			
		}else{
			//默认
			$this->where = " ORDER BY addTime DESC ";
		}
		
		//分页
		$total 			 = $NameTypeManage->act_getPageNum();
		$num     		 = 100;//每页显示的个数
		$page     		 = new Page($total,$num,'','CN');
		$this->where    .= $page->limit;
        $nameTypeListArr   = $NameTypeManage->act_nameTypeManageList($this->where);
		
		//分页
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$this->smarty->assign('show_page',$show_page);
			
		//面包屑
		$navlist = array (array ('url' => 'index.php?mod=nameSystem&act=nameSystemList','title' => '系统名称中心'),
						  array ('url' => 'index.php?mod=nameTypeManage&act=nameTypeManageList','title' => '名称分类管理'));
		$this->smarty->assign('navlist', $navlist);//二级导航
		$this->smarty->assign('toplevel', 0);//一级导航		
		$this->smarty->assign('nameTypeListArr', $nameTypeListArr);//显示数组
		//搜索选项值保留
		$this->smarty->assign('keyWords', $keyWords);
		$this->smarty->display("nameTypeManageList.htm");
		
	}
} 