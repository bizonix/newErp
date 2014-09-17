<?php
/*
 * iqc领取页
 */
class IqcView extends BaseView{  
	//iqc等待领取
    public function view_iqcList(){		
		$sku    = isset($_GET['sku'])?post_check($_GET['sku']):'';
		$IqcAct = new IqcAct();
		$where  = 'where sellerId=0 and detectStatus=0 ';
		if($sku){
			$where .= "and sku ='$sku' ";
			$this->smarty->assign('sku',$sku); 
		}

		$total = $IqcAct->act_getNowWhNum($where);
		$num      = 200;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		
		$iqcList = $IqcAct->act_getNowWhList('*',$where);

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
		
		$this->smarty->assign('iqcList',$iqcList); 

		$this->smarty->assign('secnev','1');               //二级导航
		$this->smarty->assign('module','SKU等待领取');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqc&act=iqcList'>iqc检测领取</a>",">>","等待领取");
        $this->smarty->assign('navarr',$navarr);
		
		$this->smarty->display('iqcList.html');
    }
	
	//iqc等待检测
    public function view_iqcWaitCheck(){
		$IqcAct  = new IqcAct();
		$where  = "where sellerId=0 and detectStatus=1 and getUserId='{$_SESSION['userId']}' ";
		
		$total = $IqcAct->act_getNowWhNum($where);
		$num      = 200;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		
		$iqcList = $IqcAct->act_getNowWhList('*',$where);

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
		$this->smarty->assign('iqcList',$iqcList); 
		$this->smarty->assign('secnev','1');               //二级导航
		$this->smarty->assign('module','SKU等待检测');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqc&act=iqcList'>iqc检测领取</a>",">>","等待检测");
        $this->smarty->assign('navarr',$navarr);
		
		$this->smarty->display('iqcWaitCheck.html');
    }
}