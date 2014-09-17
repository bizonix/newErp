<?php
/*
 * iqc领取页
 * last modified by Herman.Xi @ 20131016
 */
class IqcView extends BaseView{  
	//iqc等待领取
    public function view_iqcList(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$sku    = isset($_GET['sku'])?post_check($_GET['sku']):'';
		$IqcAct = new IqcAct();
		$where  = 'where sellerId=1 and detectStatus=0 and num>0 and is_delete=0 ';
		if($sku){
			$where .= "and sku ='$sku' ";
			$this->smarty->assign('sku',$sku); 
		}

		$total = $IqcAct->act_getNowWhNum($where);
		$num      = 100;//每页显示的个数
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
		
		//产品中心信息
		$sku_arr = array();
		$sku_str = '';
		foreach($iqcList as $list){
			$sku_str .= "'".$list['sku']."',";
		}
		$sku_str = "(".trim($sku_str,',').")";
		$skuinfo = UserCacheModel::goodsInfosCache("*",base64_encode( "sku in {$sku_str}"));
		if(isset($skuinfo['data'])){
			foreach($skuinfo['data'] as $info){
				$sku_arr[$info['sku']] = $info['goodsName'];
			}
		}
		$hasDelete = false; //屏蔽删除功能
		if($_SESSION['userName'] == 'chenzhiqiang@sailvan.com'){
			$hasDelete = true;
		}
		$this->smarty->assign('sku_arr',$sku_arr); 
		
		$this->smarty->assign('secnev','1');               //二级导航
		$this->smarty->assign('module','SKU等待领取');
		$this->smarty->assign('username',$_SESSION['userName']);
		$this->smarty->assign('hasDelete',$hasDelete);
		
		$navarr = array("<a href='index.php?mod=iqc&act=iqcList'>QC检测领取</a>",">>","等待领取");
        $this->smarty->assign('navarr',$navarr);
		
		$this->smarty->display('iqcList.htm');
    }
	
	//iqc等待检测
    public function view_iqcWaitCheck(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$IqcAct  = new IqcAct();
		$where  = "where sellerId=1 and is_delete=0 and detectStatus=1 and getUserId='{$_SESSION['sysUserId']}' ";
		
		$total = $IqcAct->act_getNowWhNum($where);
		$num      = 100;//每页显示的个数
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
		//产品中心信息
		$sku_arr = array();
		$sku_str = '';
		foreach($iqcList as $list){
			$sku_str .= "'".$list['sku']."',";
		}
		$sku_str = "(".trim($sku_str,',').")";
		$skuinfo = UserCacheModel::goodsInfosCache("*",base64_encode( "sku in {$sku_str}"));
		foreach($skuinfo['data'] as $info){
			$sku_arr[$info['sku']] = $info['goodsName'];
		}
		$hasDelete = false; //屏蔽删除功能
		if($_SESSION['userName'] == 'chenzhiqiang@sailvan.com'){
			$hasDelete = true;
		}		
		$this->smarty->assign('sku_arr',$sku_arr); 
		$this->smarty->assign('hasDelete',$hasDelete);
		$this->smarty->assign('secnev','1');               //二级导航
		$this->smarty->assign('module','SKU等待检测');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=iqc&act=iqcList'>QC检测领取</a>",">>","等待检测");
        $this->smarty->assign('navarr',$navarr);
		
		$this->smarty->display('iqcWaitCheck.htm');
    }
    
    //iqc 删除sku查询
    public function view_iqcRestore(){
    	$state  = isset($_GET['state'])?post_check($_GET['state']):'';
    	$this->smarty->assign('state',$state);
    	$sku    = isset($_GET['sku'])?post_check($_GET['sku']):'';
    	$IqcAct = new IqcAct();
    	$where  = " where sellerId=1 and num>0 and is_delete=1 ";
    	if($sku){
    		$where .= "and sku ='$sku' ";
    		$this->smarty->assign('sku',$sku);
    	}
    	 
    	$total = $IqcAct->act_getNowWhNum($where);
    	$num      = 100;//每页显示的个数
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
    	 
    	$sku_arr = array();
    	$sku_str = '';
    	foreach($iqcList as $list){
    		$sku_str .= "'".$list['sku']."',";
    	}
    	$sku_str = "(".trim($sku_str,',').")";
    	$skuinfo = UserCacheModel::goodsInfosCache("*",base64_encode( "sku in {$sku_str}"));
    	foreach($skuinfo['data'] as $info){
    		$sku_arr[$info['sku']] = $info['goodsName'];
    	}
    	$this->smarty->assign('sku_arr',$sku_arr);
    	 
    	$this->smarty->assign('secnev','1');               //二级导航
    	$this->smarty->assign('module','检索已删SKU');
    	$this->smarty->assign('username',$_SESSION['userName']);
    	 
    	$navarr = array("<a href='index.php?mod=iqc&act=iqcRestore'>QC检测领取</a>",">>","搜查已删sku");
    	$this->smarty->assign('navarr',$navarr);
    	 
    	$this->smarty->display('iqcRestore.htm');
    }
}